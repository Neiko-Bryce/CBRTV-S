<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Election;
use App\Models\User;
use App\Models\Vote;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    /**
     * Display the analytics page: voting statistics, trends, and election breakdowns.
     */
    public function index(): View
    {
        $totalStudents = User::where('usertype', 'student')->count();
        $totalVotes = Vote::count();
        $uniqueVoters = Vote::distinct('voter_id')->count('voter_id');
        $participationRate = $totalStudents > 0
            ? round(($uniqueVoters / $totalStudents) * 100, 1)
            : 0;

        $totalElections = Election::count();
        $completedElections = Election::where('status', 'completed')->count();
        $ongoingElections = Election::where('status', 'ongoing')->count();
        $upcomingElections = Election::where('status', 'upcoming')->count();

        // Votes in the last 7 days (for trend)
        $votesByDay = Vote::query()
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

        // Elections with vote statistics (all elections, for breakdown)
        $electionsWithStats = Election::with('organization')
            ->withCount('votes')
            ->orderByDesc('election_date')
            ->orderByDesc('id')
            ->get()
            ->map(function ($election) use ($totalStudents) {
                $votesCount = $election->votes_count;
                $uniqueInElection = Vote::where('election_id', $election->id)->distinct('voter_id')->count('voter_id');
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

        // NEW: Votes by Year Level
        $votesByYearLevel = Vote::query()
            ->join('users', 'votes.voter_id', '=', 'users.id')
            ->join('students', 'users.email', '=', 'students.student_id_number')
            ->select('students.yearlevel', DB::raw('COUNT(*) as count'))
            ->groupBy('students.yearlevel')
            ->orderBy('students.yearlevel')
            ->get()
            ->map(function ($item) {
                return [
                    'yearlevel' => $item->yearlevel ?? 'Unknown',
                    'count' => $item->count,
                ];
            });
        $maxVotesByYear = $votesByYearLevel->max('count') ?: 1;

        // NEW: Peak Voting Hours (24-hour breakdown)
        $isPostgres = DB::connection()->getDriverName() === 'pgsql';
        $hourExtract = $isPostgres
            ? DB::raw("EXTRACT(HOUR FROM created_at) as hour")
            : DB::raw("HOUR(created_at) as hour");

        $votesByHour = Vote::query()
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
        $lastTwoElections = Election::where('status', 'completed')
            ->orderByDesc('election_date')
            ->orderByDesc('id')
            ->take(2)
            ->get()
            ->map(function ($election) use ($totalStudents) {
                $uniqueInElection = Vote::where('election_id', $election->id)
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
            'votesByYearLevel',
            'maxVotesByYear',
            'peakVotingHours',
            'maxVotesByHour',
            'electionComparison'
        ));
    }
}
