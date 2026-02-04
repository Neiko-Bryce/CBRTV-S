<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Public API for live election results (no auth required)
Route::prefix('api')->group(function () {
    Route::get('/live-results', [\App\Http\Controllers\Api\LiveResultsController::class, 'getCompletedElections'])->name('api.live-results');
    Route::get('/live-results/{electionId}', [\App\Http\Controllers\Api\LiveResultsController::class, 'getElectionResults'])->name('api.live-results.election');
});

// Public candidate photo URL (no auth) so student-side images load even when DB/session is flaky
Route::get('candidates/photo/{path}', [\App\Http\Controllers\Admin\CandidateController::class, 'getPhoto'])->where('path', '.*')->name('candidates.photo.public');

// Protected Routes - Redirect to appropriate dashboard based on user type
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
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

    // Users Management
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);

    // Elections Management
    Route::resource('elections', \App\Http\Controllers\Admin\ElectionController::class);
    Route::post('elections/{id}/update-status', [\App\Http\Controllers\Admin\ElectionController::class, 'updateStatus'])->name('elections.update-status');
    Route::get('elections/stats/get', [\App\Http\Controllers\Admin\ElectionController::class, 'getStats'])->name('elections.stats.get');
    Route::get('elections/data/get', [\App\Http\Controllers\Admin\ElectionController::class, 'getElectionsData'])->name('elections.data.get');

    // Live Results Viewing (control which elections appear on the landing page)
    Route::get('live-results-viewing', [\App\Http\Controllers\Admin\LiveResultsViewController::class, 'index'])->name('live-results-viewing.index');
    Route::post('live-results-viewing/{electionId}/display', [\App\Http\Controllers\Admin\LiveResultsViewController::class, 'display'])->name('live-results-viewing.display');
    Route::post('live-results-viewing/{electionId}/hide', [\App\Http\Controllers\Admin\LiveResultsViewController::class, 'hide'])->name('live-results-viewing.hide');

    // Students Management
    Route::resource('students', \App\Http\Controllers\Admin\StudentController::class);
    Route::post('students/import', [\App\Http\Controllers\Admin\StudentController::class, 'import'])->name('students.import');

    // Student Account Management
    Route::get('student-management', [\App\Http\Controllers\Admin\StudentAccountController::class, 'index'])->name('student-management.index');
    Route::post('student-management/search', [\App\Http\Controllers\Admin\StudentAccountController::class, 'search'])->name('student-management.search');
    Route::get('student-management/generate-password', [\App\Http\Controllers\Admin\StudentAccountController::class, 'generatePassword'])->name('student-management.generate-password');
    Route::post('student-management/create-account', [\App\Http\Controllers\Admin\StudentAccountController::class, 'createAccount'])->name('student-management.create-account');
    Route::post('student-management/{userId}/regenerate-password', [\App\Http\Controllers\Admin\StudentAccountController::class, 'regeneratePassword'])->name('student-management.regenerate-password');
    Route::get('student-management/{userId}/password-history', [\App\Http\Controllers\Admin\StudentAccountController::class, 'getPasswordHistory'])->name('student-management.password-history');
    Route::delete('student-management/{userId}/delete', [\App\Http\Controllers\Admin\StudentAccountController::class, 'deleteAccount'])->name('student-management.delete');

    // Organizations Management
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
