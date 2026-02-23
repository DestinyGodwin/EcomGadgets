<?php

namespace App\Services\V1\Chat;

use App\Models\Message;
use App\Models\Conversation;
use App\Events\Chat\MessageSent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\V1\Chat\NewMessageNotification;
use Illuminate\Database\Eloquent\Collection;

class ChatService
{
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
    //             'sender_id'       => $senderId,
    //             'body'            => $body,
    //             'type'            => $images ? 'media' : 'text',
    //         ]);

    //         foreach ($images as $image) {
    //             $path = $image->store('chat', 'public');

    //             $message->media()->create([
    //                 'path' => $path,
    //             ]);
    //         }

    //         $conversation->update([
    //             'last_message_at' => now(),
    //         ]);

    //         // Get recipients directly from relationship query (cleaner & no eager load needed)
    //         $recipients = $conversation->users()
    //             ->where('users.id', '!=', $senderId)
    //             ->get();

    //         // Send notification ONLY after successful commit
    //         DB::afterCommit(function () use ($recipients, $message) {

    //             if ($recipients->isNotEmpty()) {
    //                 Notification::send(
    //                     $recipients,
    //                     new NewMessageNotification($message->id)
    //                 );
    //             }

    //             broadcast(new MessageSent($message))->toOthers();
    //         });

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

        foreach ($images as $file) {
            $message
                ->addMedia($file)
                ->toMediaCollection('chat_media');
        }

        $conversation->update([
            'last_message_at' => now(),
        ]);

        $recipients = $conversation->users()
            ->where('users.id', '!=', $senderId)
            ->get();

        DB::afterCommit(function () use ($recipients, $message) {

            if ($recipients->isNotEmpty()) {
                Notification::send(
                    $recipients,
                    new NewMessageNotification($message->id)
                );
            }

            broadcast(new MessageSent($message))->toOthers();
        });

        return $message;
    });
}
}