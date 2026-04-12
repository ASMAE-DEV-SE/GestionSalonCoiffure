<?php

namespace App\Services;

use App\Mail\Rappel24hMail;
use App\Mail\Rappel2hMail;
use App\Mail\ReservationConfirmeeMail;
use App\Mail\ReservationAnnuleeMail;
use App\Models\Notification;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    private array $messages = [
        'nouvelle_reservation' => 'Nouvelle réservation de :client pour :service le :date à :heure.',
        'reservation_confirmee' => 'Votre réservation chez :salon pour :service le :date à :heure a été confirmée.',
        'reservation_annulee'   => 'Votre réservation chez :salon pour :service le :date a été annulée. Motif : :motif.',
        'rappel_24h'            => 'Rappel : votre rendez-vous chez :salon est demain à :heure pour :service.',
        'rappel_2h'             => 'Rappel : votre rendez-vous chez :salon est dans 2h (:heure) pour :service.',
    ];

    /**
     * Crée une notification en base pour un utilisateur.
     */
    public function envoyer(int $userId, string $type, array $params = []): Notification
    {
        $message = $this->messages[$type] ?? $type;

        foreach ($params as $key => $value) {
            $message = str_replace(':' . $key, $value, $message);
        }

        return Notification::create([
            'user_id' => $userId,
            'type'    => $type,
            'donnees' => ['message' => $message],
            'lu_le'   => null,
            'cree_le' => now(),
        ]);
    }

    /**
     * Crée une notification ET envoie l'email correspondant (si disponible).
     */
    public function envoyerAvecEmail(int $userId, string $type, array $params = [], ?Reservation $reservation = null): Notification
    {
        $notif = $this->envoyer($userId, $type, $params);

        try {
            $user = User::find($userId);
            if (! $user) return $notif;

            match ($type) {
                'reservation_confirmee' => $reservation
                    ? Mail::to($user->email)->send(new ReservationConfirmeeMail($reservation))
                    : null,

                'reservation_annulee' => $reservation
                    ? Mail::to($user->email)->send(new ReservationAnnuleeMail($reservation))
                    : null,

                'rappel_24h' => $reservation
                    ? Mail::to($user->email)->send(new Rappel24hMail($reservation))
                    : null,

                'rappel_2h' => $reservation
                    ? Mail::to($user->email)->send(new Rappel2hMail($reservation))
                    : null,

                default => null,
            };
        } catch (\Throwable $e) {
            Log::error('Erreur envoi email notification', [
                'type'    => $type,
                'userId'  => $userId,
                'message' => $e->getMessage(),
            ]);
        }

        return $notif;
    }

    /**
     * Envoie les rappels 24h avant les réservations confirmées.
     */
    public function envoyerRappels24h(): int
    {
        $reservations = Reservation::where('statut', 'confirmee')
            ->where('rappel_24h', false)
            ->whereBetween('date_heure', [
                now()->addHours(23),
                now()->addHours(25),
            ])
            ->with(['client', 'salon.ville', 'service', 'employe'])
            ->get();

        foreach ($reservations as $r) {
            /** @var Reservation $r */
            $this->envoyerAvecEmail($r->client_id, 'rappel_24h', [
                'salon'   => $r->salon->nom_salon,
                'heure'   => $r->date_heure->format('H:i'),
                'service' => $r->service->nom_service,
            ], $r);

            $r->update(['rappel_24h' => true]);

            Log::info('Rappel 24h envoyé', ['reservation_id' => $r->id, 'client_id' => $r->client_id]);
        }

        return $reservations->count();
    }

    /**
     * Envoie les rappels 2h avant les réservations confirmées.
     */
    public function envoyerRappels2h(): int
    {
        $reservations = Reservation::where('statut', 'confirmee')
            ->where('rappel_2h', false)
            ->whereBetween('date_heure', [
                now()->addHours(1)->addMinutes(50),
                now()->addHours(2)->addMinutes(10),
            ])
            ->with(['client', 'salon.ville', 'service', 'employe'])
            ->get();

        foreach ($reservations as $r) {
            /** @var Reservation $r */
            $this->envoyerAvecEmail($r->client_id, 'rappel_2h', [
                'salon'   => $r->salon->nom_salon,
                'heure'   => $r->date_heure->format('H:i'),
                'service' => $r->service->nom_service,
            ], $r);

            $r->update(['rappel_2h' => true]);
        }

        return $reservations->count();
    }
}
