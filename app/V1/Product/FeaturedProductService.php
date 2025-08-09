<?php

namespace App\V1\Product;

use Exception;
use App\Models\Product;
use App\Models\FeaturedProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\FeaturedProductSubscription;

class FeaturedProductService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    // public function addProducts(string $plan_id, array $productIds){
    //     $subscription =   FeaturedProductSubscription::findOrFail($plan_id);
    //     if ($subscription->store_id !== Auth::user()->store->id){
    //         throw new Exception('Unauthorized access to subscription');
    //     }
    //     if (!$subscription->isActive()){
    //         throw new Exception('Subscription is not active');
    //     }
    //     $products = Product::whereIn('id', $productIds)
    //     ->where('store_id', Auth::user()->store->id)->get();

    //     if ($products->count !== count($productIds)){
    //         throw new Exception('Some products do not belong to your store');
    //     }

    //     $currentCount = $subscription->products()->count();
    //     $newCount = count($productIds);
    // if (($currentCount + $newCount) > $subscription->plan->max_products) {
    //     throw new Exception("Cannot add {$newCount} products. Only {$subscription->availableSlots()} slots available.");
    // }
    //     foreach ($productIds as $productId) {
    //     $subscription->featuredProducts()->create([
    //         'product_id' => $productId,
    //         'added_at' => now(),
    //         'expires_at' => $subscription->ends_at,
    //     ]);
    // }

    // return true;
    // }

    //  public function removeProducts(string $subscriptionId, array $productIds): bool
    // {
    //     $subscription = FeaturedProductSubscription::findOrFail($subscriptionId);

    //     // Verify subscription belongs to authenticated user's store
    //     if ($subscription->store_id !== Auth::user()->store->id) {
    //         throw new Exception('Unauthorized access to subscription.');
    //     }

    //     $subscription->products()->detach($productIds);
    //     return true;
    // }

    //    public function myActiveSubscriptions()
    // {
    //     $user = Auth::user();

    //     return FeaturedProduct::where('store_id', $user->store->id)
    //         ->where('is_active', true)
    //         ->where(function ($query) {
    //             $query->whereNull('ends_at')
    //                 ->orWhere('ends_at', '>', now());
    //         })
    //         ->with(['plan'])
    //         ->paginate();
    // }


    //    public function addProductsToSubscription(string $subscriptionId, array $productIds): array
    // {
    //     return DB::transaction(function () use ($subscriptionId, $productIds) {
    //         $subscription = $this->getActiveSubscription($subscriptionId);

    //         $slotsLeft = $this->calculateRemainingSlots($subscription);
    //         if ($slotsLeft <= 0) {
    //             throw new Exception('Maximum featured products already reached for this subscription.');
    //         }

    //         $productsToAdd = array_slice($productIds, 0, $slotsLeft);
    //         return $this->createFeaturedProducts($subscription->id, $productsToAdd);
    //     });
    // }

    /**
     * Ensure subscription exists and is active.
     */
    protected function getActiveSubscription(string $subscriptionId): FeaturedProductSubscription
    {
        $subscription = FeaturedProductSubscription::with('plan', 'featuredProducts')
            ->findOrFail($subscriptionId);

        if (! $subscription->isActive()) {
            throw new Exception('Subscription is not active.');
        }

        return $subscription;
    }

    /**
     * Calculate remaining slots based on max allowed and current count.
     */
    protected function calculateRemainingSlots(FeaturedProductSubscription $subscription): int
    {
        $maxAllowed = $subscription->plan->max_products;
        $currentCount = $subscription->featuredProducts->count();

        return $maxAllowed - $currentCount;
    }

    /**
     * Create featured product records.
     */
    protected function createFeaturedProducts(string $subscriptionId, array $productIds): array
    {
        $created = [];
        foreach ($productIds as $pid) {
            $created[] = FeaturedProduct::create([
                'product_id' => $pid,
                'featured_product_subscription_id' => $subscriptionId,
            ]);
        }
        return $created;
    }

    public function addProductsToSubscription(string $subscriptionId, array $productIds): bool
{
    return DB::transaction(function () use ($subscriptionId, $productIds) {
        $subscription = FeaturedProductSubscription::with('plan')
            ->findOrFail($subscriptionId);

        $storeId = Auth::user()->store->id;

        // Ownership check
        if ($subscription->store_id !== $storeId) {
            throw new Exception('Unauthorized access to subscription.');
        }

        if (!$subscription->isActive()) {
            throw new Exception('Subscription is not active.');
        }

        // Validate products belong to store
        $ownedProductsCount = Product::whereIn('id', $productIds)
            ->where('store_id', $storeId)
            ->count();

        if ($ownedProductsCount !== count($productIds)) {
            throw new Exception('Some products do not belong to your store.');
        }

        // Check slot availability
        $availableSlots = $subscription->availableSlots();
        if ($availableSlots < count($productIds)) {
            throw new Exception("Cannot add products. Only {$availableSlots} slots available.");
        }

        foreach ($productIds as $productId) {
            $subscription->featuredProducts()->create([
                'product_id' => $productId,
                'added_at' => now(),
                'expires_at' => $subscription->ends_at,
            ]);
        }

        return true;
    });
}

public function removeProductsFromSubscription(string $subscriptionId, array $productIds): int
{
    return DB::transaction(function () use ($subscriptionId, $productIds) {
        $subscription = FeaturedProductSubscription::findOrFail($subscriptionId);
        $storeId = Auth::user()->store->id;

        if ($subscription->store_id !== $storeId) {
            throw new Exception('Unauthorized access to subscription.');
        }

        return $subscription->featuredProducts()
            ->whereHas('product', fn($q) => $q->where('store_id', $storeId))
            ->whereIn('product_id', $productIds)
            ->delete();
    });
}

// public function myActiveSubscriptions()
// {
//     $storeId = Auth::user()->store->id;

//     return FeaturedProductSubscription::with('plan')
//         ->where('store_id', $storeId)
//         ->where('expires_at', '>', now())
//         ->get();
// }

  public function myActiveSubscriptions()
    {
        return FeaturedProductSubscription::with(['plan', 'featuredProducts.product'])
            ->where('store_id', Auth::user()->store->id)
            ->get();
    }

}
