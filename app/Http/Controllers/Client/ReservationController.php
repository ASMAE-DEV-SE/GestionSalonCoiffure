<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Salon;
use App\Models\Reservation;
use App\Models\Employe;
use App\Services\ReservationService;
use App\Services\GestionnaireDisponibilite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function __construct(
        private ReservationService       $reservationService,
        private GestionnaireDisponibilite $disponibiliteService,
    ) {}

    /** STEP 1 — Choix du service */
    public function step1(string $salon)
    {
        $salonModel = $this->getSalonOr404($salon);
        $categories = $salonModel->servicesActifs->pluck('categorie')->unique()->values();
        $services   = $salonModel->servicesActifs;
        return view('reservations.step1', compact('salonModel','services','categories'));
    }

    /** STEP 2 — Choix du créneau */
    public function step2(Request $request, string $salon)
    {
        $salonModel  = $this->getSalonOr404($salon);
        $sessionData = $request->session()->get("wizard_{$salonModel->id}", []);
        $serviceId   = $sessionData['service_id'] ?? null;

        if (! $serviceId) {
            return redirect()->route('reservations.step1', $salon)
                             ->with('error', 'Veuillez d\'abord choisir un service.');
        }

        $service  = $salonModel->servicesActifs()->findOrFail($serviceId);
        $employes = $salonModel->employesActifs;

        // Créneaux disponibles pour les 30 prochains jours
        $creneaux = $this->disponibiliteService->creneauxDisponibles(
            $salonModel,
            $service,
            now(),
            now()->addDays(30)
        );

        return view('reservations.step2', compact('salonModel','service','employes','creneaux'));
    }

    /** STEP 3 — Informations client */
    public function step3(Request $request, string $salon)
    {
        $salonModel = $this->getSalonOr404($salon);
        $user       = Auth::user();

        $sessionData = $request->session()->get("wizard_{$salonModel->id}", []);
        if (empty($sessionData['service_id']) || empty($sessionData['date_heure'])) {
            return redirect()->route('reservations.step1', $salon);
        }

        $service = $salonModel->servicesActifs()->find($sessionData['service_id']);
        $employe = isset($sessionData['employe_id'])
            ? Employe::find($sessionData['employe_id'])
            : null;

        return view('reservations.create', compact(
            'salonModel','service','employe','sessionData','user'
        ));
    }

    /** STEP 4 — Enregistrement */
    public function store(Request $request, string $salon)
    {
        $salonModel = $this->getSalonOr404($salon);

        $data = $request->validate([
            'service_id'    => ['required','exists:services,id'],
            'employe_id'    => ['nullable','exists:employes,id'],
            'date_heure'    => ['required','date','after:now'],
            'duree_minutes' => ['required','integer','min:15','max:480'],
            'notes_client'  => ['nullable','string','max:500'],
        ]);

        $reservation = $this->reservationService->creer(
            Auth::user(),
            $salonModel,
            $data
        );

        // Vider la session du wizard
        $request->session()->forget("wizard_{$salonModel->id}");

        return redirect()->route('reservations.confirmation', $reservation->id);
    }

    /** Confirmation */
    public function confirmation(int $id)
    {
        $reservation = Reservation::with(['salon.ville','service','employe'])
            ->where('client_id', Auth::id())
            ->findOrFail($id);

        return view('reservations.confirmation', compact('reservation'));
    }

    /** Liste réservations client */
    public function index(Request $request)
    {
        $query = Auth::user()
            ->reservations()
            ->with(['salon','service','employe','avis']);

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $reservations = $query
            ->orderByDesc('date_heure')
            ->paginate(10)
            ->withQueryString();

        return view('reservations.index', compact('reservations'));
    }

    /** Détail */
    public function show(int $id)
    {
        $reservation = Reservation::with(['salon','service','employe','avis'])
            ->where('client_id', Auth::id())
            ->findOrFail($id);

        return view('reservations.show', compact('reservation'));
    }

    /** Annulation client */
    public function annuler(Request $request, int $id)
    {
        $reservation = Reservation::where('client_id', Auth::id())->findOrFail($id);

        if (! $reservation->peutEtreAnnulee()) {
            return back()->with('error', 'Cette réservation ne peut plus être annulée (moins de 24h avant le RDV).');
        }

        $reservation->update([
            'statut'      => 'annulee',
            'annulee_par' => 'client',
            'date_annul'  => now(),
            'motif_annul' => $request->motif ?? null,
        ]);

        return redirect()->route('client.reservations.index')
                         ->with('success', 'Réservation annulée.');
    }

    /** Sauvegarde étape wizard en session */
    public function saveStep(Request $request, string $salon)
    {
        $salonModel = $this->getSalonOr404($salon);
        $step       = $request->input('step');
        $data       = $request->except(['_token','step']);

        $request->session()->put("wizard_{$salonModel->id}_{$step}", $data);
        $request->session()->put("wizard_{$salonModel->id}", array_merge(
            $request->session()->get("wizard_{$salonModel->id}", []),
            $data
        ));

        return response()->json(['ok' => true]);
    }

    private function getSalonOr404(string $slugOrId): Salon
    {
        if (is_numeric($slugOrId)) {
            return Salon::valides()->findOrFail($slugOrId);
        }

        // Recherche par slug (nom_salon slugifié) sans charger tous les salons
        $salon = Salon::valides()
            ->whereRaw('LOWER(REPLACE(REPLACE(nom_salon, " ", "-"), "\'", "")) = ?', [
                strtolower(str_replace(["'", ' '], ['', '-'], $slugOrId))
            ])
            ->first();

        // Fallback : recherche exacte sur nom_salon si le slug ne matche pas
        if (! $salon) {
            $salon = Salon::valides()->get()
                ->first(fn($s) => $s->slug === $slugOrId);
        }

        if (! $salon) abort(404);
        return $salon;
    }
}
