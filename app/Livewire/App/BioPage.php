<?php

namespace App\Livewire\App;

use App\Models\Profile;
use Illuminate\View\View;
use Livewire\Attributes\Title;

class BioPage
{
    #[Title('{profile.username} | BioTree')]
    public function show(string $username): View
    {
        $profile = Profile::where('username', $username)->firstOrFail();
        
        $links = $profile->links()->active()->ordered()->get();
        
        return view('livewire.app.bio-page', [
            'profile' => $profile,
            'links' => $links,
            'showStats' => true,
            'showQr' => true,
            'views' => $profile->pageViews()->count(),
        ]);
    }
}
