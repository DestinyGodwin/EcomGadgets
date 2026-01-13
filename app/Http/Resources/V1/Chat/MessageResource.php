<?php

namespace App\Http\Resources\V1\Chat;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $authUserId = $request->user()?->id;

        return [
            'id' => $this->id,
            'conversation_id' => $this->conversation_id,
            'type' => $this->type,
            'body' => $this->decrypted_body,
            'sender_id' => $this->sender_id,
            'receiver_id' => $this->receiverId($authUserId),
            'created_at' => $this->created_at->toISOString(),
            'sender' => new SenderResource($this->whenLoaded('sender')),
        ];
    }

    private function receiverId(?string $authUserId): ?string
    {
        if (! $authUserId || ! $this->relationLoaded('conversation')) {
            return null;
        }

        return $this->conversation->users
            ->firstWhere('id', '!=', $authUserId)
            ?->id;
    }
}



