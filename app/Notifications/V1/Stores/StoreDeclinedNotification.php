<?php

namespace App\Notifications\V1\Stores;

use App\Models\Store;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StoreDeclinedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Store $store,
        protected string $reason
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database', 'fcm'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Store Registration Was Declined')
            ->greeting("Hello {$notifiable->first_name},")
            ->line("Your store \"{$this->store->store_name}\" was declined.")
            ->line('Reason: ' . $this->reason)
            ->action('Resubmit Your Store', url("/store/edit/{$this->store->slug}"))
            ->line('Please update your store details and resubmit for review.');
    }

    public function toDatabase($notifiable): array
    {
        return [
            'store_id'   => $this->store->id,
            'store_name' => $this->store->store_name,
            'slug'       => $this->store->slug,
            'reason'     => $this->reason,
            'message'    => "Your store \"{$this->store->store_name}\" was declined.",
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
                'title' => 'Store Registration Declined',
                'body'  => "Your store \"{$this->store->store_name}\" was declined. Reason: {$this->reason}",
            ],
            'data' => [
                'store_id'   => $this->store->id,
                'store_name' => $this->store->store_name,
                'slug'       => $this->store->slug,
                'reason'     => $this->reason,
                'type'       => 'store_declined',
            ],
        ];
    }
}
