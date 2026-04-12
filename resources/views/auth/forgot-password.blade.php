@extends('layouts.app')
@section('title', 'Mot de passe oublié')

@section('content')
<div class="auth-page">

  <div class="auth-left">
    <img class="auth-left-bg" src="https://images.unsplash.com/photo-1560066984-138dadb4c035?w=900&q=80" alt="">
    <div class="auth-left-content">
      <div class="auth-left-tag">Sécurité du compte</div>
      <h1 class="auth-left-title">Réinitialisez<br>votre <em>accès</em></h1>
      <p class="auth-left-desc">Entrez votre adresse email pour recevoir un lien de réinitialisation. Le lien est valable 60 minutes.</p>
      <div class="auth-points">
        <div class="auth-point"><div class="auth-point-icon">&#9993;</div><div class="auth-point-text">Email envoyé en quelques secondes</div></div>
        <div class="auth-point"><div class="auth-point-icon">&#128274;</div><div class="auth-point-text">Lien sécurisé à usage unique</div></div>
        <div class="auth-point"><div class="auth-point-icon">&#10003;</div><div class="auth-point-text">Valable 60 minutes</div></div>
      </div>
    </div>
  </div>

  <div class="auth-right">
    <div class="auth-box">

      @if(session('success'))
        <div style="text-align:center;padding:1.5rem 0 1rem">
          <div style="width:72px;height:72px;border-radius:50%;background:rgba(197,216,157,.3);border:2px solid var(--p3);display:flex;align-items:center;justify-content:center;margin:0 auto 1.2rem;font-size:2rem">&#9993;</div>
          <div class="auth-form-title" style="font-size:1.6rem;margin-bottom:.5rem">Email envoyé !</div>
          <p style="font-size:.84rem;color:var(--ink-m);line-height:1.7;margin-bottom:1.6rem">{{ session('success') }}</p>
          <a href="{{ route('login') }}" style="font-size:.8rem;color:var(--p4d);font-weight:700;text-decoration:underline">
            &#8592; Retour à la connexion
          </a>
        </div>
      @else
        <div class="auth-form-title">Mot de passe oublié</div>
        <p class="auth-form-desc">Entrez votre adresse email pour recevoir un lien de réinitialisation.</p>

        <form method="POST" action="{{ route('password.email') }}">
          @csrf
          <div class="form-group">
            <label for="email">Adresse email</label>
            <input id="email" type="email" name="email" class="form-input"
                   value="{{ old('email') }}" placeholder="votre@email.com" required autofocus>
            @error('email')
              <div style="color:#C04A3D;font-size:.76rem;margin-top:.35rem;font-weight:600">{{ $message }}</div>
            @enderror
          </div>

          <button type="submit" class="btn-auth">Envoyer le lien</button>
        </form>

        <div class="switch-text">
          <a href="{{ route('login') }}" style="color:var(--p4d);font-weight:700;text-decoration:underline">
            &#8592; Retour à la connexion
          </a>
        </div>
      @endif

    </div>
  </div>
</div>
@endsection
