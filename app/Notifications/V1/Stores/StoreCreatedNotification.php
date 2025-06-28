<?php

namespace App\Notifications\V1\Stores;

use App\Models\Store;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Config;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class StoreCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
     public function __construct(protected Store $store)
    {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $frontendUrl = Config::get('frontend.url');
        $storeUrl    = $frontendUrl . '/stores/' . $this->store->id;
        return (new MailMessage)
            ->subject('Your Store Has Been Created')
            ->greeting('Hello ' . $notifiable->first_name . ',')
            ->line('Your store "' . $this->store->name . '" has been successfully created.')
            ->action('View Store', $storeUrl)
            ->line('Thank you for using our application!');
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
