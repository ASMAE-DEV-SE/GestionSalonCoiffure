@extends('layouts.app')
@section('title', 'Vérifier votre email')

@section('content')
<div class="auth-page">

  <div class="auth-left">
    <img class="auth-left-bg" src="https://images.unsplash.com/photo-1487412947147-5cebf100ffc2?w=900&q=80" alt="">
    <div class="auth-left-content">
      <div class="auth-left-tag">Bienvenue sur Salonify</div>
      <h1 class="auth-left-title">Une étape<br>de plus <em>encore</em></h1>
      <p class="auth-left-desc">La vérification de votre email garantit la sécurité de votre compte et vous permet de recevoir les confirmations de réservation.</p>
      <div class="auth-points">
        <div class="auth-point"><div class="auth-point-icon">&#9993;</div><div class="auth-point-text">Email de vérification envoyé</div></div>
        <div class="auth-point"><div class="auth-point-icon">&#128197;</div><div class="auth-point-text">Réservez vos rendez-vous beauté</div></div>
        <div class="auth-point"><div class="auth-point-icon">&#9733;</div><div class="auth-point-text">Accès à votre espace client complet</div></div>
      </div>
    </div>
  </div>

  <div class="auth-right">
    <div class="auth-box">

      {{-- État : déjà vérifié ou succès --}}
      @if(session('success'))
        <div style="text-align:center;padding:1.5rem 0 1rem">
          <div style="width:80px;height:80px;border-radius:50%;background:rgba(197,216,157,.3);border:2px solid var(--p3);display:flex;align-items:center;justify-content:center;margin:0 auto 1.4rem;font-size:2.2rem;animation:pulseGreen 1.8s ease-in-out infinite">&#10003;</div>
          <div class="auth-form-title" style="font-size:1.75rem">Email vérifié !</div>
          <p style="font-size:.84rem;color:var(--ink-m);line-height:1.75;margin:1rem 0 1.5rem">{{ session('success') }}</p>
          @if(auth()->user()->isSalon())
            <a href="{{ route('salon.dashboard') }}" class="btn-auth" style="display:block;text-decoration:none;margin-bottom:.9rem">Accéder à mon tableau de bord</a>
          @else
            <a href="{{ route('client.dashboard') }}" class="btn-auth" style="display:block;text-decoration:none;margin-bottom:.9rem">Accéder à mon espace</a>
          @endif
          <a href="{{ route('villes.index') }}" style="font-size:.78rem;color:var(--p4d);font-weight:700;text-decoration:underline">Rechercher un salon</a>
        </div>

      @else
        {{-- En attente de vérification --}}
        <div style="text-align:center;margin-bottom:2rem">
          <div style="width:80px;height:80px;border-radius:50%;background:rgba(197,216,157,.25);border:2px solid var(--p3);display:flex;align-items:center;justify-content:center;margin:0 auto 1.4rem;font-size:2.2rem;animation:pulseGreen 2s ease-in-out infinite">&#9993;</div>
          <div class="auth-form-title" style="font-size:1.7rem;margin-bottom:.4rem">Vérifiez votre email</div>
          <p style="font-size:.84rem;color:var(--ink-m);line-height:1.75">
            Un email a été envoyé à<br>
            <strong style="color:var(--ink-h)">{{ auth()->user()->email }}</strong>
          </p>
        </div>

        <div style="background:var(--p1);border:1.5px solid var(--p2);padding:1.2rem 1.4rem;margin-bottom:1.6rem">
          <div style="font-size:.8rem;color:var(--ink-s);line-height:1.75">
            &#128161; Cliquez sur le lien dans l'email pour activer votre compte. Si vous ne le trouvez pas, vérifiez votre dossier <strong>spams</strong>.
          </div>
        </div>

        {{-- Renvoi --}}
        @if(session('info'))
          <div style="background:var(--p1);border:1.5px solid var(--p3);padding:.8rem 1rem;margin-bottom:1rem;font-size:.8rem;color:var(--p4d);font-weight:600">
            ✓ {{ session('info') }}
          </div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}">
          @csrf
          <button type="submit" class="btn-auth">Renvoyer l'email de vérification</button>
        </form>

        <div style="text-align:center;margin-top:1.2rem">
          <form method="POST" action="{{ route('logout') }}" style="display:inline">
            @csrf
            <button type="submit" style="background:none;border:none;font-size:.76rem;color:var(--ink-m);cursor:pointer;text-decoration:underline;font-family:var(--fb)">
              Se déconnecter et utiliser un autre compte
            </button>
          </form>
        </div>
      @endif

    </div>
  </div>
</div>
@endsection
