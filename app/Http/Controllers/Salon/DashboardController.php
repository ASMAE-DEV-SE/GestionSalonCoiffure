<?php

namespace App\Http\Controllers\Salon;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Avis;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(): View
    {
        $salon = Auth::user()->salon()->with(['ville', 'employesActifs'])->firstOrFail();

        // ── RDV du jour ───────────────────────────────────────────
        $rdvAujourdhui = Reservation::where('salon_id', $salon->id)
            ->whereDate('date_heure', today())
            ->whereIn('statut', ['confirmee', 'en_attente'])
            ->with(['service', 'employe', 'client'])
            ->orderBy('date_heure')
            ->get();

        // ── KPI semaine ───────────────────────────────────────────
        $rdvSemaine = Reservation::where('salon_id', $salon->id)
            ->whereBetween('date_heure', [now()->startOfWeek(), now()->endOfWeek()])
            ->whereIn('statut', ['confirmee', 'en_attente', 'terminee'])
            ->count();

        $caSemaine = Reservation::where('reservations.salon_id', $salon->id)
            ->whereBetween('reservations.date_heure', [now()->startOfWeek(), now()->endOfWeek()])
            ->where('reservations.statut', 'terminee')
            ->join('services', 'reservations.service_id', '=', 'services.id')
            ->sum('services.prix');

        // ── En attente de confirmation ────────────────────────────
        $enAttente = Reservation::where('salon_id', $salon->id)
            ->where('statut', 'en_attente')
            ->where('date_heure', '>=', now())
            ->with(['service', 'client'])
            ->orderBy('date_heure')
            ->get();

        // ── Prochains RDV 7 jours ─────────────────────────────────
        $prochains = Reservation::where('salon_id', $salon->id)
            ->whereIn('statut', ['confirmee', 'en_attente'])
            ->whereBetween('date_heure', [now(), now()->addDays(7)])
            ->with(['service', 'employe', 'client'])
            ->orderBy('date_heure')
            ->get();

        // ── Graphique : RDV 7 derniers jours ─────────────────────
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date        = now()->subDays($i);
            $chartData[] = [
                'label' => $date->translatedFormat('D'),
                'value' => Reservation::where('salon_id', $salon->id)
                    ->whereDate('date_heure', $date->toDateString())
                    ->whereIn('statut', ['confirmee', 'terminee'])
                    ->count(),
                'today' => $i === 0,
            ];
        }

        // ── Top 5 services ────────────────────────────────────────
        $totalTerminees = Reservation::where('salon_id', $salon->id)
            ->where('statut', 'terminee')->count();

        $topServices = Reservation::where('salon_id', $salon->id)
            ->where('statut', 'terminee')
            ->selectRaw('service_id, count(*) as total')
            ->groupBy('service_id')
            ->with('service')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // ── 3 derniers avis ───────────────────────────────────────
        $derniersAvis = Avis::whereHas('reservation',
                fn($q) => $q->where('salon_id', $salon->id)
            )
            ->with('reservation.client')
            ->latest()
            ->limit(3)
            ->get();

        return view('salon.dashboard', compact(
            'salon', 'rdvAujourdhui', 'rdvSemaine', 'caSemaine',
            'enAttente', 'prochains', 'chartData',
            'topServices', 'totalTerminees', 'derniersAvis'
        ));
    }
}
