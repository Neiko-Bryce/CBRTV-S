<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Election;
use App\Models\Student;
use App\Models\User;
use App\Models\Vote;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    /**
     * Campus admins: restrict analytics to their school only. Super admins: all schools.
     * Returns null for super admin (no extra where), int for campus admin, or -1 if admin has no campus (empty stats).
     */
    private function analyticsSchoolScopeId(): ?int
    {
        $user = auth()->user();
        if (! $user) {
            return -1;
        }
        if ($user->is_super_admin) {
            return null;
        }
        if ($user->school_id) {
            return (int) $user->school_id;
        }

        return -1;
    }

    /**
     * Apply school filter to vote/election/student queries for campus admins.
     *
     * @param  \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder  $query
     * @param  string  $column  Qualified column name, e.g. 'votes.school_id' or 'school_id'
     */
    private function applyAnalyticsSchoolFilter($query, string $column = 'school_id'): void
    {
        $scopeId = $this->analyticsSchoolScopeId();
        if ($scopeId === null) {
            return;
        }
        if ($scopeId < 0) {
            $query->whereRaw('0 = 1');

            return;
        }
        $query->where($column, $scopeId);
    }

    /**
     * Vote rows for campus admins: match votes.school_id, or (legacy) null school_id on votes whose election belongs to the campus.
     * Prevents cross-campus leaks while still counting ballots inserted without school_id before that was fixed.
     */
    private function applyVoteSchoolScopeForAnalytics($query): void
    {
        $scopeId = $this->analyticsSchoolScopeId();
        if ($scopeId === null) {
            return;
        }
        if ($scopeId < 0) {
            $query->whereRaw('0 = 1');

            return;
        }

        $table = (new Vote)->getTable();

        $query->where(function ($q) use ($scopeId, $table) {
            $q->where("{$table}.school_id", $scopeId)
                ->orWhere(function ($q2) use ($scopeId, $table) {
                    $q2->whereNull("{$table}.school_id")
                        ->whereExists(function ($sub) use ($scopeId, $table) {
                            $sub->from('elections')
                                ->whereColumn('elections.id', "{$table}.election_id")
                                ->where('elections.school_id', $scopeId);
                        });
                });
        });
    }

    /**
     * Eloquent base queries without BelongsToSchool global scope so we apply a strict school_id (no cross-campus NULL leak).
     */
    private function votesForAnalytics()
    {
        return Vote::withoutGlobalScopes()->tap(fn ($q) => $this->applyVoteSchoolScopeForAnalytics($q));
    }

    private function electionsForAnalytics()
    {
        return Election::withoutGlobalScopes()->tap(fn ($q) => $this->applyAnalyticsSchoolFilter($q));
    }

    private function usersForAnalytics()
    {
        return User::withoutGlobalScopes()->tap(fn ($q) => $this->applyAnalyticsSchoolFilter($q));
    }

    /**
     * Display the analytics page: voting statistics, trends, and election breakdowns.
     */
    public function index(): View
    {
        $totalStudents = $this->usersForAnalytics()
            ->where('usertype', 'student')
            ->count();
        $totalVotes = $this->votesForAnalytics()->count();
        $uniqueVoters = $this->votesForAnalytics()
            ->distinct('voter_id')
            ->count('voter_id');
        $participationRate = $totalStudents > 0
            ? round(($uniqueVoters / $totalStudents) * 100, 1)
            : 0;

        $totalElections = $this->electionsForAnalytics()->count();
        $completedElections = $this->electionsForAnalytics()
            ->where('status', 'completed')
            ->count();
        $ongoingElections = $this->electionsForAnalytics()
            ->where('status', 'ongoing')
            ->count();
        $upcomingElections = $this->electionsForAnalytics()
            ->where('status', 'upcoming')
            ->count();

        // Votes in the last 7 days (for trend)
        $votesByDay = $this->votesForAnalytics()
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', Carbon::now()->subDays(7)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Fill in missing days with 0
        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $last7Days[] = [
                'date' => $date,
                'label' => Carbon::parse($date)->format('M j'),
                'count' => $votesByDay->get($date)->count ?? 0,
            ];
        }

        $maxVotesInPeriod = $last7Days ? max(array_column($last7Days, 'count')) : 0;

        // Elections with vote statistics
        $electionsWithStatsData = $this->electionsForAnalytics()
            ->with('organization')
            ->withCount([
                'votes' => function ($q) {
                    $q->withoutGlobalScopes();
                    $this->applyVoteSchoolScopeForAnalytics($q);
                },
            ])
            ->orderByDesc('election_date')
            ->orderByDesc('id')
            ->get();

        // Fetch unique voters for all relevant elections in one query
        $uniqueVotersByElection = $this->votesForAnalytics()
            ->whereIn('election_id', $electionsWithStatsData->pluck('id'))
            ->select('election_id', DB::raw('COUNT(DISTINCT voter_id) as count'))
            ->groupBy('election_id')
            ->pluck('count', 'election_id');

        $electionsWithStats = $electionsWithStatsData->map(function ($election) use ($totalStudents, $uniqueVotersByElection) {
            $votesCount = $election->votes_count;
            $uniqueInElection = $uniqueVotersByElection->get($election->id, 0);
            $participation = $totalStudents > 0
                ? round(($uniqueInElection / $totalStudents) * 100, 1)
                : 0;

            return (object) [
                'id' => $election->id,
                'election_name' => $election->election_name,
                'organization' => $election->organization,
                'type_of_election' => $election->type_of_election,
                'status' => $election->status,
                'votes_count' => $votesCount,
                'unique_voters' => $uniqueInElection,
                'participation_percent' => $participation,
                'election_date' => $election->election_date,
            ];
        });

        // Single breakdown: course + year + section (one chart — avoids duplicating the same ballot totals).
        $votesByCourseAndSection = $this->buildVotesByCourseYearSectionBreakdown();

        // NEW: Peak Voting Hours (24-hour breakdown)
        $isPostgres = DB::connection()->getDriverName() === 'pgsql';
        $hourExtract = $isPostgres
            ? DB::raw('EXTRACT(HOUR FROM created_at) as hour')
            : DB::raw('HOUR(created_at) as hour');

        $votesByHour = $this->votesForAnalytics()
            ->select($hourExtract, DB::raw('COUNT(*) as count'))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->keyBy('hour');

        // Fill in all 24 hours
        $peakVotingHours = [];
        for ($h = 0; $h < 24; $h++) {
            $peakVotingHours[] = [
                'hour' => $h,
                'label' => sprintf('%02d:00', $h),
                'count' => (int) ($votesByHour->get($h)->count ?? 0),
            ];
        }
        $maxVotesByHour = max(array_column($peakVotingHours, 'count')) ?: 1;

        // NEW: Election Comparison (last 2 completed elections)
        $lastTwoElections = $this->electionsForAnalytics()
            ->where('status', 'completed')
            ->orderByDesc('election_date')
            ->orderByDesc('id')
            ->take(2)
            ->get()
            ->map(function ($election) use ($totalStudents) {
                $uniqueInElection = $this->votesForAnalytics()
                    ->where('election_id', $election->id)
                    ->distinct('voter_id')
                    ->count('voter_id');
                $participation = $totalStudents > 0
                    ? round(($uniqueInElection / $totalStudents) * 100, 1)
                    : 0;

                return [
                    'name' => $election->election_name,
                    'date' => $election->election_date,
                    'unique_voters' => $uniqueInElection,
                    'participation' => $participation,
                ];
            });

        $electionComparison = [
            'current' => $lastTwoElections->first(),
            'previous' => $lastTwoElections->count() > 1 ? $lastTwoElections->last() : null,
        ];

        return view('admin.analytics.index', compact(
            'totalStudents',
            'totalVotes',
            'uniqueVoters',
            'participationRate',
            'totalElections',
            'completedElections',
            'ongoingElections',
            'upcomingElections',
            'last7Days',
            'maxVotesInPeriod',
            'electionsWithStats',
            'votesByCourseAndSection',
            'peakVotingHours',
            'maxVotesByHour',
            'electionComparison'
        ));
    }

    /**
     * Stable key for year level + section (avoids int/string mismatches in joins).
     */
    private function normalizeYearSectionKey(mixed $yearlevel, mixed $section): string
    {
        $y = $yearlevel;
        if ($y === null || $y === '') {
            $y = 'Unknown';
        } elseif (is_numeric($y)) {
            $y = (string) (int) $y;
        } else {
            $y = trim((string) $y);
            if ($y === '') {
                $y = 'Unknown';
            }
        }

        $s = $section === null || trim((string) $section) === '' ? 'N/A' : trim((string) $section);

        return $y.'|'.$s;
    }

    private function formatYearLevelLabel(string $yearToken, string $section): string
    {
        if ($yearToken === 'Unknown') {
            return 'Unknown year — '.$section;
        }
        if (is_numeric($yearToken)) {
            $y = (int) $yearToken;
            $yearLabel = match ($y) {
                1 => '1st Year',
                2 => '2nd Year',
                3 => '3rd Year',
                4 => '4th Year',
                default => $yearToken.'th Year',
            };

            return $yearLabel.' — '.$section;
        }

        return $yearToken.' — '.$section;
    }

    /**
     * Normalize course for stable grouping (empty → "Unknown").
     */
    private function normalizeCourseToken(mixed $course): string
    {
        $c = trim((string) ($course ?? ''));

        return $c === '' ? 'Unknown' : $c;
    }

    private function formatCourseYearSectionLabel(string $courseToken, string $yearToken, string $section): string
    {
        $coursePart = $courseToken === 'Unknown' ? 'Course unknown' : $courseToken;

        return $coursePart.' · '.$this->formatYearLevelLabel($yearToken, $section);
    }

    /**
     * Course + year + section in one breakdown (deduped vote rows, same as prior analytics).
     *
     * @return Collection<int, array<string, mixed>>
     */
    private function buildVotesByCourseYearSectionBreakdown(): Collection
    {
        $scopeId = $this->analyticsSchoolScopeId();
        if ($scopeId !== null && $scopeId < 0) {
            return collect();
        }

        $rowsQuery = DB::table('votes as v')
            ->join('users as u', 'v.voter_id', '=', 'u.id')
            ->leftJoin('students as s', 'u.email', '=', 's.student_id_number')
            ->select('v.id as vote_id', 'v.voter_id', 's.course', 's.yearlevel', 's.section', 's.id as student_row_id')
            ->orderBy('v.id')
            ->orderByDesc('s.id');

        if ($scopeId !== null) {
            $rowsQuery->where(function ($q) use ($scopeId) {
                $q->where('v.school_id', $scopeId)
                    ->orWhere(function ($q2) use ($scopeId) {
                        $q2->whereNull('v.school_id')
                            ->whereExists(function ($sub) use ($scopeId) {
                                $sub->from('elections as e')
                                    ->whereColumn('e.id', 'v.election_id')
                                    ->where('e.school_id', $scopeId);
                            });
                    });
            });
        }

        $rows = $rowsQuery->get();

        $oneRowPerVote = $rows->groupBy('vote_id')->map(fn (Collection $g) => $g->first());

        $statsByKey = [];
        foreach ($oneRowPerVote as $row) {
            $composite = $this->normalizeCourseToken($row->course).'###'.$this->normalizeYearSectionKey($row->yearlevel, $row->section);
            if (! isset($statsByKey[$composite])) {
                $statsByKey[$composite] = ['total_votes' => 0, 'voters' => []];
            }
            $statsByKey[$composite]['total_votes']++;
            $statsByKey[$composite]['voters'][$row->voter_id] = true;
        }

        $studentCountByKey = [];
        $studentBase = Student::withoutGlobalScopes()
            ->tap(fn ($q) => $this->applyAnalyticsSchoolFilter($q));
        foreach ($studentBase
            ->select('course', 'yearlevel', 'section', DB::raw('COUNT(*) as student_count'))
            ->groupBy('course', 'yearlevel', 'section')
            ->get() as $class) {
            $k = $this->normalizeCourseToken($class->course).'###'.$this->normalizeYearSectionKey($class->yearlevel, $class->section);
            $studentCountByKey[$k] = ($studentCountByKey[$k] ?? 0) + (int) $class->student_count;
        }

        $allKeys = collect(array_keys($statsByKey))->merge(array_keys($studentCountByKey))->unique();

        $out = $allKeys->map(function (string $composite) use ($statsByKey, $studentCountByKey) {
            $parts = explode('###', $composite, 2);
            $courseToken = $parts[0] ?? 'Unknown';
            $ys = $parts[1] ?? 'Unknown|N/A';
            [$yearToken, $section] = array_pad(explode('|', $ys, 2), 2, 'N/A');
            $stats = $statsByKey[$composite] ?? null;
            $totalVotes = $stats['total_votes'] ?? 0;
            $uniqueVoters = $stats ? count($stats['voters']) : 0;
            $studentCount = $studentCountByKey[$composite] ?? 0;

            return [
                'key' => $composite,
                'label' => $this->formatCourseYearSectionLabel($courseToken, $yearToken, $section),
                'count' => $totalVotes,
                'voter_count' => $uniqueVoters,
                'student_count' => $studentCount,
            ];
        });

        return $out->filter(fn (array $r) => $r['count'] > 0)->sortBy('key')->values();
    }
}
