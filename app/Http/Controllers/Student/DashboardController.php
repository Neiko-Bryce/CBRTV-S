<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
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

        // For each election, set datetime info and load candidates
        foreach ($elections as $election) {
            $dateString = $this->electionDateToString($election->election_date) ?? Carbon::now('Asia/Manila')->format('Y-m-d');
            $electionDate = Carbon::parse($dateString, 'Asia/Manila');
            $election->start_datetime = $this->parseStartDateTime($dateString, $election->timestarted, $electionDate);

            // End datetime (midnight 00:00 = next calendar day, same as calculateStatus)
            if (! empty($election->time_ended)) {
                $endTimeStr = trim((string) $election->time_ended);
                $endTimeParts = explode(':', $endTimeStr);
                if (count($endTimeParts) >= 2) {
                    $endTimeStr = $endTimeParts[0].':'.$endTimeParts[1].':'.(isset($endTimeParts[2]) ? $endTimeParts[2] : '00');
                }
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
        }

        return view('student.dashboard', compact('elections'));
    }

    /**
     * Display the voting history page for the current user.
     */
    public function votesHistory()
    {
        $votingHistory = Vote::withoutGlobalScopes()
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
                ];
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

        // End datetime for countdown (same midnight rule: 00:00 = next calendar day)
        $dateString = $this->electionDateToString($election->election_date) ?? Carbon::now('Asia/Manila')->format('Y-m-d');
        $electionDate = Carbon::parse($dateString, 'Asia/Manila');
        $startDateTime = $this->parseStartDateTime($dateString, $election->timestarted, $electionDate);
        $endDateTime = null;
        if (! empty($election->time_ended)) {
            $endTimeStr = trim((string) $election->time_ended);
            $endTimeParts = explode(':', $endTimeStr);
            if (count($endTimeParts) >= 2) {
                $endTimeStr = $endTimeParts[0].':'.$endTimeParts[1].':'.(isset($endTimeParts[2]) ? $endTimeParts[2] : '00');
            }
            try {
                $endDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $dateString.' '.$endTimeStr, 'Asia/Manila');
                if ($endDateTime->lessThanOrEqualTo($startDateTime)) {
                    $endDateTime->addDay();
                }
            } catch (\Exception $e) {
                $endDateTime = null;
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

        foreach ($votes as $candidateId) {
            $existingVote = Vote::withoutGlobalScopes()
                ->where('election_id', $electionId)
                ->where('candidate_id', $candidateId)
                ->where('voter_id', $userId)
                ->first();

            if (! $existingVote) {
                Vote::create([
                    'election_id' => $electionId,
                    'candidate_id' => $candidateId,
                    'voter_id' => $userId,
                ]);

                $candidate = Candidate::withoutGlobalScopes()->find($candidateId);
                if ($candidate) {
                    $candidate->increment('votes_count');
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Your votes have been submitted successfully!',
        ]);
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
                $endTimeStr = trim((string) $election->time_ended);
                $parts = explode(':', $endTimeStr);
                if (count($parts) >= 2) {
                    $endTimeStr = $parts[0].':'.$parts[1].':'.(isset($parts[2]) ? $parts[2] : '00');
                }
                if (strlen($endTimeStr) >= 5) {
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
        $timeStr = trim((string) $timestarted);
        $parts = explode(':', $timeStr);
        if (count($parts) >= 2) {
            $timeStr = $parts[0].':'.$parts[1].':'.(isset($parts[2]) ? $parts[2] : '00');
        }
        try {
            return Carbon::createFromFormat('Y-m-d H:i:s', $dateString.' '.$timeStr, 'Asia/Manila');
        } catch (\Exception $e) {
            return $electionDate->copy()->startOfDay();
        }
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
