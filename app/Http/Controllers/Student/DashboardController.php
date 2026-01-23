<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Election;
use App\Models\Candidate;
use App\Models\Vote;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the student dashboard with upcoming/ongoing elections and candidates.
     */
    public function index()
    {
        // Get current Philippine time
        $now = Carbon::now('Asia/Manila');
        
        // Get all elections and update their statuses
        $allElections = Election::all();
        foreach ($allElections as $election) {
            $calculatedStatus = $this->calculateStatus($election);
            if ($election->status !== $calculatedStatus && $election->status !== 'cancelled') {
                $election->update(['status' => $calculatedStatus]);
            } elseif (empty($election->status) || is_null($election->status)) {
                $election->update(['status' => $calculatedStatus]);
            }
        }
        
        // Get upcoming and ongoing elections
        $elections = Election::whereIn('status', ['upcoming', 'ongoing'])
            ->with(['organization'])
            ->orderByRaw("CASE WHEN status = 'ongoing' THEN 0 ELSE 1 END")
            ->orderBy('election_date', 'asc')
            ->orderBy('timestarted', 'asc')
            ->get();
        
        // For each election, calculate datetime info and load candidates
        foreach ($elections as $election) {
            // Calculate start and end datetime for countdown
            $dateString = $election->election_date instanceof \Carbon\Carbon 
                ? $election->election_date->format('Y-m-d') 
                : (string)$election->election_date;
            
            // Start datetime
            if (!empty($election->timestarted)) {
                $timeStr = trim($election->timestarted);
                $timeParts = explode(':', $timeStr);
                if (count($timeParts) == 2) {
                    $timeStr = $timeParts[0] . ':' . $timeParts[1] . ':00';
                }
                try {
                    $election->start_datetime = Carbon::createFromFormat('Y-m-d H:i:s', $dateString . ' ' . $timeStr, 'Asia/Manila');
                } catch (\Exception $e) {
                    $election->start_datetime = Carbon::parse($dateString, 'Asia/Manila')->startOfDay();
                }
            } else {
                $election->start_datetime = Carbon::parse($dateString, 'Asia/Manila')->startOfDay();
            }
            
            // End datetime
            if (!empty($election->time_ended)) {
                $endTimeStr = trim($election->time_ended);
                $endTimeParts = explode(':', $endTimeStr);
                if (count($endTimeParts) == 2) {
                    $endTimeStr = $endTimeParts[0] . ':' . $endTimeParts[1] . ':00';
                }
                try {
                    $election->end_datetime = Carbon::createFromFormat('Y-m-d H:i:s', $dateString . ' ' . $endTimeStr, 'Asia/Manila');
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
            
            // Group candidates by position
            $election->candidatesByPosition = $candidates->groupBy('position_id');
        }
        
        return view('student.dashboard', compact('elections'));
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
        
        $candidatesByPosition = $candidates->groupBy('position_id');
        
        // Get user's existing votes for this election
        $userVotes = Vote::where('election_id', $election->id)
            ->where('voter_id', Auth::id())
            ->pluck('candidate_id')
            ->toArray();
        
        // Check if user has already voted (has votes for this election)
        $hasVoted = count($userVotes) > 0;
        
        // Calculate end datetime for countdown
        $dateString = $election->election_date instanceof \Carbon\Carbon 
            ? $election->election_date->format('Y-m-d') 
            : (string)$election->election_date;
        
        $endDateTime = null;
        if (!empty($election->time_ended)) {
            $endTimeStr = trim($election->time_ended);
            $endTimeParts = explode(':', $endTimeStr);
            if (count($endTimeParts) == 2) {
                $endTimeStr = $endTimeParts[0] . ':' . $endTimeParts[1] . ':00';
            }
            try {
                $endDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $dateString . ' ' . $endTimeStr, 'Asia/Manila');
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
                'message' => 'This election is not currently active for voting.'
            ], 400);
        }
        
        $request->validate([
            'votes' => 'required|array',
            'votes.*' => 'exists:candidates,id'
        ]);
        
        $userId = Auth::id();
        $votes = $request->input('votes', []);
        
        // Get all candidates being voted for
        $candidatesToVote = Candidate::whereIn('id', $votes)
            ->where('election_id', $electionId)
            ->get();
        
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
            
            if (!$existingVote) {
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
            'message' => 'Your votes have been submitted successfully!'
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
            
            // Get date string
            $dateString = $election->election_date instanceof \Carbon\Carbon 
                ? $election->election_date->format('Y-m-d') 
                : (string)$election->election_date;
            
            $electionDate = Carbon::parse($dateString, 'Asia/Manila');
            
            // If we have a start time, use it for more precise calculation
            if (!empty($election->timestarted)) {
                try {
                    $timeStr = trim($election->timestarted);
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
            if (!empty($election->time_ended)) {
                try {
                    $endTimeStr = trim($election->time_ended);
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
                    
                    // Use the same election date for end time
                    $endDateTime = Carbon::createFromFormat(
                        'Y-m-d H:i:s',
                        $dateString . ' ' . $endTimeStr,
                        'Asia/Manila'
                    );
                    
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
            return 'upcoming'; // Default to upcoming on error
        }
    }
}
