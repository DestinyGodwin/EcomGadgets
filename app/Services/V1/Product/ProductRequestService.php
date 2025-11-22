<?php

namespace App\Services\V1\Product;

use App\Models\User;
use App\Models\ProductRequest;
use Illuminate\Support\Facades\Notification;
use App\Notifications\V1\Products\ProductRequestedNotification;

class ProductRequestService
{
    /**
     * Create a new class instance.
     */
     public function create(array $data): ProductRequest
    {
        $user = auth()->user();

        if (isset($data['image'])) {
            $data['image'] = $data['image']->store('product_requests', 'public');
        }

        $productRequest = ProductRequest::create([
            'user_id'     => $user->id,
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'image'       => $data['image'] ?? null,
        ]);

        // Notify all admins and vendors
        User::whereIn('role', ['admin', 'vendor'])
            ->where('status', 'active')->where('state_id', $user->state_id)
            ->chunkById(100, function ($recipients) use ($productRequest) {
                Notification::send($recipients, new ProductRequestedNotification($productRequest));
            });

        return $productRequest;
    }
}
