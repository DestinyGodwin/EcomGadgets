<?php

namespace App\Services\V1\Payments;

use App\Models\Product;

class FeaturedProductPaymentHandler implements PaymentHandlerInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
      public function handleSuccessfulPayment(array $meta): void
    {
        $product = Product::findOrFail($meta['product_id']);

        // Mark product as featured
        $product->update(['is_featured' => true, 'featured_expires_at' => now()->addDays($meta['duration_days'])]);

        // Log the feature
        FeaturedProductLog::create([
            'product_id' => $product->id,
            'user_id' => $meta['user_id'],
            'plan_id' => $meta['plan_id'],
            'starts_at' => now(),
            'ends_at' => now()->addDays($meta['duration_days']),
        ]);
    }
}
