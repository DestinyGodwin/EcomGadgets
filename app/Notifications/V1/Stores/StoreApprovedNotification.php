<?php

namespace App\Notifications\V1\Stores;

use App\Models\Store;
use Illuminate\Bus\Queueable;
use App\Mail\V1\Stores\StoreApprovedMail;
use Illuminate\Notifications\Notification;
use NotificationChannels\Expo\ExpoMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

class StoreApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Store $store)
    {}

    public function via($notifiable): array
    {
        return ['mail', 'database', 'expo'];
    }

    public function toMail($notifiable)
    {
        return (new StoreApprovedMail($this->store))->to($notifiable->email);
    }

    public function toDatabase($notifiable): array
    {
        return [
            'store_id'   => $this->store->id,
            'store_name' => $this->store->store_name,
            'slug'       => $this->store->slug,
            'message'    => "Your store \"{$this->store->store_name}\" has been approved.",
            'url'        => url("/store/{$this->store->slug}"),
        ];
    }

    // public function toFcm($notifiable): array
    // {
    //     $frontendUrl = config('frontend.url');
    //     $storeUrl = "{$frontendUrl}/store/{$this->store->slug}";

    //     return [
    //         'to' => $notifiable->routeNotificationForFcm(), 
    //         'notification' => [
    //             'title' => 'Store Approved',
    //             'body'  => "Your store \"{$this->store->store_name}\" has been approved.",
    //         ],
    //         'data' => [
    //             'store_id'   => $this->store->id,
    //             'store_name' => $this->store->store_name,
    //             'slug'       => $this->store->slug,
    //             'type'       => 'store_approved',
    //             'url'        => $storeUrl,
    //         ],
    //     ];
    // }

    public function toExpo($notifiable): ExpoMessage
    {
        $url = config('frontend.url') . "/store/{$this->store->slug}";

        return ExpoMessage::create('Store Approved')
            ->body("Your store \"{$this->store->store_name}\" has been approved.")
            ->data([
                'type'     => 'store_approved',
                'store_id' => $this->store->id,
                'slug'     => $this->store->slug,
                'url'      => $url,
            ])
            ->priority('high')
            ->playSound();
    }
}
