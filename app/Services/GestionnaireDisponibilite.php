<?php

namespace App\Services;

use App\Models\DisponibiliteException;
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
        // Plage horaire effective du salon pour ce jour (tient compte des exceptions)
        $plage = $this->plageSalonPourDate($salon, $date);
        if (! $plage) {
            return [];
        }

        [$debut, $fin] = $plage;

        // Si un employé précis est demandé, restreindre à ses horaires + absences
        if ($employe) {
            $plageEmp = $this->plageEmployePourDate($employe, $date);
            if (! $plageEmp) return [];
            [$dE, $fE] = $plageEmp;
            if ($dE->gt($debut)) $debut = $dE;
            if ($fE->lt($fin))   $fin   = $fE;
            if ($debut->gte($fin)) return [];
        }

        $reservations = $this->reservationsDuJour($salon, $date, $employe);

        $creneaux = [];
        $cursor   = $debut->copy();
        $dureeSvc = $service->duree_minu ?? 30;

        while ($cursor->copy()->addMinutes($dureeSvc)->lte($fin)) {
            $dateheure  = $cursor->copy();
            $disponible = $this->creneauLibre($dateheure, $dureeSvc, $reservations, $employe);

            // Si pas d'employé précis mais équipe présente, au moins un employé doit couvrir ce créneau
            if ($disponible && ! $employe) {
                $disponible = $this->auMoinsUnEmployeDispo($salon, $dateheure, $dureeSvc);
            }

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
     * Créneaux disponibles sur une plage de dates.
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
            $result->put($cursor->toDateString(), $this->creneauxDuJour($salon, $service, $cursor->copy()));
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

        $plage = $this->plageSalonPourDate($salon, $dateHeure);
        if (! $plage) return false;
        [$debut, $fin] = $plage;

        $duree   = $service->duree_minu ?? 30;
        $finCren = $dateHeure->copy()->addMinutes($duree);

        if ($dateHeure->lt($debut) || $finCren->gt($fin)) return false;

        if ($employe) {
            $plageEmp = $this->plageEmployePourDate($employe, $dateHeure);
            if (! $plageEmp) return false;
            [$dE, $fE] = $plageEmp;
            if ($dateHeure->lt($dE) || $finCren->gt($fE)) return false;
        }

        $reservations = $this->reservationsDuJour($salon, $dateHeure, $employe);

        if (! $this->creneauLibre($dateHeure, $duree, $reservations, $employe)) {
            return false;
        }

        if (! $employe) {
            return $this->auMoinsUnEmployeDispo($salon, $dateHeure, $duree);
        }

        return true;
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

        $capacite = max(1, ($salon->nb_employes ?? 1)) * 7 * 16;

        return min(100, round(($total / $capacite) * 100, 1));
    }

    /*
    |------------------------------------------------------------------
    | Helpers : plages horaires effectives (horaires hebdo + exceptions)
    |------------------------------------------------------------------
    */

    /** Retourne [Carbon $debut, Carbon $fin] pour le salon à cette date, ou null si fermé. */
    private function plageSalonPourDate(Salon $salon, Carbon $date): ?array
    {
        // Exception au niveau salon (employe_id null)
        $ex = DisponibiliteException::where('salon_id', $salon->id)
            ->whereNull('employe_id')
            ->whereDate('date', $date->toDateString())
            ->first();

        if ($ex) {
            if ($ex->ferme) return null;
            return [
                Carbon::parse($date->toDateString() . ' ' . $ex->debut),
                Carbon::parse($date->toDateString() . ' ' . $ex->fin),
            ];
        }

        // Horaires hebdo
        $jours = ['dimanche','lundi','mardi','mercredi','jeudi','vendredi','samedi'];
        $jour  = $jours[$date->dayOfWeek];

        if (! $salon->estOuvert($jour)) return null;

        $debut = $salon->heureOuverture($jour) ?? '09:00';
        $fin   = $salon->heureFermeture($jour) ?? '18:00';

        return [
            Carbon::parse($date->toDateString() . ' ' . $debut),
            Carbon::parse($date->toDateString() . ' ' . $fin),
        ];
    }

    /** Retourne [Carbon $debut, Carbon $fin] pour l'employé, ou null si absent/repos. */
    private function plageEmployePourDate(Employe $employe, Carbon $date): ?array
    {
        // Exception employé précis
        $ex = DisponibiliteException::where('employe_id', $employe->id)
            ->whereDate('date', $date->toDateString())
            ->first();

        if ($ex) {
            if ($ex->ferme) return null;
            return [
                Carbon::parse($date->toDateString() . ' ' . $ex->debut),
                Carbon::parse($date->toDateString() . ' ' . $ex->fin),
            ];
        }

        // Horaires hebdo de l'employé
        $jours = ['dimanche','lundi','mardi','mercredi','jeudi','vendredi','samedi'];
        $jour  = $jours[$date->dayOfWeek];

        $h = $employe->horaires ?? [];
        if (! $h || ! isset($h[$jour]) || ($h[$jour]['ferme'] ?? false)) {
            // Si pas d'horaires renseignés, on s'aligne sur le salon
            if (empty($h)) return null;
            return null;
        }

        return [
            Carbon::parse($date->toDateString() . ' ' . ($h[$jour]['debut'] ?? '09:00')),
            Carbon::parse($date->toDateString() . ' ' . ($h[$jour]['fin']   ?? '18:00')),
        ];
    }

    private function reservationsDuJour(Salon $salon, Carbon $date, ?Employe $employe): Collection
    {
        $q = Reservation::where('salon_id', $salon->id)
            ->whereDate('date_heure', $date->toDateString())
            ->whereIn('statut', ['en_attente', 'confirmee']);

        if ($employe) {
            $q->where('employe_id', $employe->id);
        }

        return $q->get();
    }

    /**
     * Vrai si au moins un employé actif du salon peut prendre ce créneau
     * (horaires OK, pas d'exception d'absence, pas de réservation qui chevauche).
     */
    private function auMoinsUnEmployeDispo(Salon $salon, Carbon $dateHeure, int $duree): bool
    {
        $employes = $salon->employesActifs;

        // Si aucun employé configuré, on considère disponible (salon type individuel)
        if ($employes->isEmpty()) return true;

        foreach ($employes as $emp) {
            $plage = $this->plageEmployePourDate($emp, $dateHeure);
            if (! $plage) continue;
            [$dE, $fE] = $plage;
            $finCren = $dateHeure->copy()->addMinutes($duree);
            if ($dateHeure->lt($dE) || $finCren->gt($fE)) continue;

            $resas = $this->reservationsDuJour($salon, $dateHeure, $emp);
            if ($this->creneauLibre($dateHeure, $duree, $resas, $emp)) {
                return true;
            }
        }

        return false;
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
            if ($employe && $r->employe_id && $r->employe_id !== $employe->id) {
                continue;
            }

            $debutRes = Carbon::parse($r->date_heure);
            $finRes   = $debutRes->copy()->addMinutes($r->duree_minutes ?? 30);

            if ($dateHeure->lt($finRes) && $finCreneau->gt($debutRes)) {
                return false;
            }
        }

        return true;
    }
}
