<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /*
    |------------------------------------------------------------------
    | Afficher le formulaire "mot de passe oublié"
    |------------------------------------------------------------------
    */
    public function showLinkRequestForm(): View
    {
        return view('auth.forgot-password');
    }

    /*
    |------------------------------------------------------------------
    | Envoyer le lien de réinitialisation
    |------------------------------------------------------------------
    */
    public function sendResetLinkEmail(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email'    => 'L\'adresse email n\'est pas valide.',
        ]);

        // Laravel gère l'envoi et le token via le broker Password
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success',
                'Un email de réinitialisation a été envoyé à ' . $request->email . '. ' .
                'Le lien est valable 60 minutes.'
            );
        }

        // EMAIL_NOT_FOUND → on ne révèle pas si l'email existe (sécurité)
        return back()->with('success',
            'Si cet email existe dans notre système, vous recevrez un lien de réinitialisation.'
        );
    }
}
