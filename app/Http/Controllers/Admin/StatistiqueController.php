<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Avis;
use App\Models\Reservation;
use App\Models\Salon;
use App\Models\User;
use App\Models\Ville;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StatistiqueController extends Controller
{
    public function index(Request $request): View
    {
        $debut = $request->filled('debut')
            ? Carbon::parse($request->debut)->startOfDay()
            : now()->subDays(30)->startOfDay();
        $fin = $request->filled('fin')
            ? Carbon::parse($request->fin)->endOfDay()
            : now()->endOfDay();

        // KPI sur la période
        $totalResa = Reservation::whereBetween('date_heure', [$debut, $fin])->count();
        $annulees  = Reservation::whereBetween('date_heure', [$debut, $fin])
            ->where('statut', 'annulee')->count();

        $caTotal = Reservation::whereBetween('reservations.date_heure', [$debut, $fin])
            ->whereIn('reservations.statut', ['confirmee', 'terminee'])
            ->join('services', 'services.id', '=', 'reservations.service_id')
            ->sum('services.prix');

        $inscriptions = User::where('role', 'client')
            ->whereBetween('created_at', [$debut, $fin])
            ->count();

        $tauxAnnul = $totalResa > 0 ? round($annulees / $totalResa * 100, 1) : 0;

        $noteMoyRaw = Avis::whereBetween('created_at', [$debut, $fin])->avg('note');
        $noteMoy    = $noteMoyRaw ? number_format($noteMoyRaw, 1) : '—';

        $kpi = [
            'total_resa'   => $totalResa,
            'ca_total'     => $caTotal,
            'inscriptions' => $inscriptions,
            'taux_annul'   => $tauxAnnul,
            'note_moy'     => $noteMoy,
        ];

        // Réservations par mois sur la période
        $resaParMois = [];
        $cursor = $debut->copy()->startOfMonth();
        while ($cursor->lte($fin)) {
            $count = Reservation::whereYear('date_heure', $cursor->year)
                ->whereMonth('date_heure', $cursor->month)
                ->count();
            $ca = Reservation::whereYear('reservations.date_heure', $cursor->year)
                ->whereMonth('reservations.date_heure', $cursor->month)
                ->whereIn('reservations.statut', ['confirmee', 'terminee'])
                ->join('services', 'services.id', '=', 'reservations.service_id')
                ->sum('services.prix');
            $resaParMois[] = [
                'label' => $cursor->translatedFormat('M Y'),
                'value' => $count,
                'ca'    => $ca,
            ];
            $cursor->addMonth();
        }

        // Distribution des notes (1-5)
        $totalAvis = Avis::count() ?: 1;
        $distNotes = [];
        for ($i = 1; $i <= 5; $i++) {
            $count = Avis::where('note', $i)->count();
            $distNotes[$i] = [
                'count' => $count,
                'pct'   => round($count / $totalAvis * 100),
            ];
        }

        // Top salons sur la période
        $topSalons = Salon::select('salons.id', 'salons.nom_salon')
            ->selectRaw('COUNT(reservations.id) as nb_resa')
            ->leftJoin('reservations', function ($j) use ($debut, $fin) {
                $j->on('reservations.salon_id', '=', 'salons.id')
                  ->whereBetween('reservations.date_heure', [$debut, $fin]);
            })
            ->groupBy('salons.id', 'salons.nom_salon')
            ->orderByDesc('nb_resa')
            ->limit(8)
            ->get();

        // Par catégorie de service
        $parCategorie = Reservation::whereBetween('reservations.date_heure', [$debut, $fin])
            ->join('services', 'services.id', '=', 'reservations.service_id')
            ->selectRaw('services.categorie, COUNT(reservations.id) as total')
            ->groupBy('services.categorie')
            ->orderByDesc('total')
            ->get();

        // Par ville
        $parVille = Ville::select('villes.id', 'villes.nom_ville')
            ->selectRaw('COUNT(reservations.id) as nb_resa')
            ->leftJoin('salons', 'salons.ville_id', '=', 'villes.id')
            ->leftJoin('reservations', function ($j) use ($debut, $fin) {
                $j->on('reservations.salon_id', '=', 'salons.id')
                  ->whereBetween('reservations.date_heure', [$debut, $fin]);
            })
            ->groupBy('villes.id', 'villes.nom_ville')
            ->orderByDesc('nb_resa')
            ->limit(10)
            ->get();

        return view('admin.statistiques', compact(
            'debut', 'fin', 'kpi', 'resaParMois',
            'distNotes', 'topSalons', 'parCategorie', 'parVille'
        ));
    }

    public function export(Request $request)
    {
        $debut = $request->filled('debut') ? Carbon::parse($request->debut)->startOfDay() : now()->subDays(30)->startOfDay();
        $fin   = $request->filled('fin')   ? Carbon::parse($request->fin)->endOfDay()     : now()->endOfDay();

        $reservations = Reservation::with(['client', 'salon', 'service'])
            ->whereBetween('date_heure', [$debut, $fin])
            ->orderByDesc('date_heure')
            ->get();

        $csv = "ID,Client,Salon,Service,Date,Statut,Prix\n";
        foreach ($reservations as $r) {
            $csv .= implode(',', [
                $r->id,
                '"' . ($r->client?->nomComplet() ?? '') . '"',
                '"' . ($r->salon?->nom_salon ?? '') . '"',
                '"' . ($r->service?->nom_service ?? '') . '"',
                $r->date_heure->format('Y-m-d H:i'),
                $r->statut,
                $r->service?->prix ?? 0,
            ]) . "\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="reservations-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }
}
