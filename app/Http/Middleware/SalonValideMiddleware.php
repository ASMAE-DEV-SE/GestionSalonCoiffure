<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * SalonValideMiddleware
 *
 * Appliqué après 'role:salon'.
 * Vérifie que le gérant possède un salon dont valide = 1.
 * Redirige vers une page d'attente si le salon est en attente (0)
 * ou suspendu (-1).
 */
class SalonValideMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Charger le premier salon lié à ce gérant
        $salon = $user->salons()->first();

        // Aucun salon enregistré → rediriger vers la création
        if (! $salon) {
            return redirect()->route('home')
                ->with('warning', 'Vous devez enregistrer votre salon avant de continuer.');
        }

        // Salon en attente de validation (valide = 0)
        if ($salon->valide === 0) {
            return redirect()->route('home')
                ->with('info', 'Votre salon est en cours de validation par notre équipe. Nous vous contacterons sous 24h.');
        }

        // Salon suspendu (valide = -1)
        if ($salon->valide === -1) {
            return redirect()->route('home')
                ->with('error', 'Votre salon a été suspendu. Contactez l\'administration : contact@salonify.ma');
        }

        // Injecter le salon en attribut de la requête pour les controllers
        $request->attributes->set('salon_courant', $salon);

        return $next($request);
    }
}
