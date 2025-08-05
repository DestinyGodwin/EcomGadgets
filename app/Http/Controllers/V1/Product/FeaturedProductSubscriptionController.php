<?php

namespace App\Http\Controllers\V1\Product;

use Exception;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\FeaturedProductSubscription;
use App\Services\V1\Product\ProductService;
use App\Http\Requests\V1\Product\AddProductsToSubscriptionRequest;
use App\Http\Requests\V1\Product\RemoveProductsFromSubscriptionRequest;

class FeaturedProductSubscriptionController extends Controller
{
    public function __construct(
        private ProductService $productService, ) {}
        
    public function mySubscriptions(): JsonResponse
    {
        try {
            $subscriptions = $this->productService->myActiveSubscriptions();

            return response()->json([
                'success' => true,
                'message' => 'Subscriptions retrieved successfully.',
                'data' => $subscriptions
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve subscriptions: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showSubscription(string $subscriptionId): JsonResponse
    {
        try {
            $subscription = FeaturedProductSubscription::with(['plan', 'products.images', 'store'])
                ->findOrFail($subscriptionId);

            // Verify subscription belongs to authenticated user's store
            if ($subscription->store_id !== Auth::user()->store->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to subscription.'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'message' => 'Subscription details retrieved successfully.',
                'data' => $subscription
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve subscription: ' . $e->getMessage()
            ], 500);
        }
    }

    public function addProducts(AddProductsToSubscriptionRequest $request): JsonResponse
    {
        try {
            $this->productService->addProductsToSubscription(
                $request->subscription_id,
                $request->product_ids
            );

            return response()->json([
                'success' => true,
                'message' => 'Products added to subscription successfully.'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function removeProducts(RemoveProductsFromSubscriptionRequest $request): JsonResponse
    {
        try {
            $this->productService->removeProductsFromSubscription(
                $request->subscription_id,
                $request->product_ids
            );

            return response()->json([
                'success' => true,
                'message' => 'Products removed from subscription successfully.'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function availableProducts(string $subscriptionId): JsonResponse
    {
        try {
            $subscription = FeaturedProductSubscription::findOrFail($subscriptionId);

            // Verify subscription belongs to authenticated user's store
            if ($subscription->store_id !== Auth::user()->store->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to subscription.'
                ], 403);
            }

            // Get products that are not already in any active subscription
            $usedProductIds = FeaturedProductSubscription::where('store_id', Auth::user()->store->id)
                ->where('is_active', true)
                ->where(function ($query) {
                    $query->whereNull('ends_at')
                          ->orWhere('ends_at', '>', now());
                })
                ->with('products')
                ->get()
                ->pluck('products')
                ->flatten()
                ->pluck('id');

            $availableProducts = Auth::user()->store->products()
                ->whereNotIn('id', $usedProductIds)
                ->with(['images', 'category'])
                ->latest()
                ->paginate(20);

            return response()->json([
                'success' => true,
                'message' => 'Available products retrieved successfully.',
                'data' => $availableProducts,
                'subscription_info' => [
                    'available_slots' => $subscription->availableSlots(),
                    'max_products' => $subscription->plan->max_products,
                    'current_products' => $subscription->products()->count()
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve available products: ' . $e->getMessage()
            ], 500);
        }
    }

    public function subscriptionProducts(string $subscriptionId): JsonResponse
    {
        try {
            $subscription = FeaturedProductSubscription::with(['products.images', 'plan'])
                ->findOrFail($subscriptionId);

            // Verify subscription belongs to authenticated user's store
            if ($subscription->store_id !== Auth::user()->store->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to subscription.'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'message' => 'Subscription products retrieved successfully.',
                'data' => [
                    'subscription' => $subscription,
                    'products' => $subscription->products()->with('images')->paginate(20)
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve subscription products: ' . $e->getMessage()
            ], 500);
        }
    }
}
