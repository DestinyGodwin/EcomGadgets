<?php

namespace App\Notifications\V1\Stores;

use App\Models\Store;
use Illuminate\Bus\Queueable;
use App\Mail\V1\Stores\StoreApprovedMail;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class StoreApprovedNotification extends Notification implements ShouldQueue
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
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
           return (new StoreApprovedMail($this->store))->to($notifiable->email);

    }

       public function toDatabase($notifiable)
    {
        return [
            'store_id' => $this->store->id,
            'store_name' => $this->store->store_name,
            'slug' => $this->store->slug,
            'message' => "Your store \"{$this->store->store_name}\" has been approved.",
            'url' => url("/store/{$this->store->slug}"),
        ];
    }
}
