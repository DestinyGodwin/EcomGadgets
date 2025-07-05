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
            'store_name' => $this->whenLoaded('store', $this->store?->name),
            'type' => $this->type->value,
            'type_label' => $this->type->label(),
            'amount' => $this->amount,
            'formatted_amount' => number_format($this->amount, 2),
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'status_color' => $this->status->color(),
            'channel' => $this->channel,
            'meta' => $this->meta,
            'is_pending' => $this->isPending(),
            'is_completed' => $this->isCompleted(),
            'is_failed' => $this->isFailed(),
            'is_cancelled' => $this->isCancelled(),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
