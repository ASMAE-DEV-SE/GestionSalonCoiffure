<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Avis;
use App\Models\Salon;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        // Prochain RDV confirmé ou en attente
        $prochainRdv = Reservation::with(['salon.ville', 'service', 'employe'])
            ->where('client_id', $user->id)
            ->whereIn('statut', ['en_attente', 'confirmee'])
            ->where('date_heure', '>=', now())
            ->orderBy('date_heure')
            ->first();

        // 4 derniers RDV passés
        $derniersRdv = Reservation::with(['salon', 'service', 'avis'])
            ->where('client_id', $user->id)
            ->where('statut', 'terminee')
            ->orderByDesc('date_heure')
            ->limit(4)
            ->get();

        // Réservations à venir (hors la prochaine)
        $rdvAVenir = Reservation::with(['salon', 'service'])
            ->where('client_id', $user->id)
            ->whereIn('statut', ['en_attente', 'confirmee'])
            ->where('date_heure', '>=', now())
            ->orderBy('date_heure')
            ->skip(1)->limit(4)
            ->get();

        // Statistiques personnelles
        $stats = [
            'total'     => Reservation::where('client_id', $user->id)->count(),
            'terminee'  => Reservation::where('client_id', $user->id)
                               ->where('statut', 'terminee')->count(),
            'a_venir'   => Reservation::where('client_id', $user->id)
                               ->whereIn('statut', ['en_attente', 'confirmee'])
                               ->where('date_heure', '>=', now())->count(),
            'avis'      => Avis::whereHas('reservation',
                               fn($q) => $q->where('client_id', $user->id)
                           )->count(),
        ];

        // Avis en attente de publication (réservations terminées sans avis)
        $rdvSansAvis = Reservation::with(['salon', 'service'])
            ->where('client_id', $user->id)
            ->where('statut', 'terminee')
            ->doesntHave('avis')
            ->orderByDesc('date_heure')
            ->limit(3)
            ->get();

        // Notifications non lues
        $notifications = $user->notificationsNonLues()->limit(5)->get();

        // Salons proches (même quartier en priorité, sinon même ville)
        $salonsProches = collect();
        if ($user->ville_id) {
            $query = Salon::valides()
                ->with(['ville', 'servicesActifs'])
                ->parVille($user->ville_id);

            // Priorité : même quartier
            if ($user->quartier) {
                $salonsProches = (clone $query)
                    ->parQuartier($user->quartier)
                    ->mieuxNotes()
                    ->limit(4)
                    ->get();
            }

            // Compléter avec d'autres salons de la même ville si moins de 4
            if ($salonsProches->count() < 4) {
                $exclus = $salonsProches->pluck('id')->toArray();
                $complement = $query
                    ->when(count($exclus), fn($q) => $q->whereNotIn('id', $exclus))
                    ->mieuxNotes()
                    ->limit(4 - $salonsProches->count())
                    ->get();
                $salonsProches = $salonsProches->concat($complement);
            }
        }

        return view('client.dashboard', compact(
            'user', 'prochainRdv', 'derniersRdv', 'rdvAVenir',
            'stats', 'rdvSansAvis', 'notifications', 'salonsProches'
        ));
    }
}
