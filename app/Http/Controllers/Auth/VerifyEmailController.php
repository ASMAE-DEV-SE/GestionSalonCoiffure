<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VerifyEmailController extends Controller
{
    /*
    |------------------------------------------------------------------
    | Page "Vérifiez votre email"
    |------------------------------------------------------------------
    */
    public function notice(Request $request): View|RedirectResponse
    {
        // Si déjà vérifié → rediriger
        if ($request->user()->hasVerifiedEmail()) {
            return $this->redirectApreVerification($request);
        }

        return view('auth.verify-email');
    }

    /*
    |------------------------------------------------------------------
    | Traiter le clic sur le lien du mail (GET /verify-email/{id}/{hash})
    |------------------------------------------------------------------
    */
    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->redirectApreVerification($request)
                ->with('info', 'Email déjà vérifié.');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return $this->redirectApreVerification($request)
            ->with('success', 'Email vérifié avec succès ! Bienvenue sur Salonify.');
    }

    /*
    |------------------------------------------------------------------
    | Renvoyer l'email de vérification
    |------------------------------------------------------------------
    */
    public function resend(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->redirectApreVerification($request);
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', 'Email de vérification renvoyé. Vérifiez votre boîte mail.');
    }

    /*
    |------------------------------------------------------------------
    | Redirection post-vérification selon le rôle
    |------------------------------------------------------------------
    */
    protected function redirectApreVerification(Request $request): RedirectResponse
    {
        $user = $request->user();

        return match($user->role) {
            'admin'  => redirect()->route('admin.dashboard'),
            'salon'  => redirect()->route('salon.dashboard'),
            default  => redirect()->route('client.dashboard'),
        };
    }
}
