<?php
namespace App\Mail\V1\Vendor;

use App\Models\Store;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class StoreUpdateDeclinedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Store $store,
        public string $reason
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Store Update Was Declined'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.shared.store_notification',
            with: [
                'title'      => 'Store Update Declined',
                'store'      => $this->store,
                'storeOwner'=> $this->store->user->first_name ?? 'Vendor',
                'reviewUrl'  => url("strapre.com/my-store"),
                'body'       => "Your store update request was declined for the following reason:<br><em>{$this->reason}</em>",
                'actionText' => 'View Store',
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
