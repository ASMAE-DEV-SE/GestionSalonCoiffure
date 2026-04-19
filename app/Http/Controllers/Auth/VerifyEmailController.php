<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class VerifyEmailController extends Controller
{
    public function notice(Request $request): View|RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->redirectApreVerification($request);
        }

        Log::info('Auth: page verification email affichee', ['user_id' => $request->user()->id]);

        return view('auth.verify-email');
    }

    public function verify(Request $request, int $id, string $hash): RedirectResponse
    {
        // Vérifier la signature de l'URL
        if (! $request->hasValidSignature()) {
            return redirect()->route('verification.notice')
                ->with('error', 'Le lien de vérification est invalide ou a expiré. Demandez-en un nouveau.');
        }

        $user = User::findOrFail($id);

        // Vérifier le hash email
        if (! hash_equals(sha1($user->email), $hash)) {
            return redirect()->route('verification.notice')
                ->with('error', 'Lien de vérification invalide.');
        }

        if ($user->hasVerifiedEmail()) {
            Log::info('Auth: email deja verifie', ['user_id' => $user->id]);
            // Connecter l'utilisateur s'il ne l'est pas déjà
            if (! Auth::check() || Auth::id() !== $user->id) {
                Auth::login($user);
            }
            return $this->redirectUserApreVerification($user)
                ->with('info', 'Email déjà vérifié.');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
            Log::info('Auth: email verifie avec succes', ['user_id' => $user->id]);
        }

        // Connecter l'utilisateur vérifié
        Auth::login($user);

        return $this->redirectUserApreVerification($user)
            ->with('success', 'Email vérifié avec succès ! Bienvenue sur Salonify.');
    }

    public function resend(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->redirectApreVerification($request);
        }

        Log::info('Auth: renvoi email verification', ['user_id' => $request->user()->id]);

        try {
            $request->user()->sendEmailVerificationNotification();
            return back()->with('success', 'Email de vérification renvoyé. Vérifiez votre boîte mail (et le dossier spam).');
        } catch (\Throwable $e) {
            Log::error('Erreur renvoi email verification', [
                'user_id' => $request->user()->id,
                'message' => $e->getMessage(),
            ]);
            return back()->with('error', 'Impossible d\'envoyer l\'email : ' . $e->getMessage());
        }
    }

    protected function redirectApreVerification(Request $request): RedirectResponse
    {
        return $this->redirectUserApreVerification($request->user());
    }

    protected function redirectUserApreVerification(User $user): RedirectResponse
    {
        return match($user->role) {
            'admin'  => redirect()->route('admin.dashboard'),
            'salon'  => redirect()->route('salon.dashboard'),
            default  => redirect()->route('client.dashboard'),
        };
    }
}
