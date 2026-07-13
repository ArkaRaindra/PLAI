<?php

namespace App\Notifications\Channels;

use App\Models\Notification as AppNotification;
use Illuminate\Notifications\Notification;

class InAppChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        if(! method_exists($notification, 'toInApp')) {
            return;
        }

        $payload = $notification->toInApp($notifiable);

        AppNotification::query()->create([
            'user_id' => $notifiable->getKey(),
            'title' => $payload['title'],
            'message' => $payload['message'],
            'action_url' => $payload['action_url'] ?? null
        ]);
    }
}
