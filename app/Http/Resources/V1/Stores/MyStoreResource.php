<?php

namespace App\Http\Resources\V1\Stores;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MyStoreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       return array_merge(parent::toArray($request), [
            'store_image' => asset('storage/' . $this->store_image),
        ]);
    }
}
