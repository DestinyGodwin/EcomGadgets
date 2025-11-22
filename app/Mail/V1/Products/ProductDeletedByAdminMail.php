<?php

namespace App\Mail\V1\Products;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;


class ProductDeletedByAdminMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Product $product;
    public string $reason;

    public function __construct(Product $product, string $reason)
    {
        $this->product = $product;
        $this->reason = $reason;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Product Removed by Admin'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.products.deleted_by_admin',
            with: [
                'storeOwner' => $this->product->store->user->first_name,
                'productName' => $this->product->name,
                'reason' => $this->reason,
                          ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
