@extends('layouts.dashboard')
@section('title', 'Mon profil')

@section('content')

<div class="dash-page-header">
  <div>
    <div class="dash-greeting">Mon profil</div>
    <div class="dash-date">Membre depuis {{ $user->created_at->translatedFormat('F Y') }}</div>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 280px;gap:2rem;align-items:start">
  <div>

    {{-- Informations personnelles --}}
    <div class="section-card" style="margin-bottom:1.5rem">
      <div class="card-head">
        <div class="card-title">Informations personnelles</div>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('client.profil.update') }}">
          @csrf @method('PUT')

          <div class="fields-grid">
            <div>
              <div class="field-label">Prénom</div>
              <input type="text" name="prenom" class="field-value editable"
                     value="{{ old('prenom', $user->prenom) }}" required>
              @error('prenom')<div style="color:#C04A3D;font-size:.72rem;margin-top:.3rem">{{ $message }}</div>@enderror
            </div>
            <div>
              <div class="field-label">Nom</div>
              <input type="text" name="nom" class="field-value editable"
                     value="{{ old('nom', $user->nom) }}" required>
              @error('nom')<div style="color:#C04A3D;font-size:.72rem;margin-top:.3rem">{{ $message }}</div>@enderror
            </div>
            <div>
              <div class="field-label">Adresse email</div>
              <input type="email" name="email" class="field-value editable"
                     value="{{ old('email', $user->email) }}" required>
              @error('email')<div style="color:#C04A3D;font-size:.72rem;margin-top:.3rem">{{ $message }}</div>@enderror
            </div>
            <div>
              <div class="field-label">Téléphone</div>
              <input type="tel" name="telephone" class="field-value editable"
                     value="{{ old('telephone', $user->telephone) }}" placeholder="06 6X XX XX XX">
            </div>
          </div>

          {{-- Localisation ──────────────────────────────── --}}
          <div style="margin-top:1.4rem;padding-top:1.2rem;border-top:1px solid var(--border2)">
            <div style="font-size:.78rem;font-weight:700;color:var(--ink-h);margin-bottom:.8rem;letter-spacing:.5px;text-transform:uppercase">
              &#128205; Ma localisation <span style="font-weight:400;color:var(--ink-m);text-transform:none;letter-spacing:0">(pour trouver les salons près de vous)</span>
            </div>
            <div class="fields-grid">
              <div>
                <div class="field-label">Ville</div>
                <select name="ville_id" class="field-value editable">
                  <option value="">— Choisir une ville —</option>
                  @foreach($villes as $v)
                    <option value="{{ $v->id }}" {{ old('ville_id', $user->ville_id) == $v->id ? 'selected' : '' }}>
                      {{ $v->nom_ville }}
                    </option>
                  @endforeach
                </select>
              </div>
              <div>
                <div class="field-label">Quartier</div>
                <input type="text" name="quartier" class="field-value editable"
                       value="{{ old('quartier', $user->quartier) }}"
                       placeholder="Ex : Agdal, Guéliz, Maarif…">
              </div>
            </div>
          </div>

          <div class="form-actions" style="margin-top:1.2rem">
            <button type="submit" class="btn-save">Enregistrer</button>
          </div>
        </form>
      </div>
    </div>

    {{-- Sécurité --}}
    <div class="section-card" style="margin-bottom:1.5rem">
      <div class="card-head">
        <div class="card-title">Sécurité</div>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('client.profil.password') }}">
          @csrf @method('PUT')

          <div class="security-row">
            <div>
              <div class="security-label">Mot de passe</div>
              <div class="security-sub">Dernière modification : inconnue</div>
            </div>
          </div>

          <div class="fields-grid" style="margin-top:1rem">
            <div class="field-full">
              <div class="field-label">Mot de passe actuel</div>
              <input type="password" name="mot_de_passe_actuel" class="field-value editable" placeholder="••••••••">
              @error('mot_de_passe_actuel')<div style="color:#C04A3D;font-size:.72rem;margin-top:.3rem">{{ $message }}</div>@enderror
            </div>
            <div>
              <div class="field-label">Nouveau mot de passe</div>
              <input type="password" name="nouveau_mot_de_passe" class="field-value editable" placeholder="8 caractères min.">
              @error('nouveau_mot_de_passe')<div style="color:#C04A3D;font-size:.72rem;margin-top:.3rem">{{ $message }}</div>@enderror
            </div>
            <div>
              <div class="field-label">Confirmer</div>
              <input type="password" name="nouveau_mot_de_passe_confirmation" class="field-value editable" placeholder="••••••••">
            </div>
          </div>

          <div class="form-actions" style="margin-top:1.2rem">
            <button type="submit" class="btn-save">Modifier le mot de passe</button>
          </div>
        </form>
      </div>
    </div>

    {{-- Email vérifié --}}
    <div class="section-card" style="margin-bottom:1.5rem">
      <div class="card-head">
        <div class="card-title">Vérification email</div>
      </div>
      <div class="card-body">
        @if($user->email_verifie_le)
          <div style="display:flex;align-items:center;gap:.75rem;font-size:.88rem">
            <span class="bge-ok">&#10003; Email vérifié</span>
            <span style="color:var(--ink-m)">le {{ $user->email_verifie_le->translatedFormat('d F Y') }}</span>
          </div>
        @else
          <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem">
            <span class="bge-wa">Email non vérifié</span>
            <form method="POST" action="{{ route('verification.send') }}">
              @csrf
              <button type="submit" class="btn-save" style="padding:.5rem 1.2rem;font-size:.74rem">
                Renvoyer l'email
              </button>
            </form>
          </div>
        @endif
      </div>
    </div>

    {{-- Zone de danger --}}
    <div class="danger-zone">
      <div>
        <div class="danger-title">Supprimer mon compte</div>
        <div class="danger-desc">Cette action est irréversible. Toutes vos données seront effacées définitivement.</div>
      </div>
      <button class="btn-danger" onclick="alert('Contactez contact@salonify.ma pour supprimer votre compte.')">
        Supprimer le compte
      </button>
    </div>

  </div>

  {{-- Sidebar stats --}}
  <div>
    <div class="stats-sidebar">
      <div class="stats-sidebar-title">Statistiques</div>
      <div class="stat-row"><span class="stat-key">Réservations</span><span class="stat-val large">{{ $stats['total_rdv'] }}</span></div>
      <div class="stat-row"><span class="stat-key">Salons visités</span><span class="stat-val">{{ $stats['salons_visites'] }}</span></div>
      <div class="stat-row"><span class="stat-key">Avis publiés</span><span class="stat-val">{{ $stats['avis_publies'] }}</span></div>
      <div class="stat-row"><span class="stat-key">Membre depuis</span><span class="stat-val">{{ $stats['membre_depuis'] }}</span></div>
    </div>
  </div>
</div>
@endsection
