<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Election;
use App\Models\User;
use App\Models\Vote;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        $activeElections = Election::where('status', 'ongoing')->count();

        $endingSoon = Election::where('status', 'ongoing')
            ->whereNotNull('time_ended')
            ->where('time_ended', '<=', Carbon::now()->addDays(3))
            ->where('time_ended', '>', Carbon::now())
            ->count();

        // Total Votes
        $totalVotes = Vote::count();

        $lastWeekVotes = Vote::where('created_at', '>=', Carbon::now()->subWeek())->count();
        $previousWeekVotes = Vote::whereBetween('created_at', [
            Carbon::now()->subWeeks(2)->startOfWeek(),
            Carbon::now()->subWeek()->startOfWeek(),
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
        $activeElectionIds = Election::whereIn('status', ['ongoing', 'upcoming'])
            ->orderBy('id', 'asc')
            ->pluck('id');

        // Load models with relationships only for the filtered IDs
        $activeElectionsList = Election::whereIn('id', $activeElectionIds)
            ->with('organization')
            ->withCount('votes')
            ->orderBy('id', 'asc')
            ->get();

        // Election Status Distribution for Pie Chart
        $electionStatusCounts = [
            'ongoing' => Election::where('status', 'ongoing')->count(),
            'upcoming' => Election::where('status', 'upcoming')->count(),
            'completed' => Election::where('status', 'completed')->count(),
            'cancelled' => Election::where('status', 'cancelled')->count(),
        ];
        $totalElectionsCount = array_sum($electionStatusCounts);

        return view('admin.dashboard', compact(
            'totalUsers',
            'userGrowth',
            'activeElections',
            'endingSoon',
            'totalVotes',
            'voteGrowth',
            'participationRate',
            'recentActivities',
            'activeElectionsList',
            'electionStatusCounts',
            'totalElectionsCount',
            'uniqueVoters',
            'totalStudents'
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
            Log::error('Error updating election statuses in dashboard: '.$e->getMessage());
        }
    }

    /**
     * Calculate election status (same logic as ElectionController::calculateStatus).
     *
     * Logic:
     * - UPCOMING: Current time is BEFORE start time
     * - ONGOING: Current time is >= start time AND < end time
     * - COMPLETED: Current time is >= end time
     */
    private function calculateStatus(array $electionData): string
    {
        try {
            $now = Carbon::now('Asia/Manila');

            if (empty($electionData['election_date'])) {
                return 'upcoming';
            }

            // Extract date string properly
            $dateString = $this->extractDateString($electionData['election_date']);
            if (! $dateString) {
                return 'upcoming';
            }

            // Parse start datetime
            $startDateTime = null;
            $timeStr = null;

            if (! empty($electionData['timestarted'])) {
                $timeStr = $this->normalizeTimeFormat($electionData['timestarted']);
            }

            if ($timeStr) {
                try {
                    $startDateTime = Carbon::createFromFormat(
                        'Y-m-d H:i:s',
                        $dateString.' '.$timeStr,
                        'Asia/Manila'
                    );
                } catch (\Exception $e) {
                    Log::error("Failed to parse start time: {$electionData['timestarted']}");
                }
            }

            if (! $startDateTime) {
                $startDateTime = Carbon::createFromFormat('Y-m-d', $dateString, 'Asia/Manila')->startOfDay();
            }

            // Parse end datetime
            $endDateTime = null;
            $endTimeStr = null;

            if (! empty($electionData['time_ended'])) {
                $endTimeStr = $this->normalizeTimeFormat($electionData['time_ended']);
            }

            if ($endTimeStr) {
                try {
                    $endDateTime = Carbon::createFromFormat(
                        'Y-m-d H:i:s',
                        $dateString.' '.$endTimeStr,
                        'Asia/Manila'
                    );

                    // Handle overnight elections
                    if ($endDateTime->lessThanOrEqualTo($startDateTime)) {
                        $endDateTime->addDay();
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to parse end time: {$electionData['time_ended']}");
                }
            }

            if (! $endDateTime) {
                $endDateTime = Carbon::createFromFormat('Y-m-d', $dateString, 'Asia/Manila')->endOfDay();
            }

            // Decision logic
            if ($now->greaterThanOrEqualTo($endDateTime)) {
                return 'completed';
            }

            if ($now->greaterThanOrEqualTo($startDateTime)) {
                return 'ongoing';
            }

            return 'upcoming';
        } catch (\Exception $e) {
            Log::error('Error calculating election status: '.$e->getMessage());

            return 'upcoming';
        }
    }

    /**
     * Extract date string in Y-m-d format from various input types
     */
    private function extractDateString($date)
    {
        if (empty($date)) {
            return null;
        }

        if ($date instanceof \Carbon\Carbon) {
            return $date->format('Y-m-d');
        }

        if (is_string($date)) {
            $dateStr = trim($date);

            // If it's already Y-m-d format
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateStr)) {
                return $dateStr;
            }

            // If it's an ISO 8601 format, extract just the date part
            if (strlen($dateStr) >= 10 && preg_match('/^\d{4}-\d{2}-\d{2}/', $dateStr)) {
                return substr($dateStr, 0, 10);
            }

            try {
                return Carbon::parse($dateStr)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        try {
            return Carbon::parse((string) $date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Normalize time format to H:i:s
     */
    private function normalizeTimeFormat($time)
    {
        if (empty($time)) {
            return null;
        }

        $timeStr = trim($time);
        $parts = explode(':', $timeStr);

        if (count($parts) == 2) {
            return $parts[0].':'.$parts[1].':00';
        } elseif (count($parts) == 3) {
            return $timeStr;
        }

        return null;
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

        foreach ($recentVotes->groupBy(fn ($v) => $v->voter_id.'_'.$v->election_id.'_'.$v->created_at->format('Y-m-d H:i:s'))->take(3) as $group) {
            $first = $group->first();
            $voterName = $first->voter->name ?? 'Unknown User';
            $electionName = $first->election->election_name ?? 'Unknown Election';
            $activities[] = [
                'type' => 'vote_cast',
                'icon' => 'chart',
                'icon_color' => 'gold',
                'title' => 'Vote cast',
                'description' => $voterName.' voted in '.$electionName.($group->count() > 1 ? ' ('.$group->count().' positions)' : ''),
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
                'description' => $user->name.' registered as student',
                'time' => $user->created_at->diffForHumans(),
                'created_at' => $user->created_at,
            ];
        }

        usort($activities, fn ($a, $b) => $b['created_at'] <=> $a['created_at']);

        return array_slice($activities, 0, 10);
    }
}
