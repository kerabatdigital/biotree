<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
    public string $password = '';

    /**
     * Invalidate all other browser sessions for the current user.
     */
    public function logoutOtherBrowserSessions(): void
    {
        try {
            $this->validate([
                'password' => ['required', 'string', 'current_password'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('password');

            throw $e;
        }

        Auth::logoutOtherDevices($this->password);

        $this->reset('password');

        $this->dispatch('close');
        $this->dispatch('other-browser-sessions-logged-out');
    }
}; ?>

<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Browser Sessions') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('If necessary, you may log out of all of your other browser sessions across all of your devices. This does not affect your current session.') }}
        </p>
    </header>

    <div class="flex items-center gap-4">
        <x-secondary-button
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-logout-other-browser-sessions')"
        >{{ __('Log Out Other Browser Sessions') }}</x-secondary-button>

        <x-action-message class="me-3" on="other-browser-sessions-logged-out">
            {{ __('Done.') }}
        </x-action-message>
    </div>

    <x-modal name="confirm-logout-other-browser-sessions" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="logoutOtherBrowserSessions" class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Log Out Other Browser Sessions') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Please enter your password to confirm you would like to log out of your other browser sessions across all of your devices.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                <x-text-input
                    wire:model="password"
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="{{ __('Password') }}"
                    autocomplete="current-password"
                />

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button class="ms-3">
                    {{ __('Log Out Other Browser Sessions') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>
</section>
