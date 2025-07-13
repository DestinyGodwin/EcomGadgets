<?php


namespace App\Mail\V1\Admin;

use App\Models\StoreUpdateRequest;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class StoreEditAwaitingApprovalMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public StoreUpdateRequest $updateRequest) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Store Update Pending Approval'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.shared.store_notification',
            with: [
                'title'      => 'Store Update Pending Approval',
                'store'      => $this->updateRequest->store,
                'storeOwner'=> $this->updateRequest->store->user->first_name ?? 'Vendor',
                'reviewUrl'  => url("/admin/store-update-requests/{$this->updateRequest->id}"),
                'body'       => "A store update request has been submitted by the vendor. Please review the changes and take necessary action.",
                'actionText' => 'Review Store Update',
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
