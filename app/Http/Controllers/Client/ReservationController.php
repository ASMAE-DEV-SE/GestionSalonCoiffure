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

    /** STEP 1 — Choix du service (Modernisé pour autoriser le multi-choix) */
    public function step1(string $salon)
    {
        $salonModel = $this->getSalonOr404($salon);
        $categories = $salonModel->servicesActifs->pluck('categorie')->unique()->values();
        $services   = $salonModel->servicesActifs;
        
        return view('reservations.step1', compact('salonModel','services','categories'));
    }

    /** STEP 2 — Choix du créneau (Optimisé AJAX & Multi-services) */
    public function step2(Request $request, string $salon)
    {
        $salonModel  = $this->getSalonOr404($salon);
        $sessionData = $request->session()->get("wizard_{$salonModel->id}", []);
        
        // On récupère soit le tableau service_ids, soit l'unique service_id (flexibilité)
        $serviceIds = $sessionData['service_ids'] ?? ($sessionData['service_id'] ? [$sessionData['service_id']] : null);

        if (!$serviceIds) {
            return redirect()->route('reservations.step1', $salon)
                             ->with('error', 'Veuillez d\'abord choisir au moins un service.');
        }

        $services = $salonModel->servicesActifs()->whereIn('id', $serviceIds)->get();
        $employes = $salonModel->employesActifs;

        // Note : Les créneaux sont désormais chargés via getCreneaux() en AJAX dans la vue
        return view('reservations.step2', compact('salonModel','services','employes'));
    }

    /** AJAX — Nouvelle fonction pour le chargement dynamique (indispensable pour l'optimisation) */
    public function getCreneaux(Request $request, string $salon, int $serviceId)
    {
        $salonModel = $this->getSalonOr404($salon);
        $service    = $salonModel->servicesActifs()->findOrFail($serviceId);

        $annee = (int) $request->input('annee', now()->year);
        $mois  = (int) $request->input('mois',  now()->month);

        $debut = \Carbon\Carbon::createFromDate($annee, $mois, 1)->startOfDay();
        $fin   = $debut->copy()->endOfMonth()->endOfDay();

        $creneaux = $this->disponibiliteService->creneauxDisponibles($salonModel, $service, $debut, $fin);

        return response()->json($creneaux);
    }

    /** STEP 3 — Informations client (Adapté au panier) */
    public function step3(Request $request, string $salon)
    {
        $salonModel = $this->getSalonOr404($salon);
        $user       = Auth::user();
        $sessionData = $request->session()->get("wizard_{$salonModel->id}", []);

        // On normalise les sélections pour gérer le panier
        $selections = $sessionData['selections'] ?? null;
        if (!$selections && !empty($sessionData['service_id']) && !empty($sessionData['date_heure'])) {
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

        $items = collect($selections)->map(function($s) use ($services, $employes) {
            return [
                'service'    => $services[$s['service_id']] ?? null,
                'employe'    => !empty($s['employe_id']) ? ($employes[$s['employe_id']] ?? null) : null,
                'date_heure' => $s['date_heure'],
            ];
        })->filter(fn($i) => $i['service'] !== null)->values();

        $total = $items->sum(fn($i) => (float) $i['service']->prix);

        return view('reservations.create', compact('salonModel','items','user','total'));
    }

    /** STEP 4 — Enregistrement (Utilise creerGroupe pour la flexibilité) */
    public function store(Request $request, string $salon)
    {
        $salonModel = $this->getSalonOr404($salon);

        $data = $request->validate([
            'selections'                => ['required','array','min:1'],
            'selections.*.service_id'   => ['required','exists:services,id'],
            'selections.*.employe_id'   => ['nullable','exists:employes,id'],
            'selections.*.date_heure'   => ['required','date','after:now'],
            'notes_client'              => ['nullable','string','max:500'],
        ]);

        // Appel à creerGroupe pour supporter le panier
        $reservations = $this->reservationService->creerGroupe(
            Auth::user(),
            $salonModel,
            $data['selections'],
            $data['notes_client'] ?? null
        );

        $request->session()->forget("wizard_{$salonModel->id}");

        return redirect()->route('reservations.confirmation', $reservations->first()->id);
    }

    /** Les autres fonctions (confirmation, index, show, annuler, saveStep, getSalonOr404) 
        restent identiques pour préserver l'intégrité du projet. */
}