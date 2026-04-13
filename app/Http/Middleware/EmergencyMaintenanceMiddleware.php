<?php

namespace App\Http\Middleware;

use App\Models\School;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EmergencyMaintenanceMiddleware
{
    /**
     * Path prefixes that are never treated as school slugs.
     *
     * @var list<string>
     */
    private const RESERVED_FIRST_SEGMENTS = [
        'admin',
        'student',
        'login',
        'register',
        'api',
        'build',
        'logout',
        'dashboard',
        'profile',
        'verify-email',
        'email',
        'forgot-password',
        'reset-password',
        'confirm-password',
        'password',
        'candidates',
        'storage',
        'sanctum',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $schoolId = $this->resolveMaintenanceSchoolId($request);

        if (! School::maintenanceEnabledForId($schoolId)) {
            return $next($request);
        }

        // Allow Admins and Super Admins to bypass maintenance mode completely
        if (Auth::check()) {
            $user = Auth::user();
            if (($user->usertype ?? '') === 'admin' || ($user->usertype ?? '') === 'super_admin' || $user->is_super_admin) {
                return $next($request);
            }
            // Students stay logged in but see the maintenance page (do NOT log them out)
        }

        // During maintenance: student /login is unavailable. Guests may only use admin sign-in and logout.
        $path = ltrim($request->getPathInfo(), '/');
        if ($request->routeIs('admin.login', 'admin.login.store') || $path === 'admin/login052205') {
            return $next($request);
        }
        if ($path === 'logout' || str_starts_with($path, 'logout/') || $request->routeIs('logout')) {
            return $next($request);
        }

        // Allow public assets to load so the login/maintenance pages render properly
        if ($request->is('build/*') || $request->is('candidates/photo/*') || $request->is('api/landing-page/settings') || $request->is('api/live-results*') || $request->is('api/maintenance-status')) {
            return $next($request);
        }

        // Match what the /{slug} route would store so session and polling stay consistent when we block first.
        if ($schoolId !== null) {
            $request->session()->put('school_id', $schoolId);
        }

        return response()->view('errors.503', [
            'maintenanceSchoolId' => $schoolId,
        ], 200);
    }

    /**
     * Which school's lockdown applies to this request (null = none).
     */
    private function resolveMaintenanceSchoolId(Request $request): ?int
    {
        // Home page must never inherit a previous session school for lockdown (session clears on route after middleware).
        if ($request->path() === '' || $request->path() === '/') {
            return null;
        }

        if (Auth::check()) {
            $user = Auth::user();
            if (($user->usertype ?? 'student') === 'student') {
                return $user->school_id ? (int) $user->school_id : null;
            }
        }

        $segment = $request->segment(1);
        if ($segment && ! in_array($segment, self::RESERVED_FIRST_SEGMENTS, true)) {
            $school = School::query()->where('slug', $segment)->first();
            if ($school) {
                return (int) $school->id;
            }

            return null;
        }

        if ($request->session()->has('school_id')) {
            return (int) $request->session()->get('school_id');
        }

        return null;
    }
}
