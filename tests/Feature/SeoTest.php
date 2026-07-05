<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_lists_only_published_profiles(): void
    {
        $shown = User::factory()->create();
        $shown->profile()->create(['username' => 'shown', 'is_published' => true]);

        $hidden = User::factory()->create();
        $hidden->profile()->create(['username' => 'hidden', 'is_published' => false]);

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee('/shown')
            ->assertDontSee('/hidden');
    }

    public function test_public_page_is_edge_cacheable_without_a_session_cookie(): void
    {
        $user = User::factory()->create();
        $user->profile()->create(['username' => 'creator', 'display_name' => 'Creator', 'is_published' => true]);

        $response = $this->get('/creator')->assertOk();

        $cacheControl = (string) $response->headers->get('Cache-Control');
        $this->assertStringContainsString('max-age', $cacheControl);
        $this->assertStringContainsString('s-maxage', $cacheControl);

        // No Set-Cookie means Cloudflare will actually cache the page.
        $response->assertCookieMissing(config('session.cookie'));
    }

    public function test_public_page_falls_back_to_the_default_og_image(): void
    {
        $user = User::factory()->create();
        $user->profile()->create(['username' => 'creator', 'display_name' => 'Creator', 'is_published' => true]);

        $this->get('/creator')->assertSee('og-default.png');
    }
}
