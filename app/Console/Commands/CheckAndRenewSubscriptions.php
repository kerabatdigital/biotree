<?php

namespace App\Console\Commands;

use App\Jobs\ProcessSubscriptionRenewal;
use App\Models\Subscription;
use Illuminate\Console\Command;

class CheckAndRenewSubscriptions extends Command
{
    protected $signature = 'subscriptions:renew';

    protected $description = 'Check and process subscription renewals';

    public function handle(): int
    {
        // Find subscriptions expiring within the next 24 hours
        $expiringSubscriptions = Subscription::expiring()->get();

        $this->info("Found {$expiringSubscriptions->count()} subscriptions to renew");

        foreach ($expiringSubscriptions as $subscription) {
            ProcessSubscriptionRenewal::dispatch($subscription)->onQueue('default');
        }

        return Command::SUCCESS;
    }
}
