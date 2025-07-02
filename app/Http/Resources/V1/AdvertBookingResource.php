<?php
namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdvertBookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'store_id'  => $this->store_id,
            'state_id'  => $this->state_id,
            'starts_at' => $this->starts_at,
            'ends_at'   => $this->ends_at,
            'image'     => asset('storage/' . $this->image),
            'is_dummy'  => $this->is_dummy,

        ];
    }
}
