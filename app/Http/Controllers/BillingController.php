<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Notifications\SubscriptionCreatedNotification;
use App\Services\PaymentGatewayFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BillingController extends Controller
{
    /**
     * Show upgrade page with all active plans.
     */
    public function upgrade()
    {
        $user = auth()->user();
        $subscription = $user->subscription;

        return view('billing.upgrade', [
            'plans' => Plan::active()->get(),
            'currentSubscription' => $subscription,
            'isPro' => $user->isPro(),
        ]);
    }

    /**
     * Create checkout session and redirect to payment gateway.
     */
    public function checkout(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'billing_period' => 'required|in:monthly,yearly',
            'coupon_code' => 'nullable|string',
        ]);

        $plan = Plan::findOrFail($data['plan_id']);

        // Determine price based on billing period
        $billingPeriod = $data['billing_period'];
        $priceKey = $billingPeriod === 'monthly' ? 'monthly_price_cents' : 'yearly_price_cents';
        $amountCents = $plan->{$priceKey};

        if (!$amountCents) {
            return back()->withErrors(["billing_period" => "This plan is not available for {$billingPeriod} billing"]);
        }

        // Apply coupon if provided
        $coupon = null;
        $discountCents = 0;
        if ($data['coupon_code']) {
            $coupon = Coupon::where('code', $data['coupon_code'])->first();

            if (!$coupon || !$coupon->isValid()) {
                return back()->withErrors(['coupon_code' => 'Coupon code is invalid or expired']);
            }

            $discountCents = $coupon->calculateDiscount($amountCents);
            $amountCents -= $discountCents;
        }

        // Create local pending subscription
        $externalRef = 'BT-' . $user->id . '-' . Str::random(12);

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'pending', // Temporarily set to pending, will be 'active' after payment
            'started_at' => now(),
            'expires_at' => $billingPeriod === 'monthly' ? now()->addMonth() : now()->addYear(),
            'auto_renew' => true,
            'payment_gateway' => 'toyyibpay',
        ]);

        // Create payment record
        $payment = Payment::create([
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'plan_id' => $plan->id,
            'amount' => $amountCents,
            'status' => 'pending',
        ]);

        try {
            // Get the payment gateway
            $gateway = PaymentGatewayFactory::resolve('toyyibpay');

            // Create payment session with gateway
            $checkoutUrl = $gateway->createPaymentSession(
                externalReference: $externalRef,
                amountCents: $amountCents,
                description: "{$plan->name} ({$billingPeriod})",
                returnUrl: route('billing.return'),
                callbackUrl: route('billing.callback'),
            );

            // Store gateway reference in payment (will be populated by callback)
            $payment->update(['raw_payload' => ['external_ref' => $externalRef]]);

            return redirect($checkoutUrl);
        } catch (\Exception $e) {
            $subscription->delete();
            $payment->delete();

            \Log::error('Billing checkout failed', ['error' => $e->getMessage(), 'user_id' => $user->id]);
            return back()->withErrors(['error' => 'Failed to initiate payment. Please try again.']);
        }
    }

    /**
     * Return from payment gateway (browser redirect, non-authoritative).
     */
    public function returnFromPayment(Request $request)
    {
        return view('billing.return', [
            'message' => 'Your payment is being processed. You will be notified once confirmed.',
        ]);
    }

    /**
     * Payment gateway callback webhook (authoritative fulfillment trigger).
     */
    public function callback(Request $request)
    {
        $callbackData = $request->all();

        // Verify callback authenticity
        $gateway = PaymentGatewayFactory::resolve('toyyibpay');
        if (!$gateway->verifyCallback($callbackData)) {
            \Log::warning('Invalid callback signature', ['callback' => $callbackData]);
            return response('Invalid signature', 401);
        }

        $status = $callbackData['status'] ?? null;
        $orderId = $callbackData['order_id'] ?? null;

        // Find payment by external reference
        $payment = Payment::whereJsonContains('raw_payload->external_ref', $orderId)->first();

        if (!$payment) {
            \Log::warning('Callback for unknown payment', ['order_id' => $orderId]);
            return response('Payment not found', 404);
        }

        $subscription = $payment->subscription;

        // Store callback payload
        $payment->raw_payload = $callbackData;
        $payment->save();

        // Status 1 = successful, 2 = pending, 3 = failed
        if ($status === '1') {
            try {
                // Verify payment via API
                $txn = $gateway->getTransaction($callbackData['refno'] ?? '');

                if ($txn && ($txn['billpaymentAmount'] ?? 0) == $payment->amount) {
                    $this->fulfillPayment($subscription, $payment);
                } else {
                    \Log::warning('Callback amount mismatch', ['payment_id' => $payment->id]);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to verify transaction', ['payment_id' => $payment->id, 'error' => $e->getMessage()]);
            }
        } elseif ($status === '3') {
            $payment->update(['status' => 'failed']);
            $subscription->update(['status' => 'expired']);
        }

        return response('OK', 200);
    }

    /**
     * Fulfill payment: activate subscription and mark payment complete.
     */
    private function fulfillPayment(Subscription $subscription, Payment $payment): void
    {
        // Idempotency: don't re-process if already paid
        if ($payment->status === 'paid') {
            return;
        }

        $payment->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $subscription->update(['status' => 'active']);

        // Update user plan field for legacy compatibility
        $subscription->user->update(['plan' => 'pro']);

        // Send subscription created notification
        $subscription->user->notify(new SubscriptionCreatedNotification($subscription));
    }

    /**
     * Show subscription dashboard: current plan and payment history.
     */
    public function dashboard()
    {
        $user = auth()->user();
        $subscription = $user->subscription;

        return view('billing.dashboard', [
            'subscription' => $subscription,
            'payments' => $user->payments()->latest()->paginate(10),
        ]);
    }
}
