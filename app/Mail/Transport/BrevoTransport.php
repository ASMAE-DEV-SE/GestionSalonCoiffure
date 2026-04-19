<?php

namespace App\Mail\Transport;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\MessageConverter;

class BrevoTransport extends AbstractTransport
{
    public function __construct(private string $apiKey)
    {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        $fromList = $email->getFrom();
        $from     = $fromList[0];

        $to = array_map(fn($a) => array_filter([
            'email' => $a->getAddress(),
            'name'  => $a->getName() ?: null,
        ]), $email->getTo());

        $payload = array_filter([
            'sender'      => array_filter(['email' => $from->getAddress(), 'name' => $from->getName() ?: null]),
            'to'          => array_values($to),
            'subject'     => $email->getSubject(),
            'htmlContent' => $email->getHtmlBody(),
            'textContent' => $email->getTextBody(),
        ]);

        $recipient = $to[0]['email'] ?? 'unknown';

        Log::info('Brevo: envoi email', [
            'to'          => $recipient,
            'subject'     => $email->getSubject(),
            'api_key_len' => strlen($this->apiKey),
        ]);

        $response = Http::withHeaders([
            'api-key'      => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', $payload);

        if (! $response->successful()) {
            Log::error('Brevo: échec envoi email', [
                'to'          => $recipient,
                'subject'     => $email->getSubject(),
                'http_status' => $response->status(),
                'body'        => $response->body(),
            ]);
            throw new \RuntimeException('Brevo API error: ' . $response->body());
        }

        $messageId = $response->json('messageId') ?? 'n/a';
        Log::info('Brevo: email envoyé avec succès', [
            'to'         => $recipient,
            'messageId'  => $messageId,
        ]);
    }

    public function __toString(): string
    {
        return 'brevo';
    }
}
