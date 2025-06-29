<?php

namespace App\Services\V1\Payments;

use App\Models\Product;
use App\Models\FeaturedProductLog;
use Illuminate\Support\Facades\Log;

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
        $currentExpiry = $product->featured_expires_at;

        $newExpiry = $currentExpiry && $currentExpiry->isFuture()
            ? $currentExpiry->addDays($meta['duration_days'])
            : now()->addDays($meta['duration_days']);

        $product->update([
            'is_featured' => true,
            'featured_expires_at' => $newExpiry,
        ]);

        // Log the feature
        FeaturedProductLog::create([
            'product_id' => $product->id,
            'store_id' => $meta['store_id'],
            'plan_id' => $meta['plan_id'],
            'reference' => $meta['reference'],
            'starts_at' => now(),
            'ends_at' => now()->addDays($meta['duration_days']),
        ]);
    }
}
