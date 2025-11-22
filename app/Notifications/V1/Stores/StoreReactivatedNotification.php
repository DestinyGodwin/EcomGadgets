<?php

namespace App\Notifications\V1\Stores;

use App\Models\Store;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StoreReactivatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Store $store,
        protected string $message
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database', 'fcm'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Store Has Been Reactivated')
            ->greeting("Hello {$notifiable->first_name},")
            ->line("We're glad to let you know that your store \"{$this->store->store_name}\" has been reactivated.")
            ->line('Admin Message: ' . $this->message)
            ->action('Manage Your Store', url("/store/edit/{$this->store->slug}"))
            ->line('Thank you for working with us!');
    }

    public function toDatabase($notifiable): array
    {
        return [
            'store_id'   => $this->store->id,
            'store_name' => $this->store->store_name,
            'slug'       => $this->store->slug,
            'message'    => "Your store \"{$this->store->store_name}\" has been reactivated.",
            'admin_note' => $this->message,
            'url'        => url("/store/edit/{$this->store->slug}"),
        ];
    }

    public function toFcm($notifiable): array
    {
        $frontendUrl = config('frontend.url');
        $editUrl = "{$frontendUrl}/store/edit/{$this->store->slug}";

        return [
            'to' => $notifiable->routeNotificationForFcm(),
            'notification' => [
                'title' => 'Store Reactivated',
                'body'  => "Your store \"{$this->store->store_name}\" has been reactivated.",
            ],
            'data' => [
                'store_id'   => $this->store->id,
                'store_name' => $this->store->store_name,
                'slug'       => $this->store->slug,
                'admin_note' => $this->message,
                'type'       => 'store_reactivated',
                'url'        => $editUrl,
            ],
        ];
    }
}
