<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public bool $is_published = true;

    public function mount(): void
    {
        $this->is_published = (bool) Auth::user()->profile?->is_published;
    }

    /**
     * Toggle whether the user's public bio page is visible.
     */
    public function toggleVisibility(): void
    {
        $profile = Auth::user()->profile;

        $profile->update(['is_published' => ! $profile->is_published]);

        $this->is_published = $profile->is_published;

        $this->dispatch('profile-visibility-updated');
    }
}; ?>

<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Visibility') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Control whether your bio page is publicly visible. When hidden, visitors to your link will see a not-found page.') }}
        </p>
    </header>

    <div class="flex items-center gap-4">
        <button
            type="button"
            wire:click="toggleVisibility"
            role="switch"
            aria-checked="{{ $is_published ? 'true' : 'false' }}"
            class="{{ $is_published ? 'bg-emerald-600' : 'bg-gray-300 dark:bg-gray-600' }} relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
        >
            <span class="{{ $is_published ? 'translate-x-6' : 'translate-x-1' }} inline-block h-4 w-4 transform rounded-full bg-white transition-transform"></span>
        </button>

        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
            @if($is_published)
                {{ __('Published — anyone with your link can view your page') }}
            @else
                {{ __('Hidden — your page is not publicly accessible') }}
            @endif
        </span>

        <x-action-message class="me-3" on="profile-visibility-updated">
            {{ __('Saved.') }}
        </x-action-message>
    </div>
</section>
