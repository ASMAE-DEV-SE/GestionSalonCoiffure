<?php

namespace App\Services;

use App\Models\Employe;
use App\Models\Reservation;
use App\Models\Salon;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class GestionnaireDisponibilite
{
    private int $pas = 30; // minutes entre créneaux

    /**
     * Retourne les créneaux disponibles pour un jour donné.
     * Format : [['heure'=>'09:00','datetime'=>'2026-03-19 09:00:00','disponible'=>true], ...]
     */
    public function creneauxDuJour(
        Salon    $salon,
        Service  $service,
        Carbon   $date,
        ?Employe $employe = null
    ): array {
        $jours = ['dimanche','lundi','mardi','mercredi','jeudi','vendredi','samedi'];
        $jour  = $jours[$date->dayOfWeek];

        if (! $salon->estOuvert($jour)) {
            return [];
        }

        $debut = Carbon::parse($date->toDateString() . ' ' . ($salon->heureOuverture($jour) ?? '09:00'));
        $fin   = Carbon::parse($date->toDateString() . ' ' . ($salon->heureFermeture($jour) ?? '18:00'));

        // Réservations existantes ce jour-là
        $query = Reservation::where('salon_id', $salon->id)
            ->whereDate('date_heure', $date->toDateString())
            ->whereIn('statut', ['en_attente', 'confirmee']);

        if ($employe) {
            $query->where('employe_id', $employe->id);
        }

        $reservations = $query->get();

        $creneaux = [];
        $cursor   = $debut->copy();

        while ($cursor->copy()->addMinutes($service->duree_minu ?? 30)->lte($fin)) {
            $dateheure  = $cursor->copy();
            $disponible = $this->creneauLibre($dateheure, $service->duree_minu ?? 30, $reservations, $employe);

            // Ne pas proposer les créneaux passés
            if ($dateheure->isPast()) {
                $disponible = false;
            }

            $creneaux[] = [
                'heure'      => $dateheure->format('H:i'),
                'datetime'   => $dateheure->toDateTimeString(),
                'disponible' => $disponible,
            ];

            $cursor->addMinutes($this->pas);
        }

        return $creneaux;
    }

    /**
     * Créneaux disponibles sur une plage de dates (pour le wizard step 2).
     */
    public function creneauxDisponibles(
        Salon    $salon,
        Service  $service,
        Carbon   $debut,
        Carbon   $fin
    ): Collection {
        $result = collect();

        $cursor = $debut->copy()->startOfDay();
        while ($cursor->lte($fin)) {
            $creneaux = $this->creneauxDuJour($salon, $service, $cursor->copy());
            $result->put($cursor->toDateString(), $creneaux);
            $cursor->addDay();
        }

        return $result;
    }

    /**
     * Vérifie si un créneau précis est disponible.
     */
    public function estDisponible(
        Salon    $salon,
        Service  $service,
        Carbon   $dateHeure,
        ?Employe $employe = null
    ): bool {
        if ($dateHeure->isPast()) return false;

        $jours = ['dimanche','lundi','mardi','mercredi','jeudi','vendredi','samedi'];
        $jour  = $jours[$dateHeure->dayOfWeek];

        if (! $salon->estOuvert($jour)) return false;

        $duree = $service->duree_minu ?? 30;

        $query = Reservation::where('salon_id', $salon->id)
            ->whereDate('date_heure', $dateHeure->toDateString())
            ->whereIn('statut', ['en_attente', 'confirmee']);

        if ($employe) {
            $query->where('employe_id', $employe->id);
        }

        $reservations = $query->get();

        return $this->creneauLibre($dateHeure, $duree, $reservations, $employe);
    }

    /**
     * Taux d'occupation d'un salon sur une semaine (0-100).
     */
    public function tauxOccupation(Salon $salon, Carbon $debut, Carbon $fin): float
    {
        $total    = Reservation::where('salon_id', $salon->id)
            ->whereBetween('date_heure', [$debut, $fin])
            ->whereIn('statut', ['en_attente', 'confirmee', 'terminee'])
            ->count();

        // Estimation : 8h/jour × 2 créneaux/h × 7 jours × nb_employes
        $capacite = max(1, ($salon->nb_employes ?? 1)) * 7 * 16;

        return min(100, round(($total / $capacite) * 100, 1));
    }

    /**
     * Vérifie si un créneau ne chevauche pas une réservation existante.
     */
    private function creneauLibre(
        Carbon     $dateHeure,
        int        $dureeMinutes,
        Collection $reservations,
        ?Employe   $employe = null
    ): bool {
        $finCreneau = $dateHeure->copy()->addMinutes($dureeMinutes);

        foreach ($reservations as $r) {
            // Vérification de l'employé
            if ($employe && $r->employe_id && $r->employe_id !== $employe->id) {
                continue;
            }

            $debutRes = Carbon::parse($r->date_heure);
            $finRes   = $debutRes->copy()->addMinutes($r->duree_minutes ?? 30);

            // Chevauchement si les plages se superposent
            if ($dateHeure->lt($finRes) && $finCreneau->gt($debutRes)) {
                return false;
            }
        }

        return true;
    }
}
