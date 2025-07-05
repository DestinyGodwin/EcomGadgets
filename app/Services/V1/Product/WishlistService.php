<?php

namespace App\Services\V1\Product;

use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;

class WishlistService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function add(array $data): Wishlist
{
    return Wishlist::firstOrCreate([
        'user_id' => Auth::id(),
        'product_id' => $data['product_id'],
    ]);
}

    public function remove(int $productId): bool
{
    return Wishlist::where('user_id', Auth::id())
        ->where('product_id', $productId)
        ->delete() > 0;
}

    public function list()
    {
        return Wishlist::with('product')
            ->where('user_id', Auth::id())
            ->paginate();
    }
    public function show(int $productId)
{
    return Wishlist::with('product')
        ->where('user_id', Auth::id())
        ->where('product_id', $productId)
        ->first();
}

}
