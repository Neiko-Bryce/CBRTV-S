<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Toggle emergency maintenance (lockdown) for the admin's school, or for a given school (super admin).
     */
    public function toggleMaintenance(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'maintenance_mode' => 'required|boolean',
            'school_id' => 'nullable|integer|exists:schools,id',
        ]);

        $targetSchoolId = $this->resolveTargetSchoolId($request, $user);

        $school = School::findOrFail($targetSchoolId);

        if (! $user->is_super_admin && (int) $user->school_id !== (int) $school->id) {
            abort(403, 'You can only change maintenance for your own school.');
        }

        $school->maintenance_mode = $request->boolean('maintenance_mode');
        $school->save();

        $isEnabled = $school->maintenance_mode;
        $status = $isEnabled ? 'enabled' : 'disabled';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Emergency maintenance has been {$status} for {$school->name}.",
                'maintenance_mode' => $isEnabled,
                'school_id' => $school->id,
            ]);
        }

        return redirect()->back()->with('success', "Emergency maintenance has been {$status} for {$school->name}.");
    }

    /**
     * @return int
     */
    private function resolveTargetSchoolId(Request $request, $user)
    {
        if ($user->is_super_admin) {
            if ($user->school_id) {
                return (int) $user->school_id;
            }

            $request->validate([
                'school_id' => 'required|integer|exists:schools,id',
            ]);

            return (int) $request->school_id;
        }

        if (($user->usertype ?? '') !== 'admin' || ! $user->school_id) {
            abort(403, 'No school is assigned to your account.');
        }

        return (int) $user->school_id;
    }
}
