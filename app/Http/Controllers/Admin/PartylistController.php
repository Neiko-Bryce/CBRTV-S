<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Election;
use App\Models\Partylist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PartylistController extends Controller
{
    /**
     * Display a listing of the partylists for a specific election.
     */
    public function index(Request $request, $electionId = null)
    {
        $query = Partylist::query()->with('election');

        if ($request->has('election') && $request->election) {
            $query->where('election_id', $request->election);
        } elseif ($electionId) {
            $query->where('election_id', $electionId);
        }

        // Handle search functionality
        if ($request->has('search') && ! empty($request->search)) {
            $searchTerm = trim($request->search);
            $isPostgres = DB::connection()->getDriverName() === 'pgsql';
            $likeOperator = $isPostgres ? 'ILIKE' : 'LIKE';

            $query->where(function ($q) use ($searchTerm, $likeOperator) {
                $q->where('name', $likeOperator, "%{$searchTerm}%")
                    ->orWhere('code', $likeOperator, "%{$searchTerm}%")
                    ->orWhere('description', $likeOperator, "%{$searchTerm}%");
            });
        }

        $partylists = $query->orderBy('name', 'asc')->paginate(15)->withQueryString();
        $elections = Election::orderBy('election_name', 'asc')->get();

        return view('admin.partylists.index', compact('partylists', 'elections', 'electionId'));
    }

    /**
     * Store a newly created partylist in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'election_id' => 'required|exists:elections,id',
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'logo' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        Partylist::create($validated);

        return redirect()->route('admin.partylists.index', ['election' => $request->election_id])
            ->with('success', 'Partylist created successfully.');
    }

    /**
     * Show the specified partylist.
     */
    public function show($id)
    {
        $partylist = Partylist::with('election')->findOrFail($id);

        return response()->json($partylist);
    }

    /**
     * Update the specified partylist in storage.
     */
    public function update(Request $request, $id)
    {
        $partylist = Partylist::findOrFail($id);

        $validated = $request->validate([
            'election_id' => 'required|exists:elections,id',
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'logo' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $partylist->update($validated);

        return redirect()->route('admin.partylists.index', ['election' => $request->election_id])
            ->with('success', 'Partylist updated successfully.');
    }

    /**
     * Remove the specified partylist from storage.
     */
    public function destroy($id)
    {
        $partylist = Partylist::findOrFail($id);

        // Check if partylist has candidates
        if ($partylist->candidates()->count() > 0) {
            return redirect()->route('admin.partylists.index')
                ->with('error', 'Cannot delete partylist with existing candidates.');
        }

        $partylist->delete();

        return redirect()->route('admin.partylists.index')
            ->with('success', 'Partylist deleted successfully.');
    }
}
