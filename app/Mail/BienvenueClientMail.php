<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BienvenueClientMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $prenom,
        public string $email,
        public string $urlVerification,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bienvenue sur Salonify – Vérifiez votre email',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.bienvenue_client',
        );
    }
}
