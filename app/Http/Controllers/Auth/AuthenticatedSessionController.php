<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Organization;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Administrator-only sign-in (obscured URL). Same credentials as web; role enforced in storeAdmin().
     */
    public function createAdmin(): View
    {
        return view('auth.admin-login');
    }

    /**
     * Handle an incoming authentication request (student portal only).
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();
        $userType = $user->usertype ?? 'student';

        if ($userType === 'admin') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'This sign-in is for students only. Administrators must use the administrator sign-in page.',
            ]);
        }

        $resolvedSchoolId = $this->resolveStudentSchoolIdForUser($user);

        if (School::maintenanceEnabledForId($resolvedSchoolId)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'This school is temporarily unavailable during maintenance. You cannot sign in until your administrator restores access.',
            ]);
        }

        $sessionSchoolId = session('school_id');
        if ($sessionSchoolId && $resolvedSchoolId && (int) $sessionSchoolId !== (int) $resolvedSchoolId) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'Use your own campus portal to sign in. This portal is for a different school.',
            ]);
        }

        $request->session()->regenerate();

        // Repair missing or mismatched school/org — student record is source of truth
        if ($user) {
            $student = Student::withoutGlobalScopes()
                ->where('student_id_number', $user->email)
                ->first();

            $resolvedSchoolId = $student?->school_id;
            $resolvedOrgId = $student?->organization_id;

            // Fallback: derive school from organization if student record has no school
            if (! $resolvedSchoolId && ($resolvedOrgId ?? $user->organization_id)) {
                $org = Organization::withoutGlobalScopes()->find($resolvedOrgId ?? $user->organization_id);
                if ($org && $org->school_id) {
                    $resolvedSchoolId = $org->school_id;
                }
            }
            $resolvedSchoolId = $this->canonicalizeSchoolId($resolvedSchoolId);

            if ($resolvedSchoolId && $user->school_id !== $resolvedSchoolId) {
                $user->school_id = $resolvedSchoolId;
            }
            if ($resolvedOrgId && $user->organization_id !== $resolvedOrgId) {
                $user->organization_id = $resolvedOrgId;
            }

            if ($user->isDirty()) {
                $user->save();
            }
        }

        // For students: never redirect to candidate photo or other asset URLs after login.
        // Only allow intended redirect to actual student pages (dashboard, vote, votes-history).
        $intended = session('url.intended');
        $studentDashboard = route('student.dashboard', absolute: false);
        if ($intended) {
            $path = parse_url($intended, PHP_URL_PATH);
            $isCandidatePhoto = $path && str_contains($path, 'candidates/photo');
            $isStudentPage = $path && (str_starts_with($path, '/student/dashboard')
                || str_starts_with($path, '/student/vote')
                || str_starts_with($path, '/student/votes-history'));
            if ($isCandidatePhoto || ! $isStudentPage) {
                session()->forget('url.intended');

                return redirect($studentDashboard);
            }
        }

        return redirect()->intended($studentDashboard);
    }

    /**
     * Handle administrator sign-in (admin-only URL).
     */
    public function storeAdmin(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();
        $userType = $user->usertype ?? 'student';

        $isAdministrator = in_array($userType, ['admin', 'super_admin'], true) || $user->is_super_admin;
        if (! $isAdministrator) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'This sign-in is for administrators only. Students must use the student sign-in page.',
            ]);
        }

        $request->session()->regenerate();

        $adminDashboard = route('admin.dashboard', absolute: false);
        $intended = session('url.intended');
        if ($intended) {
            $path = parse_url($intended, PHP_URL_PATH);
            if (! $path || ! str_starts_with($path, '/admin')) {
                session()->forget('url.intended');
            }
        }

        return redirect()->intended($adminDashboard);
    }

    /**
     * Resolve the student's home school ID from the student/org records (same rules as post-login repair).
     */
    private function resolveStudentSchoolIdForUser(User $user): ?int
    {
        $student = Student::withoutGlobalScopes()
            ->where('student_id_number', $user->email)
            ->first();

        $resolvedSchoolId = $student?->school_id;
        $resolvedOrgId = $student?->organization_id;

        if (! $resolvedSchoolId && ($resolvedOrgId ?? $user->organization_id)) {
            $org = Organization::withoutGlobalScopes()->find($resolvedOrgId ?? $user->organization_id);
            if ($org && $org->school_id) {
                $resolvedSchoolId = $org->school_id;
            }
        }

        return $this->canonicalizeSchoolId($resolvedSchoolId);
    }

    /**
     * Canonicalize legacy duplicate school IDs to active campus ID.
     */
    private function canonicalizeSchoolId($schoolId): ?int
    {
        if (! $schoolId || ! is_numeric($schoolId)) {
            return $schoolId ? (int) $schoolId : null;
        }

        $school = School::withoutGlobalScopes()->find((int) $schoolId);
        if (! $school) {
            return (int) $schoolId;
        }

        if ($school->slug === 'main-school') {
            $mainCampus = School::withoutGlobalScopes()->where('slug', 'main-campus')->first();
            if ($mainCampus) {
                return (int) $mainCampus->id;
            }
        }

        return (int) $schoolId;
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
