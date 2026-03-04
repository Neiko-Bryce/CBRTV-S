<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Organization;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Redirect based on user type
        $user = Auth::user();
        $userType = $user->usertype ?? 'student';

        // Repair missing school/org for legacy accounts (common after deployments)
        if ($user && ! $user->school_id) {
            $resolvedSchoolId = null;

            if ($user->organization_id) {
                $org = Organization::withoutGlobalScopes()->find($user->organization_id);
                if ($org && $org->school_id) {
                    $resolvedSchoolId = $org->school_id;
                }
            }

            if (! $resolvedSchoolId) {
                $student = Student::withoutGlobalScopes()
                    ->where('student_id_number', $user->email)
                    ->first();
                if ($student && $student->school_id) {
                    $resolvedSchoolId = $student->school_id;
                }
                if ($student && ! $user->organization_id && $student->organization_id) {
                    $user->organization_id = $student->organization_id;
                }
            }

            if ($resolvedSchoolId) {
                $user->school_id = $resolvedSchoolId;
            }

            if ($user->isDirty()) {
                $user->save();
            }
        }

        if ($userType === 'admin') {
            return redirect()->intended(route('admin.dashboard', absolute: false));
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
