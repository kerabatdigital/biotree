<?php

namespace App\Jobs;

use App\Models\Payment;
use App\Models\Subscription;
use App\Services\PaymentGatewayFactory;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;

class ProcessSubscriptionRenewal
{
    use Dispatchable, Queueable;

    public function __construct(private Subscription $subscription) {}

    public function handle(): void
    {
        // Skip if not in auto-renew mode
        if (!$this->subscription->auto_renew || $this->subscription->status !== 'active') {
            return;
        }

        // Skip if not yet expired
        if ($this->subscription->expires_at->isFuture()) {
            return;
        }

        $user = $this->subscription->user;
        $plan = $this->subscription->plan;
        $gateway = PaymentGatewayFactory::resolve($this->subscription->payment_gateway);

        // Determine renewal amount (assume monthly/yearly based on expiration pattern)
        // For now, default to monthly
        $amountCents = $plan->monthly_price_cents ?? 0;

        if (!$amountCents) {
            \Log::warning('Cannot renew: plan has no price', ['subscription_id' => $this->subscription->id]);
            return;
        }

        try {
            // Create new payment record
            $externalRef = 'BT-RENEW-' . $this->subscription->id . '-' . now()->timestamp;

            $payment = Payment::create([
                'user_id' => $user->id,
                'subscription_id' => $this->subscription->id,
                'plan_id' => $plan->id,
                'amount' => $amountCents,
                'status' => 'pending',
            ]);

            // Attempt automatic charge (gateway implementation dependent)
            // For now, this is a placeholder — actual recurring billing would be handled
            // via gateway webhooks or scheduled API calls depending on gateway support
            $payment->update(['status' => 'failed']); // Mark as failed since we're not doing real recurring yet

            // Fall back to manual renewal reminder
            dispatch(new SendSubscriptionRenewalReminder($this->subscription))->onQueue('default');
        } catch (\Exception $e) {
            \Log::error('Subscription renewal failed', [
                'subscription_id' => $this->subscription->id,
                'error' => $e->getMessage(),
            ]);

            // Send reminder email on failure
            dispatch(new SendSubscriptionRenewalReminder($this->subscription))->onQueue('default');
        }
    }
}
