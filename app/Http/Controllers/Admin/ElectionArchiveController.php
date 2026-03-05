<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArchivedElection;
use App\Services\ElectionArchiveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ElectionArchiveController extends Controller
{
    /**
     * List archived elections.
     */
    public function index(Request $request)
    {
        $query = ArchivedElection::query()
            ->with(['organization', 'archivedByUser'])
            ->withCount([
                'archivedCandidates as candidates_count',
                'archivedVotes as votes_count',
            ]);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $isPostgres = DB::connection()->getDriverName() === 'pgsql';
            $likeOperator = $isPostgres ? 'ILIKE' : 'LIKE';

            $query->where(function ($q) use ($search, $likeOperator) {
                $q->where('election_name', $likeOperator, "%{$search}%")
                    ->orWhere('election_id', $likeOperator, "%{$search}%")
                    ->orWhere('type_of_election', $likeOperator, "%{$search}%");
            });
        }

        $archivedElections = $query
            ->orderByDesc('archived_at')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.elections.archive.index', compact('archivedElections'));
    }

    /**
     * Show one archived election.
     */
    public function show($id)
    {
        $archivedElection = ArchivedElection::with([
            'organization',
            'archivedByUser',
            'archivedCandidates.archivedPartylist',
        ])
            ->withCount('archivedVotes')
            ->findOrFail($id);

        $resultsByPosition = $archivedElection->archivedCandidates
            ->groupBy(function ($candidate) {
                return ($candidate->position_name ?: 'Unassigned Position')
                    .'|'.((int) ($candidate->position_order ?? 0))
                    .'|'.((int) ($candidate->number_of_slots ?? 1));
            })
            ->map(function ($group, $key) {
                [$positionName, $positionOrder, $slots] = array_pad(explode('|', $key), 3, 0);

                return [
                    'position_name' => $positionName,
                    'position_order' => (int) $positionOrder,
                    'number_of_slots' => (int) $slots,
                    'candidates' => $group->sortByDesc('votes_count')->values(),
                ];
            })
            ->sortBy('position_order')
            ->values();

        return view('admin.elections.archive.show', compact('archivedElection', 'resultsByPosition'));
    }

    /**
     * Display archived election results on public landing page.
     */
    public function display(Request $request, $id)
    {
        $archivedElection = ArchivedElection::findOrFail($id);
        $archivedElection->update(['show_live_results' => true]);

        $message = 'Archived election results are now visible on the landing page.';
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return back()->with('success', $message);
    }

    /**
     * Hide archived election results from public landing page.
     */
    public function hide(Request $request, $id)
    {
        $archivedElection = ArchivedElection::findOrFail($id);
        $archivedElection->update(['show_live_results' => false]);

        $message = 'Archived election results are now hidden from the landing page.';
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return back()->with('success', $message);
    }

    /**
     * Archive an active election.
     */
    public function archive(Request $request, $id, ElectionArchiveService $archiveService)
    {
        try {
            $archivedElection = $archiveService->archiveElection((int) $id, Auth::user());
            $message = "Election \"{$archivedElection->election_name}\" archived successfully.";

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'archived_election_id' => $archivedElection->id,
                ]);
            }

            return redirect()->route('admin.archived-elections.index')->with('success', $message);
        } catch (\DomainException|\RuntimeException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('Failed to archive election', [
                'election_id' => $id,
                'error' => $e->getMessage(),
            ]);

            $message = 'Failed to archive election. Please try again.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 500);
            }

            return back()->with('error', $message);
        }
    }
}
