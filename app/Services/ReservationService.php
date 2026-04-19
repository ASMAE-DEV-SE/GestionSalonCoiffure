<?php

namespace App\Services;

use App\Mail\NouvelleReservationMail;
use App\Models\Employe;
use App\Models\Reservation;
use App\Models\Salon;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReservationService
{
    public function __construct(
        private GestionnaireDisponibilite $disponibilite,
        private NotificationService       $notifService
    ) {}

    /**
     * Crée une réservation après validation de disponibilité.
     */
    public function creer(User $client, Salon $salon, array $data): Reservation
    {
        $service  = Service::findOrFail($data['service_id']);
        $employe  = isset($data['employe_id']) ? Employe::find($data['employe_id']) : null;
        $dateHeure = Carbon::parse($data['date_heure']);

        // Vérification finale de disponibilité
        if (! $this->disponibilite->estDisponible($salon, $service, $dateHeure, $employe)) {
            abort(409, 'Ce créneau n\'est plus disponible. Veuillez en choisir un autre.');
        }

        $reservation = Reservation::create([
            'client_id'     => $client->id,
            'salon_id'      => $salon->id,
            'service_id'    => $service->id,
            'employe_id'    => $employe?->id,
            'date_heure'    => $dateHeure,
            'duree_minutes' => $data['duree_minutes'] ?? $service->duree_minu ?? 30,
            'notes_client'  => $data['notes_client'] ?? null,
            'statut'        => 'en_attente',
        ]);

        // Notifier le salon (base de données)
        $this->notifService->envoyer(
            $salon->user_id,
            'nouvelle_reservation',
            [
                'client'  => $client->nomComplet(),
                'service' => $service->nom_service,
                'date'    => $dateHeure->translatedFormat('D d M Y'),
                'heure'   => $dateHeure->format('H:i'),
            ]
        );

        // Email au gérant du salon
        try {
            $reservation->load(['client', 'salon.ville', 'service', 'employe']);
            Mail::to($salon->user->email)->send(new NouvelleReservationMail($reservation));
        } catch (\Throwable $e) {
            Log::error('Erreur email nouvelle_reservation', [
                'reservation_id' => $reservation->id,
                'message'        => $e->getMessage(),
            ]);
        }

        return $reservation;
    }
}
