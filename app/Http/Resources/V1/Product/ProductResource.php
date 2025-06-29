<?php

namespace App\Http\Resources\V1\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'specifications' => $this->specifications,
            // 'brand' => $this->brand,
            'price' => $this->price,
            'wholesale_price' => $this->wholesale_price,
            'images' => ProductImageResource::collection($this->images),
            'store' => [
                'id' => $this->store->id,
                'name' => $this->store->store_name,
                'slug' =>$this->store->slug,
                'store_lga' =>  $this->store->lga->name,
                'store_state' => $this->store->state->name,
                'phone_number' => $this->store->phone,

            ],
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
