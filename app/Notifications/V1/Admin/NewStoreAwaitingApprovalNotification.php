<?php

namespace App\Notifications\V1\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewStoreAwaitingApprovalNotification extends Notification 
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
public $store;

    public function __construct($store)
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
            ->subject('New Store Pending Approval')
            ->line("A new store \"{$this->store->store_name}\" has been created by a vendor.")
            ->line("Please review the store and either approve or decline it.")
            ->action('Review Store', url("/admin/stores/{$this->store->slug}"))
            ->line('Thank you.');
    }
}
