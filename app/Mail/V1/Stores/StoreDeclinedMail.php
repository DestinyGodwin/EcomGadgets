<?php
namespace App\Mail\V1\Stores;

use App\Models\Store;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StoreDeclinedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Store $store,
        public string $reason
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Store Registration Was Declined',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.stores.declined',
            with: [
                'storeOwner' => $this->store->user->first_name ?? 'Vendor',
                'store'      => $this->store,
                'reason'     => $this->reason,
                'ctaUrl'     => url("/store/edit/{$this->store->slug}"),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
