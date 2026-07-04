<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Middleware\EnsureOnboarded;
use App\Livewire\Onboarding\ClaimUsername;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

// Google OAuth (Socialite)
Route::get('auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
Route::get('auth/google/callback', [GoogleController::class, 'callback']);

// Onboarding — claim your username. Auth required, but sits *before* the onboarded gate.
Route::get('onboarding', ClaimUsername::class)
    ->middleware('auth')
    ->name('onboarding');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified', EnsureOnboarded::class])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
