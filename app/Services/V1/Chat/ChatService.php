<?php

namespace App\Services\V1\Chat;

use App\Models\Message;
use App\Models\Conversation;
use App\Events\Chat\MessageSent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Notification;
use App\Notifications\V1\Chat\NewMessageNotification;

class ChatService
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

    // public function sendMessage(
    //     Conversation $conversation,
    //     string $senderId,
    //     ?string $body,
    //     array $images = []
    // ): Message {
    //     return DB::transaction(function () use ($conversation, $senderId, $body, $images) {
    //         $message = Message::create([
    //             'conversation_id' => $conversation->id,
    //             'sender_id' => $senderId,
    //             // 'body' => $body ? Crypt::encryptString($body) : null,
    //             'body' => $body,
    //             'type' => $images ? 'media' : 'text',
    //         ]);

    //         foreach ($images as $image) {
    //             $path = $image->store('chat', 'public');

    //             $message->media()->create([
    //                 'path' => $path,

    //             ]);
    //         }

    //         $conversation->update(['last_message_at' => now()]);

    //         broadcast(new MessageSent($message))->toOthers();

    //         return $message;
    //     });
    // }


    public function sendMessage(
        Conversation $conversation,
        string $senderId,
        ?string $body,
        array $images = []
    ): Message {
        return DB::transaction(function () use ($conversation, $senderId, $body, $images) {

            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id'       => $senderId,
                'body'            => $body,
                'type'            => $images ? 'media' : 'text',
            ]);

            foreach ($images as $image) {
                $path = $image->store('chat', 'public');

                $message->media()->create([
                    'path' => $path,
                ]);
            }

            $conversation->update([
                'last_message_at' => now(),
            ]);

            $message->loadMissing('sender');
            $conversation->loadMissing('users');

            $recipients = $conversation->users
                ->where('id', '!=', $senderId);

            if ($recipients->isNotEmpty()) {
                Notification::send(
    $recipients,
    new NewMessageNotification($message->id)
);
            }

            broadcast(new MessageSent($message))->toOthers();

            return $message;
        });
    }
}
