<?php

namespace App\Services\V1\Product;

use Exception;
use App\Models\Product;
use App\Models\FeaturedProduct;
use Illuminate\Support\Facades\DB;
use App\Models\FeaturedProductPlan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\FeaturedProductSubscription;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function create(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            $store   = Auth::user()->store;
            if (! $store || ! $store->is_active || $store->status !== 'approved') {
                throw ValidationException::withMessages([
                    'store' => ['Your store must be active and approved to create products.']
                ]);
            }
            $product = $store->products()->create([
                'category_id'     => $data['category_id'],
                'name'            => $data['name'],
                'description'     => $data['description'],
                'specifications'  => $data['specifications'] ?? null,
                'brand'           => $data['brand'] ?? null,
                'price'           => $data['price'],
                'wholesale_price' => $data['wholesale_price'],
            ]);

            foreach ($data['images'] as $image) {
                $path = $image->store('products', 'public');
                $product->images()->create(['image_path' => $path]);
            }

            return $product->load('images', 'store');
        });
    }

    public function update(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            $product->update([
                'category_id'     => $data['category_id'] ?? $product->category_id,
                'name'            => $data['name'] ?? $product->name,
                'description'     => $data['description'] ?? $product->description,
                'specifications'  => $data['specifications'] ?? $product->specifications,
                'brand'           => $data['brand'] ?? $product->brand,
                'price'           => $data['price'] ?? $product->price,
                'wholesale_price' => $data['wholesale_price'] ?? $product->wholesale_price,
            ]);

            $remainingImageCount = $product->images()->count();
            $imagesToRemove      = $data['removed_images'] ?? [];

            if (! empty($imagesToRemove)) {
                $toDelete = $product->images()->whereIn('id', $imagesToRemove)->get();

                if (($remainingImageCount - $toDelete->count()) + count($data['images'] ?? []) < 1) {
                    throw new Exception('A product must have at least one image.');
                }

                foreach ($toDelete as $image) {
                    Storage::disk('public')->delete($image->image_path);
                    $image->delete();
                }
            }
            if (! empty($data['images'])) {
                foreach ($data['images'] as $image) {
                    $path = $image->store('products', 'public');
                    $product->images()->create(['image_path' => $path]);
                }
            }
            if ($product->images()->count() < 1) {
                throw new Exception('A product must have at least one image.');
            }
            return $product->load('images', 'store');
        });
    }

    public function delete(Product $product): bool
    {
        return DB::transaction(function () use ($product) {
            $product->images()->delete();
            return $product->delete();
        });
    }

  

    public function myProducts(): LengthAwarePaginator
    {
        return Auth::user()->products()->latest()->paginate();
    }

    public function find(string $product): Product
    {
        return Auth::user()->products()->findOrFail($product);
    }

    //     public function all(): LengthAwarePaginator
    //     {
    //         return $this->getFeaturedAndRegularProducts();

    //     }

    //     public function filter(array $filters): LengthAwarePaginator
    //     {
    //         return Product::with('store')
    //             ->when($filters['state_id'] ?? null, function ($q, $stateId) {
    //                 $q->whereHas('store', fn($sq) => $sq->where('state_id', $stateId));
    //             })
    //             ->when($filters['lga_id'] ?? null, function ($q, $lgaId) {
    //                 $q->whereHas('store', fn($sq) => $sq->where('lga_id', $lgaId));
    //             })
    //             ->when(isset($filters['is_featured']), function ($q) use ($filters) {
    //                 $q->where('is_featured', (bool) $filters['is_featured']);
    //             })->orderByDesc('is_featured')
    //             ->orderByDesc('featured_expires_at')->latest()->paginate();
    //     }

    //     public function findOne(string $product): Product
    //     {
    //         return Product::with(['store', 'category'])->findOrFail($product);
    //     }
    //     public function myFeaturedProducts()
    //     {
    //         return Auth::user()->products()
    //             ->where('is_featured', true)
    //             ->where(function ($query) {
    //                 $query->whereNull('featured_expires_at')
    //                     ->orWhere('featured_expires_at', '>', now());
    //             })->latest()->get();
    //     }

    //     public function getAll(): LengthAwarePaginator
    //     {
    //         return $this->getFeaturedAndRegularProducts();
    //     }

    //     private function getFeaturedAndRegularProducts($filters = []): LengthAwarePaginator
    //     {
    //         $perPage = 30;
    //         $page = request()->get('page', 1);

    //         // Get featured product plans ordered by price (descending - highest price first)
    //         $plans = FeaturedProductPlan::getPlansOrderedByPrice();

    //         // Define allocation per plan position (total 20 featured products)
    //         $planAllocations = [
    //             0 => 8, // Most expensive plan (Gold)
    //             1 => 6, // Second most expensive (Silver)  
    //             2 => 4, // Third most expensive (Bronze)
    //             3 => 2  // Fourth most expensive (Basic)
    //         ];

    //         $featuredProducts = collect();

    //         foreach ($plans as $index => $plan) {
    //             $allocation = $planAllocations[$index] ?? 0;
    //             if ($allocation === 0) continue;

    //             // Get products from active subscriptions for this plan
    //             $planProducts = collect();

    //             foreach ($plan->activeSubscriptions as $subscription) {
    //                 if ($subscription->needsRefresh()) {
    //                     $this->refreshSubscriptionProducts($subscription);
    //                 }

    //                 $planProducts = $planProducts->merge($subscription->products);
    //             }

    //             // Get the required number of products for this plan, ordered by updated_at desc
    //             $selectedProducts = $planProducts
    //                 ->sortByDesc('updated_at')
    //                 ->take($allocation);

    //             $featuredProducts = $featuredProducts->merge($selectedProducts);
    //         }

    //         // Get non-featured products to fill remaining slots
    //         $remainingSlots = $perPage - $featuredProducts->count();

    //         $nonFeaturedQuery = Product::with(['images', 'store'])
    //             ->whereNotIn('id', $featuredProducts->pluck('id'))
    //             ->latest();

    //         // Apply filters if any
    //         $nonFeaturedQuery = $this->applyFilters($nonFeaturedQuery, $filters);

    //         $nonFeaturedProducts = $nonFeaturedQuery
    //             ->take($remainingSlots)
    //             ->get();

    //         // Combine featured and non-featured products
    //         $allProducts = $featuredProducts->concat($nonFeaturedProducts);

    //         // Create paginator
    //         return new LengthAwarePaginator(
    //             $allProducts,
    //             $this->getTotalProductCount($filters),
    //             $perPage,
    //             $page,
    //             [
    //                 'path' => request()->url(),
    //                 'pageName' => 'page',
    //             ]
    //         );
    //     }

    //     private function refreshSubscriptionProducts(FeaturedProductSubscription $subscription): void
    //     {
    //         // Only update the updated_at timestamps of products already in the subscription
    //         $productIds = $subscription->products()->pluck('products.id');

    //         if ($productIds->isNotEmpty()) {
    //             Product::whereIn('id', $productIds)
    //                 ->update(['updated_at' => now()]);
    //         }

    //         // Update last refreshed time
    //         $subscription->update(['last_refreshed_at' => now()]);
    //     }

    //     private function applyFilters($query, array $filters)
    //     {
    //         if (!empty($filters['search'])) {
    //             $query->where(function ($q) use ($filters) {
    //                 $q->where('name', 'like', '%' . $filters['search'] . '%')
    //                     ->orWhere('brand', 'like', '%' . $filters['search'] . '%');
    //             });
    //         }

    //         if (!empty($filters['state_id'])) {
    //             $query->whereHas('store', function ($q) use ($filters) {
    //                 $q->where('state_id', $filters['state_id']);
    //             });
    //         }

    //         if (!empty($filters['category_id'])) {
    //             $query->where('category_id', $filters['category_id']);
    //         }

    //         if (!empty($filters['user_state_id'])) {
    //             $query->whereHas('store', function ($q) use ($filters) {
    //                 $q->where('state_id', $filters['user_state_id']);
    //             });
    //         }

    //         if (!empty($filters['user_lga_id'])) {
    //             $query->whereHas('store', function ($q) use ($filters) {
    //                 $q->where('lga_id', $filters['user_lga_id']);
    //             });
    //         }


    //         return $query;
    //     }

    //     private function getTotalProductCount(array $filters = []): int
    //     {
    //         $query = Product::query();
    //         return $this->applyFilters($query, $filters)->count();
    //     }

    //     // Method to add products to a subscription
    //     // public function addProductsToSubscription(string $subscriptionId, array $productIds): bool
    //     // {
    //     //     $subscription = FeaturedProductSubscription::findOrFail($subscriptionId);

    //     //     // Verify subscription belongs to authenticated user's store
    //     //     if ($subscription->store_id !== Auth::user()->store->id) {
    //     //         throw new Exception('Unauthorized access to subscription.');
    //     //     }

    //     //     // Verify subscription is active
    //     //     if (!$subscription->isActive()) {
    //     //         throw new Exception('Subscription is not active.');
    //     //     }

    //     //     // Verify products belong to the user's store
    //     //     $products = Product::whereIn('id', $productIds)
    //     //         ->where('store_id', Auth::user()->store->id)
    //     //         ->get();

    //     //     if ($products->count() !== count($productIds)) {
    //     //         throw new Exception('Some products do not belong to your store.');
    //     //     }

    //     //     // Check if adding these products would exceed plan limit
    //     //     $currentCount = $subscription->products()->count();
    //     //     $newCount = count($productIds);

    //     //     if (($currentCount + $newCount) > $subscription->plan->max_products) {
    //     //         throw new Exception("Cannot add {$newCount} products. Only {$subscription->availableSlots()} slots available.");
    //     //     }

    //     //     // Add products to subscription
    //     //     $subscription->products()->attach($productIds, ['added_at' => now()]);

    //     //     return true;
    //     // }
    // public function addProductsToSubscription(string $subscriptionId, array $productIds): bool
    // {
    //     $subscription = FeaturedProductSubscription::findOrFail($subscriptionId);

    //     if ($subscription->store_id !== Auth::user()->store->id) {
    //         throw new Exception('Unauthorized access to subscription.');
    //     }

    //     if (!$subscription->isActive()) {
    //         throw new Exception('Subscription is not active.');
    //     }

    //     $products = Product::whereIn('id', $productIds)
    //         ->where('store_id', Auth::user()->store->id)
    //         ->get();

    //     if ($products->count() !== count($productIds)) {
    //         throw new Exception('Some products do not belong to your store.');
    //     }

    //     $currentCount = $subscription->products()->count();
    //     $newCount = count($productIds);

    //     if (($currentCount + $newCount) > $subscription->plan->max_products) {
    //         throw new Exception("Cannot add {$newCount} products. Only {$subscription->availableSlots()} slots available.");
    //     }

    //     foreach ($productIds as $productId) {
    //         $subscription->featuredProducts()->create([
    //             'product_id' => $productId,
    //             'added_at' => now(),
    //         ]);
    //     }

    //     return true;
    // }

    //     // Method to remove products from subscription
    //     public function removeProductsFromSubscription(string $subscriptionId, array $productIds): bool
    //     {
    //         $subscription = FeaturedProductSubscription::findOrFail($subscriptionId);

    //         // Verify subscription belongs to authenticated user's store
    //         if ($subscription->store_id !== Auth::user()->store->id) {
    //             throw new Exception('Unauthorized access to subscription.');
    //         }

    //         $subscription->products()->detach($productIds);
    //         return true;
    //     }

    //     // Get user's active subscriptions
    //     public function myActiveSubscriptions()
    //     {
    //         $user = Auth::user();

    //         return FeaturedProductSubscription::where('store_id', $user->store->id)
    //             ->where('is_active', true)
    //             ->where(function ($query) {
    //                 $query->whereNull('ends_at')
    //                     ->orWhere('ends_at', '>', now());
    //             })
    //             ->with(['plan'])
    //             ->paginate();
    //     }

    //     public function getByCategory(string $categoryId, ?string $stateId = null, ?string $lgaId = null): LengthAwarePaginator
    //     {
    //         $filters = [
    //             'category_id' => $categoryId,
    //             'state_id' => $stateId,
    //             'lga_id' => $lgaId
    //         ];

    //         return $this->getFeaturedAndRegularProducts($filters);
    //     }

    //     public functio search(array $filters): LengthAwarePaginator
    //     {
    //         return $this->getFeaturedAndRegularProducts($filters);
    //     }

    //     public function getByBrand(string $brand): LengthAwarePaginator
    //     {
    //         return $this->getFeaturedAndRegularProducts(['brand' => $brand]);
    //     }

    //     public function getByUserState(): LengthAwarePaginator
    //     {
    //         $user = Auth::user();
    //         return $this->getFeaturedAndRegularProducts(['user_state_id' => $user->state_id]);
    //     }

    //     public function getByUserLga(): LengthAwarePaginator
    //     {
    //         $user = Auth::user();
    //         return $this->getFeaturedAndRegularProducts(['user_lga_id' => $user->lga_id]);
    //     }

    private function getFeaturedLimits(): array
    {
        return FeaturedProductPlan::pluck('featured_limit', 'name')->toArray();
    }

    /**
     * Apply filters to a product query.
     */
    private function applyFilters($query, array $filters): void
    {
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['brand'])) {
            $query->where('brand', 'like', '%' . $filters['brand'] . '%');
        }

        if (!empty($filters['state_id'])) {
            $query->whereHas('store', fn($q) => $q->where('state_id', $filters['state_id']));
        }

        if (!empty($filters['lga_id'])) {
            $query->whereHas('store', fn($q) => $q->where('lga_id', $filters['lga_id']));
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('brand', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }
    }

    /**
     * Get featured products grouped by plan quotas.
     */
    private function fetchFeatured(array $filters = [])
    {
        $limits = $this->getFeaturedLimits();
        $featuredProducts = collect();

        foreach ($limits as $planName => $limit) {
            if ($limit <= 0) continue;

            $query = FeaturedProduct::with([
                'product.images',
                'product.store',
                'product.category'
            ])
                ->whereHas('subscription.plan', fn($q) => $q->where('name', $planName))
                ->whereHas('product', fn($q) => $this->applyFilters($q, $filters));

            $products = $query->limit($limit)->get()->pluck('product');

            $featuredProducts = $featuredProducts->merge($products);
        }

        return $featuredProducts;
    }

    /**
     * Get non-featured products to fill remaining page slots.
     */
    private function fetchNonFeatured(array $filters = [], int $limit = 10, array $excludeIds = []): Collection
    {
        $query = Product::with(['images', 'store', 'category'])
            ->whereNotIn('id', $excludeIds);

        $this->applyFilters($query, $filters);

        return $query->limit($limit)->get();
    }

    /**
     * Merge featured and non-featured into paginated results.
     */
    // private function getCombinedProducts(array $filters = [], int $perPage = 30): LengthAwarePaginator
    // {
    //     $featured = $this->fetchFeatured($filters);
    //     $featuredIds = $featured->pluck('id')->toArray();

    //     $remainingSlots = $perPage - $featured->count();
    //     $nonFeatured = $this->fetchNonFeatured($filters, $remainingSlots, $featuredIds);

    //     $allProducts = $featured->merge($nonFeatured);

    //     // Manual pagination
    //     $page = request()->get('page', 1);
    //     $total = $allProducts->count();
    //     $items = $allProducts->forPage($page, $perPage)->values();

    //     return new LengthAwarePaginator($items, $total, $perPage, $page, [
    //         'path' => request()->url(),
    //         'query' => request()->query(),
    //     ]);
    // }
