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
            'electionsWithStats'
        ));
    }
}
