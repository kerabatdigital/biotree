<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SubscriptionCancelledNotification extends Notification
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
            ->subject('Subscription Cancelled')
            ->greeting('Your subscription has been cancelled')
            ->line('Your BioTree Pro subscription has been cancelled.')
            ->line('**Plan**: ' . $this->subscription->plan->name)
            ->line('**Cancelled**: ' . now()->format('M d, Y'))
            ->line('You will retain access to your Pro features until ' . $this->subscription->expires_at->format('M d, Y') . '.')
            ->action('Resubscribe', route('billing.upgrade'))
            ->line('We\'d love to have you back anytime!');
    }
}
