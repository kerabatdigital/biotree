<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SubscriptionRenewalFailedNotification extends Notification
{
    use Queueable;

    public function __construct(private Subscription $subscription) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Subscription Renewal Failed')
            ->greeting('Subscription Renewal Failed')
            ->line('We attempted to automatically renew your BioTree Pro subscription, but the payment failed.')
            ->line('**Plan**: ' . $this->subscription->plan->name)
            ->line('**Expiration Date**: ' . $this->subscription->expires_at->format('M d, Y'))
            ->action('Renew Now', route('billing.upgrade'))
            ->line('If you continue without renewing, you will lose access to Pro features.')
            ->line('Contact support if you need help.');
    }
}
