<?php

namespace App\Http\Resources\V1\Product;

use Illuminate\Http\Request;
use App\Http\Resources\Concerns\HasMedia;
use Illuminate\Http\Resources\Json\JsonResource;
class ProductResource extends JsonResource
{

    use HasMedia;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $isSingle = $request->routeIs(['products.show', 'admin.products.show']);

        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            // 'specifications' => $this->specifications,
            // 'brand' => $this->brand,
            'price' => $this->price,
            'is_featured' => $this->isCurrentlyFeatured(),
            'wholesale_price' => $this->wholesale_price,
            'average_rating'  => round($this->reviews->avg('rating'), 1),

            // 'images' => ProductImageResource::collection($this->images),
            // 'images' => $this->getMedia('images')->map(fn($media) => [
            //     'id' => $media->id,
            //     'url' => $isSingle
            //         ? $media->getUrl()
            //         : $media->getUrl('thumb'),
            // ]),
            'images' => $this->media(
                'images',
                $isSingle ? null : 'thumb'
            ),
            'store' => [
                'id' => $this->store->id,
                'name' => $this->store->store_name,
                'slug' => $this->store->slug,
                'store_lga' =>  $this->store->lga->name,
                // 'store_image' => asset('storage/' . $this->store->store_image),
                'store_image' => $this->getFirstMediaUrl('store_image', 'thumb'),

                'store_state' => $this->store->state->name,
                'phone_number' => $this->store->phone,
                // 'user' => [
                //     'id' => $this->store->user->id,
                //     'name' => $this->store->user->name,
                //     'email' => $this->store->user->email,
                // ],
                'user_id' => $this->store->user->id,

            ],
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
