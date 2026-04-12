<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    session()->forget('school_id');

    return response()->view('welcome')
        ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
});

// Disable password reset pages — not used in this system
Route::any('forgot-password', fn() => abort(404));
Route::any('forgot-password/{any}', fn() => abort(404))->where('any', '.*');
Route::any('reset-password', fn() => abort(404));
Route::any('reset-password/{any}', fn() => abort(404))->where('any', '.*');

// Public API for live election results (no auth required)
Route::prefix('api')->group(function () {
    Route::get('/live-results', [\App\Http\Controllers\Api\LiveResultsController::class, 'getCompletedElections'])->name('api.live-results');
    Route::get('/live-results/{electionId}', [\App\Http\Controllers\Api\LiveResultsController::class, 'getElectionResults'])->name('api.live-results.election');

    // Landing page settings (no auth required)
    Route::get('/landing-page/settings', function () {
        $aboutSettings = \App\Models\LandingPageSetting::getSectionWithExtras('about');
        $featuresSettings = \App\Models\LandingPageSetting::getSectionWithExtras('features');

        // PUBLIC API: Prioritize request over session over Auth::user() so portal view is always correct (tab-level isolation)
        $schoolId = request('school_id') ?: (session('school_id') ?: (Auth::check() ? Auth::user()->school_id : null));
        $orgId = request('organization_id') ?: (session('org_id') ?: (Auth::check() ? Auth::user()->organization_id : null));

        $organization = $orgId ? \App\Models\Organization::find($orgId) : null;
        $school = $schoolId ? \App\Models\School::find($schoolId) : null;

        return response()->json([
            'organization' => $organization ? [
                'name' => $organization->name,
                'logo' => $organization->logo_path,
            ] : null,
            'school' => $school ? [
                'name' => $school->name,
                'logo' => null, // Add logo column to schools later if needed
            ] : null,
            'about' => $aboutSettings,
            'features' => $featuresSettings,
        ]);
    })->name('api.landing-page.settings');

    // Public maintenance status endpoint - used for real-time polling
    Route::get('/maintenance-status', function () {
        return response()->json([
            'maintenance' => \App\Models\Setting::isMaintenanceModeEnabled()
        ]);
    })->name('api.maintenance-status');
});

// Public candidate photo URL (no auth) so student-side images load even when DB/session is flaky
Route::get('candidates/photo/{path}', [\App\Http\Controllers\Admin\CandidateController::class, 'getPhoto'])->where('path', '.*')->name('candidates.photo.public');

