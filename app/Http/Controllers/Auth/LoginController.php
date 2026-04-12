<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
    |------------------------------------------------------------------
    | Afficher le formulaire de connexion
    |------------------------------------------------------------------
    */
    public function showLoginForm(): View
    {
        return view('auth.connexion');
    }

    /*
    |------------------------------------------------------------------
    | Traiter la connexion
    |------------------------------------------------------------------
    */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email'         => ['required', 'email'],
            'mot_de_passe'  => ['required', 'string'],
        ], [
            'email.required'        => 'L\'adresse email est obligatoire.',
            'email.email'           => 'L\'adresse email n\'est pas valide.',
            'mot_de_passe.required' => 'Le mot de passe est obligatoire.',
        ]);

        // Rate limiting : 5 tentatives / minute par IP + email
        $this->ensureIsNotRateLimited($request);

        // Auth::attempt mappe 'password' — on envoie la valeur du champ custom
        $attempt = Auth::attempt([
            'email'    => $request->email,
            'password' => $request->mot_de_passe,
        ], $request->boolean('remember'));

        if (! $attempt) {
            RateLimiter::hit($this->throttleKey($request));

            throw ValidationException::withMessages([
                'email' => 'Email ou mot de passe incorrect.',
            ]);
        }

        RateLimiter::clear($this->throttleKey($request));
        $request->session()->regenerate();

        $user = Auth::user();

        // Vérification email obligatoire pour les clients
        if ($user->isClient() && ! $user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice')
                ->with('info', 'Veuillez vérifier votre adresse email avant de continuer.');
        }

        // Redirection selon le rôle
        return match($user->role) {
            'admin'  => redirect()->intended(route('admin.dashboard')),
            'salon'  => redirect()->intended(route('salon.dashboard')),
            default  => redirect()->intended(route('client.dashboard')),
        };
    }

    /*
    |------------------------------------------------------------------
    | Déconnexion
    |------------------------------------------------------------------
    */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'Vous avez été déconnecté avec succès.');
    }

    /*
    |------------------------------------------------------------------
    | Helpers rate limiting
    |------------------------------------------------------------------
    */
    protected function ensureIsNotRateLimited(Request $request): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => "Trop de tentatives. Réessayez dans {$seconds} secondes.",
        ]);
    }

    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(
            Str::lower($request->input('email')) . '|' . $request->ip()
        );
    }
}
