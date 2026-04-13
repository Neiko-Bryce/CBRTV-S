<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ArchivedCandidate;
use App\Models\ArchivedElection;
use App\Models\ArchivedVote;
use App\Models\Candidate;
use App\Models\Election;
use App\Models\School;
use App\Models\Vote;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LiveResultsController extends Controller
{
    /**
     * First URL segment values that are never campus slugs (aligned with EmergencyMaintenanceMiddleware).
     *
     * @var list<string>
     */
    private const RESERVED_FIRST_SEGMENTS = [
        'admin',
        'student',
        'login',
        'register',
        'api',
        'build',
        'logout',
        'dashboard',
        'profile',
        'verify-email',
        'email',
        'forgot-password',
        'reset-password',
        'confirm-password',
        'password',
        'candidates',
        'storage',
        'sanctum',
    ];

    /**
     * Get elections whose live results are displayed on the landing page (admin-controlled).
     * Only elections with show_live_results = true are returned.
     */
    public function getCompletedElections()
    {
        $now = Carbon::now('Asia/Manila');
        $schoolId = $this->resolveSchoolIdFromRequest();

        // If migration not run yet (e.g. on Railway), return empty JSON so frontend never gets HTML error page
        if (! Schema::hasColumn('elections', 'show_live_results')) {
            return response()->json([
                'success' => true,
                'elections' => [],
                'timestamp' => $now->toIso8601String(),
            ]);
        }

        try {
            // Admin-published elections on landing page (scoped by resolveSchoolIdFromRequest()).
            $elections = Election::withoutGlobalScopes()
                ->where('show_live_results', true)
                ->whereIn('status', ['upcoming', 'ongoing', 'completed'])
                ->when($schoolId, function ($query) use ($schoolId) {
                    $query->where('school_id', $schoolId);
                })
                ->with(['organization' => function ($query) {
                    $query->withoutGlobalScopes();
                }])
                ->orderBy('election_date', 'desc')
                ->orderBy('id', 'desc')
                ->get();

            $archivedElections = ArchivedElection::withoutGlobalScopes()
                ->where('show_live_results', true)
                ->where('status', 'completed')
                ->when($schoolId, function ($query) use ($schoolId) {
                    $query->where('school_id', $schoolId);
                })
                ->with(['organization' => function ($query) {
                    $query->withoutGlobalScopes();
                }])
                ->orderBy('election_date', 'desc')
                ->orderBy('id', 'desc')
                ->get();
        } catch (\Throwable $e) {
            return response()->json([
                'success' => true,
                'elections' => [],
                'timestamp' => $now->toIso8601String(),
            ]);
        }

        $results = [];

        foreach ($elections as $election) {
            $startDateTime = $this->parseElectionStartTime($election);
            $endDateTime = $this->parseElectionEndTime($election);
            $effectiveStatus = $election->status;

            // Auto-advance: upcoming → ongoing (start time has passed)
            if ($election->status === 'upcoming' && $startDateTime && $now->greaterThanOrEqualTo($startDateTime)) {
                $effectiveStatus = 'ongoing';
                $election->update(['status' => 'ongoing']);
            }

            // Auto-advance: ongoing → completed (end time has passed)
            if (in_array($effectiveStatus, ['ongoing']) && $endDateTime && $now->greaterThan($endDateTime)) {
                $effectiveStatus = 'completed';
                $election->update(['status' => 'completed']);
            }

            $candidatesByPosition = Candidate::withoutGlobalScopes()
                ->where('election_id', $election->id)
                ->with(['position', 'partylist'])
                ->withCount('votes')
                ->get()
                ->groupBy('position_id');

            $positionsData = [];

            foreach ($candidatesByPosition as $positionId => $candidates) {
                $position = $candidates->first()->position;
                if (! $position) {
                    continue;
                }
                $positionOrder = (int) ($position->order ?? 0);

                $candidatesData = [];

                if ($effectiveStatus === 'ongoing') {
                    // ONGOING: Use anonymized data (question marks, Candidate A/B/C)
                    $candidatesArray = $candidates->toArray();
                    $seed = crc32($election->id.'-'.$positionId);
                    $shuffledCandidates = $this->seededShuffle($candidatesArray, $seed);

                    $letterIndex = 0;
                    foreach ($shuffledCandidates as $candidate) {
                        $anonymousLabel = $this->getAnonymousLabel($letterIndex);
                        $currentVotes = Vote::withoutGlobalScopes()->where('candidate_id', $candidate['id'])->count();

                        $candidatesData[] = [
                            'id' => $candidate['id'],
                            'name' => "Candidate {$anonymousLabel}",
                            'photo' => null, // Hidden during ongoing
                            'votes_count' => $currentVotes,
                            'is_anonymous' => true,
                            'partylist_name' => null, // Hidden during ongoing
                        ];
                        $letterIndex++;
                    }
                } else {
                    // COMPLETED: Reveal real candidate info!
                    foreach ($candidates as $candidate) {
                        $currentVotes = Vote::withoutGlobalScopes()->where('candidate_id', $candidate->id)->count();

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
                            'partylist_name' => $candidate->partylist?->name ?? null,
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
                    'position_order' => $positionOrder,
                    'number_of_slots' => $position->number_of_slots ?? 1,
                    'candidates' => $candidatesData,
                    'total_votes' => array_sum(array_column($candidatesData, 'votes_count')),
                ];
            }

            // Sort positions by admin-configured order (same as Positions management)
            usort($positionsData, function ($a, $b) {
                return ($a['position_order'] ?? 0) <=> ($b['position_order'] ?? 0);
            });

            $distinctVoters = $election->getDistinctVoterCount();
            $quorumVoid = $effectiveStatus === 'completed' && $election->isResultsVoidDueToQuorum();

            $resultData = [
                'id' => $election->id,
                'election_name' => $election->election_name,
                'organization' => $election->organization ? $election->organization->name : null,
                'election_date' => $election->election_date->format('M d, Y'),
                'status' => $effectiveStatus,
                'positions' => $positionsData,
                'total_voters' => $distinctVoters,
                'quorum_void' => $quorumVoid,
                'quorum' => [
                    'applicable' => $election->isQuorumApplicable(),
                    'voter_capacity' => $election->voter_capacity,
                    'required_votes' => $election->getQuorumRequiredVotes(),
                    'distinct_voters' => $distinctVoters,
                    'met' => $election->isQuorumMet(),
                ],
            ];

            if ($effectiveStatus === 'upcoming') {
                // For upcoming elections, show time until election starts
                if ($startDateTime) {
                    $timeUntilStart = $now->diff($startDateTime);
                    $resultData['starts_at'] = $startDateTime->format('M d, Y g:i A');
                    $resultData['starts_in_seconds'] = $now->diffInSeconds($startDateTime, false);
                    $resultData['time_remaining'] = [
                        'days' => $timeUntilStart->d,
                        'hours' => $timeUntilStart->h,
                        'minutes' => $timeUntilStart->i,
                        'seconds' => $timeUntilStart->s,
                    ];
                }
            } elseif ($effectiveStatus === 'ongoing') {
                // For ongoing elections, show time until election ends
                if ($endDateTime) {
                    $timeUntilEnd = $now->diff($endDateTime);
                    $resultData['ends_at'] = $endDateTime->format('M d, Y g:i A');
                    $resultData['ends_in_seconds'] = $now->diffInSeconds($endDateTime, false);
                    $resultData['time_remaining'] = [
                        'days' => $timeUntilEnd->d,
                        'hours' => $timeUntilEnd->h,
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
                        'days' => $timeRemaining->d,
                        'hours' => $timeRemaining->h,
                        'minutes' => $timeRemaining->i,
                        'seconds' => $timeRemaining->s,
                    ];
                }
            }

            $results[] = $resultData;
        }

        foreach ($archivedElections as $archivedElection) {
            $candidatesByPosition = ArchivedCandidate::withoutGlobalScopes()
                ->where('archived_election_id', $archivedElection->id)
                ->with('archivedPartylist')
                ->get()
                ->groupBy(function ($candidate) {
                    return ($candidate->original_position_id ?: 'archived-position-'.$candidate->id)
                        .'|'.($candidate->position_name ?: 'Unknown Position')
                        .'|'.((int) ($candidate->position_order ?? 9999))
                        .'|'.((int) ($candidate->number_of_slots ?? 1));
                });

            $positionsData = [];
            foreach ($candidatesByPosition as $positionKey => $candidates) {
                [$positionId, $positionName, $positionOrder, $numberOfSlots] = array_pad(explode('|', $positionKey), 4, 0);

                $candidatesData = [];
                foreach ($candidates as $candidate) {
                    $currentVotes = ArchivedVote::withoutGlobalScopes()
                        ->where('archived_candidate_id', $candidate->id)
                        ->count();

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
                        'partylist_name' => $candidate->archivedPartylist?->name ?? null,
                    ];
                }

                usort($candidatesData, function ($a, $b) {
                    return $b['votes_count'] - $a['votes_count'];
                });

                $positionsData[] = [
                    'position_id' => $positionId,
                    'position_name' => $positionName ?: 'Unknown Position',
                    'position_order' => (int) $positionOrder,
                    'number_of_slots' => max(1, (int) $numberOfSlots),
                    'candidates' => $candidatesData,
                    'total_votes' => array_sum(array_column($candidatesData, 'votes_count')),
                ];
            }

            usort($positionsData, function ($a, $b) {
                return ($a['position_order'] ?? 0) <=> ($b['position_order'] ?? 0);
            });

            $effectiveStatus = 'completed';
            $endDateTime = $this->parseElectionEndTime($archivedElection);

            $resultData = [
                // Use negative IDs so archived and active entries never clash in frontend keys.
                'id' => 0 - (int) $archivedElection->id,
                'election_name' => $archivedElection->election_name,
                'organization' => $archivedElection->organization?->name,
                'election_date' => $archivedElection->election_date ? $archivedElection->election_date->format('M d, Y') : 'N/A',
                'status' => $effectiveStatus,
                'positions' => $positionsData,
                'total_voters' => ArchivedVote::withoutGlobalScopes()
                    ->where('archived_election_id', $archivedElection->id)
                    ->distinct('voter_id')
                    ->count(),
            ];

            if ($endDateTime) {
                $resultData['ended_at'] = $endDateTime->format('M d, Y g:i A');
            }

            $results[] = $resultData;
        }

        return response()->json([
            'success' => true,
            'elections' => $results,
            'timestamp' => $now->toIso8601String(),
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate');
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
        $schoolId = $this->resolveSchoolIdFromRequest();
        $rawElectionId = (string) $electionId;
        $archivedElectionId = null;

        if (preg_match('/^archived[-_](\d+)$/', $rawElectionId, $matches)) {
            $archivedElectionId = (int) $matches[1];
        } elseif (is_numeric($rawElectionId) && (int) $rawElectionId < 0) {
            $archivedElectionId = abs((int) $rawElectionId);
        }

        if (! Schema::hasColumn('elections', 'show_live_results')) {
            return response()->json([
                'success' => false,
                'message' => 'Election not found or results are not displayed.',
            ], 404);
        }

        if ($archivedElectionId) {
            $archivedElection = ArchivedElection::withoutGlobalScopes()
                ->where('id', $archivedElectionId)
                ->where('show_live_results', true)
                ->where('status', 'completed')
                ->when($schoolId, function ($query) use ($schoolId) {
                    $query->where('school_id', $schoolId);
                })
                ->first();

            if (! $archivedElection) {
                return response()->json([
                    'success' => false,
                    'message' => 'Election not found or results are not displayed.',
                ], 404);
            }

            $voteCounts = ArchivedVote::withoutGlobalScopes()
                ->where('archived_election_id', $archivedElectionId)
                ->select('archived_candidate_id', DB::raw('count(*) as votes'))
                ->groupBy('archived_candidate_id')
                ->pluck('votes', 'archived_candidate_id')
                ->toArray();

            return response()->json([
                'success' => true,
                'vote_counts' => $voteCounts,
                'total_voters' => ArchivedVote::withoutGlobalScopes()
                    ->where('archived_election_id', $archivedElectionId)
                    ->distinct('voter_id')
                    ->count(),
                'timestamp' => $now->toIso8601String(),
            ]);
        }

        $election = Election::withoutGlobalScopes()
            ->where('id', $electionId)
            ->where('show_live_results', true)
            ->whereIn('status', ['ongoing', 'completed'])
            ->when($schoolId, function ($query) use ($schoolId) {
                $query->where('school_id', $schoolId);
            })
            ->first();

        if (! $election) {
            return response()->json([
                'success' => false,
                'message' => 'Election not found or results are not displayed.',
            ], 404);
        }

        // Get vote counts
        $voteCounts = Vote::withoutGlobalScopes()->where('election_id', $electionId)
            ->select('candidate_id', DB::raw('count(*) as votes'))
            ->groupBy('candidate_id')
            ->pluck('votes', 'candidate_id')
            ->toArray();

        return response()->json([
            'success' => true,
            'vote_counts' => $voteCounts,
            'total_voters' => Vote::withoutGlobalScopes()->where('election_id', $electionId)->distinct('voter_id')->count(),
            'timestamp' => $now->toIso8601String(),
        ]);
    }

    /**
     * Resolve which school's published results apply.
     * Client calls /api/live-results (first segment is "api"), so campus comes from ?school_id=, then Referer path (/sipalay), then session, then main-campus.
     * Referer beats session so a stale session never overrides the portal page the user is on.
     */
    private function resolveSchoolIdFromRequest(): ?int
    {
        $request = request();

        $schoolParam = $request->query('school_id');
        if ($schoolParam !== null && $schoolParam !== '') {
            if (is_numeric($schoolParam)) {
                return (int) $schoolParam;
            }

            $school = School::withoutGlobalScopes()->where('slug', $schoolParam)->first();

            return $school?->id;
        }

        $fromReferer = $this->resolveSchoolIdFromReferer();
        if ($fromReferer !== null) {
            return $fromReferer;
        }

        if ($request->session()->has('school_id')) {
            return (int) $request->session()->get('school_id');
        }

        return School::withoutGlobalScopes()->where('slug', 'main-campus')->value('id');
    }

    /**
     * Infer campus from the page URL (e.g. Referer http://host/sipalay → Sipalay school id, / → main-campus).
     */
    private function resolveSchoolIdFromReferer(): ?int
    {
        $referer = request()->header('Referer');
        if (! $referer) {
            return null;
        }

        $path = parse_url($referer, PHP_URL_PATH);
        if ($path === false || $path === null) {
            return null;
        }

        $trimmed = trim($path, '/');
        if ($trimmed === '') {
            return School::withoutGlobalScopes()->where('slug', 'main-campus')->value('id');
        }

        $first = explode('/', $trimmed)[0] ?? '';
        if ($first === '' || in_array($first, self::RESERVED_FIRST_SEGMENTS, true)) {
            return null;
        }

        $school = School::withoutGlobalScopes()->where('slug', $first)->first();

        return $school ? (int) $school->id : null;
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
