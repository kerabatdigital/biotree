<?php

namespace App\Livewire\App;

use App\Models\Plan;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Upgrade to Pro')]
class BillingUpgrade extends Component
{
    public ?int $selectedPlanId = null;
    public string $billingPeriod = 'monthly';
    public string $couponCode = '';
    public bool $processingCheckout = false;

    protected function rules(): array
    {
        return [
            'selectedPlanId' => ['required', 'exists:plans,id'],
            'billingPeriod' => ['required', 'in:monthly,yearly'],
            'couponCode' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function selectPlan(int $planId, string $period = 'monthly'): void
    {
        $this->selectedPlanId = $planId;
        $this->billingPeriod = $period;
    }

    public function proceedToCheckout()
    {
        $this->validate();

        $this->processingCheckout = true;

        $checkoutUrl = route('billing.checkout', [
            'plan_id' => $this->selectedPlanId,
            'billing_period' => $this->billingPeriod,
            'coupon_code' => $this->couponCode ?: null,
        ]);

        return $this->redirect($checkoutUrl, navigate: true);
    }

    public function render()
    {
        return view('livewire.app.billing-upgrade', [
            'plans' => Plan::active()->get(),
            'currentSubscription' => auth()->user()->subscription,
        ]);
    }
}
