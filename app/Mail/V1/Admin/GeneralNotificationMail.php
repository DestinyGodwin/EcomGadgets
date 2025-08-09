<?php

namespace App\Mail\V1\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GeneralNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $subjectText;
    public string $messageText;

    public function __construct(string $subject, string $messageText)
    {
        $this->subjectText = $subject;
        $this->messageText = $messageText;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectText,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin.general_notification',
            with: [
                'messageText' => $this->messageText,
                'subject' => $this->subjectText, 
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
