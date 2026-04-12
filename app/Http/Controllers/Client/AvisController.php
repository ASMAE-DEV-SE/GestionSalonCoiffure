<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Avis;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class AvisController extends Controller
{
    /*
    |------------------------------------------------------------------
    | Formulaire de publication  GET /avis/publier/{reservation}
    |------------------------------------------------------------------
    */
    public function create(int $reservation): View
    {
        // Vérifier que la réservation appartient au client et est terminée
        $reservation = Reservation::with(['salon.ville', 'service', 'employe'])
            ->where('client_id', Auth::id())
            ->where('statut', 'terminee')
            ->doesntHave('avis')     // Pas encore d'avis
            ->findOrFail($reservation);

        return view('client.avis', compact('reservation'));
    }

    /*
    |------------------------------------------------------------------
    | Enregistrer l'avis  POST /avis
    |------------------------------------------------------------------
    */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'reservation_id' => ['required', 'exists:reservations,id'],
            'note'           => ['required', 'integer', 'between:1,5'],
            'commentaire'    => ['nullable', 'string', 'min:10', 'max:1000'],
        ], [
            'note.required'      => 'Veuillez attribuer une note.',
            'note.between'       => 'La note doit être entre 1 et 5.',
            'commentaire.min'    => 'Le commentaire doit contenir au moins 10 caractères.',
        ]);

        // Double vérification sécurité : la réservation appartient bien au client
        $reservation = Reservation::with('salon')
            ->where('client_id', Auth::id())
            ->where('statut', 'terminee')
            ->doesntHave('avis')
            ->findOrFail($request->reservation_id);

        // Créer l'avis
        Avis::create([
            'reservation_id' => $reservation->id,
            'note'           => $request->note,
            'commentaire'    => $request->commentaire,
        ]);

        // Recalculer note_moy et nb_avis du salon
        $salon  = $reservation->salon;
        $nbAvis = Avis::whereHas('reservation',
                      fn($q) => $q->where('salon_id', $salon->id)
                  )->count();
        $noteMoy = Avis::whereHas('reservation',
                       fn($q) => $q->where('salon_id', $salon->id)
                   )->avg('note');

        $salon->update([
            'nb_avis'  => $nbAvis,
            'note_moy' => round($noteMoy, 2),
        ]);

        return redirect()->route('client.dashboard')
            ->with('success', 'Votre avis a été publié. Merci pour votre retour !');
    }
}
