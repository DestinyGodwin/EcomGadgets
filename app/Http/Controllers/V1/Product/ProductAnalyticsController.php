<?php

namespace App\Http\Controllers\V1\Product;

use App\Models\Product;
use App\Http\Controllers\Controller;
use App\Services\V1\Product\ProductAnalyticsService;

class ProductAnalyticsController extends Controller
{
    public function __construct(protected ProductAnalyticsService $analyticsService) {}
        
    

    public function viewCount(Product $product)
    {
        $count = $this->analyticsService->getViewCount($product);
        return response()->json(['views' => $count]);
    }

    public function wishlistCount(Product $product)
    {
        $count = $this->analyticsService->getWishlistCount($product);
        return response()->json(['wishlists' => $count]);
    }
}
