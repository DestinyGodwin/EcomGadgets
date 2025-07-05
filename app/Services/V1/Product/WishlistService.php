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

    public function add(int $productId): Wishlist
    {
        return Wishlist::firstOrCreate([
            'user_id' => Auth::id(),
            'product_id' => $productId,
        ]);
    }

    public function remove(int $productId): void
    {
        Wishlist::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->delete();
    }

    public function list()
    {
        return Wishlist::with('product')
            ->where('user_id', Auth::id())
            ->get();
    }
}
