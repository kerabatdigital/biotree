<?php

namespace App\Livewire\Admin;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Subscriptions')]
class BillingSubscriptions extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'tailwind';

    public string $search = '';
    public string $statusFilter = 'all';
    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 25;

    public bool $showActionModal = false;
    public ?int $actionSubscriptionId = null;
    public string $actionType = '';
    public ?int $actionPlanId = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    protected function getSubscriptionsQuery()
    {
        $query = Subscription::with(['user', 'plan']);

        // Search by user name, email, or profile username
        if ($this->search) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhereHas('profile', fn($pq) => $pq->where('username', 'like', "%{$this->search}%"));
            });
        }

        // Filter by status
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        // Sort
        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query;
    }

    public function getSubscriptionsProperty()
    {
        return $this->getSubscriptionsQuery()->paginate($this->perPage);
    }

    public function openActionModal(int $subscriptionId, string $actionType): void
    {
        $this->actionSubscriptionId = $subscriptionId;
        $this->actionType = $actionType;
        $this->actionPlanId = null;
        $this->showActionModal = true;
    }

    public function closeActionModal(): void
    {
        $this->reset(['showActionModal', 'actionSubscriptionId', 'actionType', 'actionPlanId']);
    }

    public function performAction(): void
    {
        $subscription = Subscription::findOrFail($this->actionSubscriptionId);

        match ($this->actionType) {
            'upgrade' => $this->upgradeSubscription($subscription),
            'downgrade' => $this->downgradeSubscription($subscription),
            'cancel' => $this->cancelSubscription($subscription),
            default => throw new \InvalidArgumentException("Unknown action: {$this->actionType}"),
        };

        $this->closeActionModal();
    }

    protected function upgradeSubscription(Subscription $subscription): void
    {
        if (!$this->actionPlanId) {
            $this->addError('actionPlanId', 'Please select a plan');
            return;
        }

        $plan = Plan::findOrFail($this->actionPlanId);
        $subscription->update(['plan_id' => $plan->id]);

        session()->flash('message', "Upgraded {$subscription->user->name} to {$plan->name}");
    }

    protected function downgradeSubscription(Subscription $subscription): void
    {
        if (!$this->actionPlanId) {
            $this->addError('actionPlanId', 'Please select a plan');
            return;
        }

        $plan = Plan::findOrFail($this->actionPlanId);
        $subscription->update(['plan_id' => $plan->id]);

        session()->flash('message', "Downgraded {$subscription->user->name} to {$plan->name}");
    }

    protected function cancelSubscription(Subscription $subscription): void
    {
        $subscription->update(['status' => 'cancelled']);

        session()->flash('message', "Cancelled subscription for {$subscription->user->name}");
    }

    public function render()
    {
        return view('livewire.admin.billing-subscriptions', [
            'subscriptions' => $this->subscriptions,
            'plans' => Plan::active()->get(),
        ]);
    }
}
