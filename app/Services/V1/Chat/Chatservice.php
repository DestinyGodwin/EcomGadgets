<?php

namespace App\Services\V1\Chat;

use App\Models\Message;
use App\Models\Conversation;
use App\Events\Chat\MessageSent;
use Illuminate\Support\Facades\DB;

class Chatservice
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

     public function getOrCreateConversation(string $userA, string $userB): Conversation
    {
        $conversation = Conversation::whereHas('users', fn ($q) => $q->where('user_id', $userA))
            ->whereHas('users', fn ($q) => $q->where('user_id', $userB))
            ->first();

        if ($conversation) {
            return $conversation;
        }

        $conversation = Conversation::create();
        $conversation->users()->attach([$userA, $userB]);

        return $conversation;
    }

    public function sendMessage(
        Conversation $conversation,
        string $senderId,
        ?string $body,
        array $files = []
    ): Message {
        return DB::transaction(function () use ($conversation, $senderId, $body, $files) {
            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $senderId,
                'body' => $body,
                'type' => $files ? 'media' : 'text',
            ]);

            foreach ($files as $file) {
                $path = $file->store('chat', 'public');

                $message->media()->create([
                    'path' => $path,
    
                ]);
            }

            $conversation->update(['last_message_at' => now()]);

            broadcast(new MessageSent($message))->toOthers();

            return $message;
        });
    }
}
