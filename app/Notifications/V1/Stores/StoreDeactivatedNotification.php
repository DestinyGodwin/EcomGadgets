<?php

namespace App\Notifications\V1\Stores;

use App\Models\Store;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Expo\ExpoMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class StoreDeactivatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Store $store,
        protected string $message
    ) {}

    public function via($notifiable): array
    {
        // return ['mail', 'database', 'fcm'];
        return ['mail', 'database', 'expo'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Store Has Been Deactivated')
            ->greeting("Hello {$notifiable->first_name},")
            ->line("Your store \"{$this->store->store_name}\" has been deactivated.")
            ->line('Reason: ' . $this->message)
            ->line('If you believe this was a mistake or need help, please contact support.')
            ->line('Thank you for your understanding.');
    }

    public function toDatabase($notifiable): array
    {
        return [
            'store_id'   => $this->store->id,
            'store_name' => $this->store->store_name,
            'slug'       => $this->store->slug,
            'message'    => "Your store \"{$this->store->store_name}\" has been deactivated.",
            'reason'     => $this->message,
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
    //             'title' => 'Store Deactivated',
    //             'body'  => "Your store \"{$this->store->store_name}\" has been deactivated. Reason: {$this->message}",
    //         ],
    //         'data' => [
    //             'store_id'   => $this->store->id,
    //             'store_name' => $this->store->store_name,
    //             'slug'       => $this->store->slug,
    //             'reason'     => $this->message,
    //             'type'       => 'store_deactivated',
    //         ],
    //     ];
    // }

    public function toExpo($notifiable): ExpoMessage
{
    $url = config('frontend.url') . "/store/{$this->store->slug}";

    return ExpoMessage::create('Store Deactivated')
        ->body("Your store \"{$this->store->store_name}\" was deactivated. Reason: {$this->message}")
        ->data([
            'type'      => 'store_deactivated',
            'store_id'  => $this->store->id,
            'store_name'=> $this->store->store_name,
            'slug'      => $this->store->slug,
            'reason'    => $this->message,
            'url'       => $url,
        ])
        ->priority('high')
        ->playSound();
}

}
