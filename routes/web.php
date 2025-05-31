<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;

// ✅ Tetap satu route untuk /
Route::get('/', [LandingPageController::class, 'index'])->name('landingpage');

// Guest-only routes
Route::middleware(['guest'])->group(function () {
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/tugas', [TaskController::class, 'index'])->name('tasks.index');

    Route::prefix('tasks')->name('tasks.')->group(function () {
        Route::post('/', [TaskController::class, 'store'])->name('store');
        Route::get('/data/ajax', [TaskController::class, 'getTasks'])->name('ajax');
        Route::get('/{task}', [TaskController::class, 'show'])->name('show');
        Route::put('/{task}', [TaskController::class, 'update'])->name('update');
        Route::delete('/{task}', [TaskController::class, 'destroy'])->name('destroy');
        Route::patch('/{task}/toggle-complete', [TaskController::class, 'toggleComplete'])->name('toggle-complete');
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
