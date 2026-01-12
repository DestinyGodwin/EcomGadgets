<?php

namespace App\Http\Resources\V1\Chat;

use Illuminate\Http\Request;
use App\Http\Resources\V1\Auth\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
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
            'last_message_at' => $this->last_message_at,
            'users' => ConversationUserResource::collection(
                $this->whenLoaded('users')
            ),
            'last_message' => new MessageResource(
                $this->whenLoaded('lastMessage')
            ),
        ];
    }
}
