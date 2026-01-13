<?php

namespace App\Http\Resources\V1\Chat;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'last_message_at' => $this->last_message_at?->toISOString(),
            'users' => ConversationUserResource::collection(
                $this->whenLoaded('users')
            ),
            'last_message' => $this->whenLoaded('lastMessage', function () use ($request) {
                return new MessageResource($this->lastMessage, $request->user()->id);
            }),
        ];
    }
}
