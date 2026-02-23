<?php

namespace App\Notifications\V1\Chat;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Expo\ExpoMessage;

class NewMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $messageId
    ) {}

    public function via(object $notifiable): array
    {
        return ['expo'];
    }

    public function toExpo(object $notifiable): ExpoMessage
    {
        $message = Message::with(['sender.store'])
            ->findOrFail($this->messageId);

        $sender = $message->sender;

        $displayName = $sender->store?->store_name
            ?? $sender->first_name;

        $body = $message->type === 'media'
            ? '📎 Sent you an attachment'
            : $message->decrypted_body;

        return ExpoMessage::create('New Message')
            ->body("{$displayName}: {$body}")
            ->data([
                'type'            => 'new_message',
                'conversation_id' => $message->conversation_id,
                'sender_id'       => $sender->id,
            ])
            ->priority('high');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message_id' => $this->messageId,
        ];
    }
}