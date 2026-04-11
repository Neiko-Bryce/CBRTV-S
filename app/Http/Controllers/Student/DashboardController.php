<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ArchivedVote;
use App\Models\Candidate;
use App\Models\Election;
use App\Models\Position;
use App\Models\School;
use App\Models\Vote;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Display the student dashboard with upcoming/ongoing elections and candidates.
     * List is built from calculated status (current time) so it never depends on stale DB status.
     */
    public function index()
    {
        $user = Auth::user();
        $context = $this->resolveStudentContext($user);
        $resolvedSchoolId = $context['school_id'];
        $allowedSchoolIds = $this->expandEquivalentSchoolIds($resolvedSchoolId);

        $allElections = Election::withoutGlobalScopes()
            ->with('organization')
            ->when(! empty($allowedSchoolIds), function ($q) use ($allowedSchoolIds) {
                $q->where(function ($inner) use ($allowedSchoolIds) {
                    $inner->whereIn('school_id', $allowedSchoolIds)
                        ->orWhereNull('school_id');
                });
            })
            ->orderBy('election_date', 'asc')
            ->orderBy('timestarted', 'asc')
            ->get();

        // Build list from calculated status only (upcoming/ongoing).
        $elections = collect();
        foreach ($allElections as $election) {
            if (strtolower((string) ($election->status ?? '')) === 'cancelled') {
                continue;
            }
            try {
                // Calculate status in-memory for the view
                $calculatedStatus = strtolower($this->calculateStatus($election));
                $election->setAttribute('status', $calculatedStatus);

                // Do NOT update in DB here to avoid lock contention during high traffic
                // Persistence should be handled by an admin action or background task

                if (in_array($calculatedStatus, ['upcoming', 'ongoing'], true)) {
                    $elections->push($election);
                }
            } catch (\Throwable $e) {
                Log::warning('Student dashboard: skip election id='.($election->id ?? '?').': '.$e->getMessage());
            }
        }

        // Sort: ongoing first, then upcoming; then by date/time
        $elections = $elections->sortBy([
            fn ($e) => ($e->status ?? '') === 'ongoing' ? 0 : 1,
            fn ($e) => $this->electionDateToString($e->election_date) ?? '',
            fn ($e) => $e->timestarted ?? '',
        ])->values();

        $studentForCourse = $this->getStudentRecordForVoter();
        $studentCourse = $studentForCourse?->course;

        // For each election, set datetime info and load candidates
        foreach ($elections as $election) {
            $dateString = $this->electionDateToString($election->election_date) ?? Carbon::now('Asia/Manila')->format('Y-m-d');
            $electionDate = Carbon::parse($dateString, 'Asia/Manila');
            $election->start_datetime = $this->parseStartDateTime($dateString, $election->timestarted, $electionDate);

            // End datetime (midnight 00:00 = next calendar day, same as calculateStatus)
            if (! empty($election->time_ended)) {
                $endTimeStr = $this->normalizeTimeToHis(trim((string) $election->time_ended));
                if ($endTimeStr !== null) {
                    try {
                        $endDt = Carbon::createFromFormat('Y-m-d H:i:s', $dateString.' '.$endTimeStr, 'Asia/Manila');
                        if ($election->start_datetime && $endDt->lessThanOrEqualTo($election->start_datetime)) {
                            $endDt->addDay();
                        }
                        $election->end_datetime = $endDt;
                    } catch (\Exception $e) {
                        $election->end_datetime = null;
                    }
                } else {
                    $election->end_datetime = null;
                }
            } else {
                $election->end_datetime = null;
            }

            $candidates = Candidate::withoutGlobalScopes()
                ->where('election_id', $election->id)
                ->with(['position', 'partylist', 'election.organization'])
                ->orderBy('position_id', 'asc')
                ->orderBy('candidate_name', 'asc')
                ->get();

            // Group candidates by position, then sort by position order (admin-configured)
            $election->candidatesByPosition = $candidates->groupBy('position_id')
                ->sortBy(fn ($cands) => $cands->first()->position->order ?? 0);

            $userVotes = Vote::withoutGlobalScopes()
                ->where('election_id', $election->id)
                ->where('voter_id', Auth::id())
                ->count();

            $election->hasVoted = $userVotes > 0;
            $election->vote_disabled_course = ! $election->studentCourseAllowsVoting($studentCourse);
            $election->vote_disabled_capacity = $election->hasVoterCapacityLimit()
                && $election->isVoterCapacityFull()
                && ! $election->hasVoted;
        }

        return view('student.dashboard', compact('elections'));
    }

    /**
     * Display the voting history page for the current user.
     */
    public function votesHistory()
    {
        $activeHistory = Vote::withoutGlobalScopes()
            ->where('voter_id', Auth::id())
            ->with(['election.organization', 'candidate.position', 'candidate.partylist'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('election_id')
            ->map(function ($votes) {
                $election = $votes->first()->election;

                return [
                    'election' => $election,
                    'voted_at' => $votes->first()->created_at,
                    'candidates' => $votes->map(function ($vote) {
                        return [
                            'candidate' => $vote->candidate,
                            'position' => $vote->candidate->position,
                            'partylist' => $vote->candidate->partylist,
                        ];
                    })->groupBy(function ($item) {
                        return $item['position']->id ?? 'no-position';
                    }),
                    'is_archived' => false,
                ];
            })
            ->values();

        $archivedHistory = ArchivedVote::withoutGlobalScopes()
            ->where('voter_id', Auth::id())
            ->with(['archivedElection.organization', 'archivedCandidate.archivedPartylist'])
            ->orderByDesc('voted_at')
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('archived_election_id')
            ->map(function ($votes) {
                $archivedElection = $votes->first()->archivedElection;
                if (! $archivedElection) {
                    return null;
                }

                $election = (object) [
                    'id' => $archivedElection->original_election_id ?: $archivedElection->id,
                    'election_name' => $archivedElection->election_name,
                    'election_date' => $archivedElection->election_date,
                    'venue' => $archivedElection->venue,
                    'organization' => $archivedElection->organization,
                ];

                $candidates = $votes->map(function ($vote) {
                    $candidate = $vote->archivedCandidate;
                    if (! $candidate) {
                        return null;
                    }

                    $candidateObject = (object) [
                        'id' => $candidate->original_candidate_id ?: $candidate->id,
                        'candidate_name' => $candidate->candidate_name,
                        'photo' => $candidate->photo,
                    ];

                    $positionObject = (object) [
                        'id' => $candidate->original_position_id ?: ('archived-position-'.$candidate->id),
                        'name' => $candidate->position_name ?: 'Position',
                    ];

                    $partylistObject = null;
                    if ($candidate->archivedPartylist) {
                        $partylistObject = (object) [
                            'name' => $candidate->archivedPartylist->name,
                            'color' => $candidate->archivedPartylist->color,
                        ];
                    }

                    return [
                        'candidate' => $candidateObject,
                        'position' => $positionObject,
                        'partylist' => $partylistObject,
                    ];
                })
                    ->filter()
                    ->groupBy(function ($item) {
                        return $item['position']->id ?? 'archived-no-position';
                    });

                return [
                    'election' => $election,
                    'voted_at' => $votes->first()->voted_at ?: $votes->first()->created_at,
                    'candidates' => $candidates,
                    'is_archived' => true,
                ];
            })
            ->filter()
            ->values();

        $votingHistory = $activeHistory
            ->concat($archivedHistory)
            ->sortByDesc(function ($history) {
                $votedAt = $history['voted_at'] ?? null;
                if ($votedAt instanceof \DateTimeInterface) {
                    return $votedAt->getTimestamp();
                }

                $timestamp = strtotime((string) $votedAt);

                return $timestamp ?: 0;
            })
            ->values();

        return view('student.voteshistory', compact('votingHistory'));
    }

    /**
     * Display the voting page for a specific election.
     */
    public function vote($electionId)
    {
        $election = Election::withoutGlobalScopes()
            ->with(['organization', 'candidates.position', 'candidates.partylist'])
            ->findOrFail($electionId);
        $this->assertElectionAccess($election);

        // Check if election is ongoing
        $calculatedStatus = $this->calculateStatus($election);
        if ($calculatedStatus !== 'ongoing') {
            return redirect()->route('student.dashboard')
                ->with('error', 'This election is not currently active for voting.');
        }

        $candidates = Candidate::withoutGlobalScopes()
            ->where('election_id', $election->id)
            ->with(['position', 'partylist'])
            ->orderBy('position_id', 'asc')
            ->orderBy('candidate_name', 'asc')
            ->get();

        $candidatesByPosition = $candidates->groupBy('position_id')
            ->sortBy(fn ($cands) => $cands->first()->position->order ?? 0);

        $userVotes = Vote::withoutGlobalScopes()
            ->where('election_id', $election->id)
            ->where('voter_id', Auth::id())
            ->pluck('candidate_id')
            ->toArray();

        // Check if user has already voted (has votes for this election)
        $hasVoted = count($userVotes) > 0;

        // If user has already voted, redirect to dashboard with message
        if ($hasVoted) {
            return redirect()->route('student.dashboard')
                ->with('info', 'You have already submitted your votes for this election.');
        }

        if (! $election->acceptsNewDistinctVoter((int) Auth::id())) {
            return redirect()->route('student.dashboard')
                ->with('error', 'This election has reached its voter capacity ('.(int) $election->voter_capacity.' distinct voters). No additional ballots can be accepted.');
        }

        // End datetime for countdown (same midnight rule: 00:00 = next calendar day)
        $dateString = $this->electionDateToString($election->election_date) ?? Carbon::now('Asia/Manila')->format('Y-m-d');
        $electionDate = Carbon::parse($dateString, 'Asia/Manila');
        $startDateTime = $this->parseStartDateTime($dateString, $election->timestarted, $electionDate);
        $endDateTime = null;
        if (! empty($election->time_ended)) {
            $endTimeStr = $this->normalizeTimeToHis(trim((string) $election->time_ended));
            if ($endTimeStr !== null) {
                try {
                    $endDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $dateString.' '.$endTimeStr, 'Asia/Manila');
                    if ($endDateTime->lessThanOrEqualTo($startDateTime)) {
                        $endDateTime->addDay();
                    }
                } catch (\Exception $e) {
                    $endDateTime = null;
                }
            }
        }

        return view('student.vote', compact('election', 'candidatesByPosition', 'userVotes', 'endDateTime', 'hasVoted'));
    }

    /**
     * Submit votes for an election.
     */
    public function submitVote(Request $request, $electionId)
    {
        $election = Election::withoutGlobalScopes()->findOrFail($electionId);
        $this->assertElectionAccess($election);

        // Check if election is ongoing
        $calculatedStatus = $this->calculateStatus($election);
        if ($calculatedStatus !== 'ongoing') {
            return response()->json([
                'success' => false,
                'message' => 'This election is not currently active for voting.',
            ], 400);
        }

        $request->validate([
            'votes' => 'required|array',
            'votes.*' => 'exists:candidates,id',
        ]);

        $userId = Auth::id();
        $votes = array_values(array_unique($request->input('votes', [])));

        // 1. Atomic Lock to prevent double-submission race conditions
        // This ensures if a user double-clicks submit, the second request is blocked immediately
        $lock = \Illuminate\Support\Facades\Cache::lock("submit_vote_{$electionId}_{$userId}", 10);

        if (! $lock->get()) {
            return response()->json([
                'success' => false,
                'message' => 'Your vote is currently being processed. Please wait.',
            ], 429);
        }

        try {
            $alreadyVoted = Vote::withoutGlobalScopes()
                ->where('election_id', $electionId)
                ->where('voter_id', $userId)
                ->exists();
            if ($alreadyVoted) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already submitted your votes for this election.',
                ], 409);
            }

            if (! $election->acceptsNewDistinctVoter((int) $userId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'This election has reached its voter capacity ('.(int) $election->voter_capacity.' distinct voters). No additional ballots can be accepted.',
                ], 403);
            }

            $candidatesToVote = Candidate::withoutGlobalScopes()
                ->whereIn('id', $votes)
                ->where('election_id', $electionId)
                ->get();

            $positions = Position::withoutGlobalScopes()
                ->where('organization_id', $election->organization_id)
                ->get()
                ->keyBy('id');

            // Group selected votes by position
            $votesByPosition = $candidatesToVote->groupBy('position_id');

            // Validate vote counts per position
            foreach ($votesByPosition as $positionId => $candidates) {
                $position = $positions[$positionId] ?? null;
                if ($position) {
                    // If number_of_slots is null or 0, default to 1 (safety fallback)
                    $maxSlots = $position->number_of_slots > 0 ? $position->number_of_slots : 1;

                    if ($candidates->count() > $maxSlots) {
                        return response()->json([
                            'success' => false,
                            'message' => "You selected too many candidates for {$position->name}. Max allowed is {$maxSlots}.",
                        ], 422);
                    }
                }
            }

            // 2. Database Transaction Wrap
            // Ensures ALL votes are saved, or NONE are. Prevents partial ballots on crash.
            \Illuminate\Support\Facades\DB::transaction(function () use ($electionId, $userId, $votes) {
                $electionLocked = Election::withoutGlobalScopes()->lockForUpdate()->find($electionId);
                if (! $electionLocked || ! $electionLocked->acceptsNewDistinctVoter((int) $userId)) {
                    throw new \RuntimeException('VOTER_CAPACITY_FULL');
                }

                $votesToInsert = [];
                $candidateIdsToIncrement = [];

                foreach ($votes as $candidateId) {
                    // Prepare bulk insert
                    $votesToInsert[] = [
                        'election_id' => $electionId,
                        'candidate_id' => $candidateId,
                        'voter_id' => $userId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    $candidateIdsToIncrement[] = $candidateId;
                }

                if (! empty($votesToInsert)) {
                    // Insert all votes at once (much faster than looping)
                    Vote::insert($votesToInsert);

                    // 3. Batch candidate increment
                    // Decreases Row Lock time significantly for MySQL
                    if (! empty($candidateIdsToIncrement)) {
                        Candidate::withoutGlobalScopes()
                            ->whereIn('id', $candidateIdsToIncrement)
                            ->increment('votes_count');
                    }
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Your votes have been submitted successfully!',
            ]);

        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'VOTER_CAPACITY_FULL') {
                return response()->json([
                    'success' => false,
                    'message' => 'This election has reached its voter capacity. No additional ballots can be accepted.',
                ], 403);
            }
            throw $e;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Voting Error (Election: {$electionId}, User: {$userId}): ".$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving your votes. Please try again.',
            ], 500);
        } finally {
            // Always release the lock when done
            $lock->release();
        }
    }

    /**
     * Calculate election status based on current time.
     * Uses the same logic as ElectionController.
     */
    private function calculateStatus($election)
    {
        try {
            $now = Carbon::now('Asia/Manila');

            if (empty($election->election_date)) {
                return 'upcoming';
            }

            $dateString = $this->electionDateToString($election->election_date);
            if (! $dateString) {
                return 'upcoming';
            }

            $electionDate = Carbon::parse($dateString, 'Asia/Manila');

            // Start datetime
            $electionDateTime = $this->parseStartDateTime($dateString, $election->timestarted, $electionDate);

            // End datetime: if time_ended is 00:00 (midnight) or <= start, treat as next calendar day
            if (! empty($election->time_ended)) {
                $endTimeStr = $this->normalizeTimeToHis(trim((string) $election->time_ended));
                if ($endTimeStr !== null) {
                    try {
                        $endDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $dateString.' '.$endTimeStr, 'Asia/Manila');
                        if ($endDateTime->lessThanOrEqualTo($electionDateTime)) {
                            $endDateTime->addDay();
                        }
                        if ($now->greaterThanOrEqualTo($endDateTime)) {
                            return 'completed';
                        }
                    } catch (\Exception $e) {
                        Log::debug('calculateStatus end time: '.$e->getMessage());
                    }
                }
            }

            if ($now->greaterThanOrEqualTo($electionDateTime)) {
                return 'ongoing';
            }

            return 'upcoming';
        } catch (\Exception $e) {
            Log::error('Error calculating election status: '.$e->getMessage());

            return 'upcoming';
        }
    }

    private function electionDateToString($electionDate): ?string
    {
        if ($electionDate === null) {
            return null;
        }
        if ($electionDate instanceof \Carbon\Carbon || $electionDate instanceof \DateTimeInterface) {
            return $electionDate->format('Y-m-d');
        }
        $s = trim((string) $electionDate);
        if ($s === '') {
            return null;
        }
        try {
            return Carbon::parse($s)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    private function parseStartDateTime(string $dateString, $timestarted, Carbon $electionDate): Carbon
    {
        if (empty($timestarted)) {
            return $electionDate->copy()->startOfDay();
        }
        $timeStr = $this->normalizeTimeToHis(trim((string) $timestarted));
        if ($timeStr === null) {
            return $electionDate->copy()->startOfDay();
        }
        try {
            return Carbon::createFromFormat('Y-m-d H:i:s', $dateString.' '.$timeStr, 'Asia/Manila');
        } catch (\Exception $e) {
            return $electionDate->copy()->startOfDay();
        }
    }

    /**
     * Normalize time string to HH:ii:ss (two-digit hour and minute) for createFromFormat('Y-m-d H:i:s').
     * Handles "2:31", "02:31", "02:31:00", "2:31:00" etc. Returns null if invalid.
     */
    private function normalizeTimeToHis(string $time): ?string
    {
        $parts = array_map('trim', explode(':', $time));
        if (count($parts) < 2) {
            return null;
        }
        $h = (int) $parts[0];
        $i = (int) $parts[1];
        $s = isset($parts[2]) ? (int) $parts[2] : 0;
        if ($h < 0 || $h > 23 || $i < 0 || $i > 59 || $s < 0 || $s > 59) {
            return null;
        }

        return sprintf('%02d:%02d:%02d', $h, $i, $s);
    }

    /**
     * Resolve the effective school/org for the logged-in student.
     */
    private function resolveStudentContext($user): array
    {
        $student = null;
        if ($user && $user->email) {
            $student = \App\Models\Student::withoutGlobalScopes()
                ->where('student_id_number', $user->email)
                ->first();
        }

        $organizationId = $student?->organization_id ?: $user?->organization_id;
        $schoolId = $student?->school_id ?: $user?->school_id;
        if (! $schoolId && $organizationId) {
            $org = \App\Models\Organization::withoutGlobalScopes()->find($organizationId);
            if ($org && $org->school_id) {
                $schoolId = $org->school_id;
            }
        }
        if (! $schoolId) {
            $schoolId = request('school_id') ?: session('school_id');
        }

        $resolvedSchoolId = null;
        if ($schoolId) {
            if (is_numeric($schoolId)) {
                $resolvedSchoolId = (int) $schoolId;
            } else {
                $school = School::where('slug', $schoolId)->first();
                $resolvedSchoolId = $school?->id;
            }
        }
        $resolvedSchoolId = $this->canonicalizeSchoolId($resolvedSchoolId);

        // Persist missing links for legacy accounts or canonicalized school mapping.
        if ($user && $resolvedSchoolId && $user->school_id !== $resolvedSchoolId) {
            $user->school_id = $resolvedSchoolId;
        }
        if ($user && $organizationId && (! $user->organization_id || ($student && $student->organization_id && $user->organization_id !== $student->organization_id))) {
            $user->organization_id = $organizationId;
        }
        if ($user && $user->isDirty()) {
            $user->save();
        }

        return [
            'school_id' => $resolvedSchoolId,
            'organization_id' => $organizationId,
        ];
    }

    /**
     * Ensure students only access elections from their school/org.
     */
    private function assertElectionAccess(Election $election): void
    {
        $user = Auth::user();
        $context = $this->resolveStudentContext($user);
        $studentSchoolId = $this->canonicalizeSchoolId($context['school_id']);
        $electionSchoolId = $this->canonicalizeSchoolId($election->school_id);
        $schoolOk = ! $studentSchoolId || ! $electionSchoolId || $electionSchoolId === $studentSchoolId;
        if (! $schoolOk) {
            abort(403, 'You are not authorized to access this election.');
        }
        $student = $this->getStudentRecordForVoter();
        $course = $student?->course;
        if (! $election->studentCourseAllowsVoting($course)) {
            abort(403, 'You are not eligible to vote in this election based on your course.');
        }
    }

    /**
     * Student row linked to this login (student_id_number = user email).
     */
    private function getStudentRecordForVoter(): ?\App\Models\Student
    {
        $user = Auth::user();
        if (! $user || ! $user->email) {
            return null;
        }

        return \App\Models\Student::withoutGlobalScopes()
            ->where('student_id_number', $user->email)
            ->first();
    }

    /**
     * Canonicalize legacy duplicate school IDs to the active campus ID.
     */
    private function canonicalizeSchoolId($schoolId): ?int
    {
        if (! $schoolId || ! is_numeric($schoolId)) {
            return $schoolId ? (int) $schoolId : null;
        }

        $school = School::withoutGlobalScopes()->find((int) $schoolId);
        if (! $school) {
            return (int) $schoolId;
        }

        // Legacy data can contain "main-school" while active records use "main-campus".
        if ($school->slug === 'main-school') {
            $mainCampus = School::withoutGlobalScopes()->where('slug', 'main-campus')->first();
            if ($mainCampus) {
                return (int) $mainCampus->id;
            }
        }

        return (int) $schoolId;
    }

    /**
     * Expand equivalent legacy school IDs (main-school/main-campus) for list queries.
     */
    private function expandEquivalentSchoolIds(?int $schoolId): array
    {
        if (! $schoolId) {
            return [];
        }

        $ids = [(int) $schoolId];
        $school = School::withoutGlobalScopes()->find((int) $schoolId);
        if (! $school) {
            return $ids;
        }

        if ($school->slug === 'main-school' || $school->slug === 'main-campus') {
            $legacy = School::withoutGlobalScopes()->whereIn('slug', ['main-school', 'main-campus'])->pluck('id')->all();
            foreach ($legacy as $id) {
                $ids[] = (int) $id;
            }
        }

        return array_values(array_unique($ids));
    }
}
