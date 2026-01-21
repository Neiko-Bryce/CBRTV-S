<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the organizations.
     */
    public function index(Request $request)
    {
        $query = Organization::query();
        
        // Handle search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = trim($request->search);
            $isPostgres = DB::connection()->getDriverName() === 'pgsql';
            $likeOperator = $isPostgres ? 'ILIKE' : 'LIKE';
            
            $query->where(function($q) use ($searchTerm, $likeOperator) {
                $q->where('name', $likeOperator, "%{$searchTerm}%")
                  ->orWhere('code', $likeOperator, "%{$searchTerm}%")
                  ->orWhere('description', $likeOperator, "%{$searchTerm}%");
            });
        }
        
        $organizations = $query->withCount('positions')->orderBy('name', 'asc')->paginate(15)->withQueryString();
        
        return view('admin.organizations.index', compact('organizations'));
    }

    /**
     * Store a newly created organization in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:organizations,code',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Organization::create($validated);

        return redirect()->route('admin.organizations.index')
            ->with('success', 'Organization created successfully.');
    }

    /**
     * Show the specified organization.
     */
    public function show($id)
    {
        $organization = Organization::findOrFail($id);
        return response()->json($organization);
    }

    /**
     * Update the specified organization in storage.
     */
    public function update(Request $request, $id)
    {
        $organization = Organization::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:organizations,code,' . $id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $organization->update($validated);

        return redirect()->route('admin.organizations.index')
            ->with('success', 'Organization updated successfully.');
    }

    /**
     * Remove the specified organization from storage.
     */
    public function destroy($id)
    {
        $organization = Organization::findOrFail($id);
        
        // Check if organization has elections
        if ($organization->elections()->count() > 0) {
            return redirect()->route('admin.organizations.index')
                ->with('error', 'Cannot delete organization with existing elections.');
        }
        
        $organization->delete();

        return redirect()->route('admin.organizations.index')
            ->with('success', 'Organization deleted successfully.');
    }
}
