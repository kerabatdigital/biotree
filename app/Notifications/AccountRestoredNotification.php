<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountRestoredNotification extends Notification
{
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your BioTree account has been restored')
            ->greeting('Hi '.($notifiable->name ?: 'there').',')
            ->line('Your BioTree account access has been restored.')
            ->action('Go to your dashboard', route('dashboard'));
    }
}
