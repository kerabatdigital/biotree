<?php

use App\Http\Middleware\EnsureAdmin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust the Coolify/Traefik reverse proxy so X-Forwarded-Proto is honored
        // (otherwise Laravel generates http:// asset URLs behind an https-terminating proxy).
        $middleware->trustProxies(at: '*');

        // Page-view beacon is public and sent via navigator.sendBeacon (no CSRF token).
        // ToyyibPay callback is a webhook from external servers (no CSRF token).
        $middleware->validateCsrfTokens(except: [
            'track/*',
            'billing/callback',
        ]);

        // Admin middleware alias
        $middleware->alias([
            'admin' => EnsureAdmin::class,
        ]);

        // Required for Auth::logoutOtherDevices() to actually invalidate other sessions.
        $middleware->web(append: [
            \Illuminate\Session\Middleware\AuthenticateSession::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