private function getCombinedProducts(array $filters = [], int $perPage = 30): LengthAwarePaginator
{
    $page = request()->get('page', 1);

    // Fetch featured products according to plan limits
    $featured = $this->fetchFeatured($filters);
    $featuredIds = $featured->pluck('id')->toArray();

    // Determine how many non-featured slots remain for this page
    $remainingSlots = $perPage - $featured->count();
    if ($remainingSlots < 0) {
        $remainingSlots = 0;
    }

    // Base query for non-featured products
    $nonFeaturedQuery = Product::with(['images', 'store', 'category'])
        ->whereNotIn('id', $featuredIds);
    $this->applyFilters($nonFeaturedQuery, $filters);

    // Get total count (featured count + filtered non-featured count)
    $nonFeaturedTotal = (clone $nonFeaturedQuery)->count();
    $total = $featured->count() + $nonFeaturedTotal;

    // Paginate non-featured properly using DB offset/limit
    $offset = max(0, ($page - 1) * $perPage - $featured->count());
    $nonFeatured = $nonFeaturedQuery
        ->skip($offset)
        ->take($remainingSlots)
        ->get();

    // Merge featured & non-featured for this page
    $allProducts = $featured->merge($nonFeatured);

    return new LengthAwarePaginator(
        $allProducts,
        $total,
        $perPage,
        $page,
        [
            'path' => request()->url(),
            'query' => request()->query(),
        ]
    );
}

    /**
     * Public methods for various fetch types.
     */
    public function getAll(): LengthAwarePaginator
    {
        return $this->getCombinedProducts();
    }

    public function getByCategory(string $categoryId, ?string $stateId = null, ?string $lgaId = null): LengthAwarePaginator
    {
        return $this->getCombinedProducts([
            'category_id' => $categoryId,
            'state_id' => $stateId,
            'lga_id' => $lgaId,
        ]);
    }

    public function getByBrand(string $brand): LengthAwarePaginator
    {
        return $this->getCombinedProducts(['brand' => $brand]);
    }

    public function search(array $filters): LengthAwarePaginator
    {
        return $this->getCombinedProducts($filters);
    }

    public function getByUserState(): LengthAwarePaginator
    {
        $user = Auth::user();
        return $this->getCombinedProducts(['state_id' => $user->state_id]);
    }

    public function getByUserLga(): LengthAwarePaginator
    {
        $user = Auth::user();
        return $this->getCombinedProducts(['lga_id' => $user->lga_id]);
    }

    public function getByState($stateId): LengthAwarePaginator
    {
        return $this->getCombinedProducts(['state_id' => $stateId]);
    }

    public function getByLga($lgaId): LengthAwarePaginator
    {
        return $this->getCombinedProducts(['lga_id' => $lgaId]);
    }

    public function myFeaturedProducts()
    {
        $userStoreId = Auth::user()->store->id;

        return FeaturedProduct::with(['product.images'])
    ->whereHas('subscription', fn($q) => $q->where('store_id', $userStoreId))
            ->get()
            ->pluck('product');
    }
}
