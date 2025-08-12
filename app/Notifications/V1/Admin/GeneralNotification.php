<?php

namespace App\Notifications\V1\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Mail\V1\Admin\GeneralNotificationMail;

class GeneralNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $subject;
    protected string $message;

    public function __construct(string $subject, string $message)
    {
        $this->subject = $subject;
        $this->message = $message;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new GeneralNotificationMail($this->subject, $this->message))
            ->to($notifiable->email);
    }

    public function toDatabase($notifiable): array
    {
        return [
            'subject' => $this->subject,
            'message' => $this->message,
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
