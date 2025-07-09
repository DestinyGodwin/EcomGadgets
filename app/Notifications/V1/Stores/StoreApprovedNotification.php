<?php

namespace App\Notifications\V1\Stores;

use App\Models\Store;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class StoreApprovedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public $store;

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
            ->subject('Your Store Has Been Approved')
            ->greeting("Hello {$notifiable->name},")
            ->line("Your store \"{$this->store->store_name}\" has been reviewed and approved.")
            ->action('Visit Your Store', url("/vendor/store/{$this->store->id}"))
            ->line('Thank you for using our platform!');
    }
}
