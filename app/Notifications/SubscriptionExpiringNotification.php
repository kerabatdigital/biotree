<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SubscriptionExpiringNotification extends Notification
{
    use Queueable;

    public function __construct(private Subscription $subscription) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $daysUntilExpiry = $this->subscription->expires_at->diffInDays(now());

        return (new MailMessage)
            ->subject("Your BioTree Pro subscription expires in {$daysUntilExpiry} days")
            ->greeting("Your subscription expires soon")
            ->line("Your BioTree Pro subscription will expire in {$daysUntilExpiry} days.")
            ->line('**Plan**: ' . $this->subscription->plan->name)
            ->line('**Expires**: ' . $this->subscription->expires_at->format('M d, Y'))
            ->line("@if ($this->subscription->auto_renew)We'll automatically attempt to renew your subscription on the expiration date.@else(Auto-renewal is disabled)@endif")
            ->action('Manage Subscription', route('billing.subscriptions'))
            ->line('Thank you for your continued support!');
    }
}
