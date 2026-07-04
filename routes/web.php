<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\OutboundClickController;
use App\Http\Controllers\PublicProfileController;
use App\Http\Controllers\TrackController;
use App\Http\Middleware\EnsureOnboarded;
use App\Livewire\App\Analytics;
use App\Livewire\App\AppearanceEditor;
use App\Livewire\App\LinkEditor;
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

// Authenticated app (requires a completed profile).
Route::middleware(['auth', 'verified', EnsureOnboarded::class])->group(function () {
    Route::get('dashboard', Analytics::class)->name('dashboard');
    Route::get('links', LinkEditor::class)->name('links');
    Route::get('appearance', AppearanceEditor::class)->name('appearance');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Click tracking → outbound redirect (public). Link resolved by ULID.
Route::get('out/{link}', OutboundClickController::class)->name('link.out');

// Page-view beacon (CSRF-exempt — see bootstrap/app.php).
Route::post('track/view', [TrackController::class, 'view'])->name('track.view');

require __DIR__.'/auth.php';

// Public profile page — catch-all, MUST remain the last route so every
// named/app route above takes precedence. Reserved usernames can't be claimed.
Route::get('{username}', [PublicProfileController::class, 'show'])
    ->where('username', '[A-Za-z0-9_]+')
    ->name('profile.show');
