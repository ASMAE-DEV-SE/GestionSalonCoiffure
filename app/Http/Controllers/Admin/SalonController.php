<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\SalonSuspenduMail;
use App\Mail\SalonValideMail;
use App\Models\Salon;
use App\Models\Ville;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class SalonController extends Controller
{
    public function index(Request $request): View
    {
        $query = Salon::with(['user', 'ville']);

        if ($request->filled('statut')) {
            $valide = match($request->statut) {
                'valide'   => 1,
                'attente'  => 0,
                'suspendu' => -1,
                default    => null,
            };
            if ($valide !== null) {
                $query->where('valide', $valide);
            }
        }
        if ($request->filled('ville_id')) {
            $query->where('ville_id', $request->ville_id);
        }
        if ($request->filled('q')) {
            $query->where('nom_salon', 'like', '%' . $request->q . '%');
        }

        $salons = $query->latest()->paginate(15)->withQueryString();

        $compteurs = [
            'valides'   => Salon::where('valide', 1)->count(),
            'attente'   => Salon::where('valide', 0)->count(),
            'suspendus' => Salon::where('valide', -1)->count(),
        ];

        $villes = Ville::orderBy('nom_ville')->get();

        return view('admin.salons', compact('salons', 'compteurs', 'villes'));
    }

    public function show(int $id): View
    {
        $salon = Salon::with([
            'user', 'ville', 'services', 'employes',
            'reservations.client', 'reservations.service',
        ])->findOrFail($id);

        $stats = [
            'reservations' => $salon->reservations->count(),
            'terminées'    => $salon->reservations->where('statut', 'terminee')->count(),
            'avis'         => $salon->avis->count(),
        ];

        return view('admin.salon_detail', compact('salon', 'stats'));
    }

    public function valider(int $id): RedirectResponse
    {
        $salon = Salon::with(['user', 'ville'])->findOrFail($id);
        $salon->update(['valide' => 1, 'date_valid' => now()]);

        // Notification en base
        app(NotificationService::class)->envoyer($salon->user_id, 'salon_valide', [
            'salon' => $salon->nom_salon,
        ]);

        // Email au gérant
        try {
            Mail::to($salon->user->email)->send(new SalonValideMail($salon));
        } catch (\Throwable $e) {
            Log::error('Erreur email salon_valide', ['salon_id' => $id, 'message' => $e->getMessage()]);
        }

        return back()->with('success', "Salon « {$salon->nom_salon} » validé. Le gérant a été notifié.");
    }

    public function suspendre(Request $request, int $id): RedirectResponse
    {
        $request->validate(['motif' => ['required', 'string', 'max:500']]);

        $salon = Salon::with(['user', 'ville'])->findOrFail($id);
        $salon->update(['valide' => -1]);

        // Notification en base
        app(NotificationService::class)->envoyer($salon->user_id, 'salon_suspendu', [
            'salon' => $salon->nom_salon,
        ]);

        // Email au gérant
        try {
            Mail::to($salon->user->email)->send(new SalonSuspenduMail($salon, $request->motif));
        } catch (\Throwable $e) {
            Log::error('Erreur email salon_suspendu', ['salon_id' => $id, 'message' => $e->getMessage()]);
        }

        return back()->with('success', "Salon « {$salon->nom_salon} » suspendu. Le gérant a été notifié.");
    }

    public function destroy(int $id): RedirectResponse
    {
        $salon = Salon::findOrFail($id);
        $nom   = $salon->nom_salon;
        $salon->delete();

        return redirect()->route('admin.salons.index')->with('success', "Salon « {$nom} » supprimé.");
    }
}
