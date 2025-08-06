<?php

namespace App\Http\Resources\V1\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\V1\FeaturedProductPlanResource;

class MyFeaturedSubscriptionResource extends JsonResource
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
            'starts_at' => $this->starts_at?->toDateTimeString(),
            'ends_at' => $this->ends_at?->toDateTimeString(),
            'is_active' => $this->is_active,
            'plan' => new FeaturedProductPlanResource($this->whenLoaded('plan')),
            'products' => MyProductResource::collection($this->whenLoaded('products')),
        ];
    }
}
