<?php

namespace App\Http\Controllers;

use App\Models\Profile;

class PublicProfileController extends Controller
{
    public function show(string $username)
    {
        $profile = Profile::query()
            ->where('username', $username)
            ->where('is_published', true)
            ->firstOrFail();

        $links = $profile->links()->active()->ordered()->get();

        return view('public.profile', [
            'profile' => $profile,
            'links' => $links,
        ]);
    }
}
