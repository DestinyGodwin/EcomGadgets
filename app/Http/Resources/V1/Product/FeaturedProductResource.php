<?php

namespace App\Http\Resources\V1\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\V1\Product\ProductAnalyticsService;

class FeaturedProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $analytics = app(ProductAnalyticsService::class);

        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            // 'specifications' => $this->specifications,
            // 'brand' => $this->brand,
            'price' => $this->price,
            'wholesale_price' => $this->wholesale_price,
            'is_featured' => $this->isCurrentlyFeatured(),
            'featured_expires_at' => $this->featured_expires_at,
            'average_rating'  => round($this->reviews->avg('rating'), 1),
            'images' => ProductImageResource::collection($this->images),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'view_count' => $analytics->getViewCount($this->resource),
            'wishlist_count' => $analytics->getWishlistCount($this->resource),
        ];
    }
}
