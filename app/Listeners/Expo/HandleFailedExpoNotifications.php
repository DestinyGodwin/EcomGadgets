<?php

namespace App\Listeners\Expo;

use Illuminate\Queue\InteractsWithQueue;
use NotificationChannels\Expo\ExpoError;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Events\NotificationFailed;

class HandleFailedExpoNotifications
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
      
    public function handle(NotificationFailed $event): void
    {
        if ($event->channel !== 'expo') {
            return;
        }

        $error = $event->data;

        if (
            ! is_array($error) ||
            ($error['type'] ?? null) !== 'DeviceNotRegistered'
        ) {
            return;
        }

        $event->notifiable
            ->devices()
            ->where('expo_token', (string) $error['token'])
            ->delete();
    }
}
