<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Election;
use App\Models\Partylist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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
        // New partylists: only elections not yet finished (upcoming or in progress).
        $electionsForModal = Election::query()
            ->whereIn('status', ['upcoming', 'ongoing'])
            ->orderBy('election_date', 'asc')
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.partylists.index', compact('partylists', 'elections', 'electionsForModal', 'electionId'));
    }

    /**
     * Store a newly created partylist in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'election_id' => [
                'required',
                Rule::exists('elections', 'id')->where(function ($q) {
                    $q->whereIn('status', ['upcoming', 'ongoing']);
                }),
            ],
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'logo' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        // Inherit organization_id from election
        $election = Election::findOrFail($validated['election_id']);
        $validated['organization_id'] = $election->organization_id;

        $validated['is_active'] = $request->boolean('is_active');

        $partylist = Partylist::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Partylist created successfully.',
                'partylist' => $partylist,
            ]);
        }

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
            'election_id' => [
                'required',
                'exists:elections,id',
                function (string $attribute, mixed $value, \Closure $fail) use ($partylist) {
                    $election = Election::find($value);
                    if (! $election) {
                        return;
                    }
                    if (in_array($election->status, ['upcoming', 'ongoing'], true)) {
                        return;
                    }
                    if ((int) $value === (int) $partylist->election_id) {
                        return;
                    }
                    $fail('Choose an election that is upcoming or in progress, or keep the current election.');
                },
            ],
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'logo' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $partylist->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Partylist updated successfully.',
                'partylist' => $partylist->fresh(),
            ]);
        }

        return redirect()->route('admin.partylists.index', ['election' => $request->election_id])
            ->with('success', 'Partylist updated successfully.');
    }

    /**
     * Remove the specified partylist from storage.
     */
    public function destroy(Request $request, $id)
    {
        $partylist = Partylist::findOrFail($id);

        // Check if partylist has candidates
        if ($partylist->candidates()->count() > 0) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Cannot delete partylist with existing candidates.'], 422);
            }

            return redirect()->route('admin.partylists.index')
                ->with('error', 'Cannot delete partylist with existing candidates.');
        }

        $partylist->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Partylist deleted successfully.']);
        }

        return redirect()->route('admin.partylists.index')
            ->with('success', 'Partylist deleted successfully.');
    }
}
