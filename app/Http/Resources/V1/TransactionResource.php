<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
     public function toArray($request)
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'store_id' => $this->store_id,
            'type' => $this->type,
            'amount' => $this->amount,
            'status' => $this->status,
            'channel' => $this->channel,
            'meta' => $this->meta,
            'created_at' => $this->created_at,
        ];
    }
}
