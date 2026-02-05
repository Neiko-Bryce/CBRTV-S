<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class RegistrationAccessController extends Controller
{
    private const SESSION_KEY = 'registration_access_until';
    private const TTL_MINUTES = 15;

    public function showAccessForm(): View
    {
        return view('auth.register-access');
    }

    public function verifyAccess(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'access_code' => ['required', 'string', 'max:255'],
        ]);

        $key = config('app.registration_access_key', 'neikocbrtvs');
        if (! hash_equals($key, $request->input('access_code'))) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Invalid access code.'], 422);
            }
            return redirect()->route('register.access')
                ->with('error', 'Invalid access code. You cannot access registration.')
                ->withInput($request->only('access_code'));
        }

        Session::put(self::SESSION_KEY, now()->addMinutes(self::TTL_MINUTES)->timestamp);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'redirect' => route('register'),
            ]);
        }
        return redirect()->route('register')->with('success', 'Access granted. You can now create your account.');
    }
}
