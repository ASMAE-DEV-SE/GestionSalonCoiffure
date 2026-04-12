@extends('layouts.app')
@section('title', 'Inscription')

@section('content')
<div class="auth-page">

  <div class="auth-left">
    <img class="auth-left-bg" src="https://images.unsplash.com/photo-1487412947147-5cebf100ffc2?w=900&q=80" alt="">
    <div class="auth-left-content">
      <div class="auth-left-tag">Rejoignez Salonify</div>
      <h1 class="auth-left-title">Créez votre<br>compte <em>gratuit</em></h1>
      <p class="auth-left-desc">Accédez aux meilleurs salons de beauté du Maroc. Inscription en moins de 2 minutes.</p>
      <div class="auth-points">
        <div class="auth-point"><div class="auth-point-icon">&#10003;</div><div class="auth-point-text">Réservation gratuite, paiement au salon</div></div>
        <div class="auth-point"><div class="auth-point-icon">&#10003;</div><div class="auth-point-text">Inscription en 2 minutes</div></div>
        <div class="auth-point"><div class="auth-point-icon">&#9986;</div><div class="auth-point-text">Vous êtes un salon ? Inscrivez votre établissement</div></div>
      </div>
    </div>
  </div>

  <div class="auth-right">
    <div class="auth-box">

      <div class="auth-tabs">
        <div class="auth-tab" onclick="window.location='{{ route('login') }}'">Connexion</div>
        <div class="auth-tab active">Inscription</div>
      </div>

      {{-- Choix du rôle --}}
      <div class="role-label">Je m'inscris en tant que</div>
      <div class="role-grid" id="roleGrid">
        <div class="role-option selected" id="roleClient" onclick="setRole('client')">
          <div class="role-icon">&#9786;</div>
          <div class="role-name">Client</div>
          <div class="role-desc">Je cherche un salon</div>
        </div>
        <div class="role-option" id="roleSalon" onclick="setRole('salon')">
          <div class="role-icon">&#9986;</div>
          <div class="role-name">Salon</div>
          <div class="role-desc">Je gère un établissement</div>
        </div>
      </div>

      <form method="POST" action="{{ route('register') }}" id="registerForm">
        @csrf
        <input type="hidden" name="role" id="roleInput" value="{{ old('role','client') }}">

        <div class="row-2col">
          <div class="form-group">
            <label>Prénom</label>
            <input type="text" name="prenom" class="form-input" value="{{ old('prenom') }}" placeholder="Salma" required>
            @error('prenom')<div style="color:#C04A3D;font-size:.72rem;margin-top:.3rem">{{ $message }}</div>@enderror
          </div>
          <div class="form-group">
            <label>Nom</label>
            <input type="text" name="nom" class="form-input" value="{{ old('nom') }}" placeholder="Benali" required>
            @error('nom')<div style="color:#C04A3D;font-size:.72rem;margin-top:.3rem">{{ $message }}</div>@enderror
          </div>
        </div>

        <div class="form-group">
          <label>Adresse email</label>
          <input type="email" name="email" class="form-input" value="{{ old('email') }}" placeholder="votre@email.com" required>
          @error('email')<div style="color:#C04A3D;font-size:.72rem;margin-top:.3rem">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
          <label>Téléphone <span style="font-weight:400;font-size:.78rem;color:var(--ink-m)">(optionnel — pour les rappels SMS)</span></label>
          <input type="tel" name="telephone" class="form-input" value="{{ old('telephone') }}" placeholder="06 6X XX XX XX">
        </div>

        <div class="form-group">
          <label>Mot de passe</label>
          <div class="password-wrap">
            <input type="password" name="mot_de_passe" id="pw1" class="form-input" placeholder="8 caractères minimum" required>
            <button type="button" class="password-eye" onclick="const i=document.getElementById('pw1');i.type=i.type==='password'?'text':'password'">&#128065;</button>
          </div>
          @error('mot_de_passe')<div style="color:#C04A3D;font-size:.72rem;margin-top:.3rem">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
          <label>Confirmer le mot de passe</label>
          <div class="password-wrap">
            <input type="password" name="mot_de_passe_confirmation" id="pw2" class="form-input" placeholder="••••••••" required>
            <button type="button" class="password-eye" onclick="const i=document.getElementById('pw2');i.type=i.type==='password'?'text':'password'">&#128065;</button>
          </div>
        </div>

        {{-- Champs salon (masqués par défaut) --}}
        <div id="salonFields" style="display:none">
          <div style="height:1px;background:var(--border2);margin:1rem 0"></div>
          <div style="font-size:.72rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--ink-m);margin-bottom:.9rem">Informations du salon</div>

          <div class="form-group">
            <label>Nom du salon</label>
            <input type="text" name="nom_salon" class="form-input" value="{{ old('nom_salon') }}" placeholder="Elegance Coiffure">
            @error('nom_salon')<div style="color:#C04A3D;font-size:.72rem;margin-top:.3rem">{{ $message }}</div>@enderror
          </div>

          <div class="form-group">
            <label>Ville</label>
            <select name="ville_id" class="form-input">
              <option value="">— Sélectionner une ville —</option>
              @foreach($villes as $ville)
                <option value="{{ $ville->id }}" {{ old('ville_id') == $ville->id ? 'selected' : '' }}>
                  {{ $ville->nom_ville }}
                </option>
              @endforeach
            </select>
            @error('ville_id')<div style="color:#C04A3D;font-size:.72rem;margin-top:.3rem">{{ $message }}</div>@enderror
          </div>

          <div class="form-group">
            <label>Adresse complète</label>
            <input type="text" name="adresse" class="form-input" value="{{ old('adresse') }}" placeholder="12, Rue Ibn Sina, Agdal">
            @error('adresse')<div style="color:#C04A3D;font-size:.72rem;margin-top:.3rem">{{ $message }}</div>@enderror
          </div>

          <div style="background:var(--p1);border:1.5px solid var(--p2);padding:.9rem 1.1rem;margin-bottom:1rem;font-size:.78rem;color:var(--ink-s);line-height:1.65">
            &#128161; Votre salon sera examiné par notre équipe sous 24h avant d'être publié sur la plateforme.
          </div>
        </div>

        <div class="checkbox-row">
          <input type="checkbox" name="cgv" id="cgv" required>
          <label for="cgv">J'accepte les <a href="#">conditions générales d'utilisation</a> et la <a href="#">politique de confidentialité</a> de Salonify.</label>
        </div>
        @error('cgv')<div style="color:#C04A3D;font-size:.72rem;margin-bottom:.7rem">{{ $message }}</div>@enderror

        <button type="submit" class="btn-auth">Créer mon compte</button>
      </form>

      <div class="switch-text">
        Déjà inscrit ?<a href="{{ route('login') }}">Se connecter</a>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
function setRole(role) {
  document.getElementById('roleInput').value = role;
  document.getElementById('roleClient').classList.toggle('selected', role === 'client');
  document.getElementById('roleSalon').classList.toggle('selected', role === 'salon');
  document.getElementById('salonFields').style.display = role === 'salon' ? 'block' : 'none';
}
// Restaurer le rôle si erreur de validation
setRole('{{ old('role','client') }}');
</script>
@endpush
@endsection
