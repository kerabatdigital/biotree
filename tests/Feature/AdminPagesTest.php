<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function admin(): User
    {
        $user = User::factory()->create();
        // role/plan are not mass-assignable; set directly.
        $user->forceFill(['role' => 'admin', 'email_verified_at' => now()])->save();

        return $user;
    }

    public function test_admin_can_view_the_users_page(): void
    {
        $this->actingAs($this->admin())->get('/admin/users')->assertOk();
    }

    public function test_admin_can_view_the_reports_page(): void
    {
        $this->actingAs($this->admin())->get('/admin/reports')->assertOk();
    }

    public function test_admin_can_view_the_dashboard(): void
    {
        $this->actingAs($this->admin())->get('/admin')->assertOk();
    }

    public function test_a_non_admin_cannot_access_admin_pages(): void
    {
        $user = User::factory()->create();
        $user->forceFill(['email_verified_at' => now()])->save();

        $this->actingAs($user)->get('/admin/users')->assertForbidden();
        $this->actingAs($user)->get('/admin/reports')->assertForbidden();
    }
}
