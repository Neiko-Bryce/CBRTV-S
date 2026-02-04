<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Election;
use App\Models\Vote;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LiveResultsController extends Controller
{
    /**
     * Get elections whose live results are displayed on the landing page (admin-controlled).
     * Only elections with show_live_results = true are returned.
     */
    public function getCompletedElections()
    {
        $now = Carbon::now('Asia/Manila');

        // Only elections that admin has turned on for landing page display
        $elections = Election::where('show_live_results', true)
            ->whereIn('status', ['ongoing', 'completed'])
            ->with(['organization'])
            ->get();

        $results = [];

        foreach ($elections as $election) {
            // Get candidates grouped by position with vote counts
            $candidatesByPosition = Candidate::where('election_id', $election->id)
                ->with(['position'])
                ->withCount('votes')
                ->get()
                ->groupBy('position_id');

            $positionsData = [];

            foreach ($candidatesByPosition as $positionId => $candidates) {
                $position = $candidates->first()->position;
                if (! $position) {
                    continue;
                }

                $candidatesData = [];

                if ($election->status === 'ongoing') {
                    // ONGOING: Use anonymized data (question marks, Candidate A/B/C)
                    $candidatesArray = $candidates->toArray();
                    $seed = crc32($election->id.'-'.$positionId);
                    $shuffledCandidates = $this->seededShuffle($candidatesArray, $seed);

                    $letterIndex = 0;
                    foreach ($shuffledCandidates as $candidate) {
                        $anonymousLabel = $this->getAnonymousLabel($letterIndex);
                        $currentVotes = Vote::where('candidate_id', $candidate['id'])->count();

                        $candidatesData[] = [
                            'id' => $candidate['id'],
                            'name' => "Candidate {$anonymousLabel}",
                            'photo' => null, // Hidden during ongoing
                            'votes_count' => $currentVotes,
                            'is_anonymous' => true,
                        ];
                        $letterIndex++;
                    }
                } else {
                    // COMPLETED: Reveal real candidate info!
                    foreach ($candidates as $candidate) {
                        $currentVotes = Vote::where('candidate_id', $candidate->id)->count();

                        // Build photo URL
                        $photoUrl = null;
                        if ($candidate->photo) {
                            $photoUrl = route('candidates.photo.public', ['path' => $candidate->photo]);
                        }

                        $candidatesData[] = [
                            'id' => $candidate->id,
                            'name' => $candidate->candidate_name,
                            'photo' => $photoUrl,
                            'votes_count' => $currentVotes,
                            'is_anonymous' => false,
                        ];
                    }
                }

                // Sort by votes (highest first)
                usort($candidatesData, function ($a, $b) {
                    return $b['votes_count'] - $a['votes_count'];
                });

                $positionsData[] = [
                    'position_id' => $positionId,
                    'position_name' => $position->name,
                    'candidates' => $candidatesData,
                    'total_votes' => array_sum(array_column($candidatesData, 'votes_count')),
                ];
            }

            // Calculate time remaining based on election status
            $endDateTime = $this->parseElectionEndTime($election);
            $startDateTime = $this->parseElectionStartTime($election);

            $resultData = [
                'id' => $election->id,
                'election_name' => $election->election_name,
                'organization' => $election->organization ? $election->organization->name : null,
                'election_date' => $election->election_date->format('M d, Y'),
                'status' => $election->status,
                'positions' => $positionsData,
                'total_voters' => Vote::where('election_id', $election->id)->distinct('voter_id')->count(),
            ];

            if ($election->status === 'ongoing') {
                // For ongoing elections, show time until election ends
                if ($endDateTime) {
                    $timeUntilEnd = $now->diff($endDateTime);
                    $resultData['ends_at'] = $endDateTime->format('M d, Y g:i A');
                    $resultData['ends_in_seconds'] = $now->diffInSeconds($endDateTime, false);
                    $resultData['time_remaining'] = [
                        'hours' => $timeUntilEnd->h + ($timeUntilEnd->d * 24),
                        'minutes' => $timeUntilEnd->i,
                        'seconds' => $timeUntilEnd->s,
                    ];
                }
                if ($startDateTime) {
                    $resultData['started_at'] = $startDateTime->format('M d, Y g:i A');
                }
            } else {
                // For completed elections, show time until results expire (24 hours after end)
                if ($endDateTime) {
                    $expiresAt = $endDateTime->copy()->addHours(24);
                    $timeRemaining = $now->diff($expiresAt);
                    $resultData['ended_at'] = $endDateTime->format('M d, Y g:i A');
                    $resultData['expires_at'] = $expiresAt->format('M d, Y g:i A');
                    $resultData['expires_in_seconds'] = $now->diffInSeconds($expiresAt, false);
                    $resultData['time_remaining'] = [
                        'hours' => $timeRemaining->h + ($timeRemaining->d * 24),
                        'minutes' => $timeRemaining->i,
                        'seconds' => $timeRemaining->s,
                    ];
                }
            }

            $results[] = $resultData;
        }

        return response()->json([
            'success' => true,
            'elections' => $results,
            'timestamp' => $now->toIso8601String(),
        ]);
    }

    /**
     * Parse the election end datetime from date and time fields.
     */
    private function parseElectionEndTime($election)
    {
        if (empty($election->time_ended) || empty($election->election_date)) {
            return null;
        }

        try {
            $dateStr = $election->election_date->format('Y-m-d');
            $timeStr = $election->time_ended;

            // Handle different time formats (HH:MM or HH:MM:SS)
            if (strlen($timeStr) === 5) {
                $timeStr .= ':00';
            }

            return Carbon::createFromFormat('Y-m-d H:i:s', $dateStr.' '.$timeStr, 'Asia/Manila');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Parse the election start datetime from date and time fields.
     */
    private function parseElectionStartTime($election)
    {
        if (empty($election->timestarted) || empty($election->election_date)) {
            return null;
        }

        try {
            $dateStr = $election->election_date->format('Y-m-d');
            $timeStr = $election->timestarted;

            // Handle different time formats (HH:MM or HH:MM:SS)
            if (strlen($timeStr) === 5) {
                $timeStr .= ':00';
            }

            return Carbon::createFromFormat('Y-m-d H:i:s', $dateStr.' '.$timeStr, 'Asia/Manila');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get real-time vote counts for a specific election (only if show_live_results is on).
     */
    public function getElectionResults($electionId)
    {
        $now = Carbon::now('Asia/Manila');

        $election = Election::where('id', $electionId)
            ->where('show_live_results', true)
            ->whereIn('status', ['ongoing', 'completed'])
            ->first();

        if (! $election) {
            return response()->json([
                'success' => false,
                'message' => 'Election not found or results are not displayed.',
            ], 404);
        }

        // Get vote counts
        $voteCounts = Vote::where('election_id', $electionId)
            ->select('candidate_id', DB::raw('count(*) as votes'))
            ->groupBy('candidate_id')
            ->pluck('votes', 'candidate_id')
            ->toArray();

        return response()->json([
            'success' => true,
            'vote_counts' => $voteCounts,
            'total_voters' => Vote::where('election_id', $electionId)->distinct('voter_id')->count(),
            'timestamp' => $now->toIso8601String(),
        ]);
    }

    /**
     * Shuffle array with a seed for consistent results.
     */
    private function seededShuffle(array $array, int $seed): array
    {
        mt_srand($seed);
        $keys = array_keys($array);

        for ($i = count($keys) - 1; $i > 0; $i--) {
            $j = mt_rand(0, $i);
            $temp = $keys[$i];
            $keys[$i] = $keys[$j];
            $keys[$j] = $temp;
        }

        $shuffled = [];
        foreach ($keys as $key) {
            $shuffled[] = $array[$key];
        }

        // Reset the random seed
        mt_srand();

        return $shuffled;
    }

    /**
     * Generate anonymous label (A, B, C, ... Z, AA, AB, etc.)
     */
    private function getAnonymousLabel($index)
    {
        $letters = '';
        $index++;

        while ($index > 0) {
            $index--;
            $letters = chr(65 + ($index % 26)).$letters;
            $index = intdiv($index, 26);
        }

        return $letters;
    }
}
