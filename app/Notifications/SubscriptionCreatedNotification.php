<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SubscriptionCreatedNotification extends Notification
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
            ->subject('Welcome to BioTree Pro')
            ->greeting('Welcome to BioTree Pro!')
            ->line('Your subscription has been activated successfully.')
            ->line('**Plan**: ' . $this->subscription->plan->name)
            ->line('**Started**: ' . $this->subscription->started_at->format('M d, Y'))
            ->line('**Renews**: ' . $this->subscription->expires_at->format('M d, Y'))
            ->action('View Subscription', route('billing.subscriptions'))
            ->line('Thank you for choosing BioTree!');
    }
}
