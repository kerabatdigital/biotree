<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountSuspendedNotification extends Notification
{
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your BioTree account has been suspended')
            ->greeting('Hi '.($notifiable->name ?: 'there').',')
            ->line('Your BioTree account has been suspended by an administrator.')
            ->line('If you believe this is a mistake, please contact support.');
    }
}
