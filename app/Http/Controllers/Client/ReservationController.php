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
use Illuminate\Support\Facades\Log;

class ReservationController extends Controller
{
    public function __construct(
        private ReservationService       $reservationService,
        private GestionnaireDisponibilite $disponibiliteService,
    ) {}

    /** STEP 1 — Choix des services (multi) */
    public function step1(string $salon)
    {
        $salonModel = $this->getSalonOr404($salon);
        Log::info('[Client] Wizard step1', [
            'user_id'  => Auth::id(),
            'salon_id' => $salonModel->id,
        ]);
        $categories = $salonModel->servicesActifs->pluck('categorie')->unique()->values();
        $services   = $salonModel->servicesActifs;
        return view('reservations.step1', compact('salonModel','services','categories'));
    }

    /** STEP 2 — Choix des créneaux (un par service) */
    public function step2(Request $request, string $salon)
    {
        $salonModel  = $this->getSalonOr404($salon);
        $sessionData = $request->session()->get("wizard_{$salonModel->id}", []);
        $serviceIds  = $sessionData['service_ids'] ?? null;

        // Rétro-compatibilité : ancien format single-service
        if (! $serviceIds && ! empty($sessionData['service_id'])) {
            $serviceIds = [$sessionData['service_id']];
        }

        if (empty($serviceIds)) {
            return redirect()->route('reservations.step1', $salon)
                             ->with('error', 'Veuillez d\'abord choisir au moins un service.');
        }

        $services = $salonModel->servicesActifs()->whereIn('id', $serviceIds)->get();
        if ($services->isEmpty()) {
            return redirect()->route('reservations.step1', $salon)
                             ->with('error', 'Les services choisis sont introuvables.');
        }

        $employes = $salonModel->employesActifs;

        // Créneaux par service pour les 30 prochains jours
        $creneauxParService = [];
        foreach ($services as $svc) {
            $creneauxParService[$svc->id] = $this->disponibiliteService->creneauxDisponibles(
                $salonModel, $svc, now(), now()->addDays(30)
            );
        }

        return view('reservations.step2', compact('salonModel','services','employes','creneauxParService'));
    }

    /** STEP 3 — Informations client */
    public function step3(Request $request, string $salon)
    {
        $salonModel = $this->getSalonOr404($salon);
        $user       = Auth::user();

        $sessionData = $request->session()->get("wizard_{$salonModel->id}", []);

        // Normaliser : accepter ancien format (service_id + date_heure)
        $selections = $sessionData['selections'] ?? null;
        if (! $selections && ! empty($sessionData['service_id']) && ! empty($sessionData['date_heure'])) {
            $selections = [[
                'service_id' => (int) $sessionData['service_id'],
                'date_heure' => $sessionData['date_heure'],
                'employe_id' => $sessionData['employe_id'] ?? null,
            ]];
        }

        if (empty($selections)) {
            return redirect()->route('reservations.step1', $salon);
        }

        $ids      = collect($selections)->pluck('service_id')->all();
        $services = $salonModel->servicesActifs()->whereIn('id', $ids)->get()->keyBy('id');
        $employes = Employe::whereIn('id', collect($selections)->pluck('employe_id')->filter()->all())->get()->keyBy('id');

        // Préparer les items pour la vue
        $items = collect($selections)->map(function($s) use ($services, $employes) {
            return [
                'service'    => $services[$s['service_id']] ?? null,
                'employe'    => !empty($s['employe_id']) ? ($employes[$s['employe_id']] ?? null) : null,
                'date_heure' => $s['date_heure'],
            ];
        })->filter(fn($i) => $i['service'] !== null)->values();

        $total = $items->sum(fn($i) => (float) $i['service']->prix);

        return view('reservations.create', compact(
            'salonModel','items','user','total'
        ));
    }

    /** STEP 4 — Enregistrement (multi-réservations) */
    public function store(Request $request, string $salon)
    {
        $salonModel = $this->getSalonOr404($salon);

        Log::info('[Client] Wizard store → tentative création groupe', [
            'user_id'  => Auth::id(),
            'salon_id' => $salonModel->id,
            'count'    => count($request->input('selections', [])),
        ]);

        $data = $request->validate([
            'selections'                  => ['required','array','min:1'],
            'selections.*.service_id'    => ['required','exists:services,id'],
            'selections.*.employe_id'    => ['nullable','exists:employes,id'],
            'selections.*.date_heure'    => ['required','date','after:now'],
            'notes_client'                => ['nullable','string','max:500'],
        ]);

        $reservations = $this->reservationService->creerGroupe(
            Auth::user(),
            $salonModel,
            $data['selections'],
            $data['notes_client'] ?? null
        );

        $request->session()->forget("wizard_{$salonModel->id}");

        Log::info('[Client] Wizard store ✓ groupe créé', [
            'reservation_ids' => $reservations->pluck('id'),
            'user_id'         => Auth::id(),
        ]);

        return redirect()->route('reservations.confirmation', $reservations->first()->id);
    }

    /** Confirmation */
    public function confirmation(int $id)
    {
        $reservation = Reservation::with(['salon.ville','service','employe'])
            ->where('client_id', Auth::id())
            ->findOrFail($id);

        // Si la réservation fait partie d'un groupe, charger toutes les réservations du groupe
        $groupe = null;
        if ($reservation->groupe_uuid) {
            $groupe = Reservation::with(['service','employe'])
                ->where('groupe_uuid', $reservation->groupe_uuid)
                ->where('client_id', Auth::id())
                ->orderBy('date_heure')
                ->get();
        }

        return view('reservations.confirmation', compact('reservation','groupe'));
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
            Log::warning('[Client] Annulation refusée (trop tard)', [
                'reservation_id' => $id,
                'user_id'        => Auth::id(),
            ]);
            return back()->with('error', 'Cette réservation ne peut plus être annulée (moins de 24h avant le RDV).');
        }

        $reservation->update([
            'statut'      => 'annulee',
            'annulee_par' => 'client',
            'date_annul'  => now(),
            'motif_annul' => $request->motif ?? null,
        ]);

        Log::info('[Client] Reservation annulée par client', [
            'reservation_id' => $id,
            'user_id'        => Auth::id(),
            'motif'          => $request->motif,
        ]);

        return redirect()->route('client.reservations.index')
                         ->with('success', 'Réservation annulée.');
    }

    /** Sauvegarde étape wizard en session */
    public function saveStep(Request $request, string $salon)
    {
        $salonModel = $this->getSalonOr404($salon);
        $step       = $request->input('step');
        $data       = $request->except(['_token', 'step', 'redirect_to']);

        $request->session()->put("wizard_{$salonModel->id}_{$step}", $data);
        $request->session()->put("wizard_{$salonModel->id}", array_merge(
            $request->session()->get("wizard_{$salonModel->id}", []),
            $data
        ));

        // Formulaire HTML classique → redirect_to présent
        if ($redirectTo = $request->input('redirect_to')) {
            return redirect($redirectTo);
        }

        // Requête AJAX → réponse JSON
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
