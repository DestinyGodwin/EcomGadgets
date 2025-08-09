<?php

namespace App\Notifications\V1\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Mail\V1\Admin\NewStoreAwaitingApprovalMail;

class NewStoreAwaitingApprovalNotification extends Notification implements ShouldQueue
{
    use Queueable;

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
        $subject = 'New Store Pending Approval';
        $messageText = "A new store \"{$this->store->store_name}\" has been created.\n\n"
            . "Please review the store and either approve or decline it.\n\n"
            . "Review here: " . url("/admin/stores/{$this->store->slug}");

        return new NewStoreAwaitingApprovalMail($subject, $messageText);
    }
}
