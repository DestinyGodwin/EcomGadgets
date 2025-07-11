<?php
namespace App\Services\V1\Product;

use App\Models\Product;
use App\Models\ProductView;
use App\Models\ProductWishlist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ProductAnalyticsService
{
    /**
     * Create a new class instance.
     */
    public function trackView(Product $product): void
    {
        if (! $product->is_featured) {
            return;
        }

        $fingerprint = $this->generateFingerprint();
        $cacheKey    = "viewed:product:{$product->id}:{$fingerprint}";

        if (! Cache::has($cacheKey)) {
       ProductView::create([
    'product_id' => $product->id,
    'user_id'    => Auth::check() ? Auth::id() : null,
    'ip_address' => request()->ip(),
    'user_agent' => request()->userAgent(),
]);

            Cache::put($cacheKey, true, now()->addHour());
        }
    }

    public function addToWishlist(Product $product): ProductWishlist | null
    {
        if (! $product->is_featured) {
            return null;
        }

        return ProductWishlist::firstOrCreate([
            'product_id' => $product->id,
           'user_id'    => Auth::user()->id(),

        ]);
    }

    public function getViewCount(Product $product): int
    {
        return $product->is_featured ? $product->views()->count() : 0;
    }

    public function getWishlistCount(Product $product): int
    {
        return $product->is_featured ? $product->wishlists()->count() : 0;
    }

    private function generateFingerprint(): string
    {
        $deviceId = request()->header('X-Device-ID');
        return sha1(($deviceId ?? request()->ip()) . '|' . request()->userAgent());
    }

}
