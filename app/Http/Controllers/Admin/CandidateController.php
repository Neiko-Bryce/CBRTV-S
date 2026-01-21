<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Election;
use App\Models\Position;
use App\Models\Partylist;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CandidateController extends Controller
{
    /**
     * Display a listing of the candidates for a specific election.
     */
    public function index(Request $request, $electionId = null)
    {
        $query = Candidate::query()->with(['election', 'position', 'partylist', 'student']);
        
        if ($request->has('election') && $request->election) {
            $query->where('election_id', $request->election);
        } elseif ($electionId) {
            $query->where('election_id', $electionId);
        }
        
        // Handle search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = trim($request->search);
            $isPostgres = DB::connection()->getDriverName() === 'pgsql';
            $likeOperator = $isPostgres ? 'ILIKE' : 'LIKE';
            
            $query->where(function($q) use ($searchTerm, $likeOperator) {
                $q->where('candidate_name', $likeOperator, "%{$searchTerm}%")
                  ->orWhere('biography', $likeOperator, "%{$searchTerm}%")
                  ->orWhere('platform', $likeOperator, "%{$searchTerm}%");
            });
        }
        
        // Filter by position
        if ($request->has('position_id') && $request->position_id) {
            $query->where('position_id', $request->position_id);
        }
        
        // Filter by partylist
        if ($request->has('partylist_id') && $request->partylist_id) {
            $query->where('partylist_id', $request->partylist_id);
        }
        
        $candidates = $query->orderBy('position_id', 'asc')->orderBy('candidate_name', 'asc')->paginate(15)->withQueryString();
        $elections = Election::orderBy('election_name', 'asc')->get();
        
        return view('admin.candidates.index', compact('candidates', 'elections', 'electionId'));
    }

    /**
     * Get positions for a specific organization (via election).
     */
    public function getPositions($electionId)
    {
        $election = Election::with('organization.positions')->findOrFail($electionId);
        
        if (!$election->organization) {
            return response()->json(['positions' => []]);
        }
        
        $positions = $election->organization->positions;
        
        return response()->json(['positions' => $positions]);
    }

    /**
     * Get partylists for a specific election.
     */
    public function getPartylists($electionId)
    {
        $partylists = Partylist::where('election_id', $electionId)
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();
        
        return response()->json(['partylists' => $partylists]);
    }

    /**
     * Store a newly created candidate in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'election_id' => 'required|exists:elections,id',
            'position_id' => 'required|exists:positions,id',
            'partylist_id' => 'nullable|exists:partylists,id',
            'student_id' => 'nullable|exists:students,id',
            'candidate_name' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'biography' => 'nullable|string',
            'platform' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('candidates', 'public');
            $validated['photo'] = $photoPath;
        }

        Candidate::create($validated);

        return redirect()->route('admin.candidates.index', ['election' => $request->election_id])
            ->with('success', 'Candidate created successfully.');
    }

    /**
     * Store multiple candidates (for partylist).
     */
    public function storeMultiple(Request $request)
    {
        $validated = $request->validate([
            'election_id' => 'required|exists:elections,id',
            'partylist_id' => 'required|exists:partylists,id',
            'candidates' => 'required|array|min:1',
            'candidates.*.position_id' => 'required|exists:positions,id',
            'candidates.*.student_id' => 'nullable|exists:students,id',
            'candidates.*.candidate_name' => 'required|string|max:255',
            'candidates.*.photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'candidates.*.biography' => 'nullable|string',
            'candidates.*.platform' => 'nullable|string',
        ]);

        $electionId = $validated['election_id'];
        $partylistId = $validated['partylist_id'];

        foreach ($validated['candidates'] as $candidateData) {
            $candidateData['election_id'] = $electionId;
            $candidateData['partylist_id'] = $partylistId;

            // Handle photo upload
            if (isset($candidateData['photo']) && $candidateData['photo']->isValid()) {
                $photoPath = $candidateData['photo']->store('candidates', 'public');
                $candidateData['photo'] = $photoPath;
            } else {
                unset($candidateData['photo']);
            }

            Candidate::create($candidateData);
        }

        return redirect()->route('admin.candidates.index', ['election' => $electionId])
            ->with('success', 'Candidates created successfully.');
    }

    /**
     * Show the specified candidate.
     */
    public function show($id)
    {
        $candidate = Candidate::with(['election', 'position', 'partylist', 'student'])->findOrFail($id);
        return response()->json($candidate);
    }

    /**
     * Update the specified candidate in storage.
     */
    public function update(Request $request, $id)
    {
        $candidate = Candidate::findOrFail($id);
        
        $validated = $request->validate([
            'election_id' => 'required|exists:elections,id',
            'position_id' => 'required|exists:positions,id',
            'partylist_id' => 'nullable|exists:partylists,id',
            'student_id' => 'nullable|exists:students,id',
            'candidate_name' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'biography' => 'nullable|string',
            'platform' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($candidate->photo) {
                Storage::disk('public')->delete($candidate->photo);
            }
            $photoPath = $request->file('photo')->store('candidates', 'public');
            $validated['photo'] = $photoPath;
        }

        $candidate->update($validated);

        return redirect()->route('admin.candidates.index', ['election' => $request->election_id])
            ->with('success', 'Candidate updated successfully.');
    }

    /**
     * Remove the specified candidate from storage.
     */
    public function destroy($id)
    {
        $candidate = Candidate::findOrFail($id);
        
        // Delete photo if exists
        if ($candidate->photo) {
            Storage::disk('public')->delete($candidate->photo);
        }
        
        $candidate->delete();

        return redirect()->route('admin.candidates.index')
            ->with('success', 'Candidate deleted successfully.');
    }
}
