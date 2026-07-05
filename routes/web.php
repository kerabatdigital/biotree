<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\OutboundClickController;
use App\Http\Controllers\PublicProfileController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TrackController;
use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\EnsureOnboarded;
use App\Livewire\Admin\Overview;
use App\Livewire\Admin\Reports;
use App\Livewire\Admin\Settings;
use App\Livewire\Admin\UserDetail;
use App\Livewire\Admin\Users;
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

// Billing routes — protected by auth + verified (no onboarding required)
Route::middleware(['auth', 'verified'])->prefix('billing')->group(function () {
    Route::get('upgrade', \App\Livewire\App\BillingUpgrade::class)->name('billing.upgrade');
    Route::get('subscriptions', \App\Livewire\App\SubscriptionDashboard::class)->name('billing.subscriptions');
    Route::get('checkout', [BillingController::class, 'checkout'])->name('billing.checkout');
    Route::get('return', [BillingController::class, 'returnFromPayment'])->name('billing.return');
    Route::get('dashboard', [BillingController::class, 'dashboard'])->name('billing.dashboard');
});

// ToyyibPay callback webhook (CSRF-exempt via bootstrap/app.php configuration)
Route::post('billing/callback', [BillingController::class, 'callback'])->name('billing.callback');

// Admin routes - protected by auth + verified + admin middleware
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', Overview::class)->name('admin.dashboard');
    Route::get('/users', Users::class)->name('admin.users');
    Route::get('/users/{user}', UserDetail::class)->name('admin.users.show');
    Route::get('/reports', Reports::class)->name('admin.reports');
    Route::get('/settings', Settings::class)->name('admin.settings');
    Route::get('/billing/plans', \App\Livewire\Admin\BillingPlans::class)->name('admin.billing.plans');
    Route::get('/billing/subscriptions', \App\Livewire\Admin\BillingSubscriptions::class)->name('admin.billing.subscriptions');
    Route::get('/billing/coupons', \App\Livewire\Admin\BillingCoupons::class)->name('admin.billing.coupons');
});

// Click tracking → outbound redirect (public). Link resolved by ULID.
Route::get('out/{link}', OutboundClickController::class)->name('link.out');

// Page-view beacon (CSRF-exempt — see bootstrap/app.php).
Route::post('track/view', [TrackController::class, 'view'])->name('track.view');

// SEO
Route::get('sitemap.xml', SitemapController::class)->name('sitemap');

require __DIR__.'/auth.php';

// Public profile page — catch-all, MUST remain the last route so every
// named/app route above takes precedence. Reserved usernames can't be claimed.
// No session/cookies on the public page → no Set-Cookie → Cloudflare-cacheable.
Route::get('{username}', [PublicProfileController::class, 'show'])
    ->where('username', '[A-Za-z0-9_]+')
    ->withoutMiddleware([
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class,
        \Livewire\Features\SupportDisablingBackButtonCache\DisableBackButtonCacheMiddleware::class,
    ])
    ->name('profile.show');
