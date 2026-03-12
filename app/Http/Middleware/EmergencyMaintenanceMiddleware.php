<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class EmergencyMaintenanceMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If maintenance mode is OFF, proceed normally
        if (! Setting::isMaintenanceModeEnabled()) {
            return $next($request);
        }

        // Allow Admins and Super Admins to bypass maintenance mode completely
        if (Auth::check()) {
            if (Auth::user()->usertype === 'admin' || Auth::user()->usertype === 'super_admin' || Auth::user()->is_super_admin) {
                return $next($request);
            }
            // Students stay logged in but see the maintenance page (do NOT log them out)
        }

        // Allow everyone to view the login page (and logout) so admins can still log in/out
        if ($request->is('login') || $request->is('login/*') || $request->is('logout') || $request->routeIs('login') || $request->routeIs('password.*')) {
            return $next($request);
        }
        
        // Allow public assets to load so the login/maintenance pages render properly
        if ($request->is('build/*') || $request->is('candidates/photo/*') || $request->is('api/landing-page/settings') || $request->is('api/live-results*') || $request->is('api/maintenance-status')) {
             return $next($request);
        }

        // For all other requests (students, guests trying to vote), return the Maintenance View
        return response()->view('errors.503', [], 200);
    }
}
