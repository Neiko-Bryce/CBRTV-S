<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Election;
use App\Models\Position;
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
        $allElections = Election::with('organization')->orderBy('election_date', 'asc')->orderBy('timestarted', 'asc')->get();

        // Build list from calculated status only (upcoming/ongoing). Persist status to DB for admin consistency.
        $elections = collect();
        foreach ($allElections as $election) {
            if (strtolower((string) ($election->status ?? '')) === 'cancelled') {
                continue;
            }
            try {
                $calculatedStatus = strtolower($this->calculateStatus($election));
                $election->setAttribute('status', $calculatedStatus);
                $election->update(['status' => $calculatedStatus]);
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

            // Load candidates for all elections (but only show when ongoing)
            $candidates = Candidate::where('election_id', $election->id)
                ->with(['position', 'partylist', 'election.organization'])
                ->orderBy('position_id', 'asc')
                ->orderBy('candidate_name', 'asc')
                ->get();

            // Group candidates by position, then sort by position order (admin-configured)
            $election->candidatesByPosition = $candidates->groupBy('position_id')
                ->sortBy(fn ($cands) => $cands->first()->position->order ?? 0);

            // Check if user has already voted for this election
            $userVotes = Vote::where('election_id', $election->id)
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
        // Get voting history for the current user
        $votingHistory = Vote::where('voter_id', Auth::id())
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
        $election = Election::with(['organization', 'candidates.position', 'candidates.partylist'])
            ->findOrFail($electionId);

        // Check if election is ongoing
        $calculatedStatus = $this->calculateStatus($election);
        if ($calculatedStatus !== 'ongoing') {
            return redirect()->route('student.dashboard')
                ->with('error', 'This election is not currently active for voting.');
        }

        // Get candidates grouped by position
        $candidates = Candidate::where('election_id', $election->id)
            ->with(['position', 'partylist'])
            ->orderBy('position_id', 'asc')
            ->orderBy('candidate_name', 'asc')
            ->get();

        // Group by position and sort by position order (admin-configured)
        $candidatesByPosition = $candidates->groupBy('position_id')
            ->sortBy(fn ($cands) => $cands->first()->position->order ?? 0);

        // Get user's existing votes for this election
        $userVotes = Vote::where('election_id', $election->id)
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
        $election = Election::findOrFail($electionId);

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
        $votes = $request->input('votes', []);

        // Get all candidates being voted for
        $candidatesToVote = Candidate::whereIn('id', $votes)
            ->where('election_id', $electionId)
            ->get();

        // Get available positions and their slots
        $positions = Position::where('organization_id', $election->organization_id)->get()->keyBy('id');

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

        // Get existing votes for this election
        $existingVotes = Vote::where('election_id', $electionId)
            ->where('voter_id', $userId)
            ->with('candidate')
            ->get();

        // Delete existing votes for positions that are being re-voted
        foreach ($candidatesToVote as $candidate) {
            $existingVote = $existingVotes->firstWhere('candidate.position_id', $candidate->position_id);
            if ($existingVote) {
                // Decrement vote count for old candidate
                $oldCandidate = Candidate::find($existingVote->candidate_id);
                if ($oldCandidate && $oldCandidate->votes_count > 0) {
                    $oldCandidate->decrement('votes_count');
                }
                $existingVote->delete();
            }
        }

        // Create new votes and update vote counts
        foreach ($votes as $candidateId) {
            // Check if vote already exists (shouldn't happen, but safety check)
            $existingVote = Vote::where('election_id', $electionId)
                ->where('candidate_id', $candidateId)
                ->where('voter_id', $userId)
                ->first();

            if (! $existingVote) {
                Vote::create([
                    'election_id' => $electionId,
                    'candidate_id' => $candidateId,
                    'voter_id' => $userId,
                ]);

                // Update candidate vote count
                $candidate = Candidate::find($candidateId);
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
}
