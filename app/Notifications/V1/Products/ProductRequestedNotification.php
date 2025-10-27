<?php

namespace App\Notifications\V1\Products;

use App\Models\ProductRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProductRequestedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected ProductRequest $productRequest) {}

    public function via($notifiable): array
    {
        return ['mail', 'database', 'fcm'];
    }

    public function toMail($notifiable)
    {
        $frontendUrl = config('frontend.url');
        $productUrl  = "{$frontendUrl}/product-requests/{$this->productRequest->id}";

        return (new MailMessage)
            ->subject('New Product Request from a User')
            ->greeting("Hello {$notifiable->first_name},")
            ->line("A user requested the product \"{$this->productRequest->name}\".")
            ->line("Description: " . ($this->productRequest->description ?? 'N/A'))
            ->action('View Product Request', $productUrl)
            ->line('You may have this item available in your store.');
    }

    public function toDatabase($notifiable): array
    {
        $frontendUrl = config('frontend.url');
        $productUrl  = "{$frontendUrl}/product-requests/{$this->productRequest->id}";

        return [
            'product_request_id' => $this->productRequest->id,
            'name'               => $this->productRequest->name,
            'description'        => $this->productRequest->description,
            'message'            => "A user requested the product \"{$this->productRequest->name}\".",
            'url'                => $productUrl,
        ];
    }

    public function toFcm($notifiable): array
    {
        $frontendUrl = config('frontend.url');
        $productUrl  = "{$frontendUrl}/product-requests/{$this->productRequest->id}";

        return [
            'to' => $notifiable->routeNotificationForFcm(),
            'notification' => [
                'title' => 'New Product Request',
                'body'  => "A user requested the product \"{$this->productRequest->name}\".",
            ],
            'data' => [
                'product_request_id' => $this->productRequest->id,
                'name'               => $this->productRequest->name,
                'description'        => $this->productRequest->description,
                'type'               => 'product_request',
                'url'                => $productUrl,
            ],
        ];
    }
}
