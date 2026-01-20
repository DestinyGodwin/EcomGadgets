<?php

namespace App\Http\Resources\V1\Chat;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'profile_picture' => $this->getFirstMediaUrl('images', 'optimized'),
            'store_name' => $this->when(
                $this->relationLoaded('store') && $this->store,
                fn () => $this->store->store_name
            ),
        ];
    }
}
