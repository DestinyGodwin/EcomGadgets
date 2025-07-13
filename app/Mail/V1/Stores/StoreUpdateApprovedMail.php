<?php
namespace App\Mail\V1\Vendor;

use App\Models\Store;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class StoreUpdateApprovedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Store $store) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Store Update Has Been Approved'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.shared.store_notification',
            with: [
                'title'      => 'Store Update Approved',
                'store'      => $this->store,
                'storeOwner'=> $this->store->user->first_name ?? 'Vendor',
                'reviewUrl'  => url("/vendor/my-store"),
                'body'       => "Your store update request has been approved. The changes have been applied to your store.",
                'actionText' => 'View Store',
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
