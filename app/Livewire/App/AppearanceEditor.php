<?php

namespace App\Livewire\App;

use App\Models\Profile;
use App\Support\ThemeBuilder;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Appearance')]
class AppearanceEditor extends Component
{
    use WithFileUploads;

    public string $display_name = '';
    public string $tagline = '';
    public string $bio = '';
    public $avatar = null;

    // Theme
    public string $preset = 'custom';
    public string $bg = '';
    public string $bg_end = '';
    public string $text = '';
    public string $accent = '';
    public string $button_style = 'soft';
    public string $button_radius = '16px';
    public string $avatar_shape = 'circle';
    public string $font = 'figtree';
    public string $bg_animation = 'none';
    public string $link_animation = 'none';

    public function mount(): void
    {
        $profile = $this->profile();
        $theme = $profile->effectiveTheme();

        $this->display_name = $profile->display_name ?? '';
        $this->tagline = $profile->tagline ?? '';
        $this->bio = $profile->bio ?? '';

        foreach (['preset', 'bg', 'bg_end', 'text', 'accent', 'button_style', 'button_radius', 'avatar_shape', 'font', 'bg_animation', 'link_animation'] as $key) {
            $this->{$key} = (string) ($theme[$key] ?? '');
        }
    }

    protected function profile(): Profile
    {
        return auth()->user()->profile;
    }

    public function applyPreset(string $name): void
    {
        $preset = config("biotree.theme_presets.{$name}");

        if ($preset) {
            $this->preset = $name;
            $this->bg = $preset['bg'] ?? '';
            $this->bg_end = $preset['bg_end'] ?? $preset['bg'] ?? '';
            $this->text = $preset['text'] ?? '';
            $this->accent = $preset['accent'] ?? '';
        }
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['bg', 'bg_end', 'text', 'accent'], true)) {
            $this->preset = 'custom';
        }
    }

    public function removeAvatar(): void
    {
        $profile = $this->profile();

        if ($profile->avatar_path) {
            Storage::disk('public')->delete($profile->avatar_path);
            $profile->update(['avatar_path' => null]);
        }

        $this->avatar = null;
    }

    public function save(): void
    {
        $this->validate();

        $profile = $this->profile();

        if ($this->avatar) {
            if ($profile->avatar_path) {
                Storage::disk('public')->delete($profile->avatar_path);
            }
            $profile->avatar_path = $this->avatar->store('avatars', 'public');
        }

        $profile->display_name = $this->display_name ?: null;
        $profile->tagline = $this->tagline ?: null;
        $profile->bio = $this->bio ?: null;
        $profile->theme = $this->themeInputs();
        $profile->save();

        $this->avatar = null;
        $this->dispatch('saved');
    }

    protected function rules(): array
    {
        return [
            'display_name' => ['nullable', 'string', 'max:60'],
            'tagline' => ['nullable', 'string', 'max:120'],
            'bio' => ['nullable', 'string', 'max:500'],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'bg' => ['required', 'string', 'max:32'],
            'bg_end' => ['required', 'string', 'max:32'],
            'text' => ['required', 'string', 'max:32'],
            'accent' => ['required', 'string', 'max:32'],
            'button_style' => ['required', 'in:soft,solid,outline'],
            'button_radius' => ['required', 'string', 'max:12'],
            'avatar_shape' => ['required', 'in:circle,rounded,square'],
            'font' => ['required', 'in:'.implode(',', array_keys(config('biotree.fonts')))],
            'bg_animation' => ['required', 'in:'.implode(',', array_keys(config('biotree.bg_animations')))],
            'link_animation' => ['required', 'in:'.implode(',', array_keys(config('biotree.link_animations')))],
        ];
    }

    protected function themeInputs(): array
    {
        return [
            'preset' => $this->preset,
            'bg' => $this->bg,
            'bg_end' => $this->bg_end,
            'text' => $this->text,
            'accent' => $this->accent,
            'button_style' => $this->button_style,
            'button_radius' => $this->button_radius,
            'avatar_shape' => $this->avatar_shape,
            'font' => $this->font,
            'bg_animation' => $this->bg_animation,
            'link_animation' => $this->link_animation,
        ];
    }

    public function render()
    {
        $theme = ThemeBuilder::build($this->themeInputs());

        return view('livewire.app.appearance-editor', [
            'presets' => config('biotree.theme_presets'),
            'bgAnimations' => config('biotree.bg_animations'),
            'linkAnimations' => config('biotree.link_animations'),
            'fonts' => config('biotree.fonts'),
            'theme' => $theme,
            'profile' => $this->profile(),
            'links' => $this->profile()->links()->active()->ordered()->get(),
        ]);
    }
}
