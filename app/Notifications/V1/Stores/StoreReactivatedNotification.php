<?php

namespace App\Notifications\V1\Stores;

use App\Models\Store;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class StoreReactivatedNotification extends Notification implemnts ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
   public function __construct(protected Store $store,protected string $message ) {}

    public function via($notifiable)
    {
        return ['mail'];
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


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
