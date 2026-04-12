<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Redirige l'utilisateur vers la page d'authentification Google.
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Traite le retour de Google après authentification.
     * Détermine le rôle (admin / salon / client) et redirige en conséquence.
     */
    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            return redirect()->route('login')
                ->with('error', 'Connexion Google annulée ou expirée. Veuillez réessayer.');
        }

        // Chercher un compte existant par email
        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            // Compte existant → connexion directe
            // Marquer l'email comme vérifié si ce n'est pas encore fait
            if (! $user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
            }

            Auth::login($user, remember: true);

            return $this->redirectParRole($user);
        }

        // Nouveau compte → créer en tant que client (Google = email vérifié)
        $user = User::create([
            'prenom'           => $googleUser->user['given_name']  ?? explode(' ', $googleUser->getName())[0],
            'nom'              => $googleUser->user['family_name'] ?? (explode(' ', $googleUser->getName())[1] ?? ''),
            'email'            => $googleUser->getEmail(),
            'mot_de_passe'     => Hash::make(Str::random(32)), // mot de passe aléatoire
            'role'             => 'client',
            'email_verifie_le' => now(), // Google garantit l'email vérifié
        ]);

        Auth::login($user, remember: true);

        return redirect()->route('client.dashboard')
            ->with('success', 'Bienvenue sur Salonify, ' . $user->prenom . ' ! Votre compte a été créé via Google.');
    }

    /**
     * Redirige selon le rôle de l'utilisateur connecté.
     */
    private function redirectParRole(User $user): RedirectResponse
    {
        return match($user->role) {
            'admin'  => redirect()->route('admin.dashboard')
                            ->with('success', 'Connecté en tant qu\'administrateur.'),
            'salon'  => redirect()->route('salon.dashboard')
                            ->with('success', 'Connecté en tant que gérant — ' . ($user->salon?->nom_salon ?? '')),
            default  => redirect()->route('client.dashboard')
                            ->with('success', 'Bienvenue, ' . $user->prenom . ' !'),
        };
    }
}
