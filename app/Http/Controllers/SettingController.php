<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    /**
     * Toggle the emergency maintenance mode on or off.
     */
    public function toggleMaintenance(Request $request)
    {
        $request->validate([
            'maintenance_mode' => 'required|boolean',
        ]);

        $isEnabled = $request->boolean('maintenance_mode');
        
        Setting::setMaintenanceMode($isEnabled);

        $status = $isEnabled ? 'enabled' : 'disabled';
        
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Emergency Maintenance Mode has been gracefully {$status}.",
                'maintenance_mode' => $isEnabled
            ]);
        }
        
        return redirect()->back()->with('success', "Emergency Maintenance Mode has been gracefully {$status}.");
    }
}
