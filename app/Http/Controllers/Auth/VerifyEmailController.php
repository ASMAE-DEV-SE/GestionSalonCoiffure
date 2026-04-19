<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
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

    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            Log::info('Auth: email deja verifie', ['user_id' => $request->user()->id]);
            return $this->redirectApreVerification($request)
                ->with('info', 'Email déjà vérifié.');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
            Log::info('Auth: email verifie avec succes', ['user_id' => $request->user()->id]);
        }

        return $this->redirectApreVerification($request)
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
        $user = $request->user();

        return match($user->role) {
            'admin'  => redirect()->route('admin.dashboard'),
            'salon'  => redirect()->route('salon.dashboard'),
            default  => redirect()->route('client.dashboard'),
        };
    }
}
