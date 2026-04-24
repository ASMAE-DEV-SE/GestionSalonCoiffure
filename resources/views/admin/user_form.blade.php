@extends('layouts.admin')
@section('title', isset($user) ? 'Modifier l\'utilisateur' : 'Nouvel utilisateur')

@section('content')

<div class="admin-page-header">
  <div>
    <div class="admin-page-title">{{ isset($user) ? 'Modifier — ' . $user->nomComplet() : 'Nouvel utilisateur' }}</div>
    <div class="admin-page-subtitle">{{ isset($user) ? $user->email : 'Créer un compte administrateur, gérant ou client' }}</div>
  </div>
  <div class="admin-header-actions">
    <a href="{{ route('admin.users.index') }}" class="btn-admin-ghost">&#8592; Retour</a>
  </div>
</div>

<div style="max-width:700px">
  <div class="admin-table-card">
    <div class="admin-table-header">
      <div class="admin-table-title">{{ isset($user) ? 'Informations du compte' : 'Nouveau compte' }}</div>
    </div>
    <div style="padding:2rem">

      <form method="POST"
            action="{{ isset($user) ? route('admin.users.update', $user->id) : route('admin.users.store') }}">
        @csrf
        @if(isset($user)) @method('PUT') @endif

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem 1.4rem;margin-bottom:1.2rem">
          <div>
            <label class="pse-label">Prénom *</label>
            <input type="text" name="prenom" class="pse-input"
                   value="{{ old('prenom', $user->prenom ?? '') }}" required>
            @error('prenom')<div style="color:#C04A3D;font-size:.72rem;margin-top:.3rem">{{ $message }}</div>@enderror
          </div>
          <div>
            <label class="pse-label">Nom *</label>
            <input type="text" name="nom" class="pse-input"
                   value="{{ old('nom', $user->nom ?? '') }}" required>
            @error('nom')<div style="color:#C04A3D;font-size:.72rem;margin-top:.3rem">{{ $message }}</div>@enderror
          </div>
          <div>
            <label class="pse-label">Email *</label>
            <input type="email" name="email" class="pse-input"
                   value="{{ old('email', $user->email ?? '') }}" required>
            @error('email')<div style="color:#C04A3D;font-size:.72rem;margin-top:.3rem">{{ $message }}</div>@enderror
          </div>
          <div>
            <label class="pse-label">Téléphone</label>
            <input type="tel" name="telephone" class="pse-input"
                   value="{{ old('telephone', $user->telephone ?? '') }}">
          </div>
          <div>
            <label class="pse-label">Rôle *</label>
            <select name="role" class="pse-input" required>
              @foreach(['client' => 'Client', 'salon' => 'Gérant de salon', 'admin' => 'Administrateur'] as $v => $l)
                <option value="{{ $v }}" {{ old('role', $user->role ?? 'client') === $v ? 'selected' : '' }}>{{ $l }}</option>
              @endforeach
            </select>
            @error('role')<div style="color:#C04A3D;font-size:.72rem;margin-top:.3rem">{{ $message }}</div>@enderror
          </div>
          <div>
            <label class="pse-label">Ville</label>
            <select name="ville_id" class="pse-input">
              <option value="">—</option>
              @foreach(($villes ?? []) as $v)
                <option value="{{ $v->id }}" {{ (string) old('ville_id', $user->ville_id ?? '') === (string) $v->id ? 'selected' : '' }}>{{ $v->nom_ville }}</option>
              @endforeach
            </select>
            @error('ville_id')<div style="color:#C04A3D;font-size:.72rem;margin-top:.3rem">{{ $message }}</div>@enderror
          </div>
          <div>
            <label class="pse-label">Quartier</label>
            <input type="text" name="quartier" class="pse-input"
                   value="{{ old('quartier', $user->quartier ?? '') }}">
            @error('quartier')<div style="color:#C04A3D;font-size:.72rem;margin-top:.3rem">{{ $message }}</div>@enderror
          </div>
        </div>

        {{-- Mot de passe --}}
        <div style="border-top:1.5px solid var(--border2);padding-top:1.2rem;margin-bottom:1.5rem">
          <div style="font-size:.7rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--ink-m);margin-bottom:1rem">
            {{ isset($user) ? 'Nouveau mot de passe (laisser vide pour ne pas changer)' : 'Mot de passe *' }}
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
            <div>
              <label class="pse-label">{{ isset($user) ? 'Nouveau mot de passe' : 'Mot de passe *' }}</label>
              <input type="password" name="mot_de_passe" class="pse-input"
                     placeholder="8 caractères minimum" {{ isset($user) ? '' : 'required' }}>
              @error('mot_de_passe')<div style="color:#C04A3D;font-size:.72rem;margin-top:.3rem">{{ $message }}</div>@enderror
            </div>
          </div>
        </div>

        <div style="display:flex;gap:.75rem">
          <button type="submit" class="btn-auth" style="display:inline-block;width:auto;padding:.82rem 2.5rem;cursor:pointer;border:none">
            {{ isset($user) ? 'Enregistrer les modifications' : 'Créer le compte' }}
          </button>
          <a href="{{ route('admin.users.index') }}" class="btn-gh" style="padding:.82rem 2rem;display:inline-block;text-decoration:none">Annuler</a>
        </div>
      </form>

    </div>
  </div>
</div>
@endsection
