<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Redirect the user to Google's OAuth consent screen.
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the callback from Google.
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            return redirect()->route('login')->withErrors([
                'email' => 'Google sign-in failed or was cancelled. Please try again.',
            ]);
        }

        $user = User::query()
            ->where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user) {
            // Link Google to an existing email/password account (only fill what's missing).
            $user->forceFill([
                'google_id' => $user->google_id ?: $googleUser->getId(),
                'avatar' => $user->avatar ?: $googleUser->getAvatar(),
                'last_login_at' => now(),
            ])->save();
        } else {
            $user = User::create([
                'name' => $googleUser->getName() ?: $googleUser->getNickname() ?: 'BioTree User',
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'last_login_at' => now(),
            ]);

            // Google has already verified this email address.
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        if ($user->isSuspended()) {
            return redirect()->route('login')->withErrors([
                'email' => 'This account has been suspended.',
            ]);
        }

        Auth::login($user, remember: true);

        return $user->hasCompletedOnboarding()
            ? redirect()->intended(route('dashboard', absolute: false))
            : redirect()->route('onboarding');
    }
}
