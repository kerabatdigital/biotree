<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function profileWithLinks(): Profile
    {
        $user = User::factory()->create();
        $profile = $user->profile()->create([
            'username' => 'creator', 'display_name' => 'Cool Creator', 'is_published' => true,
        ]);
        $profile->links()->create(['type' => 'link', 'title' => 'My Site', 'url' => 'https://example.com', 'icon' => 'globe', 'sort_order' => 0, 'is_active' => true]);
        $profile->links()->create(['type' => 'link', 'title' => 'Hidden Link', 'url' => 'https://hidden.test', 'sort_order' => 1, 'is_active' => false]);

        return $profile;
    }

    public function test_public_page_renders_for_a_published_profile(): void
    {
        $this->profileWithLinks();

        $this->get('/creator')
            ->assertOk()
            ->assertSee('Cool Creator')
            ->assertSee('My Site')
            ->assertDontSee('Hidden Link'); // inactive links are not shown
    }

    public function test_unknown_username_returns_404(): void
    {
        $this->get('/nobody')->assertNotFound();
    }

    public function test_unpublished_profile_returns_404(): void
    {
        $user = User::factory()->create();
        $user->profile()->create(['username' => 'draft', 'is_published' => false]);

        $this->get('/draft')->assertNotFound();
    }

    public function test_clicking_a_link_redirects_and_records_a_click(): void
    {
        $profile = $this->profileWithLinks();
        $link = $profile->links()->where('title', 'My Site')->first();

        $this->get("/out/{$link->ulid}")->assertRedirect('https://example.com');

        $this->assertDatabaseHas('link_clicks', [
            'link_id' => $link->id,
            'profile_id' => $profile->id,
        ]);
        $this->assertSame(1, (int) $link->fresh()->clicks_count);
    }

    public function test_out_route_404s_when_the_link_has_no_url(): void
    {
        $profile = $this->profileWithLinks();
        $header = $profile->links()->create(['type' => 'header', 'title' => 'Section', 'sort_order' => 5]);

        $this->get("/out/{$header->ulid}")->assertNotFound();
    }

    public function test_view_beacon_records_a_page_view(): void
    {
        $profile = $this->profileWithLinks();

        $this->post('/track/view', ['profile' => $profile->id])->assertNoContent();

        $this->assertDatabaseHas('page_views', ['profile_id' => $profile->id]);
    }

    public function test_view_beacon_ignores_unpublished_profiles(): void
    {
        $user = User::factory()->create();
        $profile = $user->profile()->create(['username' => 'draft', 'is_published' => false]);

        $this->post('/track/view', ['profile' => $profile->id])->assertNoContent();

        $this->assertDatabaseCount('page_views', 0);
    }

    public function test_app_routes_take_precedence_over_the_username_catch_all(): void
    {
        // /login must resolve to the auth route, not the {username} catch-all.
        $this->get('/login')->assertOk()->assertSee('Log in');
    }
}
