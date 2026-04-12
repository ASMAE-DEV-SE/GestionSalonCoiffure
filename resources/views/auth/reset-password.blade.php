@extends('layouts.app')
@section('title', 'Nouveau mot de passe')

@section('content')
<div class="auth-page">

  <div class="auth-left">
    <img class="auth-left-bg" src="https://images.unsplash.com/photo-1560066984-138dadb4c035?w=900&q=80" alt="">
    <div class="auth-left-content">
      <div class="auth-left-tag">Sécurité du compte</div>
      <h1 class="auth-left-title">Nouveau mot<br>de <em>passe</em></h1>
      <p class="auth-left-desc">Choisissez un mot de passe robuste pour protéger votre espace Salonify.</p>
      <div class="auth-points">
        <div class="auth-point"><div class="auth-point-icon">&#10003;</div><div class="auth-point-text">8 caractères minimum</div></div>
        <div class="auth-point"><div class="auth-point-icon">&#10003;</div><div class="auth-point-text">Au moins une majuscule et un chiffre</div></div>
        <div class="auth-point"><div class="auth-point-icon">&#10003;</div><div class="auth-point-text">Évitez les mots de passe déjà utilisés</div></div>
      </div>
    </div>
  </div>

  <div class="auth-right">
    <div class="auth-box">

      <div class="auth-form-title">Nouveau mot de passe</div>
      <p class="auth-form-desc">Choisissez un nouveau mot de passe sécurisé pour votre compte.</p>

      <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="form-group">
          <label for="email">Adresse email</label>
          <input id="email" type="email" name="email" class="form-input"
                 value="{{ old('email', $email ?? '') }}" required autofocus>
          @error('email')<div style="color:#C04A3D;font-size:.76rem;margin-top:.35rem;font-weight:600">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
          <label>Nouveau mot de passe</label>
          <div class="password-wrap">
            <input type="password" name="mot_de_passe" id="pw1" class="form-input"
                   placeholder="8 caractères minimum" required oninput="checkStrength(this.value)">
            <button type="button" class="password-eye"
                    onclick="const i=document.getElementById('pw1');i.type=i.type==='password'?'text':'password';this.style.color=i.type==='text'?'var(--p4)':''">&#128065;</button>
          </div>
          {{-- Barre de force --}}
          <div style="margin-top:.55rem">
            <div style="height:4px;background:var(--border2);border-radius:2px;overflow:hidden;margin-bottom:.3rem">
              <div id="strengthFill" style="height:100%;width:0%;transition:width .3s,background .3s;border-radius:2px;background:var(--p4)"></div>
            </div>
            <div id="strengthLabel" style="font-size:.7rem;font-weight:600;color:var(--ink-m)"></div>
          </div>
          @error('mot_de_passe')<div style="color:#C04A3D;font-size:.76rem;margin-top:.35rem;font-weight:600">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
          <label>Confirmer le mot de passe</label>
          <div class="password-wrap">
            <input type="password" name="mot_de_passe_confirmation" id="pw2"
                   class="form-input" placeholder="••••••••" required>
            <button type="button" class="password-eye"
                    onclick="const i=document.getElementById('pw2');i.type=i.type==='password'?'text':'password'">&#128065;</button>
          </div>
          <div id="matchMsg" style="font-size:.7rem;margin-top:.35rem"></div>
        </div>

        <button type="submit" class="btn-auth">Réinitialiser le mot de passe</button>
      </form>

    </div>
  </div>
</div>

@push('scripts')
<script>
function checkStrength(v) {
  const fill  = document.getElementById('strengthFill');
  const label = document.getElementById('strengthLabel');
  let score   = 0;
  if (v.length >= 8)           score++;
  if (/[A-Z]/.test(v))         score++;
  if (/[0-9]/.test(v))         score++;
  if (v.length >= 12)          score++;
  const pct    = score * 25;
  const colors = ['#C04A3D','#D4A844','#89986D','#4A7C1F'];
  const labels = ['Trop court','Moyen','Bon','Excellent'];
  fill.style.width      = pct + '%';
  fill.style.background = v.length ? colors[Math.max(0,score-1)] : 'var(--border2)';
  label.textContent     = v.length ? labels[Math.max(0,score-1)] : '';
  label.style.color     = v.length ? colors[Math.max(0,score-1)] : 'var(--ink-m)';
}
document.getElementById('pw2').addEventListener('input', function() {
  const msg = document.getElementById('matchMsg');
  if (!this.value) { msg.textContent = ''; return; }
  if (this.value === document.getElementById('pw1').value) {
    msg.textContent = '✓ Les mots de passe correspondent';
    msg.style.color = 'var(--p4d)';
  } else {
    msg.textContent = '✗ Les mots de passe ne correspondent pas';
    msg.style.color = '#C04A3D';
  }
});
</script>
@endpush
@endsection
