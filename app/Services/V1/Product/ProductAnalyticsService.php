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
    // public function trackView(Product $product): void
    // {
    //     if (! $product->is_featured) {
    //         return;
    //     }

    //     $fingerprint = $this->generateFingerprint();
    //     $cacheKey    = "viewed:product:{$product->id}:{$fingerprint}";

    //     if (! Cache::has($cacheKey)) {
    //         ProductView::create([
    //             'product_id' => $product->id,
    //             'user_id'    => Auth::check() ? Auth::user()->id : null,
    //             'ip_address' => request()->ip(),
    //             'user_agent' => request()->userAgent(),
    //         ]);

    //         // Cache::put($cacheKey, true, now()->addHour());
    //         Cache::put($cacheKey, true, now()->addMinutes(2));

    //     }
    // }

 

    private function generateFingerprint(): string
    {
        $deviceId = request()->header('X-Device-ID');
        return sha1(($deviceId ?? request()->ip()) . '|' . request()->userAgent());
    }

    /**
     * Track a view only if the product is currently featured.
     */
    public function trackView(Product $product): void
    {
        if (! $product->isCurrentlyFeatured()) {
            return;
        }

        $fingerprint = $this->generateFingerprint();
        $cacheKey = "viewed:product:{$product->id}:{$fingerprint}";

        if (! Cache::has($cacheKey)) {
            ProductView::create([
                'product_id' => $product->id,
                'user_id'    => Auth::check() ? Auth::id() : null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            Cache::put($cacheKey, true, now()->addMinutes(2));
        }
    }


    /**
     * Get total view count if the product is featured.
     */
    public function getViewCount(Product $product): int
    {
        return $product->isCurrentlyFeatured() ? $product->views()->count() : 0;
    }

    /**
     * Get total wishlist count if the product is featured.
     */
    public function getWishlistCount(Product $product): int
    {
        return $product->isCurrentlyFeatured() ? $product->wishlists()->count() : 0;
    }

     /**
     * Add product to wishlist if it is currently featured.
     */
    public function addToWishlist(Product $product): ?ProductWishlist
    {
        if (! $product->isCurrentlyFeatured()) {
            return null;
        }

        return ProductWishlist::firstOrCreate([
            'product_id' => $product->id,
            'user_id'    => Auth::id(),
        ]);
    }

  

}
