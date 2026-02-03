<?php

namespace App\Notifications\V1\Chat;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Expo\ExpoMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private Message $message)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['expo'];
    }

    /**
     * Get the mail representation of the notification.
     */
     public function toExpo($notifiable): ExpoMessage
    {
        $sender = $this->message->sender;

        $body = $this->message->type === 'media'
            ? '📎 Sent you an attachment'
            : $this->message->decrypted_body;

        return ExpoMessage::create('New Message')
            ->body("{$sender->first_name}: {$body}")
            ->data([
                'type' => 'new_message',
                'conversation_id' => $this->message->conversation_id,
                'sender_id' => $sender->id,
            ])
            ->priority('high');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
