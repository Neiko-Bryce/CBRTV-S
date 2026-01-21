<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Election;
use App\Models\Organization;
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
        $query = Candidate::query()->with(['election.organization', 'position', 'partylist', 'student']);
        
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
        $organizations = Organization::where('is_active', true)->orderBy('name', 'asc')->get();
        
        return view('admin.candidates.index', compact('candidates', 'elections', 'organizations', 'electionId'));
    }

    /**
     * Get positions for a specific organization.
     */
    public function getPositionsByOrganization($organizationId)
    {
        $organization = Organization::with('positions')->findOrFail($organizationId);
        $positions = $organization->positions;
        
        return response()->json(['positions' => $positions]);
    }

    /**
     * Get elections for a specific organization.
     */
    public function getElectionsByOrganization($organizationId)
    {
        $elections = Election::where('organization_id', $organizationId)
            ->orderBy('election_name', 'asc')
            ->get();
        
        return response()->json(['elections' => $elections]);
    }

    /**
     * Get positions for a specific organization (via election) - kept for backward compatibility.
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
        try {
            // Convert to integer to ensure proper matching
            $electionId = (int) $electionId;
            
            // Verify election exists
            $election = Election::find($electionId);
            if (!$election) {
                \Log::warning('Election not found: ' . $electionId);
                return response()->json([
                    'partylists' => [],
                    'error' => 'Election not found'
                ]);
            }
            
            // Get all partylists for this election
            $partylists = Partylist::where('election_id', $electionId)
                ->orderBy('name', 'asc')
                ->get();
            
            // Debug: Get all partylists to see what election_ids they have
            $allPartylists = Partylist::select('id', 'name', 'election_id')
                ->orderBy('election_id')
                ->orderBy('name')
                ->get();
            
            \Log::info("Fetching partylists for election ID: {$electionId}, Election Name: {$election->election_name}, Found: {$partylists->count()} partylists");
            \Log::info("All partylists in database: " . json_encode($allPartylists->toArray()));
            
            // Return as simple array format with debug info
            return response()->json([
                'partylists' => $partylists->map(function($partylist) {
                    return [
                        'id' => $partylist->id,
                        'name' => $partylist->name,
                        'election_id' => $partylist->election_id,
                    ];
                })->toArray(),
                'election_id' => $electionId,
                'election_name' => $election->election_name,
                'count' => $partylists->count(),
                'debug' => [
                    'all_partylists' => $allPartylists->map(function($pl) {
                        return [
                            'id' => $pl->id,
                            'name' => $pl->name,
                            'election_id' => $pl->election_id
                        ];
                    })->toArray()
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching partylists: ' . $e->getMessage());
            return response()->json([
                'partylists' => [],
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created candidate in storage.
     */
    public function store(Request $request)
    {
        // Convert empty partylist_id to null before validation (for Independent candidates)
        $request->merge([
            'partylist_id' => $request->partylist_id ?: null,
        ]);

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
            'partylist_platform' => 'nullable|string',
            'candidates' => 'required|array|min:1',
            'candidates.*.position_id' => 'required|exists:positions,id',
            'candidates.*.student_id' => 'nullable|exists:students,id',
            'candidates.*.candidate_name' => 'required|string|max:255',
            'candidates.*.photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'candidates.*.biography' => 'nullable|string',
        ]);

        $electionId = $validated['election_id'];
        $partylistId = $validated['partylist_id'];
        $sharedPlatform = $validated['partylist_platform'] ?? null;

        foreach ($validated['candidates'] as $index => $candidateData) {
            $candidateData['election_id'] = $electionId;
            $candidateData['partylist_id'] = $partylistId;
            // Apply shared platform to all candidates
            $candidateData['platform'] = $sharedPlatform;

            // Handle photo upload - check if file exists in request
            if ($request->hasFile("candidates.{$index}.photo")) {
                $photo = $request->file("candidates.{$index}.photo");
                if ($photo->isValid()) {
                    $photoPath = $photo->store('candidates', 'public');
                    $candidateData['photo'] = $photoPath;
                } else {
                    unset($candidateData['photo']);
                }
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
        
        // Convert empty partylist_id to null before validation (for Independent candidates)
        $request->merge([
            'partylist_id' => $request->partylist_id ?: null,
        ]);

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

    /**
     * Serve candidate photo.
     */
    public function getPhoto($path)
    {
        // Handle path - it might already include 'candidates/' or just be the filename
        $fullPath = str_starts_with($path, 'candidates/') ? $path : 'candidates/' . $path;
        
        if (!Storage::disk('public')->exists($fullPath)) {
            abort(404);
        }
        
        $file = Storage::disk('public')->get($fullPath);
        $mimeType = Storage::disk('public')->mimeType($fullPath);
        
        return response($file, 200)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'inline')
            ->header('Cache-Control', 'public, max-age=31536000');
    }
}
