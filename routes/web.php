<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

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
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
    
    // Users Management
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    
    // Students Management
    Route::resource('students', \App\Http\Controllers\Admin\StudentController::class);
    Route::post('students/import', [\App\Http\Controllers\Admin\StudentController::class, 'import'])->name('students.import');
});

// Student-only routes:
Route::middleware(['auth', 'student'])->group(function () {
    Route::get('/student/dashboard', function () {
        return view('student.dashboard');
    })->name('student.dashboard');
});

// Breeze authentication routes (login, register, password reset, email verification)
require __DIR__.'/auth.php';
