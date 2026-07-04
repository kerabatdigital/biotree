<?php

namespace App\Livewire\App;

use App\Models\Link;
use App\Models\Profile;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('My Links')]
class LinkEditor extends Component
{
    public ?int $editingId = null;

    public bool $showForm = false;

    // Form fields
    public string $type = 'link';

    public string $title = '';

    public string $url = '';

    public ?string $icon = null;

    protected function profile(): Profile
    {
        return auth()->user()->profile;
    }

    public function getLinksProperty()
    {
        return $this->profile()->links()->ordered()->get();
    }

    protected function rules(): array
    {
        return [
            'type' => ['required', Rule::in(Link::TYPES)],
            'title' => ['required', 'string', 'max:100'],
            'icon' => ['nullable', 'string', 'max:60'],
            'url' => [
                Rule::requiredIf(fn () => in_array($this->type, ['link', 'social'], true)),
                'nullable', 'url:http,https', 'max:2048',
            ],
        ];
    }

    protected function messages(): array
    {
        return [
            'url.required' => 'A link needs a URL.',
            'url.url' => 'Enter a valid URL (including https://).',
            'title.required' => 'Give it a title.',
        ];
    }

    public function newLink(string $type = 'link'): void
    {
        $this->resetForm();
        $this->type = in_array($type, Link::TYPES, true) ? $type : 'link';
        $this->showForm = true;
    }

    public function editLink(int $id): void
    {
        $link = $this->profile()->links()->findOrFail($id);

        $this->editingId = $link->id;
        $this->type = $link->type;
        $this->title = (string) $link->title;
        $this->url = (string) $link->url;
        $this->icon = $link->icon;
        $this->showForm = true;
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->type === 'header') {
            $data['url'] = null;
        }

        if ($this->editingId) {
            $this->profile()->links()->findOrFail($this->editingId)->update($data);
        } else {
            $data['sort_order'] = ($this->profile()->links()->max('sort_order') ?? -1) + 1;
            $data['is_active'] = true;
            $this->profile()->links()->create($data);
        }

        $this->resetForm();
    }

    public function toggleActive(int $id): void
    {
        $link = $this->profile()->links()->findOrFail($id);
        $link->update(['is_active' => ! $link->is_active]);
    }

    public function deleteLink(int $id): void
    {
        $this->profile()->links()->whereKey($id)->delete();

        if ($this->editingId === $id) {
            $this->resetForm();
        }
    }

    /**
     * Persist a new order. Only reorders links owned by this profile.
     */
    public function reorder(array $ids): void
    {
        $owned = $this->profile()->links()->pluck('id')->all();

        foreach ($ids as $position => $id) {
            if (in_array((int) $id, $owned, true)) {
                Link::whereKey($id)->update(['sort_order' => $position]);
            }
        }
    }

    public function selectIcon(?string $icon): void
    {
        $this->icon = $icon;
    }

    public function cancelForm(): void
    {
        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->reset(['editingId', 'showForm', 'type', 'title', 'url', 'icon']);
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.app.link-editor', [
            'links' => $this->links,
            'iconGroups' => config('biotree.link_icons', []),
            'profileUsername' => $this->profile()->username,
        ]);
    }
}
