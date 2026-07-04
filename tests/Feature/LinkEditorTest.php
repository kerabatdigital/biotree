<?php

namespace Tests\Feature;

use App\Livewire\App\LinkEditor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LinkEditorTest extends TestCase
{
    use RefreshDatabase;

    protected function onboardedUser(string $username = 'creator'): User
    {
        $user = User::factory()->create();
        $user->profile()->create(['username' => $username, 'display_name' => 'Creator']);

        return $user;
    }

    public function test_links_page_requires_onboarding(): void
    {
        $user = User::factory()->create(); // no profile yet

        $this->actingAs($user)->get('/links')->assertRedirect(route('onboarding'));
    }

    public function test_the_editor_renders_for_an_onboarded_user(): void
    {
        $user = $this->onboardedUser();

        $this->actingAs($user)
            ->get('/links')
            ->assertOk()
            ->assertSeeLivewire(LinkEditor::class);
    }

    public function test_a_user_can_add_a_link(): void
    {
        $user = $this->onboardedUser();

        Livewire::actingAs($user)->test(LinkEditor::class)
            ->call('newLink', 'link')
            ->set('title', 'My Site')
            ->set('url', 'https://example.com')
            ->set('icon', 'globe')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('links', [
            'profile_id' => $user->profile->id,
            'title' => 'My Site',
            'url' => 'https://example.com',
            'icon' => 'globe',
            'type' => 'link',
        ]);
    }

    public function test_a_link_requires_a_valid_url(): void
    {
        $user = $this->onboardedUser();

        Livewire::actingAs($user)->test(LinkEditor::class)
            ->call('newLink', 'link')
            ->set('title', 'Bad')
            ->set('url', 'not-a-url')
            ->call('save')
            ->assertHasErrors('url');
    }

    public function test_a_header_does_not_require_a_url(): void
    {
        $user = $this->onboardedUser();

        Livewire::actingAs($user)->test(LinkEditor::class)
            ->call('newLink', 'header')
            ->set('title', 'Section')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('links', [
            'profile_id' => $user->profile->id,
            'title' => 'Section',
            'type' => 'header',
            'url' => null,
        ]);
    }

    public function test_a_user_can_toggle_and_delete_a_link(): void
    {
        $user = $this->onboardedUser();
        $link = $user->profile->links()->create([
            'type' => 'link', 'title' => 'X', 'url' => 'https://x.com', 'sort_order' => 0,
        ]);

        Livewire::actingAs($user)->test(LinkEditor::class)->call('toggleActive', $link->id);
        $this->assertFalse($link->fresh()->is_active);

        Livewire::actingAs($user)->test(LinkEditor::class)->call('deleteLink', $link->id);
        $this->assertDatabaseMissing('links', ['id' => $link->id]);
    }

    public function test_reorder_persists_the_new_order(): void
    {
        $user = $this->onboardedUser();
        $a = $user->profile->links()->create(['type' => 'link', 'title' => 'A', 'url' => 'https://a.com', 'sort_order' => 0]);
        $b = $user->profile->links()->create(['type' => 'link', 'title' => 'B', 'url' => 'https://b.com', 'sort_order' => 1]);

        Livewire::actingAs($user)->test(LinkEditor::class)->call('reorder', [$b->id, $a->id]);

        $this->assertSame(0, $b->fresh()->sort_order);
        $this->assertSame(1, $a->fresh()->sort_order);
    }

    public function test_a_user_cannot_delete_another_users_link(): void
    {
        $owner = $this->onboardedUser('owner');
        $link = $owner->profile->links()->create(['type' => 'link', 'title' => 'Owned', 'url' => 'https://o.com', 'sort_order' => 0]);

        $intruder = $this->onboardedUser('intruder');

        Livewire::actingAs($intruder)->test(LinkEditor::class)->call('deleteLink', $link->id);

        // The link still belongs to the owner and was not touched.
        $this->assertDatabaseHas('links', ['id' => $link->id]);
    }
}
