<?php

namespace App\Notifications\V1\Products;

use Illuminate\Bus\Queueable;
use App\Models\ProductRequest;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Mail\V1\Products\ProductRequestedMail;
use Illuminate\Notifications\Messages\MailMessage;

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
        return (new ProductRequestedMail($this->productRequest, $notifiable->first_name))
            ->to($notifiable->email);
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
