<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Election;
use App\Models\Vote;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        // Total Users (students only)
        $totalUsers = User::where('usertype', 'student')->count();

        $previousMonthUsers = User::where('usertype', 'student')
            ->where('created_at', '<', Carbon::now()->subMonth()->startOfMonth())
            ->count();

        $userGrowth = $previousMonthUsers > 0
            ? round((($totalUsers - $previousMonthUsers) / $previousMonthUsers) * 100, 1)
            : 0;

        // Update statuses for ALL elections first (same logic as ElectionController::index)
        $this->syncElectionStatuses();

        // Refresh the query to ensure we get updated statuses from database
        // Use DB::table to avoid model caching issues
        $activeElections = DB::table('elections')->where('status', 'ongoing')->count();

        $endingSoon = DB::table('elections')
            ->where('status', 'ongoing')
            ->whereNotNull('time_ended')
            ->where('time_ended', '<=', Carbon::now()->addDays(3))
            ->where('time_ended', '>', Carbon::now())
            ->count();

        // Total Votes
        $totalVotes = Vote::count();

        $lastWeekVotes = Vote::where('created_at', '>=', Carbon::now()->subWeek())->count();
        $previousWeekVotes = Vote::whereBetween('created_at', [
            Carbon::now()->subWeeks(2)->startOfWeek(),
            Carbon::now()->subWeek()->startOfWeek()
        ])->count();

        $voteGrowth = $previousWeekVotes > 0
            ? round((($lastWeekVotes - $previousWeekVotes) / $previousWeekVotes) * 100, 1)
            : 0;

        // Participation Rate
        $totalStudents = User::where('usertype', 'student')->count();
        $uniqueVoters = Vote::distinct('voter_id')->count('voter_id');
        $participationRate = $totalStudents > 0
            ? round(($uniqueVoters / $totalStudents) * 100, 1)
            : 0;

        $recentActivities = $this->getRecentActivities();

        // Active Elections table: ONLY ongoing and upcoming elections (exclude completed and cancelled)
        // Query directly from database using DB::table to get fresh data after status updates
        // This ensures we get the exact same data as /admin/elections page
        $activeElectionIds = DB::table('elections')
            ->whereIn('status', ['ongoing', 'upcoming'])
            ->orderBy('id', 'asc')
            ->pluck('id');

        // Load models with relationships only for the filtered IDs
        $activeElectionsList = Election::whereIn('id', $activeElectionIds)
            ->with('organization')
            ->withCount('votes')
            ->orderBy('id', 'asc')
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'userGrowth',
            'activeElections',
            'endingSoon',
            'totalVotes',
            'voteGrowth',
            'participationRate',
            'recentActivities',
            'activeElectionsList'
        ));
    }

    /**
     * Sync election statuses using the same logic as ElectionController::index.
     */
    private function syncElectionStatuses(): void
    {
        try {
            $allElections = Election::all();
            foreach ($allElections as $election) {
                // Don't auto-update if status is manually set to cancelled
                // BUT always allow update to completed (election ended)
                if ($election->status === 'cancelled') {
                    // Still check if election has ended - completed takes precedence
                    $calculatedStatus = $this->calculateStatus($election->toArray());
                    if ($calculatedStatus === 'completed') {
                        // Election ended, update to completed even if cancelled
                        $election->update(['status' => 'completed']);
                    }
                    continue; // Skip further auto-update for manually set statuses
                }

                // Convert any old "rescheduled" status to "upcoming"
                if ($election->status === 'rescheduled') {
                    $election->update(['status' => 'upcoming']);
                }

                $calculatedStatus = $this->calculateStatus($election->toArray());
                // Always update if status changed
                if ($election->status !== $calculatedStatus) {
                    $election->update(['status' => $calculatedStatus]);
                } elseif (empty($election->status) || $election->status === null) {
                    // Set status if it's null
                    $election->update(['status' => $calculatedStatus]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error updating election statuses in dashboard: ' . $e->getMessage());
        }
    }

    /**
     * Calculate election status (same logic as ElectionController::calculateStatus).
     */
    private function calculateStatus(array $electionData): string
    {
        try {
            $now = Carbon::now('Asia/Manila');

            if (empty($electionData['election_date'])) {
                return 'upcoming';
            }

            if ($electionData['election_date'] instanceof \Carbon\Carbon) {
                $dateString = $electionData['election_date']->format('Y-m-d');
            } else {
                $dateString = is_string($electionData['election_date'])
                    ? $electionData['election_date']
                    : (string) $electionData['election_date'];
            }

            $electionDate = Carbon::parse($dateString, 'Asia/Manila');

            if (!empty($electionData['timestarted'])) {
                try {
                    $timeStr = trim($electionData['timestarted']);
                    // Normalize time format - timestarted might be H:i or H:i:s
                    $timeParts = explode(':', $timeStr);
                    if (count($timeParts) == 2) {
                        // H:i format, add seconds
                        $timeStr = $timeParts[0] . ':' . $timeParts[1] . ':00';
                    } elseif (count($timeParts) == 3) {
                        // Already H:i:s format, use as is
                        $timeStr = $timeStr;
                    } else {
                        throw new \Exception('Invalid time format');
                    }
                    
                    $electionDateTime = Carbon::createFromFormat(
                        'Y-m-d H:i:s',
                        $dateString . ' ' . $timeStr,
                        'Asia/Manila'
                    );
                } catch (\Exception $e) {
                    $electionDateTime = $electionDate->copy()->startOfDay();
                }
            } else {
                $electionDateTime = $electionDate->copy()->startOfDay();
            }

            // Check if election has ended FIRST (this takes priority)
            if (!empty($electionData['time_ended'])) {
                try {
                    $endTimeStr = trim($electionData['time_ended']);
                    // Normalize time format
                    $endTimeParts = explode(':', $endTimeStr);
                    if (count($endTimeParts) == 2) {
                        // H:i format, add seconds
                        $endTimeStr = $endTimeParts[0] . ':' . $endTimeParts[1] . ':00';
                    } elseif (count($endTimeParts) == 3) {
                        // Already H:i:s format, use as is
                        $endTimeStr = $endTimeStr;
                    } else {
                        throw new \Exception('Invalid end time format');
                    }
                    
                    $endDateTime = Carbon::createFromFormat(
                        'Y-m-d H:i:s',
                        $dateString . ' ' . $endTimeStr,
                        'Asia/Manila'
                    );
                    
                    // Handle overnight elections: if end time is earlier than start time,
                    // it means the election ends the next day
                    if (isset($electionDateTime) && $endDateTime->lessThanOrEqualTo($electionDateTime)) {
                        $endDateTime->addDay();
                    }
                    
                    // If current time is past the end time, election is completed
                    if ($now->greaterThanOrEqualTo($endDateTime)) {
                        return 'completed';
                    }
                } catch (\Exception $e) {
                    Log::error('Error parsing end time in calculateStatus: ' . $e->getMessage());
                    // If end time parsing fails, continue with start time check
                }
            }

            // Check if election has started (only if not ended)
            if ($now->greaterThanOrEqualTo($electionDateTime)) {
                return 'ongoing';
            }
            
            // Election hasn't started yet
            return 'upcoming';
        } catch (\Exception $e) {
            Log::error('Error calculating election status: ' . $e->getMessage());
            return 'upcoming';
        }
    }

    /**
     * Get recent activities for the dashboard.
     */
    private function getRecentActivities(): array
    {
        $activities = [];

        foreach (Election::with('organization')->orderBy('created_at', 'desc')->limit(3)->get() as $election) {
            $activities[] = [
                'type' => 'election_created',
                'icon' => 'check',
                'icon_color' => 'green',
                'title' => 'New election created',
                'description' => $election->election_name,
                'time' => $election->created_at->diffForHumans(),
                'created_at' => $election->created_at,
            ];
        }

        $recentVotes = Vote::with(['election', 'voter'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentVotes->groupBy(fn ($v) => $v->voter_id . '_' . $v->election_id . '_' . $v->created_at->format('Y-m-d H:i:s'))->take(3) as $group) {
            $first = $group->first();
            $voterName = $first->voter->name ?? 'Unknown User';
            $electionName = $first->election->election_name ?? 'Unknown Election';
            $activities[] = [
                'type' => 'vote_cast',
                'icon' => 'chart',
                'icon_color' => 'gold',
                'title' => 'Vote cast',
                'description' => $voterName . ' voted in ' . $electionName . ($group->count() > 1 ? ' (' . $group->count() . ' positions)' : ''),
                'time' => $first->created_at->diffForHumans(),
                'created_at' => $first->created_at,
            ];
        }

        foreach (User::where('usertype', 'student')->orderBy('created_at', 'desc')->limit(2)->get() as $user) {
            $activities[] = [
                'type' => 'user_registered',
                'icon' => 'user',
                'icon_color' => 'green',
                'title' => 'New user registered',
                'description' => $user->name . ' registered as student',
                'time' => $user->created_at->diffForHumans(),
                'created_at' => $user->created_at,
            ];
        }

        usort($activities, fn ($a, $b) => $b['created_at'] <=> $a['created_at']);

        return array_slice($activities, 0, 10);
    }
}
