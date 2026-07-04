<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Keep authenticated users who haven't claimed a username on the
 * onboarding flow until they do. Do NOT apply this to the onboarding
 * route itself, or it will loop.
 */
class EnsureOnboarded
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->hasCompletedOnboarding()) {
            return redirect()->route('onboarding');
        }

        return $next($request);
    }
}
