<?php

namespace App\Http\Resources\V1\Stores;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
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
            'store_name' => $this->store_name,
            'store_description' => $this->store_description,
            'phone' => $this->phone,
            'email' => $this->email,
            'store_image' => asset('storage/' . $this->store_image),
            'address' => $this->address

        ];

    }
}
