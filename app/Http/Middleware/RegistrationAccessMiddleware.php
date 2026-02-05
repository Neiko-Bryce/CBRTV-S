<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class RegistrationAccessMiddleware
{
    private const SESSION_KEY = 'registration_access_until';
    private const TTL_MINUTES = 15;

    /**
     * Block /register unless the user has already entered the correct access code
     * (e.g. neikocbrtvs) on the landing page or /register/access and has a valid session.
     * Typing /register in the URL does nothing without the code.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $until = Session::get(self::SESSION_KEY);
        if (is_numeric($until) && (int) $until > time()) {
            return $next($request);
        }

        Session::forget(self::SESSION_KEY);
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Registration is not available. Enter the access code on the landing page first.'], 403);
        }
        return redirect()->route('register.access');
    }
}
