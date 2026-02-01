<?php

namespace App\Http\Resources\V1\Product;

use Illuminate\Http\Request;
use App\Http\Resources\Concerns\HasMedia;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\V1\Product\ReviewResource;
use App\Http\Resources\V1\Product\ProductImageResource;

class ViewProductResource extends JsonResource
{
    use HasMedia;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'category_id'     => $this->category_id,
            'name'            => $this->name,
            'slug'            => $this->slug,
            'description'     => $this->description,
            'price'           => $this->price,
            'is_featured' => $this->isCurrentlyFeatured(),
            'wholesale_price' => $this->wholesale_price,
            // 'images'          => ProductImageResource::collection($this->images),
            'images' => $this->media('images', 'thumb'),

            'store'           => [
                'id'           => $this->store->id,
                'name'         => $this->store->store_name,
                'slug'         => $this->store->slug,
                'store_lga'    => $this->store->lga->name,
                'store_image'  => asset('storage/' . $this->store->store_image),
                'store_state'  => $this->store->state->name,
                'phone_number' => $this->store->phone,
                'user_id'      => $this->store->user->id,
            ],

            'average_rating'  => round($this->reviews->avg('rating'), 1),
            'reviews'         => ReviewResource::collection($this->whenLoaded('reviews')),

            'created_at'      => $this->created_at->toDateTimeString(),
        ];
    }
}
