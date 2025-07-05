<?php

namespace App\Http\Controllers\V1\Product;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\V1\Product\WishlistService;
use App\Http\Resources\V1\Product\WishlistResource;
use App\Http\Requests\V1\Product\StoreWishlistRequest;

class WishlistController extends Controller
{
    protected WishlistService $wishlistService;

    public function __construct(WishlistService $wishlistService)
    {
        $this->wishlistService = $wishlistService;
    }

    /**
     * Display the user's wishlist.
     */
    public function index()
    {
        $wishlist = $this->wishlistService->list();
        return WishlistResource::collection($wishlist);
    }

    /**
     * Add a product to the wishlist.
     */
    public function store(StoreWishlistRequest $request): WishlistResource
    {
        $wishlistItem = $this->wishlistService->add($request->validated());
        return new WishlistResource($wishlistItem);
    }

    /**
     * Remove a product from the wishlist.
     */
   public function destroy(int $productId): JsonResponse
{
    $deleted = $this->wishlistService->remove($productId);

    if (! $deleted) {
        return response()->json(['message' => 'Wishlist item not found'], 404);
    }

    return response()->json(['message' => 'Product removed from wishlist']);
}
}
