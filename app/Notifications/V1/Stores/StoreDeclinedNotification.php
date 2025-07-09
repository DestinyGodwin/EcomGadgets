<?php

namespace App\Notifications\V1\Stores;

use App\Models\Store;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class StoreDeclinedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
  public function __construct(protected Store $store, protected string $reason) {}

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Store Registration Was Declined')
            ->greeting("Hello {$notifiable->first_name},")
            ->line("Your store \"{$this->store->store_name}\" was declined.")
            ->line('Reason: ' . $this->reason)
            ->action('Resubmit Your Store', url("/vendor/store/edit/{$this->store->slug}"))
            ->line('Please update your store details and resubmit for review.');
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
