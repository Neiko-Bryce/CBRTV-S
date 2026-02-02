<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PositionController extends Controller
{
    /**
     * Display a listing of the positions for a specific organization.
     */
    public function index(Request $request, $organizationId = null)
    {
        $query = Position::query()->with('organization');

        if ($request->has('organization') && $request->organization) {
            $query->where('organization_id', $request->organization);
        } elseif ($organizationId) {
            $query->where('organization_id', $organizationId);
        }

        // Handle search functionality
        if ($request->has('search') && ! empty($request->search)) {
            $searchTerm = trim($request->search);
            $isPostgres = DB::connection()->getDriverName() === 'pgsql';
            $likeOperator = $isPostgres ? 'ILIKE' : 'LIKE';

            $query->where(function ($q) use ($searchTerm, $likeOperator) {
                $q->where('name', $likeOperator, "%{$searchTerm}%")
                    ->orWhere('description', $likeOperator, "%{$searchTerm}%");
            });
        }

        $positions = $query->orderBy('order', 'asc')->orderBy('name', 'asc')->paginate(15)->withQueryString();
        $organizations = Organization::where('is_active', true)->orderBy('name', 'asc')->get();

        return view('admin.positions.index', compact('positions', 'organizations', 'organizationId'));
    }

    /**
     * Store a newly created position in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'organization_id' => 'required|exists:organizations,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        Position::create($validated);

        return redirect()->route('admin.positions.index', ['organization' => $request->organization_id])
            ->with('success', 'Position created successfully.');
    }

    /**
     * Show the specified position.
     */
    public function show($id)
    {
        $position = Position::with('organization')->findOrFail($id);

        return response()->json($position);
    }

    /**
     * Update the specified position in storage.
     */
    public function update(Request $request, $id)
    {
        $position = Position::findOrFail($id);

        $validated = $request->validate([
            'organization_id' => 'required|exists:organizations,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $position->update($validated);

        return redirect()->route('admin.positions.index', ['organization' => $request->organization_id])
            ->with('success', 'Position updated successfully.');
    }

    /**
     * Remove the specified position from storage.
     */
    public function destroy($id)
    {
        $position = Position::findOrFail($id);

        // Check if position has candidates
        if ($position->candidates()->count() > 0) {
            return redirect()->route('admin.positions.index')
                ->with('error', 'Cannot delete position with existing candidates.');
        }

        $position->delete();

        return redirect()->route('admin.positions.index')
            ->with('success', 'Position deleted successfully.');
    }
}
