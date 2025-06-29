<?php

namespace App\Services\V1\Payments;

use Carbon\Carbon;
use App\Models\Product;
use App\Models\FeaturedProductLog;


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

        $currentExpiry = $product->featured_expires_at;

        if ($currentExpiry && !($currentExpiry instanceof Carbon)) {
            $currentExpiry = Carbon::parse($currentExpiry);
        }

        $newExpiry = $currentExpiry && $currentExpiry->isFuture()
            ? $currentExpiry->copy()->addDays($meta['duration_days']) // ← FIXED
            : now()->addDays($meta['duration_days']);

        FeaturedProductLog::create([
            'product_id' => $product->id,
            'store_id' => $meta['store_id'],
            'plan_id' => $meta['plan_id'],
            'reference' => $meta['reference'],
            'starts_at' => $currentExpiry ?? now(),
            'ends_at' => $newExpiry,
        ]);

        $product->update([
            'is_featured' => true,
            'featured_expires_at' => $newExpiry,
        ]);
    }
}
