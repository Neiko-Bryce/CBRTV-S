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
        $userType = $user->usertype ?? $user->role ?? 'student';
        
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
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
});

// Student-only routes:
Route::middleware(['auth', 'student'])->group(function () {
    Route::get('/student/dashboard', function () {
        return view('student.dashboard');
    })->name('student.dashboard');
});

// Breeze authentication routes (login, register, password reset, email verification)
require __DIR__.'/auth.php';
