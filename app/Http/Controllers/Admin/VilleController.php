<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ville;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VilleController extends Controller
{
    public function index(): View
    {
        $villes = Ville::withCount([
            'salons as salons_count',
            'salons as salons_valides_count' => fn($q) => $q->where('valide', 1),
        ])->orderBy('nom_ville')->paginate(20);

        return view('admin.villes', compact('villes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nom_ville'   => ['required', 'string', 'max:100', 'unique:villes,nom_ville'],
            'code_postal' => ['required', 'string', 'max:10'],
            'region'      => ['required', 'string', 'max:100'],
        ]);

        Ville::create([
            'nom_ville'   => $request->nom_ville,
            'code_postal' => $request->code_postal,
            'region'      => $request->region,
            'pays'        => 'Maroc',
            'actif'       => 1,
        ]);

        return back()->with('success', 'Ville ajoutée.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $ville = Ville::findOrFail($id);

        if ($ville->salons()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer : des salons sont rattachés à cette ville.');
        }

        $ville->delete();

        return back()->with('success', 'Ville supprimée.');
    }
}
