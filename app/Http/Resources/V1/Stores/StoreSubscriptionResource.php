<?php

namespace App\Http\Resources\V1\Stores;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreSubscriptionResource extends JsonResource
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
            'store_id' => $this->store_id,
            'store_name' => $this->store->store_name,
            'amount' => $this->amount,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
            'transaction_id' => $this->transaction_id,
        ];
    }
}
