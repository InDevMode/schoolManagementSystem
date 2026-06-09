<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPasswordAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public string $plainPassword;

    /**
     * @param  \App\Models\User  $user
     * @param  string            $plainPassword  Mot de passe en clair (avant hashage)
     */
    public function __construct($user, string $plainPassword)
    {
        $this->user          = $user;
        $this->plainPassword = $plainPassword;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre nouveau mot de passe — ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.reset_password_admin',
            with: [
                'user'          => $this->user,
                'plainPassword' => $this->plainPassword,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
