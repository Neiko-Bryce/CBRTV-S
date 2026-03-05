<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArchivedCandidate;
use App\Models\ArchivedElection;
use App\Models\ArchivedVote;
use App\Models\Candidate;
use App\Models\Election;
use App\Models\Student;
use App\Models\Vote;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display the reports index page.
     */
    public function index()
    {
        $elections = Election::orderBy('election_date', 'desc')->get();
        $archivedElections = ArchivedElection::orderByDesc('archived_at')->orderByDesc('id')->get();

        // Get unique filter values from students table
        $courses = Student::distinct()->whereNotNull('course')->pluck('course')->sort()->values();
        $yearlevels = Student::distinct()->whereNotNull('yearlevel')->pluck('yearlevel')->sort()->values();
        $sections = Student::distinct()->whereNotNull('section')->pluck('section')->sort()->values();

        return view('admin.reports.index', compact('elections', 'archivedElections', 'courses', 'yearlevels', 'sections'));
    }

    /**
     * Generate report data based on filters.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'election_ref' => ['required', 'string', 'regex:/^((active|archived)_\d+|\d+)$/'],
            'filter_type' => 'nullable|in:all,course,yearlevel,section',
            'filter_value' => 'nullable|string',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        [$source, $electionId] = $this->parseElectionRef((string) $request->election_ref);
        $filterType = $request->filter_type ?? 'all';
        $filterValue = $request->filter_value;
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;

        $election = $this->getElectionForReport($source, $electionId);

        // Get report data
        $reportData = $this->getReportData($source, $electionId, $filterType, $filterValue, $dateFrom, $dateTo);

        // Get filter options for the selected election's voters
        $courses = $this->getVoterCourses($source, $electionId, $dateFrom, $dateTo);
        $yearlevels = $this->getVoterYearLevels($source, $electionId, $dateFrom, $dateTo);
        $sections = $this->getVoterSections($source, $electionId, $dateFrom, $dateTo);

        return response()->json([
            'success' => true,
            'election' => $election,
            'election_ref' => "{$source}_{$electionId}",
            'data' => $reportData,
            'filters' => [
                'courses' => $courses,
                'yearlevels' => $yearlevels,
                'sections' => $sections,
            ],
        ]);
    }

    /**
     * Display print-friendly report.
     */
    public function print(Request $request, $electionRef)
    {
        $filterType = $request->query('filter_type', 'all');
        $filterValue = $request->query('filter_value');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        try {
            [$source, $electionId] = $this->parseElectionRef((string) $electionRef);
        } catch (\InvalidArgumentException $e) {
            abort(404);
        }

        $election = $this->getElectionForReport($source, $electionId);
        $reportData = $this->getReportData($source, $electionId, $filterType, $filterValue, $dateFrom, $dateTo);

        return view('admin.reports.print', [
            'election' => $election,
            'reportData' => $reportData,
            'filterType' => $filterType,
            'filterValue' => $filterValue,
            'electionRef' => $electionRef,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'generatedAt' => now(),
        ]);
    }

    /**
     * Get report data with optional filtering.
     */
    private function getReportData(string $source, int $electionId, $filterType = 'all', $filterValue = null, ?string $dateFrom = null, ?string $dateTo = null): array
    {
        $voteTable = $this->voteTable($source);
        $voteIdColumn = $voteTable.'.id';
        $voteElectionColumn = $voteTable.'.'.$this->voteElectionColumn($source);

        // Get all votes for this election using scoped source.
        $votesQuery = $this->newVoteQuery($source)->where($voteElectionColumn, $electionId);
        $this->applyVoteDateRange($votesQuery, $source, $dateFrom, $dateTo);

        // Apply filters based on voter's student data
        if ($filterType !== 'all' && $filterValue) {
            $votesQuery->join('users', $voteTable.'.voter_id', '=', 'users.id')
                ->join('students', 'users.email', '=', 'students.student_id_number')
                ->where('students.'.$filterType, $filterValue);
        }

        // Get filtered vote IDs
        $filteredVoteIds = $votesQuery->pluck($voteIdColumn);

        // Total votes count
        $totalVotes = $filteredVoteIds->count();

        // Unique voters
        $uniqueVoterIds = $this->newVoteQuery($source)
            ->whereIn($voteIdColumn, $filteredVoteIds)
            ->distinct()
            ->pluck($voteTable.'.voter_id');
        $totalParticipants = $uniqueVoterIds->count();

        // Get total eligible students (those who have user accounts)
        $eligibleStudentsQuery = Student::join('users', 'students.student_id_number', '=', 'users.email')
            ->where('users.usertype', 'student');

        if ($filterType !== 'all' && $filterValue) {
            $eligibleStudentsQuery->where('students.'.$filterType, $filterValue);
        }

        $totalEligible = $eligibleStudentsQuery->count();

        // Participation rate
        $participationRate = $totalEligible > 0 ? round(($totalParticipants / $totalEligible) * 100, 1) : 0;

        $resultsByPosition = [];
        if ($source === 'active') {
            $candidates = Candidate::where('election_id', $electionId)
                ->with(['position', 'partylist', 'student'])
                ->get();

            $voteCounts = Vote::whereIn('id', $filteredVoteIds)
                ->select('candidate_id', DB::raw('count(*) as count'))
                ->groupBy('candidate_id')
                ->pluck('count', 'candidate_id');

            $candidates->each(function ($candidate) use ($voteCounts) {
                $candidate->filtered_votes = $voteCounts->get($candidate->id, 0);
            });

            $candidatesByPosition = $candidates->groupBy('position_id');

            foreach ($candidatesByPosition as $positionId => $group) {
                $position = $group->first()->position;
                $positionName = $position ? $position->name : 'Unknown Position';
                $positionOrder = $position ? ($position->order ?? 0) : 9999;
                $numberOfSlots = $position ? ($position->number_of_slots ?? 1) : 1;

                $sortedCandidates = $group->sortByDesc('filtered_votes')->values();

                $resultsByPosition[] = [
                    'position_name' => $positionName,
                    'position_order' => $positionOrder,
                    'number_of_slots' => $numberOfSlots,
                    'candidates' => $sortedCandidates,
                ];
            }
        } else {
            $candidates = ArchivedCandidate::where('archived_election_id', $electionId)
                ->with(['archivedPartylist', 'student'])
                ->get();

            $voteCounts = ArchivedVote::whereIn('id', $filteredVoteIds)
                ->select('archived_candidate_id', DB::raw('count(*) as count'))
                ->groupBy('archived_candidate_id')
                ->pluck('count', 'archived_candidate_id');

            $candidates->each(function ($candidate) use ($voteCounts) {
                $candidate->filtered_votes = $voteCounts->get($candidate->id, 0);
                // Keep frontend shape identical to active report payload.
                $candidate->setRelation('partylist', $candidate->archivedPartylist);
            });

            $candidatesByPosition = $candidates->groupBy(function ($candidate) {
                return ($candidate->original_position_id ?: 'no-position')
                    .'|'.($candidate->position_name ?: 'Unknown Position')
                    .'|'.((int) ($candidate->position_order ?? 9999))
                    .'|'.((int) ($candidate->number_of_slots ?? 1));
            });

            foreach ($candidatesByPosition as $positionKey => $group) {
                [$positionId, $positionName, $positionOrder, $numberOfSlots] = array_pad(explode('|', $positionKey), 4, 0);
                $sortedCandidates = $group->sortByDesc('filtered_votes')->values();

                $resultsByPosition[] = [
                    'position_name' => $positionName ?: 'Unknown Position',
                    'position_order' => (int) $positionOrder,
                    'number_of_slots' => max(1, (int) $numberOfSlots),
                    'candidates' => $sortedCandidates,
                ];
            }
        }

        usort($resultsByPosition, function ($a, $b) {
            return $a['position_order'] <=> $b['position_order'];
        });

        // Get participation by course/yearlevel/section for the filtered data
        $participationBreakdown = $this->getParticipationBreakdown($source, $electionId, $dateFrom, $dateTo);

        // Get gender breakdown of voters
        $maleVoters = DB::table($voteTable)
            ->join('users', $voteTable.'.voter_id', '=', 'users.id')
            ->join('students', 'users.email', '=', 'students.student_id_number')
            ->whereIn($voteIdColumn, $filteredVoteIds)
            ->where('students.gender', 'Male')
            ->distinct($voteTable.'.voter_id')
            ->count($voteTable.'.voter_id');

        $femaleVoters = DB::table($voteTable)
            ->join('users', $voteTable.'.voter_id', '=', 'users.id')
            ->join('students', 'users.email', '=', 'students.student_id_number')
            ->whereIn($voteIdColumn, $filteredVoteIds)
            ->where('students.gender', 'Female')
            ->distinct($voteTable.'.voter_id')
            ->count($voteTable.'.voter_id');

        $election = $this->getElectionForReport($source, $electionId);
        $electionYear = null;
        if ($election->election_date) {
            $electionYear = $election->election_date instanceof Carbon
                ? $election->election_date->format('Y')
                : Carbon::parse($election->election_date)->format('Y');
        }

        $reportPeriodLabel = 'All time';
        if ($dateFrom && $dateTo) {
            $reportPeriodLabel = Carbon::parse($dateFrom)->format('M d, Y').' to '.Carbon::parse($dateTo)->format('M d, Y');
        } elseif ($dateFrom) {
            $reportPeriodLabel = 'From '.Carbon::parse($dateFrom)->format('M d, Y');
        } elseif ($dateTo) {
            $reportPeriodLabel = 'Until '.Carbon::parse($dateTo)->format('M d, Y');
        }

        return [
            'totalVotes' => $totalVotes,
            'totalParticipants' => $totalParticipants,
            'totalEligible' => $totalEligible,
            'participationRate' => $participationRate,
            'maleVoters' => $maleVoters,
            'femaleVoters' => $femaleVoters,
            'resultsByPosition' => $resultsByPosition,
            'participationBreakdown' => $participationBreakdown,
            'electionYear' => $electionYear ?: date('Y'),
            'reportPeriodLabel' => $reportPeriodLabel,
        ];
    }

    /**
     * Get participation breakdown by different criteria.
     */
    private function getParticipationBreakdown(string $source, int $electionId, ?string $dateFrom = null, ?string $dateTo = null)
    {
        $voteTable = $this->voteTable($source);
        $voteElectionColumn = $this->voteElectionColumn($source);

        // Get all voters for this election
        $voterIdsQuery = $this->newVoteQuery($source)
            ->where($voteTable.'.'.$voteElectionColumn, $electionId);
        $this->applyVoteDateRange($voterIdsQuery, $source, $dateFrom, $dateTo);
        $voterIds = $voterIdsQuery->distinct()->pluck($voteTable.'.voter_id');

        // By Course
        $byCourse = Student::join('users', 'students.student_id_number', '=', 'users.email')
            ->whereIn('users.id', $voterIds)
            ->whereNotNull('students.course')
            ->select('students.course', DB::raw('count(*) as count'))
            ->groupBy('students.course')
            ->orderByDesc('count')
            ->get();

        // By Year Level
        $byYearlevel = Student::join('users', 'students.student_id_number', '=', 'users.email')
            ->whereIn('users.id', $voterIds)
            ->whereNotNull('students.yearlevel')
            ->select('students.yearlevel', DB::raw('count(*) as count'))
            ->groupBy('students.yearlevel')
            ->orderBy('students.yearlevel')
            ->get();

        // By Section
        $bySection = Student::join('users', 'students.student_id_number', '=', 'users.email')
            ->whereIn('users.id', $voterIds)
            ->whereNotNull('students.section')
            ->select('students.section', DB::raw('count(*) as count'))
            ->groupBy('students.section')
            ->orderByDesc('count')
            ->get();

        return [
            'byCourse' => $byCourse,
            'byYearlevel' => $byYearlevel,
            'bySection' => $bySection,
        ];
    }

    /**
     * Get unique courses from voters of an election.
     */
    private function getVoterCourses(string $source, int $electionId, ?string $dateFrom = null, ?string $dateTo = null)
    {
        $voteTable = $this->voteTable($source);
        $voteElectionColumn = $this->voteElectionColumn($source);

        $voterIdsQuery = $this->newVoteQuery($source)
            ->where($voteTable.'.'.$voteElectionColumn, $electionId);
        $this->applyVoteDateRange($voterIdsQuery, $source, $dateFrom, $dateTo);
        $voterIds = $voterIdsQuery->distinct()->pluck($voteTable.'.voter_id');

        return Student::join('users', 'students.student_id_number', '=', 'users.email')
            ->whereIn('users.id', $voterIds)
            ->whereNotNull('students.course')
            ->distinct()
            ->pluck('students.course')
            ->sort()
            ->values();
    }

    /**
     * Get unique year levels from voters of an election.
     */
    private function getVoterYearLevels(string $source, int $electionId, ?string $dateFrom = null, ?string $dateTo = null)
    {
        $voteTable = $this->voteTable($source);
        $voteElectionColumn = $this->voteElectionColumn($source);

        $voterIdsQuery = $this->newVoteQuery($source)
            ->where($voteTable.'.'.$voteElectionColumn, $electionId);
        $this->applyVoteDateRange($voterIdsQuery, $source, $dateFrom, $dateTo);
        $voterIds = $voterIdsQuery->distinct()->pluck($voteTable.'.voter_id');

        return Student::join('users', 'students.student_id_number', '=', 'users.email')
            ->whereIn('users.id', $voterIds)
            ->whereNotNull('students.yearlevel')
            ->distinct()
            ->pluck('students.yearlevel')
            ->sort()
            ->values();
    }

    /**
     * Get unique sections from voters of an election.
     */
    private function getVoterSections(string $source, int $electionId, ?string $dateFrom = null, ?string $dateTo = null)
    {
        $voteTable = $this->voteTable($source);
        $voteElectionColumn = $this->voteElectionColumn($source);

        $voterIdsQuery = $this->newVoteQuery($source)
            ->where($voteTable.'.'.$voteElectionColumn, $electionId);
        $this->applyVoteDateRange($voterIdsQuery, $source, $dateFrom, $dateTo);
        $voterIds = $voterIdsQuery->distinct()->pluck($voteTable.'.voter_id');

        return Student::join('users', 'students.student_id_number', '=', 'users.email')
            ->whereIn('users.id', $voterIds)
            ->whereNotNull('students.section')
            ->distinct()
            ->pluck('students.section')
            ->sort()
            ->values();
    }

    /**
     * Parse selected election reference (active_12, archived_5, or legacy numeric id).
     *
     * @throws \InvalidArgumentException
     */
    private function parseElectionRef(string $electionRef): array
    {
        $ref = trim($electionRef);

        if (preg_match('/^(active|archived)_(\d+)$/', $ref, $matches)) {
            return [$matches[1], (int) $matches[2]];
        }

        if (ctype_digit($ref)) {
            return ['active', (int) $ref];
        }

        throw new \InvalidArgumentException('Invalid election reference.');
    }

    /**
     * Resolve election model by source.
     */
    private function getElectionForReport(string $source, int $electionId)
    {
        if ($source === 'archived') {
            return ArchivedElection::with('organization')->findOrFail($electionId);
        }

        return Election::with('organization')->findOrFail($electionId);
    }

    private function voteTable(string $source): string
    {
        return $source === 'archived' ? 'archived_votes' : 'votes';
    }

    private function voteElectionColumn(string $source): string
    {
        return $source === 'archived' ? 'archived_election_id' : 'election_id';
    }

    private function newVoteQuery(string $source)
    {
        return $source === 'archived' ? ArchivedVote::query() : Vote::query();
    }

    /**
     * Apply report date range to vote queries.
     */
    private function applyVoteDateRange($query, string $source, ?string $dateFrom = null, ?string $dateTo = null): void
    {
        $column = $source === 'archived' ? 'archived_votes.voted_at' : 'votes.created_at';

        if ($dateFrom) {
            $query->whereDate($column, '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate($column, '<=', $dateTo);
        }
    }
}
