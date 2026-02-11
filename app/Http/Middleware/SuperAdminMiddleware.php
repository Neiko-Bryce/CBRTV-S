<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        if (! auth()->user()->is_super_admin) {
            // If it's an AJAX request, return JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only Super Admins can edit system-wide sections.'
                ], 403);
            }

            // Otherwise redirect back (or to dashboard) with a trigger for the modal
            return redirect()->guest(route('admin.dashboard'))->with('show_restricted_modal', true);
        }

        return $next($request);
    }
}
