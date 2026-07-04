<?php

namespace App\Livewire\Onboarding;

use App\Models\Profile;
use App\Rules\NotReservedUsername;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
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
        $this->validateOnly('username');
    }

    public function claim(): void
    {
        $data = $this->validate();

        auth()->user()->profile()->create([
            'username' => $data['username'],
            'display_name' => $data['display_name'],
            'is_published' => true,
        ]);

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

        $reserved = array_map('strtolower', config('biotree.reserved_usernames', []));
        $candidate = $base;
        $i = 0;

        while (in_array($candidate, $reserved, true) || Profile::where('username', $candidate)->exists()) {
            $candidate = $base.(++$i);
        }

        return $candidate;
    }
}
