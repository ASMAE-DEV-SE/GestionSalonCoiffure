<?php

namespace App\Http\Controllers\Salon;

use App\Http\Controllers\Controller;
use App\Models\Avis;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class AvisController extends Controller
{
    private function salon()
    {
        return Auth::user()->salon()->firstOrFail();
    }

    /*
    |------------------------------------------------------------------
    | GET /salon/avis
    |------------------------------------------------------------------
    */
    public function index(Request $request): View
    {
        $salon = $this->salon();

        $query = Avis::whereHas('reservation', fn($q) =>
                $q->where('salon_id', $salon->id)
            )
            ->with(['reservation.client', 'reservation.service']);

        // Filtre note
        if ($request->filled('note')) {
            $query->where('note', (int) $request->note);
        }

        // Filtre sans réponse
        if ($request->boolean('sans_reponse')) {
            $query->whereNull('reponse_salon');
        }

        $avis = $query->latest()->paginate(12)->withQueryString();

        // Stats résumé
        $stats = [
            'total'        => Avis::whereHas('reservation', fn($q) =>
                                  $q->where('salon_id', $salon->id))->count(),
            'sans_reponse' => Avis::whereHas('reservation', fn($q) =>
                                  $q->where('salon_id', $salon->id))
                                  ->whereNull('reponse_salon')->count(),
            'note_moy'     => $salon->note_moy,
        ];

        return view('salon.avis', compact('salon', 'avis', 'stats'));
    }

    /*
    |------------------------------------------------------------------
    | POST /salon/avis/{id}/repondre
    |------------------------------------------------------------------
    */
    public function repondre(Request $request, int $id): RedirectResponse
    {
        $salon = $this->salon();

        // Vérifier que l'avis appartient bien au salon
        $avis = Avis::whereHas('reservation',
                fn($q) => $q->where('salon_id', $salon->id)
            )->findOrFail($id);

        $request->validate([
            'reponse_salon' => ['required', 'string', 'min:10', 'max:1000'],
        ], [
            'reponse_salon.required' => 'La réponse est obligatoire.',
            'reponse_salon.min'      => 'La réponse doit contenir au moins 10 caractères.',
        ]);

        $avis->update(['reponse_salon' => $request->reponse_salon]);

        return back()->with('success', 'Votre réponse a été publiée.');
    }

    /*
    |------------------------------------------------------------------
    | POST /salon/avis/{id}/signaler
    |------------------------------------------------------------------
    */
    public function signaler(Request $request, int $id): RedirectResponse
    {
        $salon = $this->salon();

        $avis = Avis::whereHas('reservation',
                fn($q) => $q->where('salon_id', $salon->id)
            )->findOrFail($id);

        $request->validate([
            'motif_signalement' => ['required', 'string', 'max:255'],
        ], [
            'motif_signalement.required' => 'Veuillez indiquer le motif du signalement.',
        ]);

        // Notifier les admins via log (à connecter à une table signalements si besoin)
        \Illuminate\Support\Facades\Log::warning('Avis signalé', [
            'avis_id'  => $avis->id,
            'salon_id' => $salon->id,
            'motif'    => $request->motif_signalement,
        ]);

        return back()->with('info',
            'Signalement transmis à notre équipe. Nous examinerons cet avis sous 48h.'
        );
    }
}