// Protected Routes - Redirect to appropriate dashboard based on user type
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();
        $userType = $user->usertype ?? 'student';

        if ($userType === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('student.dashboard');
    })->name('dashboard');

    // Profile routes (from Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin-only routes:
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/analytics', [\App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('analytics.index');

    // Users Management
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);

    // Elections Management
    Route::resource('elections', \App\Http\Controllers\Admin\ElectionController::class);
    Route::post('elections/{id}/archive', [\App\Http\Controllers\Admin\ElectionArchiveController::class, 'archive'])->name('elections.archive');
    Route::get('archived-elections', [\App\Http\Controllers\Admin\ElectionArchiveController::class, 'index'])->name('archived-elections.index');
    Route::get('archived-elections/{id}', [\App\Http\Controllers\Admin\ElectionArchiveController::class, 'show'])->name('archived-elections.show');
    Route::post('archived-elections/{id}/display', [\App\Http\Controllers\Admin\ElectionArchiveController::class, 'display'])->name('archived-elections.display');
    Route::post('archived-elections/{id}/hide', [\App\Http\Controllers\Admin\ElectionArchiveController::class, 'hide'])->name('archived-elections.hide');
    Route::post('elections/{id}/update-status', [\App\Http\Controllers\Admin\ElectionController::class, 'updateStatus'])->name('elections.update-status');
    Route::get('elections/stats/get', [\App\Http\Controllers\Admin\ElectionController::class, 'getStats'])->name('elections.stats.get');
    Route::get('elections/data/get', [\App\Http\Controllers\Admin\ElectionController::class, 'getElectionsData'])->name('elections.data.get');

    // Live Results Viewing (control which elections appear on the landing page)
    Route::get('live-results-viewing', [\App\Http\Controllers\Admin\LiveResultsViewController::class, 'index'])->name('live-results-viewing.index');
    Route::get('live-results-viewing/{electionId}/results', [\App\Http\Controllers\Admin\LiveResultsViewController::class, 'getElectionResults'])->name('live-results-viewing.results');
    Route::post('live-results-viewing/{electionId}/display', [\App\Http\Controllers\Admin\LiveResultsViewController::class, 'display'])->name('live-results-viewing.display');
    Route::post('live-results-viewing/{electionId}/hide', [\App\Http\Controllers\Admin\LiveResultsViewController::class, 'hide'])->name('live-results-viewing.hide');

    // Students Management (specific routes before resource so they match first)
    Route::delete('students/destroy-all', [\App\Http\Controllers\Admin\StudentController::class, 'destroyAll'])->name('students.destroy-all');
    Route::post('students/import', [\App\Http\Controllers\Admin\StudentController::class, 'import'])->name('students.import');
    Route::resource('students', \App\Http\Controllers\Admin\StudentController::class);

    // Student Account Management
    Route::get('student-management', [\App\Http\Controllers\Admin\StudentAccountController::class, 'index'])->name('student-management.index');
    Route::get('student-management/suggest', [\App\Http\Controllers\Admin\StudentAccountController::class, 'suggest'])->name('student-management.suggest');
    Route::post('student-management/search', [\App\Http\Controllers\Admin\StudentAccountController::class, 'search'])->name('student-management.search');
    Route::get('student-management/generate-password', [\App\Http\Controllers\Admin\StudentAccountController::class, 'generatePassword'])->name('student-management.generate-password');
    Route::post('student-management/create-account', [\App\Http\Controllers\Admin\StudentAccountController::class, 'createAccount'])->name('student-management.create-account');
    Route::post('student-management/{userId}/regenerate-password', [\App\Http\Controllers\Admin\StudentAccountController::class, 'regeneratePassword'])->name('student-management.regenerate-password');
    Route::get('student-management/{userId}/password-history', [\App\Http\Controllers\Admin\StudentAccountController::class, 'getPasswordHistory'])->name('student-management.password-history');
    Route::delete('student-management/{userId}/delete', [\App\Http\Controllers\Admin\StudentAccountController::class, 'deleteAccount'])->name('student-management.delete');

    // Organizations Management
    Route::get('organizations/{organization}/positions', [\App\Http\Controllers\Admin\OrganizationController::class, 'positions'])->name('organizations.positions');
    Route::resource('organizations', \App\Http\Controllers\Admin\OrganizationController::class);

    // Positions Management
    Route::get('positions', [\App\Http\Controllers\Admin\PositionController::class, 'index'])->name('positions.index');
    Route::get('positions/{id}', [\App\Http\Controllers\Admin\PositionController::class, 'show'])->name('positions.show');
    Route::post('positions', [\App\Http\Controllers\Admin\PositionController::class, 'store'])->name('positions.store');
    Route::put('positions/{id}', [\App\Http\Controllers\Admin\PositionController::class, 'update'])->name('positions.update');
    Route::delete('positions/{id}', [\App\Http\Controllers\Admin\PositionController::class, 'destroy'])->name('positions.destroy');

    // Partylists Management
    Route::get('partylists', [\App\Http\Controllers\Admin\PartylistController::class, 'index'])->name('partylists.index');
    Route::get('partylists/{id}', [\App\Http\Controllers\Admin\PartylistController::class, 'show'])->name('partylists.show');
    Route::post('partylists', [\App\Http\Controllers\Admin\PartylistController::class, 'store'])->name('partylists.store');
    Route::put('partylists/{id}', [\App\Http\Controllers\Admin\PartylistController::class, 'update'])->name('partylists.update');
    Route::delete('partylists/{id}', [\App\Http\Controllers\Admin\PartylistController::class, 'destroy'])->name('partylists.destroy');

    // Candidates Management
    Route::get('candidates', [\App\Http\Controllers\Admin\CandidateController::class, 'index'])->name('candidates.index');
    Route::get('candidates/photo/{path}', [\App\Http\Controllers\Admin\CandidateController::class, 'getPhoto'])->where('path', '.*')->name('candidates.photo');
    Route::get('candidates/positions/{electionId}', [\App\Http\Controllers\Admin\CandidateController::class, 'getPositions'])->name('candidates.positions');
    Route::get('candidates/positions-by-organization/{organizationId}', [\App\Http\Controllers\Admin\CandidateController::class, 'getPositionsByOrganization'])->name('candidates.positions-by-organization');
    Route::get('candidates/elections-by-organization/{organizationId}', [\App\Http\Controllers\Admin\CandidateController::class, 'getElectionsByOrganization'])->name('candidates.elections-by-organization');
    Route::get('candidates/partylists/{electionId}', [\App\Http\Controllers\Admin\CandidateController::class, 'getPartylists'])->name('candidates.partylists');
    Route::get('candidates/{id}', [\App\Http\Controllers\Admin\CandidateController::class, 'show'])->name('candidates.show');
    Route::post('candidates', [\App\Http\Controllers\Admin\CandidateController::class, 'store'])->name('candidates.store');
    Route::post('candidates/multiple', [\App\Http\Controllers\Admin\CandidateController::class, 'storeMultiple'])->name('candidates.store-multiple');
    Route::put('candidates/{id}', [\App\Http\Controllers\Admin\CandidateController::class, 'update'])->name('candidates.update');
    Route::delete('candidates/{id}', [\App\Http\Controllers\Admin\CandidateController::class, 'destroy'])->name('candidates.destroy');

    // Reports
    Route::get('reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::post('reports/generate', [\App\Http\Controllers\Admin\ReportController::class, 'generate'])->name('reports.generate');
    Route::post('reports/by-date-range', [\App\Http\Controllers\Admin\ReportController::class, 'generateByDateRange'])->name('reports.by-date-range');
    Route::get('reports/by-date-range/print', [\App\Http\Controllers\Admin\ReportController::class, 'printByDateRange'])->name('reports.by-date-range.print');
    Route::get('reports/{electionRef}/print', [\App\Http\Controllers\Admin\ReportController::class, 'print'])->name('reports.print');

    // Landing Page Management (All Admins can manage their own scoped sections)
    Route::get('landing-page', [\App\Http\Controllers\Admin\LandingPageController::class, 'index'])->name('landing-page.index');
    Route::post('landing-page', [\App\Http\Controllers\Admin\LandingPageController::class, 'update'])->name('landing-page.update');
    Route::post('landing-page/reset', [\App\Http\Controllers\Admin\LandingPageController::class, 'reset'])->name('landing-page.reset');

    // Landing Page Management (Super Admin Only - System-wide Management)
    Route::middleware(['super_admin'])->group(function () {
        // School Management (Write Operations)
        Route::post('schools', [\App\Http\Controllers\Admin\SchoolController::class, 'store'])->name('schools.store');
        Route::put('schools/{school}', [\App\Http\Controllers\Admin\SchoolController::class, 'update'])->name('schools.update');
        Route::delete('schools/{school}', [\App\Http\Controllers\Admin\SchoolController::class, 'destroy'])->name('schools.destroy');
    });

    // School Management (Read Operations - All Admins)
    Route::get('schools', [\App\Http\Controllers\Admin\SchoolController::class, 'index'])->name('schools.index');
    Route::get('schools/{school}', [\App\Http\Controllers\Admin\SchoolController::class, 'show'])->name('schools.show');

    // Admin Profile
    Route::get('profile', [\App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [\App\Http\Controllers\Admin\ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('profile', [\App\Http\Controllers\Admin\ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // System Settings
    Route::post('settings/maintenance', [\App\Http\Controllers\SettingController::class, 'toggleMaintenance'])->name('settings.maintenance.toggle');
});

// Student-only routes:
Route::middleware(['auth', 'student'])->group(function () {
    Route::get('/student/dashboard', [\App\Http\Controllers\Student\DashboardController::class, 'index'])->name('student.dashboard');
    Route::get('/student/vote/{electionId}', [\App\Http\Controllers\Student\DashboardController::class, 'vote'])->name('student.vote');
    Route::post('/student/vote/{electionId}', [\App\Http\Controllers\Student\DashboardController::class, 'submitVote'])->name('student.submit-vote');
    Route::get('/student/votes-history', [\App\Http\Controllers\Student\DashboardController::class, 'votesHistory'])->name('student.votes-history');
});

// Breeze authentication routes (login, register, password reset, email verification)
require __DIR__.'/auth.php';

// School-specific landing page (catch-all route at the bottom)
Route::get('/{slug}', function ($slug) {
    $school = \App\Models\School::where('slug', $slug)->first();

    if ($school) {
        // Store school ID in session so guest students see school-specific content
        session(['school_id' => $school->id]);

        return response()->view('welcome', ['school' => $school])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    // If it's not a school slug, let it fall through or abort
    abort(404);
})->name('school.portal');
