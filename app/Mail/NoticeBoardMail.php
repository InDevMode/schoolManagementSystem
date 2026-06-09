<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NoticeBoardMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  object  $notice  Instance de CommunicateModel avec created_by_name
     * @param  object  $recipient  Instance User destinataire
     */
    public function __construct(
        public readonly object $notice,
        public readonly object $recipient,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '📢 ' . $this->notice->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.notice_board',
            with: [
                'notice'    => $this->notice,
                'recipient' => $this->recipient,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
