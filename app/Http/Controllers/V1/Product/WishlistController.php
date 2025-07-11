<?php
namespace App\Http\Controllers\V1\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Product\StoreWishlistRequest;
use App\Http\Resources\V1\Product\WishlistResource;
use App\Models\Product;
use App\Models\ProductWishlist;
use App\Services\V1\Product\WishlistService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

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

        $product = Product::findOrFail($request->product_id);

        if ($product->is_featured) {
            ProductWishlist::firstOrCreate([
                'product_id' => $product->id,
                'user_id'    => Auth::user()->id(),
            ]);
        }
        return new WishlistResource($wishlistItem);
    }

    /**
     * Remove a product from the wishlist.
     */
    public function destroy($productId): JsonResponse
    {
        $deleted = $this->wishlistService->remove($productId);

        if (! $deleted) {
            return response()->json(['message' => 'Wishlist item not found'], 404);
        }

        return response()->json(['message' => 'Product removed from wishlist']);
    }

    public function show($productId): WishlistResource | JsonResponse
    {
        $wishlistItem = $this->wishlistService->show($productId);

        if (! $wishlistItem) {
            return response()->json(['message' => 'Wishlist item not found'], 404);
        }

        return new WishlistResource($wishlistItem);
    }
}
