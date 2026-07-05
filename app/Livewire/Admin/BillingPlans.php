<?php

namespace App\Livewire\Admin;

use App\Models\Plan;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Billing Plans')]
class BillingPlans extends Component
{
    public $plans;

    public bool $showForm = false;
    public ?int $editingId = null;

    public string $name = '';
    public string $slug = '';
    public ?string $description = null;
    public ?int $monthly_price_cents = null;
    public ?int $yearly_price_cents = null;
    public array $features = [];
    public array $limits = [];
    public bool $is_active = true;

    public function mount(): void
    {
        $this->loadPlans();
    }

    protected function loadPlans(): void
    {
        $this->plans = Plan::latest()->get();
    }

    protected function rules(): array
    {
        $uniqueSlug = $this->editingId ? ",{$this->editingId}" : '';

        return [
            'name' => ['required', 'string', 'max:60'],
            'slug' => ['required', 'string', 'max:60', "unique:plans,slug{$uniqueSlug}"],
            'description' => ['nullable', 'string', 'max:500'],
            'monthly_price_cents' => ['nullable', 'integer', 'min:0'],
            'yearly_price_cents' => ['nullable', 'integer', 'min:0'],
            'features' => ['array'],
            'limits' => ['array'],
            'is_active' => ['boolean'],
        ];
    }

    public function newPlan(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function editPlan(int $id): void
    {
        $plan = Plan::findOrFail($id);

        $this->editingId = $plan->id;
        $this->name = $plan->name;
        $this->slug = $plan->slug;
        $this->description = $plan->description;
        $this->monthly_price_cents = $plan->monthly_price_cents;
        $this->yearly_price_cents = $plan->yearly_price_cents;
        $this->features = $plan->features ?? [];
        $this->limits = $plan->limits ?? [];
        $this->is_active = $plan->is_active;
        $this->showForm = true;
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->editingId) {
            $plan = Plan::findOrFail($this->editingId);
            $plan->update($data);
        } else {
            Plan::create($data);
        }

        $this->loadPlans();
        $this->resetForm();
    }

    public function toggleActive(int $id): void
    {
        $plan = Plan::findOrFail($id);
        $plan->update(['is_active' => !$plan->is_active]);
        $this->loadPlans();
    }

    public function deletePlan(int $id): void
    {
        $plan = Plan::findOrFail($id);

        // Don't delete if subscriptions exist
        if ($plan->subscriptions()->exists()) {
            session()->flash('error', 'Cannot delete plan with active subscriptions');
            return;
        }

        $plan->delete();
        $this->loadPlans();
    }

    public function generateSlug(): void
    {
        $this->slug = Str::slug($this->name);
    }

    public function cancelForm(): void
    {
        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->reset(['editingId', 'showForm', 'name', 'slug', 'description', 'monthly_price_cents', 'yearly_price_cents', 'features', 'limits', 'is_active']);
        $this->is_active = true;
    }

    public function render()
    {
        return view('livewire.admin.billing-plans');
    }
}
