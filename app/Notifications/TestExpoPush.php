<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use NotificationChannels\Expo\ExpoMessage;

class TestExpoPush extends Notification implements ShouldQueue
{
    use Queueable;

    public function via($notifiable): array
    {
        return ['expo'];
    }

    public function toExpo($notifiable): ExpoMessage
    {
        Log::info('Sending Expo push', [
            'user_id' => $notifiable->id,
            'tokens'  => $notifiable->routeNotificationForExpo(),
        ]);

        return ExpoMessage::create('Test')
            ->body('hello')
            ->priority('high')
            ->playSound();
    }
}
