<?php

namespace Tests\Feature;

use App\Livewire\App\AppearanceEditor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class AppearanceEditorTest extends TestCase
{
    use RefreshDatabase;

    protected function onboardedUser(): User
    {
        $user = User::factory()->create();
        $user->profile()->create(['username' => 'creator', 'display_name' => 'Creator']);

        return $user;
    }

    public function test_appearance_page_requires_onboarding(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/appearance')->assertRedirect(route('onboarding'));
    }

    public function test_the_editor_renders(): void
    {
        $user = $this->onboardedUser();

        $this->actingAs($user)
            ->get('/appearance')
            ->assertOk()
            ->assertSeeLivewire(AppearanceEditor::class);
    }

    public function test_applying_a_preset_sets_colours(): void
    {
        $user = $this->onboardedUser();

        Livewire::actingAs($user)->test(AppearanceEditor::class)
            ->call('applyPreset', 'ocean')
            ->assertSet('accent', '#38bdf8')
            ->assertSet('preset', 'ocean');
    }

    public function test_saving_persists_profile_basics_and_theme(): void
    {
        $user = $this->onboardedUser();

        Livewire::actingAs($user)->test(AppearanceEditor::class)
            ->set('display_name', 'New Name')
            ->set('tagline', 'Hello world')
            ->call('applyPreset', 'sunset')
            ->set('button_style', 'solid')
            ->set('font', 'poppins')
            ->call('save')
            ->assertHasNoErrors();

        $profile = $user->profile->fresh();
        $this->assertSame('New Name', $profile->display_name);
        $this->assertSame('Hello world', $profile->tagline);
        $this->assertSame('sunset', $profile->theme['preset']);
        $this->assertSame('solid', $profile->theme['button_style']);
        $this->assertSame('poppins', $profile->theme['font']);

        // publicTheme expands inputs into concrete render values.
        $this->assertArrayHasKey('button_bg', $profile->publicTheme());
    }

    public function test_invalid_button_style_is_rejected(): void
    {
        $user = $this->onboardedUser();

        Livewire::actingAs($user)->test(AppearanceEditor::class)
            ->set('button_style', 'wobble')
            ->call('save')
            ->assertHasErrors('button_style');
    }

    public function test_custom_css_with_html_tags_is_rejected(): void
    {
        $user = $this->onboardedUser();

        Livewire::actingAs($user)->test(AppearanceEditor::class)
            ->set('custom_css', '</style><script>alert(1)</script>')
            ->call('save')
            ->assertHasErrors('custom_css');

        // Plain CSS still saves fine.
        Livewire::actingAs($user)->test(AppearanceEditor::class)
            ->set('custom_css', '.btn { color: red; }')
            ->call('save')
            ->assertHasNoErrors('custom_css');
    }

    public function test_avatar_upload_is_stored(): void
    {
        Storage::fake('public');
        $user = $this->onboardedUser();

        Livewire::actingAs($user)->test(AppearanceEditor::class)
            ->set('avatar', UploadedFile::fake()->image('me.jpg', 400, 400))
            ->call('save')
            ->assertHasNoErrors();

        $profile = $user->profile->fresh();
        $this->assertNotNull($profile->avatar_path);
        Storage::disk('public')->assertExists($profile->avatar_path);
    }
}
