<?php

namespace App\Mail\V1\Stores;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Store;

class StoreUnderReviewMail extends Mailable
{
    use Queueable, SerializesModels;

    public $store;
    public $storeOwnerName;

    /**
     * Create a new message instance.
     */
    public function __construct(Store $store)
    {
        $this->store = $store;
        $this->storeOwnerName = $store->user->name;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Store is Under Review'
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.stores.under_review',
            with: [
                'storeOwnerName' => $this->storeOwnerName,
                'store' => $this->store,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
