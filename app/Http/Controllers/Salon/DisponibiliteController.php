<?php

namespace App\Http\Controllers\Salon;

use App\Http\Controllers\Controller;
use App\Models\Employe;
use App\Models\Reservation;
use App\Services\GestionnaireDisponibilite;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class DisponibiliteController extends Controller
{
    public function __construct(
        private GestionnaireDisponibilite $gestionnaire
    ) {}

    private function salon()
    {
        return Auth::user()->salon()->firstOrFail();
    }

    /*
    |------------------------------------------------------------------
    | GET /salon/disponibilites
    | Affiche le calendrier hebdomadaire avec les réservations et bloqués
    |------------------------------------------------------------------
    */
    public function index(Request $request): View
    {
        $salon = $this->salon();

        // Semaine affichée (défaut = semaine courante)
        $debutSemaine = $request->filled('debut')
            ? \Carbon\Carbon::parse($request->debut)->startOfWeek()
            : now()->startOfWeek();

        $finSemaine = $debutSemaine->copy()->endOfWeek();

        // Réservations de la semaine avec relations
        $reservations = Reservation::where('salon_id', $salon->id)
            ->whereBetween('date_heure', [$debutSemaine, $finSemaine])
            ->whereIn('statut', ['en_attente', 'confirmee'])
            ->with(['service', 'employe', 'client'])
            ->orderBy('date_heure')
            ->get();

        // Employés actifs pour le filtre
        $employes = Employe::where('salon_id', $salon->id)->actifs()->get();

        // Filtrer par employé si demandé
        $employeFiltre = $request->employe_id;
        if ($employeFiltre) {
            $reservations = $reservations->where('employe_id', $employeFiltre);
        }

        // Organiser par jour et heure pour la vue calendrier
        $calendrier = $this->organiserParJour($reservations, $debutSemaine);

        // Taux d'occupation de la semaine (en %)
        $tauxOccupation = $this->gestionnaire->tauxOccupation($salon, $debutSemaine, $finSemaine);

        // CA estimé de la semaine
        $caEstime = $reservations->sum(fn($r) => $r->service->prix ?? 0);

        return view('salon.disponibilites', compact(
            'salon', 'employes', 'calendrier',
            'debutSemaine', 'finSemaine',
            'tauxOccupation', 'caEstime', 'employeFiltre'
        ));
    }

    /*
    |------------------------------------------------------------------
    | GET API — créneaux disponibles (appelé par le wizard)
    | /api/disponibilites?salon_id=1&service_id=2&date=2026-03-19
    |------------------------------------------------------------------
    */
    public function creneaux(Request $request): JsonResponse
    {
        $request->validate([
            'salon_id'   => ['required', 'exists:salons,id'],
            'service_id' => ['required', 'exists:services,id'],
            'date'       => ['required', 'date'],
            'employe_id' => ['nullable', 'exists:employes,id'],
        ]);

        $salon   = \App\Models\Salon::valides()->findOrFail($request->salon_id);
        $service = $salon->servicesActifs()->findOrFail($request->service_id);
        $date    = \Carbon\Carbon::parse($request->date);

        $creneaux = $this->gestionnaire->creneauxDuJour(
            $salon,
            $service,
            $date,
            $request->employe_id ? Employe::find($request->employe_id) : null
        );

        return response()->json($creneaux);
    }

    /*
    |------------------------------------------------------------------
    | POST /salon/disponibilites/bloquer
    | Bloquer un créneau manuellement (pause, absence...)
    |------------------------------------------------------------------
    */
    public function bloquer(Request $request): RedirectResponse
    {
        $salon = $this->salon();

        $request->validate([
            'employe_id' => ['required', 'exists:employes,id'],
            'date_heure' => ['required', 'date', 'after:now'],
            'duree'      => ['required', 'integer', 'min:15', 'max:480'],
            'motif'      => ['nullable', 'string', 'max:120'],
        ]);

        // Créer une réservation fantôme de type "bloqué"
        // On réutilise la table reservations avec notes_salon = '__bloque__'
        $employe = Employe::where('salon_id', $salon->id)
            ->findOrFail($request->employe_id);

        Reservation::create([
            'client_id'     => Auth::id(),   // le gérant lui-même
            'salon_id'      => $salon->id,
            'service_id'    => $salon->servicesActifs()->first()->id,
            'employe_id'    => $employe->id,
            'date_heure'    => $request->date_heure,
            'duree_minutes' => $request->duree,
            'statut'        => 'confirmee',
            'notes_salon'   => '__bloque__',
            'notes_client'  => $request->motif ?? 'Créneau bloqué',
        ]);

        return back()->with('success', 'Créneau bloqué pour ' . $employe->nomComplet() . '.');
    }

    /*
    |------------------------------------------------------------------
    | DELETE /salon/disponibilites/{id}
    |------------------------------------------------------------------
    */
    public function debloquer(int $id): RedirectResponse
    {
        $salon = $this->salon();

        $bloc = Reservation::where('salon_id', $salon->id)
            ->where('notes_salon', '__bloque__')
            ->findOrFail($id);

        $bloc->delete();

        return back()->with('success', 'Créneau débloqué.');
    }

    /*
    |------------------------------------------------------------------
    | Helper — organiser les réservations par jour (Carbon)
    |------------------------------------------------------------------
    */
    private function organiserParJour($reservations, \Carbon\Carbon $debutSemaine): array
    {
        $calendrier = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $debutSemaine->copy()->addDays($i);
            $calendrier[$date->format('Y-m-d')] = [
                'date'         => $date,
                'reservations' => $reservations->filter(fn($r) =>
                    $r->date_heure->toDateString() === $date->toDateString()
                )->values(),
            ];
        }

        return $calendrier;
    }
}
