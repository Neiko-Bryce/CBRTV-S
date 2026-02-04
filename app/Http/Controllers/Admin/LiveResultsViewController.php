<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Election;
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
}
