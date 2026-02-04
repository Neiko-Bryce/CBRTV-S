<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Election;
use App\Models\Vote;
use Illuminate\Http\Request;

class LiveResultsViewController extends Controller
{
    /**
     * List elections for controlling live results visibility on the landing page.
     */
    public function index()
    {
        $elections = Election::with('organization')
            ->whereIn('status', ['upcoming', 'ongoing', 'completed'])
            ->orderByRaw("CASE WHEN status = 'ongoing' THEN 0 WHEN status = 'upcoming' THEN 1 ELSE 2 END")
            ->orderBy('election_date', 'desc')
            ->orderBy('timestarted', 'desc')
            ->paginate(15);

        return view('admin.live-results-viewing.index', compact('elections'));
    }

    /**
     * Turn on live results display for this election on the landing page.
     */
    public function display(Request $request, $electionId)
    {
        $election = Election::findOrFail($electionId);
        $election->update(['show_live_results' => true]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Results are now displayed on the landing page.']);
        }

        return redirect()->route('admin.live-results-viewing.index')
            ->with('success', 'Live results for "'.$election->election_name.'" are now displayed on the landing page.');
    }

    /**
     * Turn off live results display for this election on the landing page.
     */
    public function hide(Request $request, $electionId)
    {
        $election = Election::findOrFail($electionId);
        $election->update(['show_live_results' => false]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Results are no longer displayed on the landing page.']);
        }

        return redirect()->route('admin.live-results-viewing.index')
            ->with('success', 'Live results for "'.$election->election_name.'" are no longer displayed on the landing page.');
    }

    /**
     * Get live results for one election (positions, candidates, vote counts) for admin preview.
     * Does not require show_live_results; admin can view any ongoing/completed election.
     */
    public function getElectionResults($electionId)
    {
        $election = Election::with('organization')
            ->whereIn('status', ['upcoming', 'ongoing', 'completed'])
            ->find($electionId);

        if (! $election) {
            return response()->json(['success' => false, 'message' => 'Election not found.'], 404);
        }

        // Use live vote count from votes table (alias to avoid conflict with candidates.votes_count column)
        $candidatesByPosition = Candidate::where('election_id', $election->id)
            ->with('position')
            ->withCount(['votes as live_votes_count'])
            ->get()
            ->groupBy('position_id');

        $positionsData = [];
        foreach ($candidatesByPosition as $positionId => $candidates) {
            $position = $candidates->first()->position;
            if (! $position) {
                continue;
            }

            $list = $candidates->sortByDesc('live_votes_count')->values();
            $totalVotes = $list->sum('live_votes_count');

            $candidatesData = [];
            foreach ($list as $c) {
                $voteCount = (int) ($c->live_votes_count ?? 0);
                $photoUrl = $c->photo
                    ? route('candidates.photo.public', ['path' => $c->photo])
                    : null;
                $candidatesData[] = [
                    'id' => $c->id,
                    'name' => $c->candidate_name ?? 'Unknown',
                    'votes_count' => $voteCount,
                    'photo_url' => $photoUrl,
                ];
            }

            $positionsData[] = [
                'position_id' => $positionId,
                'position_name' => $position->name,
                'candidates' => $candidatesData,
                'total_votes' => $totalVotes,
            ];
        }

        $totalVoters = Vote::where('election_id', $election->id)->distinct('voter_id')->count();

        return response()->json([
            'success' => true,
            'election' => [
                'id' => $election->id,
                'election_name' => $election->election_name,
                'organization' => $election->organization ? $election->organization->name : null,
                'status' => $election->status,
                'positions' => $positionsData,
                'total_voters' => $totalVoters,
            ],
        ]);
    }

}
