<?php

namespace Tests\Feature;

use App\Livewire\Onboarding\ClaimUsername;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OnboardingTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_without_a_profile_is_redirected_to_onboarding(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertRedirect(route('onboarding'));
    }

    public function test_a_user_can_claim_a_username(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(ClaimUsername::class)
            ->set('display_name', 'Jane Doe')
            ->set('username', 'janedoe')
            ->call('claim')
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'username' => 'janedoe',
            'display_name' => 'Jane Doe',
        ]);
    }

    public function test_reserved_usernames_are_rejected(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(ClaimUsername::class)
            ->set('display_name', 'Nope')
            ->set('username', 'admin')
            ->call('claim')
            ->assertHasErrors('username');

        $this->assertDatabaseCount('profiles', 0);
    }

    public function test_duplicate_usernames_are_rejected(): void
    {
        $taken = User::factory()->create();
        $taken->profile()->create(['username' => 'taken', 'display_name' => 'Taken']);

        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(ClaimUsername::class)
            ->set('display_name', 'Someone')
            ->set('username', 'taken')
            ->call('claim')
            ->assertHasErrors('username');
    }

    public function test_invalid_username_characters_are_rejected(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(ClaimUsername::class)
            ->set('display_name', 'Someone')
            ->set('username', 'Bad Name!')
            ->call('claim')
            ->assertHasErrors('username');
    }

    public function test_a_username_is_suggested_on_mount(): void
    {
        $user = User::factory()->create(['email' => 'coolcreator@example.com']);
        $this->actingAs($user);

        Livewire::test(ClaimUsername::class)
            ->assertSet('username', 'coolcreator');
    }
}
