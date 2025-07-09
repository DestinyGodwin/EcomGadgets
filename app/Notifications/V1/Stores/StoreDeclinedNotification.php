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
  protected $store;

    public function __construct(Store $store)
    {
        $this->store = $store;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Store Registration Was Declined')
            ->greeting("Hello {$notifiable->first_name},")
            ->line("We regret to inform you that your store \"{$this->store->store_name}\" was declined after review.")
            ->line('Please review your details and resubmit the required information for approval.')
            ->action('Resubmit Your Store', url('/store/update')) 
            ->line('If you have any questions, feel free to contact support.');
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
