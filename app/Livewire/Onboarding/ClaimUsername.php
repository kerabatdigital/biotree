<?php

namespace App\Livewire\Onboarding;

use App\Models\Profile;
use App\Notifications\WelcomeNotification;
use App\Rules\NotReservedUsername;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Claim your link')]
class ClaimUsername extends Component
{
    public string $username = '';

    public string $display_name = '';

    public function mount(): void
    {
        // Already onboarded — nothing to do here.
        if (auth()->user()->hasCompletedOnboarding()) {
            $this->redirectRoute('dashboard', navigate: true);

            return;
        }

        $this->display_name = auth()->user()->name ?? '';
        $this->username = $this->suggestUsername();
    }

    protected function rules(): array
    {
        return [
            'username' => [
                'required', 'string',
                'min:'.config('biotree.username.min', 3),
                'max:'.config('biotree.username.max', 30),
                'regex:/^[a-z0-9_]+$/',
                new NotReservedUsername,
                Rule::unique('profiles', 'username'),
            ],
            'display_name' => ['required', 'string', 'max:60'],
        ];
    }

    protected function messages(): array
    {
        return [
            'username.regex' => 'Use only lowercase letters, numbers and underscores.',
            'username.unique' => 'That username is already taken.',
        ];
    }

    public function updatedUsername(): void
    {
        $this->username = Str::lower(trim($this->username));
        // Live feedback is driven by the usernameStatus computed property below,
        // so we don't run validateOnly() here (it would render premium/reserved
        // handles as hard red errors instead of the friendly "buy from admin" hint).
    }

    /**
     * Live availability state for the username field.
     * Returns: null | 'short' | 'invalid' | 'reserved' | 'premium' | 'taken' | 'available'
     */
    #[Computed]
    public function usernameStatus(): ?string
    {
        $u = strtolower(trim($this->username));

        if ($u === '') {
            return null;
        }

        if (! preg_match('/^[a-z0-9_]+$/', $u)) {
            return 'invalid';
        }

        if (strlen($u) < config('biotree.username.min', 3)) {
            return 'short';
        }

        if (in_array($u, array_map('strtolower', config('biotree.reserved_usernames', [])), true)) {
            return 'reserved';
        }

        if (in_array($u, array_map('strtolower', config('biotree.premium_usernames', [])), true)) {
            return 'premium';
        }

        if (Profile::where('username', $u)->exists()) {
            return 'taken';
        }

        return 'available';
    }

    /**
     * Contact address for buying a premium handle.
     */
    #[Computed]
    public function premiumContact(): ?string
    {
        return config('biotree.premium_username_contact');
    }

    public function claim(): void
    {
        $data = $this->validate();

        auth()->user()->profile()->create([
            'username' => $data['username'],
            'display_name' => $data['display_name'],
            'is_published' => true,
        ]);

        auth()->user()->notify(new WelcomeNotification);

        $this->redirectRoute('dashboard', navigate: true);
    }

    public function render()
    {
        return view('livewire.onboarding.claim-username');
    }

    /**
     * Suggest an available username from the user's email local-part.
     */
    protected function suggestUsername(): string
    {
        $base = Str::of(auth()->user()->email)
            ->before('@')
            ->lower()
            ->replaceMatches('/[^a-z0-9_]/', '')
            ->toString();

        $base = $base !== '' ? substr($base, 0, 20) : 'user';

        $blocked = array_map('strtolower', array_merge(
            config('biotree.reserved_usernames', []),
            config('biotree.premium_usernames', []),
        ));
        $candidate = $base;
        $i = 0;

        while (in_array($candidate, $blocked, true) || Profile::where('username', $candidate)->exists()) {
            $candidate = $base.(++$i);
        }

        return $candidate;
    }
}
