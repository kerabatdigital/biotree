<?php

namespace App\Jobs;

use App\Models\Subscription;
use App\Notifications\SubscriptionRenewalFailedNotification;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;

class SendSubscriptionRenewalReminder
{
    use Dispatchable, Queueable;

    public function __construct(private Subscription $subscription) {}

    public function handle(): void
    {
        $user = $this->subscription->user;
        $user->notify(new SubscriptionRenewalFailedNotification($this->subscription));
    }
}
