<?php

namespace App\Http\Controllers\Salon;

use App\Http\Controllers\Controller;
use App\Models\Employe;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EmployeController extends Controller
{
    private function salon()
    {
        return Auth::user()->salon()->firstOrFail();
    }

    /*
    |------------------------------------------------------------------
    | GET /salon/employes
    |------------------------------------------------------------------
    */
    public function index(): View
    {
        $salon   = $this->salon();
        $employes = Employe::where('salon_id', $salon->id)
            ->orderBy('prenom')
            ->get();

        return view('salon.employes', compact('salon', 'employes'));
    }

    /*
    |------------------------------------------------------------------
    | POST /salon/employes
    |------------------------------------------------------------------
    */
    public function store(Request $request): RedirectResponse
    {
        $salon = $this->salon();

        $data = $request->validate([
            'prenom'      => ['required', 'string', 'max:80'],
            'nom'         => ['required', 'string', 'max:80'],
            'email'       => ['nullable', 'email', 'max:180'],
            'tel'         => ['nullable', 'string', 'max:20'],
            'specialites' => ['nullable', 'array'],
            'specialites.*'=> ['string', 'max:60'],
            'horaires'    => ['nullable', 'array'],
            'photo'       => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
        ], [
            'prenom.required' => 'Le prénom est obligatoire.',
            'nom.required'    => 'Le nom est obligatoire.',
            'photo.image'     => 'Le fichier doit être une image (JPEG, PNG, WebP).',
            'photo.max'       => 'La photo ne doit pas dépasser 2 Mo.',
        ]);

        $data['salon_id']    = $salon->id;
        $data['actif']       = $request->boolean('actif');
        $data['specialites'] = $request->specialites ?? [];
        $data['horaires']    = $this->buildHoraires($request);

        // Upload photo
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')
                ->store('employes', 'public');
        }

        Employe::create($data);

        // Mettre à jour le compteur nb_employes du salon
        $salon->update(['nb_employes' => $salon->employesActifs()->count()]);

        return redirect()->route('salon.employes.index')
            ->with('success', $data['prenom'] . ' ' . $data['nom'] . ' ajouté(e) à l\'équipe.');
    }

    /*
    |------------------------------------------------------------------
    | PUT /salon/employes/{id}
    |------------------------------------------------------------------
    */
    public function update(Request $request, int $id): RedirectResponse
    {
        $salon   = $this->salon();
        $employe = Employe::where('salon_id', $salon->id)->findOrFail($id);

        $data = $request->validate([
            'prenom'       => ['required', 'string', 'max:80'],
            'nom'          => ['required', 'string', 'max:80'],
            'email'        => ['nullable', 'email', 'max:180'],
            'tel'          => ['nullable', 'string', 'max:20'],
            'specialites'  => ['nullable', 'array'],
            'specialites.*'=> ['string', 'max:60'],
            'horaires'     => ['nullable', 'array'],
            'photo'        => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
        ]);

        $data['actif']       = $request->boolean('actif');
        $data['specialites'] = $request->specialites ?? [];
        $data['horaires']    = $this->buildHoraires($request);

        // Nouvelle photo
        if ($request->hasFile('photo')) {
            if ($employe->photo) {
                Storage::disk('public')->delete($employe->photo);
            }
            $data['photo'] = $request->file('photo')->store('employes', 'public');
        }

        $employe->update($data);

        // Recalcul compteur
        $salon->update(['nb_employes' => $salon->employesActifs()->count()]);

        return redirect()->route('salon.employes.index')
            ->with('success', 'Profil de ' . $employe->nomComplet() . ' mis à jour.');
    }

    /*
    |------------------------------------------------------------------
    | DELETE /salon/employes/{id}
    |------------------------------------------------------------------
    */
    public function destroy(int $id): RedirectResponse
    {
        $salon   = $this->salon();
        $employe = Employe::where('salon_id', $salon->id)->findOrFail($id);

        $rdvFuturs = $employe->reservationsAVenir()->count();
        if ($rdvFuturs > 0) {
            return back()->with('error',
                "{$rdvFuturs} RDV futur(s) assigné(s) à cet employé. Réassignez-les avant de supprimer."
            );
        }

        if ($employe->photo) {
            Storage::disk('public')->delete($employe->photo);
        }

        $nom = $employe->nomComplet();
        $employe->delete();
        $salon->update(['nb_employes' => $salon->employesActifs()->count()]);

        return redirect()->route('salon.employes.index')
            ->with('success', "{$nom} retiré(e) de l'équipe.");
    }

    /*
    |------------------------------------------------------------------
    | POST toggle actif (AJAX)
    |------------------------------------------------------------------
    */
    public function toggleActif(int $id): JsonResponse
    {
        $salon   = $this->salon();
        $employe = Employe::where('salon_id', $salon->id)->findOrFail($id);
        $employe->update(['actif' => ! $employe->actif]);
        $salon->update(['nb_employes' => $salon->employesActifs()->count()]);

        return response()->json([
            'actif'   => $employe->actif,
            'message' => $employe->actif
                ? $employe->nomComplet() . ' activé(e).'
                : $employe->nomComplet() . ' désactivé(e).',
        ]);
    }

    /*
    |------------------------------------------------------------------
    | Helper — construire le JSON horaires depuis le formulaire
    |------------------------------------------------------------------
    */
    private function buildHoraires(Request $request): array
    {
        $jours    = ['lundi','mardi','mercredi','jeudi','vendredi','samedi','dimanche'];
        $horaires = [];

        foreach ($jours as $jour) {
            $ferme = $request->boolean("horaires_{$jour}_ferme", false);
            $horaires[$jour] = [
                'debut'  => $ferme ? null : $request->input("horaires_{$jour}_debut"),
                'fin'    => $ferme ? null : $request->input("horaires_{$jour}_fin"),
                'ferme'  => $ferme,
            ];
        }

        return $horaires;
    }
}
