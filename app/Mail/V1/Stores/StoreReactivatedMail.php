<?php

namespace App\Mail\V1\Stores;

use App\Models\Store;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class StoreReactivatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Store $store,
        public string $message
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Store Has Been Reactivated',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.stores.reactivated',
            with: [
                'storeOwner' => $this->store->user->first_name ?? 'Vendor',
                'store' => $this->store,
                'message' => $this->message,
                'ctaUrl' => url("/store/edit/{$this->store->slug}")
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
