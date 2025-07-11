<?php

namespace App\Mail\V1\Admin;

use App\Models\Store;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Content;

class NewStoreAwaitingApprovalMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Store $store) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Store Pending Approval'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin.new_store_pending',
            with: [
                'store' => $this->store,
                'storeOwner' => $this->store->user->first_name ?? 'Vendor',
                'reviewUrl' => url("/admin/stores/{$this->store->slug}")
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
