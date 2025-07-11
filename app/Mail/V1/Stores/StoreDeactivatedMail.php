<?php

namespace App\Mail\V1\Stores;

use App\Models\Store;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class StoreDeactivatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Store $store,
        public string $reason
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Store Has Been Deactivated',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.stores.deactivated',
            with: [
                'storeOwner' => $this->store->user->first_name ?? 'Vendor',
                'store' => $this->store,
                'reason' => $this->reason,
                'supportUrl' => url('/support')
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
