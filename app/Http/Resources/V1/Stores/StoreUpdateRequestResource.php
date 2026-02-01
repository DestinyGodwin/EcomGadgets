<?php

namespace App\Http\Resources\V1\Stores;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreUpdateRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'store_id'       => $this->store_id,
            'new_data'       => $this->new_data,
            'status'         => $this->status,
            'admin_feedback' => $this->admin_feedback,
            'created_at'     => $this->created_at,

            'store' => [
                'store_name'           => $this->store->store_name,
                'slug'                 => $this->store->slug,
                'store_description'    => $this->store->store_description,
                'store_image' => $this->getFirstMediaUrl('store_image', 'thumb'),
                'store_cac_image'      => $this->store->store_cac_image,
                'store_id_image'       => $this->store->store_id_image,
                'phone'                => $this->store->phone,
                'email'                => $this->store->email,
                'state_id'             => $this->store->state_id,
                'lga_id'               => $this->store->lga_id,
                'address'              => $this->store->address,
                'subscription_expires_at' => $this->store->subscription_expires_at,
                'is_active'            => (bool) $this->store->is_active,
                'status'               => $this->store->status,
                'created_at'           => $this->store->created_at,
                'updated_at'           => $this->store->updated_at,
            ]
        ];
    }
}
