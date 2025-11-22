<?php

namespace App\Mail\V1\Products;

use App\Models\ProductRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProductRequestedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public ProductRequest $productRequest,
        public string $recipientName
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Product Request from a User'
        );
    }

    public function content(): Content
    {
        $frontendUrl = config('frontend.url');
        $ctaUrl = "{$frontendUrl}/product-requests/{$this->productRequest->id}";

        return new Content(
            view: 'emails.products.product_requested',
            with: [
                'recipientName' => $this->recipientName,
                'productName' => $this->productRequest->name,
                'description' => $this->productRequest->description ?? 'N/A',
                'ctaUrl' => $ctaUrl,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
