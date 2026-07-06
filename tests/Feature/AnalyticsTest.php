<?php

namespace Tests\Feature;

use App\Livewire\App\Analytics;
use App\Models\LinkClick;
use App\Models\PageView;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AnalyticsTest extends TestCase
{
    use RefreshDatabase;

    protected function onboardedUser(): User
    {
        $user = User::factory()->create();
        $user->profile()->create(['username' => 'creator', 'display_name' => 'Creator']);

        return $user;
    }

    public function test_dashboard_requires_onboarding(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/dashboard')->assertRedirect(route('onboarding'));
    }

    public function test_dashboard_renders(): void
    {
        $user = $this->onboardedUser();

        $this->actingAs($user)->get('/dashboard')->assertOk()->assertSeeLivewire(Analytics::class);
    }

    public function test_it_aggregates_views_clicks_and_breakdowns(): void
    {
        $user = $this->onboardedUser();
        $profile = $user->profile;
        $link = $profile->links()->create(['type' => 'link', 'title' => 'My Site', 'url' => 'https://s.test', 'sort_order' => 0]);

        PageView::insert([
            ['profile_id' => $profile->id, 'visitor_hash' => 'a', 'device' => 'mobile', 'country' => 'MY', 'referrer_host' => null, 'created_at' => now()],
            ['profile_id' => $profile->id, 'visitor_hash' => 'a', 'device' => 'mobile', 'country' => 'MY', 'referrer_host' => null, 'created_at' => now()],
            ['profile_id' => $profile->id, 'visitor_hash' => 'b', 'device' => 'desktop', 'country' => 'SG', 'referrer_host' => 'google.com', 'created_at' => now()],
            ['profile_id' => $profile->id, 'visitor_hash' => 'c', 'device' => 'desktop', 'country' => 'MY', 'referrer_host' => null, 'created_at' => now()],
        ]);
        LinkClick::insert([
            ['link_id' => $link->id, 'profile_id' => $profile->id, 'device' => 'mobile', 'created_at' => now()],
            ['link_id' => $link->id, 'profile_id' => $profile->id, 'device' => 'mobile', 'created_at' => now()],
        ]);

        $component = Livewire::actingAs($user)->test(Analytics::class);

        // The dashboard is #[Lazy]; hydrate past the skeleton by firing the
        // __lazyLoad call with the encoded param embedded in the placeholder.
        preg_match('/__lazyLoad\((?:&#039;|\')(.+?)(?:&#039;|\')\)/', $component->html(), $m);

        $component->call('__lazyLoad', $m[1])
            ->assertSee('My Site')      // top link title
            ->assertSee('50%')          // CTR: 2 clicks / 4 views
            ->assertSee('MY')           // top country
            ->assertSee('google.com');  // referrer
    }

    public function test_the_range_can_be_switched(): void
    {
        $user = $this->onboardedUser();

        Livewire::actingAs($user)->test(Analytics::class)
            ->assertSet('range', 28)
            ->call('setRange', 7)
            ->assertSet('range', 7);
    }
}
