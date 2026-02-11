<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Organization;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    private const SESSION_KEY = 'registration_access_until';

    /**
     * Only allow registration page if the user has already entered the secret code (e.g. neikocbrtvs).
     * If not, redirect to the access page. Typing /register in the URL does nothing without the code.
     */
    private function ensureRegistrationAccess(Request $request): ?RedirectResponse
    {
        $until = $request->session()->get(self::SESSION_KEY);
        if (! is_numeric($until) || (int) $until <= time()) {
            $request->session()->forget(self::SESSION_KEY);
            return redirect()->route('register.access');
        }
        return null;
    }

    /**
     * Display the registration view. Blocked unless secret code was entered on landing or register/access.
     */
    public function create(Request $request): View|RedirectResponse
    {
        $redirect = $this->ensureRegistrationAccess($request);
        if ($redirect !== null) {
            return $redirect;
        }

        // Get organization from session (stored via /s/{slug} route)
        $organization = null;
        if ($request->session()->has('organization_id')) {
            $organization = Organization::find($request->session()->get('organization_id'));
        }

        return view('auth.register', compact('organization'));
    }

    /**
     * Handle an incoming registration request.
     * User type (student or admin) is taken from the form selection.
     * Blocked unless secret code was entered first.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $redirect = $this->ensureRegistrationAccess($request);
        if ($redirect !== null) {
            return $redirect;
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'usertype' => ['required', 'string', 'in:student,admin'],
            'school_name' => ['required_if:usertype,admin', 'string', 'max:255'],
        ]);

        $organizationId = null;

        // If registering as admin, create a new organization
        if ($validated['usertype'] === 'admin') {
            $org = Organization::create([
                'name' => $validated['school_name'],
                'slug' => Str::slug($validated['school_name']),
                'is_active' => true,
            ]);
            $organizationId = $org->id;
        } else {
            // If registering as student, use the organization from session (if available)
            // This is stored when the student visits /s/{slug}
            $organizationId = $request->session()->get('organization_id') ?: $request->session()->get('org_id');
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'usertype' => $validated['usertype'],
            'organization_id' => $organizationId,
            'is_super_admin' => false,
        ]);

        event(new Registered($user));

        $request->session()->forget('registration_access_until');

        return redirect()->route('login')->with('success', 'Registration successful! Please login with your credentials to continue.');
    }
}
