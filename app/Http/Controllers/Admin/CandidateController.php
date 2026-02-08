<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Election;
use App\Models\Organization;
use App\Models\Partylist;
use App\Models\Position;
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
        if ($request->has('search') && ! empty($request->search)) {
            $searchTerm = trim($request->search);
            $isPostgres = DB::connection()->getDriverName() === 'pgsql';
            $likeOperator = $isPostgres ? 'ILIKE' : 'LIKE';

            $query->where(function ($q) use ($searchTerm, $likeOperator) {
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
     * Only returns upcoming or ongoing elections (not completed) for adding new candidates.
     * When editing, we need to include the candidate's current election even if completed.
     */
    public function getElectionsByOrganization($organizationId, Request $request)
    {
        $query = Election::where('organization_id', $organizationId);

        // If editing a candidate, include their current election even if completed
        if ($request->has('include_election_id') && $request->include_election_id) {
            $query->where(function ($q) use ($request) {
                $q->whereIn('status', ['upcoming', 'ongoing'])
                    ->orWhere('id', $request->include_election_id);
            });
        } else {
            // For new candidates, only show upcoming/ongoing
            $query->whereIn('status', ['upcoming', 'ongoing']);
        }

        $elections = $query->orderBy('election_name', 'asc')->get();

        return response()->json(['elections' => $elections]);
    }

    /**
     * Get positions for a specific organization (via election) - kept for backward compatibility.
     */
    public function getPositions($electionId)
    {
        $election = Election::with('organization.positions')->findOrFail($electionId);

        if (! $election->organization) {
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
            if (! $election) {
                \Log::warning('Election not found: '.$electionId);

                return response()->json([
                    'partylists' => [],
                    'error' => 'Election not found',
                ]);
            }

            // Get all active partylists for this election
            $partylists = Partylist::where('election_id', $electionId)
                ->where('is_active', true)
                ->orderBy('name', 'asc')
                ->get();

            \Log::info("Fetching active partylists for election ID: {$electionId}, Election Name: {$election->election_name}, Found: {$partylists->count()} active partylists");

            // Return as simple array format
            return response()->json([
                'partylists' => $partylists->map(function ($partylist) {
                    return [
                        'id' => $partylist->id,
                        'name' => $partylist->name,
                        'election_id' => $partylist->election_id,
                    ];
                })->toArray(),
                'election_id' => $electionId,
                'election_name' => $election->election_name,
                'count' => $partylists->count(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching partylists: '.$e->getMessage());

            return response()->json([
                'partylists' => [],
                'error' => $e->getMessage(),
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
            $photoPath = $request->file('photo')->store('candidates', $this->photoDisk());
            $validated['photo'] = $photoPath;
        }

        // Check Party Nomination Limit
        if ($validated['partylist_id']) {
            $position = Position::findOrFail($validated['position_id']);
            $existingCount = Candidate::where('election_id', $validated['election_id'])
                ->where('position_id', $validated['position_id'])
                ->where('partylist_id', $validated['partylist_id'])
                ->count();

            if ($existingCount >= $position->number_of_slots) {
                return back()->withInput()->with('error', "Party limit reached! This position only allows {$position->number_of_slots} candidates per party.");
            }
        }

        Candidate::create($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Candidate created successfully.']);
        }

        return redirect()->route('admin.candidates.index', ['election' => $request->election_id])
            ->with('success', 'Candidate created successfully.');
    }

    /**
     * Store multiple candidates (for partylist).
     */
    public function storeMultiple(Request $request)
    {
        // Filter out candidates with empty names (skipped slots)
        if ($request->has('candidates') && is_array($request->candidates)) {
            $filteredCandidates = collect($request->candidates)
                ->filter(function ($candidate) {
                    return !empty($candidate['candidate_name']);
                })
                ->values()
                ->toArray();
            
            $request->merge(['candidates' => $filteredCandidates]);
        }
        
        // Custom validation for checking duplicate/limit before proceeding? 
        // We do it manually below anyway.

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

        // Group candidates by position and validate limits
        $candidatesByPosition = collect($validated['candidates'])->groupBy('position_id');
        
        foreach ($candidatesByPosition as $positionId => $candidates) {
            $position = Position::findOrFail($positionId);
            $existingCount = Candidate::where('election_id', $electionId)
                ->where('position_id', $positionId)
                ->where('partylist_id', $partylistId)
                ->count();
            
            $newCount = count($candidates);
            
            if (($existingCount + $newCount) > $position->number_of_slots) {
                $errorMsg = "Party limit reached for {$position->name}! This position only allows {$position->number_of_slots} candidates per party. You already have {$existingCount} and are trying to add {$newCount} more.";
                
                if ($request->wantsJson()) {
                    return response()->json(['message' => $errorMsg, 'errors' => ['candidates' => [$errorMsg]]], 422);
                }
                return back()->withInput()->with('error', $errorMsg);
            }
        }

        foreach ($validated['candidates'] as $index => $candidateData) {
            $candidateData['election_id'] = $electionId;
            $candidateData['partylist_id'] = $partylistId;
            // Apply shared platform to all candidates
            if ($sharedPlatform) {
                $candidateData['platform'] = $sharedPlatform;
            }

            // Handle photo upload - check if file exists in request
            if ($request->hasFile("candidates.{$index}.photo")) {
                $photo = $request->file("candidates.{$index}.photo");
                if ($photo->isValid()) {
                    $photoPath = $photo->store('candidates', $this->photoDisk());
                    $candidateData['photo'] = $photoPath;
                } else {
                    unset($candidateData['photo']);
                }
            } else {
                unset($candidateData['photo']);
            }

            Candidate::create($candidateData);
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Candidates created successfully.']);
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
                Storage::disk($this->photoDisk())->delete($candidate->photo);
            }
            $photoPath = $request->file('photo')->store('candidates', $this->photoDisk());
            $validated['photo'] = $photoPath;
        }

        $candidate->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Candidate updated successfully.']);
        }

        return redirect()->route('admin.candidates.index', ['election' => $request->election_id])
            ->with('success', 'Candidate updated successfully.');
    }

    /**
     * Remove the specified candidate from storage.
     */
    public function destroy(Request $request, $id)
    {
        $candidate = Candidate::findOrFail($id);

        // Delete photo if exists
        if ($candidate->photo) {
            Storage::disk($this->photoDisk())->delete($candidate->photo);
        }

        $candidate->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Candidate deleted successfully.',
            ]);
        }

        return redirect()->route('admin.candidates.index')
            ->with('success', 'Candidate deleted successfully.');
    }

    /**
     * Disk used for candidate photos (public = local, s3 = cloud). Use s3 when deployed without a volume.
     */
    private function photoDisk(): string
    {
        return config('filesystems.candidate_photos_disk', 'public');
    }

    /**
     * Serve candidate photo. Returns a placeholder SVG when file is missing or on any error (e.g. ephemeral storage, DB hiccups).
     */
    public function getPhoto($path)
    {
        try {
            $disk = $this->photoDisk();
            $fullPath = str_starts_with($path, 'candidates/') ? $path : 'candidates/'.$path;

            if (! Storage::disk($disk)->exists($fullPath)) {
                return $this->placeholderPhotoResponse();
            }

            $file = Storage::disk($disk)->get($fullPath);
            $mimeType = Storage::disk($disk)->mimeType($fullPath);

            return response($file, 200)
                ->header('Content-Type', $mimeType)
                ->header('Content-Disposition', 'inline')
                ->header('Cache-Control', 'public, max-age=31536000');
        } catch (\Throwable $e) {
            return $this->placeholderPhotoResponse();
        }
    }

    /**
     * Return a placeholder image (SVG) when the candidate photo is missing (e.g. ephemeral storage on deploy).
     */
    private function placeholderPhotoResponse()
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 24 24" fill="none" stroke="#15803d" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 4-6 8-6s8 2 8 6"/></svg>';
        return response($svg, 200)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Content-Disposition', 'inline')
            ->header('Cache-Control', 'public, max-age=3600');
    }
}
