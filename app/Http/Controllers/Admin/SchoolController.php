<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SchoolController extends Controller
{
    /**
     * Display a listing of the schools.
     */
    public function index(Request $request)
    {
        $query = School::query();

        // Handle search functionality
        if ($request->has('search') && ! empty($request->search)) {
            $searchTerm = trim($request->search);
            $isPostgres = DB::connection()->getDriverName() === 'pgsql';
            $likeOperator = $isPostgres ? 'ILIKE' : 'LIKE';

            $query->where(function ($q) use ($searchTerm, $likeOperator) {
                $q->where('name', $likeOperator, "%{$searchTerm}%")
                    ->orWhere('slug', $likeOperator, "%{$searchTerm}%")
                    ->orWhere('location', $likeOperator, "%{$searchTerm}%");
            });
        }

        $schools = $query->orderBy('name', 'asc')->paginate(15)->withQueryString();

        return view('admin.schools.index', compact('schools'));
    }

    /**
     * Store a newly created school in storage.
     */
    public function store(Request $request)
    {
        // Role check
        if (! auth()->user()->is_super_admin) {
            $msg = 'Unauthorized. Only Super Admins can create schools.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 403);
            }
            return redirect()->route('admin.schools.index')->with('error', $msg);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:schools,slug',
            'location' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Final check to ensure slug is set
        if (empty($validated['slug'])) {
             $validated['slug'] = 'school-' . uniqid();
        }

        $school = School::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'School created successfully.',
                'school' => $school
            ]);
        }

        return redirect()->route('admin.schools.index')
            ->with('success', 'School created successfully.');
    }

    /**
     * Show the specified school.
     */
    public function show($id)
    {
        $school = School::findOrFail($id);
        
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json($school);
        }
        
        return view('admin.schools.show', compact('school'));
    }

    /**
     * Update the specified school in storage.
     */
    public function update(Request $request, $id)
    {
        $school = School::findOrFail($id);

        // Role check
        if (! auth()->user()->is_super_admin) {
            $msg = 'Unauthorized. Only Super Admins can update schools.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 403);
            }
            return redirect()->route('admin.schools.index')->with('error', $msg);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:schools,slug,'.$id,
            'location' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        // If is_active is not present in the request (e.g. checkbox unchecked), it should be false
        $validated['is_active'] = $request->has('is_active');

        $school->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'School updated successfully.',
                'school' => $school
            ]);
        }

        return redirect()->route('admin.schools.index')
            ->with('success', 'School updated successfully.');
    }

    /**
     * Remove the specified school from storage.
     */
    public function destroy(Request $request, $id)
    {
        $school = School::findOrFail($id);

        // Role check
        if (! auth()->user()->is_super_admin) {
            $msg = 'Unauthorized. Only Super Admins can delete schools.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 403);
            }
            return redirect()->route('admin.schools.index')->with('error', $msg);
        }

        // Check for users or other dependencies
        if ($school->users()->count() > 0) {
            $msg = 'Cannot delete school with existing admin accounts.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->route('admin.schools.index')->with('error', $msg);
        }

        if ($school->organizations()->count() > 0) {
            $msg = 'Cannot delete school with existing organizations.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->route('admin.schools.index')->with('error', $msg);
        }

        $school->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'School deleted successfully.']);
        }
        
        return redirect()->route('admin.schools.index')
            ->with('success', 'School deleted successfully.');
    }
}
