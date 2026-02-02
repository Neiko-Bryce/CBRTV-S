<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Election;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ElectionController extends Controller
{
    /**
     * Display a listing of the elections.
     */
    public function index(Request $request)
    {
        // Update statuses for ALL elections first using direct DB queries for reliability
        try {
            // Fetch all elections directly from DB to avoid any caching
            $allElectionData = DB::table('elections')->get();

            foreach ($allElectionData as $electionRow) {
                $electionArray = (array) $electionRow;

                // Don't auto-update if status is manually set to cancelled
                if ($electionArray['status'] === 'cancelled') {
                    $calculatedStatus = $this->calculateStatus($electionArray);
                    if ($calculatedStatus === 'completed') {
                        DB::table('elections')
                            ->where('id', $electionArray['id'])
                            ->update(['status' => 'completed', 'updated_at' => now()]);
                    }

                    continue;
                }

                // Convert any old "rescheduled" status to "upcoming"
                if ($electionArray['status'] === 'rescheduled') {
                    DB::table('elections')
                        ->where('id', $electionArray['id'])
                        ->update(['status' => 'upcoming', 'updated_at' => now()]);
                    $electionArray['status'] = 'upcoming';
                }

                $calculatedStatus = $this->calculateStatus($electionArray);

                // Always update if status changed or is empty
                if ($electionArray['status'] !== $calculatedStatus || empty($electionArray['status'])) {
                    DB::table('elections')
                        ->where('id', $electionArray['id'])
                        ->update(['status' => $calculatedStatus, 'updated_at' => now()]);

                    \Log::info("Election #{$electionArray['id']} status updated: {$electionArray['status']} -> {$calculatedStatus}");
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error updating election statuses in index: '.$e->getMessage());
        }

        // Fetch elections FRESH after all status updates (use new query to avoid any caching)
        $elections = Election::query()
            ->when($request->has('status') && $request->status, function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->has('search') && ! empty($request->search), function ($q) use ($request) {
                $searchTerm = trim($request->search);
                $isPostgres = DB::connection()->getDriverName() === 'pgsql';
                $likeOperator = $isPostgres ? 'ILIKE' : 'LIKE';
                $q->where(function ($inner) use ($searchTerm, $likeOperator) {
                    $inner->where('election_name', $likeOperator, "%{$searchTerm}%")
                        ->orWhere('type_of_election', $likeOperator, "%{$searchTerm}%")
                        ->orWhere('description', $likeOperator, "%{$searchTerm}%")
                        ->orWhere('venue', $likeOperator, "%{$searchTerm}%")
                        ->orWhere('election_id', $likeOperator, "%{$searchTerm}%");
                });
            })
            ->orderBy('id', 'asc')
            ->paginate(15)
            ->withQueryString();

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
            if (empty($validated['type_of_election']) && ! empty($validated['organization_id'])) {
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
                    $validated['election_date'].' '.$validated['timestarted'].':00',
                    'Asia/Manila'
                );
                if ($electionDateTime->isPast()) {
                    if ($request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Cannot create an election with a past date and time. Please select a future date and time.',
                            'errors' => ['election_date' => ['The election date and time must be in the future (Philippine Time).']],
                        ], 422);
                    }

                    return back()->withErrors(['election_date' => 'The election date and time must be in the future (Philippine Time).'])->withInput();
                }
            } elseif ($electionDate->isPast()) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot create an election with a past date. Please select today or a future date.',
                        'errors' => ['election_date' => ['The election date must be today or in the future (Philippine Time).']],
                    ], 422);
                }

                return back()->withErrors(['election_date' => 'The election date must be today or in the future (Philippine Time).'])->withInput();
            }

            // Note: We allow overnight elections (e.g., 11:00 PM to 12:00 AM next day)
            // The calculateStatus function handles this by adding a day to end time if needed

            // Auto-generate election_id if not provided
            if (empty($validated['election_id'])) {
                $validated['election_id'] = 'ELEC-'.time().'-'.rand(1000, 9999);
            }

            // Calculate initial status if not manually set
            if (empty($validated['status']) || ! isset($validated['status'])) {
                $validated['status'] = $this->calculateStatus($validated);
            }

            $election = Election::create($validated);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Election created successfully.',
                    'election' => $election,
                ]);
            }

            return redirect()->route('admin.elections.index')
                ->with('success', 'Election created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Error creating election: '.$e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all(),
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while creating the election: '.$e->getMessage(),
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
                'election' => $election,
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
                'election' => $electionData,
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
            // No restriction on past dates when editing - admin can update to any date/time
            // Note: We allow overnight elections (e.g., 11:00 PM to 12:00 AM next day)
            // The calculateStatus function handles this by adding a day to end time if needed

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
            } elseif ($dateChanged && (! isset($validated['status']) || empty($validated['status']))) {
                // If date/time changed and status is not set, set to upcoming
                $validated['status'] = 'upcoming';
            } elseif (empty($validated['status']) || ! isset($validated['status'])) {
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
                    ],
                ]);
            }

            return redirect()->route('admin.elections.index')
                ->with('success', 'Election updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Error updating election: '.$e->getMessage(), [
                'exception' => $e,
                'election_id' => $id,
                'request_data' => $request->all(),
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating the election: '.$e->getMessage(),
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
                'message' => 'Election deleted successfully.',
            ]);
        }

        return redirect()->route('admin.elections.index')
            ->with('success', 'Election deleted successfully.');
    }

    /**
     * Calculate election status based on date and time
     *
     * Logic:
     * - UPCOMING: Current time is BEFORE start time
     * - ONGOING: Current time is >= start time AND < end time
     * - COMPLETED: Current time is >= end time
     *
     * For overnight elections (e.g., 11 PM to 2 AM):
     * - Start: election_date at timestarted
     * - End: election_date + 1 day at time_ended (if time_ended <= timestarted)
     */
    private function calculateStatus($electionData)
    {
        try {
            // Get current time in Philippine timezone
            $now = Carbon::now('Asia/Manila');

            // If no election date, default to upcoming
            if (empty($electionData['election_date'])) {
                return 'upcoming';
            }

            // Extract date string properly - handle Carbon, ISO string, or simple Y-m-d string
            $dateString = $this->extractDateString($electionData['election_date']);
            if (! $dateString) {
                \Log::error('calculateStatus: Could not extract date from: '.print_r($electionData['election_date'], true));

                return 'upcoming';
            }

            // Parse start datetime - ALWAYS use the timestarted if available
            $startDateTime = null;
            $timeStr = null;

            if (! empty($electionData['timestarted'])) {
                $timeStr = $this->normalizeTimeFormat($electionData['timestarted']);
            }

            if ($timeStr) {
                try {
                    $startDateTime = Carbon::createFromFormat(
                        'Y-m-d H:i:s',
                        $dateString.' '.$timeStr,
                        'Asia/Manila'
                    );
                } catch (\Exception $e) {
                    \Log::error("Failed to parse start time '{$timeStr}' with date '{$dateString}': ".$e->getMessage());
                }
            }

            // If no valid start time was parsed, use start of election date
            // But LOG this as it might indicate a problem
            if (! $startDateTime) {
                \Log::warning("calculateStatus: No valid start time, using startOfDay for date: {$dateString}");
                $startDateTime = Carbon::createFromFormat('Y-m-d', $dateString, 'Asia/Manila')->startOfDay();
            }

            // Parse end datetime
            $endDateTime = null;
            $endTimeStr = null;

            if (! empty($electionData['time_ended'])) {
                $endTimeStr = $this->normalizeTimeFormat($electionData['time_ended']);
            }

            if ($endTimeStr) {
                try {
                    $endDateTime = Carbon::createFromFormat(
                        'Y-m-d H:i:s',
                        $dateString.' '.$endTimeStr,
                        'Asia/Manila'
                    );

                    // CRITICAL: Handle overnight elections
                    // If end time is <= start time, the election ends the NEXT DAY
                    if ($endDateTime->lessThanOrEqualTo($startDateTime)) {
                        $endDateTime->addDay();
                    }
                } catch (\Exception $e) {
                    \Log::error("Failed to parse end time '{$endTimeStr}' with date '{$dateString}': ".$e->getMessage());
                }
            }

            // If no end time, use end of election date
            if (! $endDateTime) {
                $endDateTime = Carbon::createFromFormat('Y-m-d', $dateString, 'Asia/Manila')->endOfDay();
            }

            // Log for debugging
            $electionId = $electionData['id'] ?? 'unknown';
            \Log::info("calculateStatus Election #{$electionId}: Now={$now->toDateTimeString()}, Start={$startDateTime->toDateTimeString()}, End={$endDateTime->toDateTimeString()}, TimeStarted={$electionData['timestarted']}, TimeEnded=".($electionData['time_ended'] ?? 'null'));

            // DECISION LOGIC (order matters!)
            // 1. Check if COMPLETED first (current time >= end time)
            if ($now->greaterThanOrEqualTo($endDateTime)) {
                \Log::info("calculateStatus Election #{$electionId}: COMPLETED (now >= end)");

                return 'completed';
            }

            // 2. Check if ONGOING (current time >= start time AND < end time)
            if ($now->greaterThanOrEqualTo($startDateTime)) {
                \Log::info("calculateStatus Election #{$electionId}: ONGOING (now >= start AND now < end)");

                return 'ongoing';
            }

            // 3. Otherwise UPCOMING (current time < start time)
            \Log::info("calculateStatus Election #{$electionId}: UPCOMING (now < start)");

            return 'upcoming';

        } catch (\Exception $e) {
            \Log::error('Error calculating election status: '.$e->getMessage().' - '.$e->getTraceAsString());

            return 'upcoming'; // Default to upcoming on error
        }
    }

    /**
     * Extract date string in Y-m-d format from various input types
     */
    private function extractDateString($date)
    {
        if (empty($date)) {
            return null;
        }

        // If it's a Carbon instance, format it directly
        if ($date instanceof \Carbon\Carbon) {
            return $date->format('Y-m-d');
        }

        // If it's a string, try to extract just the date part
        if (is_string($date)) {
            $dateStr = trim($date);

            // If it's already Y-m-d format (10 chars: YYYY-MM-DD)
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateStr)) {
                return $dateStr;
            }

            // If it's an ISO 8601 format like "2026-01-29T00:00:00.000000+08:00" or "2026-01-29T00:00:00.000000Z"
            // Extract just the date part (first 10 characters)
            if (strlen($dateStr) >= 10 && preg_match('/^\d{4}-\d{2}-\d{2}/', $dateStr)) {
                return substr($dateStr, 0, 10);
            }

            // Try to parse with Carbon as a fallback
            try {
                return Carbon::parse($dateStr)->format('Y-m-d');
            } catch (\Exception $e) {
                \Log::error("extractDateString: Could not parse date string: {$dateStr}");

                return null;
            }
        }

        // Last resort - try to convert to string and parse
        try {
            return Carbon::parse((string) $date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Normalize time format to H:i:s
     */
    private function normalizeTimeFormat($time)
    {
        if (empty($time)) {
            return null;
        }

        $timeStr = trim($time);
        $parts = explode(':', $timeStr);

        if (count($parts) == 2) {
            // H:i format, add seconds
            return $parts[0].':'.$parts[1].':00';
        } elseif (count($parts) == 3) {
            // Already H:i:s format
            return $timeStr;
        }

        return null;
    }

    /**
     * Update election status (called via AJAX for real-time updates)
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $election = Election::findOrFail($id);

            $validated = $request->validate([
                'status' => 'required|in:upcoming,ongoing,completed,cancelled',
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
                'stats' => $stats,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status.',
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
            \Log::error('Error updating election statuses in getStats: '.$e->getMessage());
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
            'stats' => $stats,
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

            if ($request->has('search') && ! empty($request->search)) {
                $searchTerm = trim($request->search);
                $isPostgres = DB::connection()->getDriverName() === 'pgsql';
                $likeOperator = $isPostgres ? 'ILIKE' : 'LIKE';

                $query->where(function ($q) use ($searchTerm, $likeOperator) {
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
                \Log::error('Error updating election statuses in getElectionsData: '.$e->getMessage());
            }

            // Get paginated elections
            $elections = $query->orderBy('id', 'asc')->paginate(15)->withQueryString();

            // Format elections data for frontend
            $electionsData = $elections->map(function ($election) {
                // Calculate timestamps
                $electionTimestamp = null;
                $endTimestamp = null;

                if ($election->election_date) {
                    try {
                        $dateString = $election->election_date->format('Y-m-d');

                        if ($election->timestarted && ! empty(trim($election->timestarted))) {
                            $timeStr = trim($election->timestarted);
                            $timeParts = explode(':', $timeStr);
                            if (count($timeParts) == 2) {
                                $timeStr = $timeParts[0].':'.$timeParts[1].':00';
                            }

                            if ($timeStr) {
                                $datetimeString = $dateString.' '.$timeStr;
                                $electionDT = Carbon::createFromFormat('Y-m-d H:i:s', $datetimeString, 'Asia/Manila');
                                $electionTimestamp = $electionDT->timestamp * 1000;
                            }
                        } else {
                            $electionDT = Carbon::createFromFormat('Y-m-d', $dateString, 'Asia/Manila')->startOfDay();
                            $electionTimestamp = $electionDT->timestamp * 1000;
                        }

                        if ($election->time_ended && ! empty(trim($election->time_ended)) && $electionTimestamp) {
                            $endTimeStr = trim($election->time_ended);
                            $endTimeParts = explode(':', $endTimeStr);
                            if (count($endTimeParts) == 2) {
                                $endTimeStr = $endTimeParts[0].':'.$endTimeParts[1].':00';
                            }

                            if ($endTimeStr) {
                                $endDatetimeString = $dateString.' '.$endTimeStr;
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
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getElectionsData: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch elections data',
            ], 500);
        }
    }
}
