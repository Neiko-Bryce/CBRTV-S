<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Election;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ElectionController extends Controller
{
    /**
     * Display a listing of the elections.
     */
    public function index(Request $request)
    {
        $query = Election::query();
        
        // Filter by status if provided
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Handle search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = trim($request->search);
            $isPostgres = DB::connection()->getDriverName() === 'pgsql';
            $likeOperator = $isPostgres ? 'ILIKE' : 'LIKE';
            
            $query->where(function($q) use ($searchTerm, $likeOperator) {
                $q->where('election_name', $likeOperator, "%{$searchTerm}%")
                  ->orWhere('type_of_election', $likeOperator, "%{$searchTerm}%")
                  ->orWhere('description', $likeOperator, "%{$searchTerm}%")
                  ->orWhere('venue', $likeOperator, "%{$searchTerm}%")
                  ->orWhere('election_id', $likeOperator, "%{$searchTerm}%");
            });
        }
        
        // Update statuses for ALL elections first (not just current page) - this ensures stats are accurate
        try {
            $allElections = Election::all();
            foreach ($allElections as $election) {
                // Don't auto-update if status is manually set to cancelled
                // BUT always allow update to completed (election ended)
                if ($election->status === 'cancelled') {
                    // Still check if election has ended - completed takes precedence
                    $calculatedStatus = $this->calculateStatus($election->toArray());
                    if ($calculatedStatus === 'completed') {
                        // Election ended, update to completed even if cancelled
                        $election->update(['status' => 'completed']);
                    }
                    continue; // Skip further auto-update for manually set statuses
                }
                
                // Convert any old "rescheduled" status to "upcoming"
                if ($election->status === 'rescheduled') {
                    $election->update(['status' => 'upcoming']);
                }
                
                $calculatedStatus = $this->calculateStatus($election->toArray());
                // Always update if status changed
                if ($election->status !== $calculatedStatus) {
                    $election->update(['status' => $calculatedStatus]);
                } elseif (empty($election->status) || is_null($election->status)) {
                    // Set status if it's null
                    $election->update(['status' => $calculatedStatus]);
                }
            }
        } catch (\Exception $e) {
            // Log error but don't break the page
            \Log::error('Error updating election statuses in index: ' . $e->getMessage());
        }
        
        // Order by ID to keep elections in stable position even after updates
        $elections = $query->orderBy('id', 'asc')->paginate(15)->withQueryString();
        
        // Get statistics for ALL elections (after updating statuses)
        // Use DB::table for direct query to avoid any model caching issues
        $stats = [
            'total' => \DB::table('elections')->count(),
            'upcoming' => \DB::table('elections')->where('status', 'upcoming')->count(),
            'ongoing' => \DB::table('elections')->where('status', 'ongoing')->count(),
            'completed' => \DB::table('elections')->where('status', 'completed')->count(),
            'cancelled' => \DB::table('elections')->where('status', 'cancelled')->count(),
        ];
        
        $organizations = \App\Models\Organization::where('is_active', true)->orderBy('name', 'asc')->get();
        
        return view('admin.elections.index', compact('elections', 'stats', 'organizations'));
    }

    /**
     * Show the form for creating a new election.
     */
    public function create()
    {
        return view('admin.elections.create');
    }

    /**
     * Store a newly created election in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'election_id' => 'nullable|string|max:255|unique:elections',
                'election_name' => 'required|string|max:255',
                'organization_id' => 'required|exists:organizations,id',
                'type_of_election' => 'nullable|string|max:255', // Keep for backward compatibility
                'description' => 'nullable|string',
                'venue' => 'nullable|string|max:255',
                'election_date' => 'required|date|after_or_equal:today',
                'timestarted' => 'nullable|date_format:H:i',
                'time_ended' => 'nullable|date_format:H:i',
                'status' => 'nullable|in:upcoming,ongoing,completed,cancelled',
            ]);
            
            // Set type_of_election from organization name if not provided
            if (empty($validated['type_of_election']) && !empty($validated['organization_id'])) {
                $organization = \App\Models\Organization::find($validated['organization_id']);
                if ($organization) {
                    $validated['type_of_election'] = $organization->name;
                }
            }

            // Strong validation: Cannot create election in the past
            $nowPH = Carbon::now('Asia/Manila');
            $electionDate = Carbon::parse($validated['election_date'], 'Asia/Manila');
            
            // If election date is today, check time
            if ($electionDate->isToday() && $validated['timestarted']) {
                $electionDateTime = Carbon::createFromFormat(
                    'Y-m-d H:i:s',
                    $validated['election_date'] . ' ' . $validated['timestarted'] . ':00',
                    'Asia/Manila'
                );
                if ($electionDateTime->isPast()) {
                    if ($request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Cannot create an election with a past date and time. Please select a future date and time.',
                            'errors' => ['election_date' => ['The election date and time must be in the future (Philippine Time).']]
                        ], 422);
                    }
                    return back()->withErrors(['election_date' => 'The election date and time must be in the future (Philippine Time).'])->withInput();
                }
            } elseif ($electionDate->isPast()) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot create an election with a past date. Please select today or a future date.',
                        'errors' => ['election_date' => ['The election date must be today or in the future (Philippine Time).']]
                    ], 422);
                }
                return back()->withErrors(['election_date' => 'The election date must be today or in the future (Philippine Time).'])->withInput();
            }

            // Validate time_ended is after timestarted if both are provided
            if ($validated['timestarted'] && $validated['time_ended']) {
                if (strtotime($validated['time_ended']) <= strtotime($validated['timestarted'])) {
                    if ($request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Time ended must be after time started.',
                            'errors' => ['time_ended' => ['Time ended must be after time started.']]
                        ], 422);
                    }
                    return back()->withErrors(['time_ended' => 'Time ended must be after time started.'])->withInput();
                }
            }

            // Auto-generate election_id if not provided
            if (empty($validated['election_id'])) {
                $validated['election_id'] = 'ELEC-' . time() . '-' . rand(1000, 9999);
            }

            // Calculate initial status if not manually set
            if (empty($validated['status']) || !isset($validated['status'])) {
                $validated['status'] = $this->calculateStatus($validated);
            }

            $election = Election::create($validated);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Election created successfully.',
                    'election' => $election
                ]);
            }

            return redirect()->route('admin.elections.index')
                ->with('success', 'Election created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Error creating election: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while creating the election: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->withErrors(['error' => 'An error occurred while creating the election.'])->withInput();
        }
    }

    /**
     * Display the specified election.
     */
    public function show($id)
    {
        $election = Election::findOrFail($id);
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'election' => $election
            ]);
        }

        return view('admin.elections.show', compact('election'));
    }

    /**
     * Show the form for editing the specified election.
     */
    public function edit($id)
    {
        $election = Election::with('organization')->findOrFail($id);
        
        if (request()->ajax()) {
            // Format date properly for JavaScript
            $electionData = $election->toArray();
            if ($election->election_date instanceof \Carbon\Carbon) {
                $electionData['election_date'] = $election->election_date->format('Y-m-d');
            }
            
            // Include organization_id if available
            if ($election->organization) {
                $electionData['organization_id'] = $election->organization->id;
            }
            
            return response()->json([
                'success' => true,
                'election' => $electionData
            ]);
        }

        return view('admin.elections.edit', compact('election'));
    }

    /**
     * Update the specified election in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $election = Election::findOrFail($id);
            
            $validated = $request->validate([
                'election_name' => 'required|string|max:255',
                'type_of_election' => 'required|string|max:255',
                'description' => 'nullable|string',
                'venue' => 'nullable|string|max:255',
                'election_date' => 'required|date',
                'timestarted' => 'nullable|date_format:H:i',
                'time_ended' => 'nullable|date_format:H:i',
                'status' => 'nullable|in:upcoming,ongoing,completed,cancelled',
            ]);

            // Allow editing of date/time freely when updating
            // Only validate that time_ended is after timestarted if both are provided
            // No restriction on past dates when editing - admin can update to any date/time

            // Validate time_ended is after timestarted if both are provided
            if ($validated['timestarted'] && $validated['time_ended']) {
                if (strtotime($validated['time_ended']) <= strtotime($validated['timestarted'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Time ended must be after time started.',
                        'errors' => ['time_ended' => ['Time ended must be after time started.']]
                    ], 422);
                }
            }

            // Don't update election_id - it's auto-generated and should not change
            // Remove election_id from validated data if present
            unset($validated['election_id']);
            
            // Handle status: if manually set to cancelled, always preserve it
            // Otherwise, if date/time changed, set to upcoming; if not set, calculate it
            $oldElection = $election->toArray();
            $dateChanged = $oldElection['election_date'] != $validated['election_date'] || 
                          $oldElection['timestarted'] != ($validated['timestarted'] ?? null) ||
                          $oldElection['time_ended'] != ($validated['time_ended'] ?? null);
            
            // If status is explicitly set to cancelled, always preserve it (don't override)
            if (isset($validated['status']) && $validated['status'] === 'cancelled') {
                // Keep cancelled status - don't change it
                $validated['status'] = 'cancelled';
            } elseif ($dateChanged && (!isset($validated['status']) || empty($validated['status']))) {
                // If date/time changed and status is not set, set to upcoming
                $validated['status'] = 'upcoming';
            } elseif (empty($validated['status']) || !isset($validated['status'])) {
                // If status is not manually set and date didn't change, calculate it
                $validated['status'] = $this->calculateStatus($validated);
            }
            // If status is manually set to something else (not cancelled), keep it

            $election->update($validated);

            if ($request->ajax()) {
                // Return fresh election data with all fields including status
                $freshElection = $election->fresh();
                return response()->json([
                    'success' => true,
                    'message' => 'Election updated successfully.',
                    'election' => [
                        'id' => $freshElection->id,
                        'election_id' => $freshElection->election_id,
                        'election_name' => $freshElection->election_name,
                        'type_of_election' => $freshElection->type_of_election,
                        'description' => $freshElection->description,
                        'venue' => $freshElection->venue,
                        'election_date' => $freshElection->election_date ? $freshElection->election_date->format('Y-m-d') : null,
                        'timestarted' => $freshElection->timestarted,
                        'time_ended' => $freshElection->time_ended,
                        'status' => $freshElection->status,
                    ]
                ]);
            }

            return redirect()->route('admin.elections.index')
                ->with('success', 'Election updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Error updating election: ' . $e->getMessage(), [
                'exception' => $e,
                'election_id' => $id,
                'request_data' => $request->all()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating the election: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->withErrors(['error' => 'An error occurred while updating the election.'])->withInput();
        }
    }

    /**
     * Remove the specified election from storage.
     */
    public function destroy($id, Request $request)
    {
        $election = Election::findOrFail($id);
        $election->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Election deleted successfully.'
            ]);
        }

        return redirect()->route('admin.elections.index')
            ->with('success', 'Election deleted successfully.');
    }

    /**
     * Calculate election status based on date and time
     */
    private function calculateStatus($electionData)
    {
        try {
            $now = Carbon::now('Asia/Manila');
            
            if (empty($electionData['election_date'])) {
                return 'upcoming';
            }
            
            // Get date string - handle both Carbon instance and string
            if ($electionData['election_date'] instanceof \Carbon\Carbon) {
                $dateString = $electionData['election_date']->format('Y-m-d');
            } else {
                $dateString = is_string($electionData['election_date']) 
                    ? $electionData['election_date'] 
                    : (string)$electionData['election_date'];
            }
            
            $electionDate = Carbon::parse($dateString, 'Asia/Manila');
            
            // If we have a start time, use it for more precise calculation
            if (!empty($electionData['timestarted'])) {
                try {
                    $timeStr = trim($electionData['timestarted']);
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
            if (!empty($electionData['time_ended'])) {
                try {
                    $endTimeStr = trim($electionData['time_ended']);
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
                    
                    $endDateTime = Carbon::createFromFormat(
                        'Y-m-d H:i:s',
                        $dateString . ' ' . $endTimeStr,
                        'Asia/Manila'
                    );
                    
                    // If current time is past the end time, election is completed
                    if ($now->greaterThanOrEqualTo($endDateTime)) {
                        \Log::info("calculateStatus: Election ended - Now: {$now->toDateTimeString()}, End: {$endDateTime->toDateTimeString()}");
                        return 'completed';
                    }
                } catch (\Exception $e) {
                    \Log::error('Error parsing end time in calculateStatus: ' . $e->getMessage());
                    // If end time parsing fails, continue with start time check
                }
            }
            
            // Check if election has started (only if not ended)
            if ($now->greaterThanOrEqualTo($electionDateTime)) {
                \Log::info("calculateStatus: Election ongoing - Now: {$now->toDateTimeString()}, Start: {$electionDateTime->toDateTimeString()}");
                return 'ongoing';
            }
            
            // Election hasn't started yet
            return 'upcoming';
        } catch (\Exception $e) {
            \Log::error('Error calculating election status: ' . $e->getMessage());
            return 'upcoming'; // Default to upcoming on error
        }
    }

    /**
     * Update election status (called via AJAX for real-time updates)
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $election = Election::findOrFail($id);
            
            $validated = $request->validate([
                'status' => 'required|in:upcoming,ongoing,completed,cancelled'
            ]);
            
            $election->update(['status' => $validated['status']]);
            
            // Return updated stats along with success message
            // Use DB::table for direct query to avoid any model caching issues
            $stats = [
                'total' => \DB::table('elections')->count(),
                'upcoming' => \DB::table('elections')->where('status', 'upcoming')->count(),
                'ongoing' => \DB::table('elections')->where('status', 'ongoing')->count(),
                'completed' => \DB::table('elections')->where('status', 'completed')->count(),
                'cancelled' => \DB::table('elections')->where('status', 'cancelled')->count(),
            ];
            
            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully.',
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status.'
            ], 500);
        }
    }

    /**
     * Get current election statistics (for real-time stats updates)
     */
    public function getStats()
    {
        // Update statuses for ALL elections first - CRITICAL for accurate stats
        try {
            // Use fresh query to avoid caching - get raw data from database
            $allElections = \DB::table('elections')->get();
            $updatedCount = 0;
            
            foreach ($allElections as $electionRow) {
                // Convert to array for calculateStatus - ensure proper field names
                $electionData = [
                    'id' => $electionRow->id,
                    'election_date' => $electionRow->election_date,
                    'timestarted' => $electionRow->timestarted,
                    'time_ended' => $electionRow->time_ended,
                    'status' => $electionRow->status,
                ];
                
                // Always calculate the correct status first
                $calculatedStatus = $this->calculateStatus($electionData);
                
                $currentStatus = $electionRow->status;
                
                \Log::info("getStats: Election {$electionRow->id} - Current: '{$currentStatus}', Calculated: '{$calculatedStatus}', Date: {$electionRow->election_date}, Start: {$electionRow->timestarted}, End: {$electionRow->time_ended}");
                
                // Don't auto-update if status is manually set to cancelled
                // BUT always allow update to completed (election ended takes precedence)
                if ($currentStatus === 'cancelled' && $calculatedStatus !== 'completed') {
                    continue; // Skip auto-update for manually set statuses (unless completed)
                }
                
                // Convert any old "rescheduled" status to "upcoming"
                if ($currentStatus === 'rescheduled') {
                    \DB::table('elections')
                        ->where('id', $electionRow->id)
                        ->update(['status' => 'upcoming']);
                    $updatedCount++;
                    continue;
                }
                
                // Always update if status changed or is null/empty
                // IMPORTANT: If calculated status is 'completed', always update (election ended)
                if ($calculatedStatus === 'completed' || $currentStatus !== $calculatedStatus || empty($currentStatus) || is_null($currentStatus)) {
                    // Use direct DB update to avoid model events/caching
                    \DB::table('elections')
                        ->where('id', $electionRow->id)
                        ->update(['status' => $calculatedStatus]);
                    $updatedCount++;
                    \Log::info("getStats: Updated election {$electionRow->id} from '{$currentStatus}' to '{$calculatedStatus}'");
                }
            }
            \Log::info("getStats: Updated {$updatedCount} election statuses");
        } catch (\Exception $e) {
            \Log::error('Error updating election statuses in getStats: ' . $e->getMessage());
        }
        
        // Refresh the model cache to ensure we get the latest data
        \DB::getQueryLog();
        
        // Get fresh counts after updating all statuses - use fresh query to ensure accuracy
        // Use DB::table for direct query to avoid any model caching issues
        $stats = [
            'total' => \DB::table('elections')->count(),
            'upcoming' => \DB::table('elections')->where('status', 'upcoming')->count(),
            'ongoing' => \DB::table('elections')->where('status', 'ongoing')->count(),
            'completed' => \DB::table('elections')->where('status', 'completed')->count(),
        ];
        
        \Log::info("getStats: Returning stats - Total: {$stats['total']}, Upcoming: {$stats['upcoming']}, Ongoing: {$stats['ongoing']}, Completed: {$stats['completed']}");
        
        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * Get elections data for real-time table updates
     */
    public function getElectionsData(Request $request)
    {
        try {
            $query = Election::with('organization');
            
            // Apply same filters as index method
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }
            
            if ($request->has('search') && !empty($request->search)) {
                $searchTerm = trim($request->search);
                $isPostgres = DB::connection()->getDriverName() === 'pgsql';
                $likeOperator = $isPostgres ? 'ILIKE' : 'LIKE';
                
                $query->where(function($q) use ($searchTerm, $likeOperator) {
                    $q->where('election_name', $likeOperator, "%{$searchTerm}%")
                      ->orWhere('type_of_election', $likeOperator, "%{$searchTerm}%")
                      ->orWhere('description', $likeOperator, "%{$searchTerm}%")
                      ->orWhere('venue', $likeOperator, "%{$searchTerm}%")
                      ->orWhere('election_id', $likeOperator, "%{$searchTerm}%");
                });
            }
            
            // Update statuses for ALL elections first
            try {
                $allElections = Election::all();
                foreach ($allElections as $election) {
                    if ($election->status === 'cancelled') {
                        $calculatedStatus = $this->calculateStatus($election->toArray());
                        if ($calculatedStatus === 'completed') {
                            $election->update(['status' => 'completed']);
                        }
                        continue;
                    }
                    
                    if ($election->status === 'rescheduled') {
                        $election->update(['status' => 'upcoming']);
                    }
                    
                    $calculatedStatus = $this->calculateStatus($election->toArray());
                    if ($election->status !== $calculatedStatus) {
                        $election->update(['status' => $calculatedStatus]);
                    } elseif (empty($election->status) || is_null($election->status)) {
                        $election->update(['status' => $calculatedStatus]);
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Error updating election statuses in getElectionsData: ' . $e->getMessage());
            }
            
            // Get paginated elections
            $elections = $query->orderBy('id', 'asc')->paginate(15)->withQueryString();
            
            // Format elections data for frontend
            $electionsData = $elections->map(function($election) {
                // Calculate timestamps
                $electionTimestamp = null;
                $endTimestamp = null;
                
                if ($election->election_date) {
                    try {
                        $dateString = $election->election_date->format('Y-m-d');
                        
                        if ($election->timestarted && !empty(trim($election->timestarted))) {
                            $timeStr = trim($election->timestarted);
                            $timeParts = explode(':', $timeStr);
                            if (count($timeParts) == 2) {
                                $timeStr = $timeParts[0] . ':' . $timeParts[1] . ':00';
                            }
                            
                            if ($timeStr) {
                                $datetimeString = $dateString . ' ' . $timeStr;
                                $electionDT = Carbon::createFromFormat('Y-m-d H:i:s', $datetimeString, 'Asia/Manila');
                                $electionTimestamp = $electionDT->timestamp * 1000;
                            }
                        } else {
                            $electionDT = Carbon::createFromFormat('Y-m-d', $dateString, 'Asia/Manila')->startOfDay();
                            $electionTimestamp = $electionDT->timestamp * 1000;
                        }
                        
                        if ($election->time_ended && !empty(trim($election->time_ended)) && $electionTimestamp) {
                            $endTimeStr = trim($election->time_ended);
                            $endTimeParts = explode(':', $endTimeStr);
                            if (count($endTimeParts) == 2) {
                                $endTimeStr = $endTimeParts[0] . ':' . $endTimeParts[1] . ':00';
                            }
                            
                            if ($endTimeStr) {
                                $endDatetimeString = $dateString . ' ' . $endTimeStr;
                                $endDT = Carbon::createFromFormat('Y-m-d H:i:s', $endDatetimeString, 'Asia/Manila');
                                $endTimestamp = $endDT->timestamp * 1000;
                            }
                        }
                    } catch (\Exception $e) {
                        // Log error but continue
                    }
                }
                
                return [
                    'id' => $election->id,
                    'election_id' => $election->election_id,
                    'election_name' => $election->election_name,
                    'type_of_election' => $election->organization->name ?? $election->type_of_election,
                    'organization_id' => $election->organization_id,
                    'description' => $election->description,
                    'description_limited' => $election->description ? \Illuminate\Support\Str::limit($election->description, 50) : null,
                    'venue' => $election->venue,
                    'election_date' => $election->election_date ? $election->election_date->format('Y-m-d') : null,
                    'election_date_formatted' => $election->election_date ? $election->election_date->format('M d, Y') : null,
                    'timestarted' => $election->timestarted,
                    'time_ended' => $election->time_ended,
                    'status' => $election->status ?? 'upcoming',
                    'election_timestamp' => $electionTimestamp,
                    'end_timestamp' => $endTimestamp,
                ];
            });
            
            return response()->json([
                'success' => true,
                'elections' => $electionsData,
                'pagination' => [
                    'current_page' => $elections->currentPage(),
                    'last_page' => $elections->lastPage(),
                    'per_page' => $elections->perPage(),
                    'total' => $elections->total(),
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getElectionsData: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch elections data'
            ], 500);
        }
    }
}
