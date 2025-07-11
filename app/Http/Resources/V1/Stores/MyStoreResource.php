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
        return [
            'id'                      => $this->id,
            'store_name'              => $this->store_name,
            'store_description'       => $this->store_description,
            'slug' => $this->slug,
            'phone'                   => $this->phone,
            'email'                   => $this->email,
            'address'                 => $this->address,
            'lga_id'                  => $this->lga_id,
            'state_id'                => $this->state_id,
            'store_image'             => asset('storage/' . $this->store_image),
            'subscription_expires_at' => $this->subscription_expires_at,
            'is_active'               => $this->is_active,
            'status'                  => $this->status,
            'updated_at'              => $this->updated_at,
            'created_at'              => $this->created_at,
        ];

    }
}
