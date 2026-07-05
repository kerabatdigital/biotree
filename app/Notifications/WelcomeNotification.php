<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification
{
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $username = $notifiable->profile?->username;

        return (new MailMessage)
            ->subject('Welcome to BioTree')
            ->greeting('Hi '.($notifiable->name ?: 'there').'!')
            ->line($username
                ? "You've claimed your link: biotree.my/{$username}."
                : "You're all set up on BioTree.")
            ->line('Add your links and customize your page to get started.')
            ->action('Go to your dashboard', route('dashboard'));
    }
}
