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

        // Public, edge-cacheable (Cloudflare honours s-maxage). Views are still
        // counted client-side via the beacon, so caching the HTML is safe.
        return response()
            ->view('public.profile', [
                'profile' => $profile,
                'links' => $links,
            ])
            ->header('Cache-Control', 'public, max-age=60, s-maxage=300, stale-while-revalidate=600');
    }
}
