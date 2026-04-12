<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Ville;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class VilleController extends Controller
{
    public function index(): View
    {
        $villes = Ville::actives()
            ->withCount(['salonsValides'])
            ->orderByDesc('salons_valides_count')
            ->orderBy('nom_ville')
            ->get();

        $totalSalons = $villes->sum('salons_valides_count');

        // Ville enregistrée du client connecté (pour la mise en avant)
        $villeUtilisateur = null;
        if (Auth::check() && Auth::user()->ville_id) {
            $villeUtilisateur = $villes->firstWhere('id', Auth::user()->ville_id);
        }

        return view('public.villes', compact('villes', 'totalSalons', 'villeUtilisateur'));
    }
}
