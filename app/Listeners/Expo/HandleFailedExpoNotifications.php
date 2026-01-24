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
        public function handle(NotificationFailed $event)
    {
        if ($event->channel !== 'expo') {
            return;
        }

        /** @var ExpoError $error */
        $error = $event->data;

        if ($error->type->isDeviceNotRegistered()) {
            $event->notifiable
                ->devices()
                ->where('expo_token', (string) $error->token)
                ->delete();
        }
    }
}
