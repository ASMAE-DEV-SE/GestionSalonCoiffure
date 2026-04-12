<?php

namespace App\Http\Controllers\Salon;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Employe;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function __construct(private NotificationService $notifService) {}

    private function salon()
    {
        return Auth::user()->salon()->firstOrFail();
    }

    /*
    |------------------------------------------------------------------
    | GET /salon/reservations
    |------------------------------------------------------------------
    */
    public function index(Request $request): View
    {
        $salon = $this->salon();

        $query = Reservation::where('salon_id', $salon->id)
            ->with(['service', 'employe', 'client', 'avis']);

        // Filtre statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Filtre date
        if ($request->filled('date')) {
            $query->whereDate('date_heure', $request->date);
        }

        // Filtre employé
        if ($request->filled('employe_id')) {
            $query->where('employe_id', $request->employe_id);
        }

        // Tri par défaut : prochains d'abord
        $query->orderBy('date_heure', $request->get('tri') === 'ancien' ? 'asc' : 'desc');

        $reservations = $query->paginate(15)->withQueryString();

        $employes = Employe::where('salon_id', $salon->id)->actifs()->get();

        // Compteurs par statut pour les onglets
        $compteurs = [
            'en_attente' => Reservation::where('salon_id', $salon->id)
                ->where('statut', 'en_attente')
                ->where('date_heure', '>=', now())->count(),
            'confirmee'  => Reservation::where('salon_id', $salon->id)
                ->where('statut', 'confirmee')
                ->where('date_heure', '>=', now())->count(),
            'terminee'   => Reservation::where('salon_id', $salon->id)
                ->where('statut', 'terminee')->count(),
            'annulee'    => Reservation::where('salon_id', $salon->id)
                ->where('statut', 'annulee')->count(),
        ];

        return view('salon.reservations', compact(
            'salon', 'reservations', 'employes', 'compteurs'
        ));
    }

    /*
    |------------------------------------------------------------------
    | GET /salon/reservations/{id}
    |------------------------------------------------------------------
    */
    public function show(int $id): View
    {
        $salon       = $this->salon();
        $reservation = Reservation::where('salon_id', $salon->id)
            ->with(['service', 'employe', 'client', 'avis'])
            ->findOrFail($id);

        return view('salon.reservation_detail', compact('salon', 'reservation'));
    }

    /*
    |------------------------------------------------------------------
    | POST /salon/reservations/{id}/confirmer
    |------------------------------------------------------------------
    */
    public function confirmer(int $id): RedirectResponse
    {
        $salon       = $this->salon();
        $reservation = Reservation::where('salon_id', $salon->id)
            ->where('statut', 'en_attente')
            ->findOrFail($id);

        $reservation->update(['statut' => 'confirmee']);

        // Envoyer notification + email au client
        $this->notifService->envoyerAvecEmail(
            $reservation->client_id,
            'reservation_confirmee',
            [
                'salon'    => $salon->nom_salon,
                'service'  => $reservation->service->nom_service,
                'date'     => $reservation->date_heure->translatedFormat('D d M Y'),
                'heure'    => $reservation->date_heure->format('H:i'),
            ],
            $reservation->load(['client', 'salon.ville', 'service', 'employe'])
        );

        return back()->with('success', 'Réservation confirmée. Le client a été notifié.');
    }

    /*
    |------------------------------------------------------------------
    | POST /salon/reservations/{id}/terminer
    |------------------------------------------------------------------
    */
    public function terminer(int $id): RedirectResponse
    {
        $salon       = $this->salon();
        $reservation = Reservation::where('salon_id', $salon->id)
            ->where('statut', 'confirmee')
            ->findOrFail($id);

        $reservation->update(['statut' => 'terminee']);

        return back()->with('success', 'Réservation marquée comme terminée.');
    }

    /*
    |------------------------------------------------------------------
    | POST /salon/reservations/{id}/annuler
    |------------------------------------------------------------------
    */
    public function annuler(Request $request, int $id): RedirectResponse
    {
        $salon       = $this->salon();
        $reservation = Reservation::where('salon_id', $salon->id)
            ->whereIn('statut', ['en_attente', 'confirmee'])
            ->findOrFail($id);

        $request->validate([
            'motif' => ['required', 'string', 'max:255'],
        ], [
            'motif.required' => 'Veuillez indiquer le motif d\'annulation.',
        ]);

        $reservation->update([
            'statut'      => 'annulee',
            'annulee_par' => 'salon',
            'date_annul'  => now(),
            'motif_annul' => $request->motif,
        ]);

        // Notifier le client + email
        $this->notifService->envoyerAvecEmail(
            $reservation->client_id,
            'reservation_annulee',
            [
                'salon'   => $salon->nom_salon,
                'service' => $reservation->service->nom_service,
                'date'    => $reservation->date_heure->translatedFormat('D d M Y'),
                'motif'   => $request->motif,
            ],
            $reservation->load(['client', 'salon.ville', 'service'])
        );

        return back()->with('success', 'Réservation annulée. Le client a été informé.');
    }
}
