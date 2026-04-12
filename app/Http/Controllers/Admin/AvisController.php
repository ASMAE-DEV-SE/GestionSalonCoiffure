<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Avis;
use App\Models\Salon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AvisController extends Controller
{
    public function index(Request $request): View
    {
        $query = Avis::with(['reservation.client', 'reservation.salon', 'reservation.service']);

        if ($request->filled('note')) {
            $query->where('note', (int) $request->note);
        }
        if ($request->filled('sans_reponse')) {
            $query->whereNull('reponse_salon');
        }
        if ($request->filled('salon_id')) {
            $query->whereHas('reservation', fn($q) => $q->where('salon_id', $request->salon_id));
        }

        $avis = $query->latest()->paginate(20)->withQueryString();

        $totalAvis     = Avis::count();
        $noteMoyRaw    = Avis::avg('note');
        $sansReponse   = Avis::whereNull('reponse_salon')->count();

        $stats = [
            'total'        => $totalAvis,
            'note_moy'     => $noteMoyRaw ? number_format($noteMoyRaw, 1) : '—',
            'sans_reponse' => $sansReponse,
        ];

        $salons = Salon::orderBy('nom_salon')->get();

        return view('admin.avis', compact('avis', 'stats', 'salons'));
    }

    public function approuver(int $id): RedirectResponse
    {
        return back()->with('success', 'Avis maintenu.');
    }

    public function destroy(int $id): RedirectResponse
    {
        Avis::findOrFail($id)->delete();

        return back()->with('success', 'Avis supprimé.');
    }
}
