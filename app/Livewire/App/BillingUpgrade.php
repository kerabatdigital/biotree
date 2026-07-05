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
    public string $phone = '';
    public bool $processingCheckout = false;

    public function mount(): void
    {
        $this->phone = auth()->user()->phone ?? '';
    }

    protected function rules(): array
    {
        return [
            'selectedPlanId' => ['required', 'exists:plans,id'],
            'billingPeriod' => ['required', 'in:monthly,yearly'],
            'couponCode' => ['nullable', 'string', 'max:50'],
            // ToyyibPay requires a payer phone number to create a bill.
            'phone' => ['required', 'string', 'regex:/^\+?[0-9]{8,15}$/'],
        ];
    }

    public function selectPlan(int $planId): void
    {
        $plan = Plan::findOrFail($planId);

        $this->selectedPlanId = $planId;
        $this->billingPeriod = $plan->monthly_price_cents ? 'monthly' : 'yearly';
    }

    public function proceedToCheckout()
    {
        $this->validate();

        $this->processingCheckout = true;

        // Persist the phone number on the user record (not passed via URL — it's PII).
        // The checkout controller reads it from auth()->user()->phone.
        auth()->user()->update(['phone' => $this->phone]);

        $checkoutUrl = route('billing.checkout', [
            'plan_id' => $this->selectedPlanId,
            'billing_period' => $this->billingPeriod,
            'coupon_code' => $this->couponCode ?: null,
        ]);

        return $this->redirect($checkoutUrl, navigate: true);
    }

    public function render()
    {
        $subscription = auth()->user()->subscription;

        return view('livewire.app.billing-upgrade', [
            'plans' => Plan::active()->get(),
            // Only surface a subscription that's actually in force — pending (abandoned
            // checkout) or expired/cancelled rows shouldn't read as "your current plan".
            'currentSubscription' => $subscription?->status === 'active' ? $subscription : null,
        ]);
    }
}
