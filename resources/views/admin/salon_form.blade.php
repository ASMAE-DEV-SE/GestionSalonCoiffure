@extends('layouts.admin')
@section('title', 'Modifier — ' . $salon->nom_salon)

@section('content')

<div class="admin-page-header">
  <div>
    <div class="admin-page-title">Modifier — {{ $salon->nom_salon }}</div>
    <div class="admin-page-subtitle">Coordonnées et informations du salon</div>
  </div>
  <div class="admin-header-actions">
    <a href="{{ route('admin.salons.show', $salon->id) }}" class="btn-admin-ghost">&#8592; Retour</a>
  </div>
</div>

<div style="max-width:860px">
  <div class="admin-table-card">
    <div class="admin-table-header">
      <div class="admin-table-title">Informations du salon</div>
    </div>
    <div style="padding:2rem">

      <form method="POST" action="{{ route('admin.salons.update', $salon->id) }}">
        @csrf
        @method('PUT')

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem 1.4rem;margin-bottom:1.2rem">
          <div style="grid-column:1/-1">
            <label class="pse-label">Nom du salon *</label>
            <input type="text" name="nom_salon" class="pse-input"
                   value="{{ old('nom_salon', $salon->nom_salon) }}" required>
            @error('nom_salon')<div style="color:#C04A3D;font-size:.72rem;margin-top:.3rem">{{ $message }}</div>@enderror
          </div>

          <div>
            <label class="pse-label">Ville *</label>
            <select name="ville_id" class="pse-input" required>
              @foreach($villes as $v)
                <option value="{{ $v->id }}" {{ (string) old('ville_id', $salon->ville_id) === (string) $v->id ? 'selected' : '' }}>{{ $v->nom_ville }}</option>
              @endforeach
            </select>
            @error('ville_id')<div style="color:#C04A3D;font-size:.72rem;margin-top:.3rem">{{ $message }}</div>@enderror
          </div>
          <div>
            <label class="pse-label">Code postal</label>
            <input type="text" name="code_postal" class="pse-input"
                   value="{{ old('code_postal', $salon->code_postal) }}">
            @error('code_postal')<div style="color:#C04A3D;font-size:.72rem;margin-top:.3rem">{{ $message }}</div>@enderror
          </div>

          <div>
            <label class="pse-label">Adresse *</label>
            <input type="text" name="adresse" class="pse-input"
                   value="{{ old('adresse', $salon->adresse) }}" required>
            @error('adresse')<div style="color:#C04A3D;font-size:.72rem;margin-top:.3rem">{{ $message }}</div>@enderror
          </div>
          <div>
            <label class="pse-label">Quartier</label>
            <input type="text" name="quartier" class="pse-input"
                   value="{{ old('quartier', $salon->quartier) }}">
            @error('quartier')<div style="color:#C04A3D;font-size:.72rem;margin-top:.3rem">{{ $message }}</div>@enderror
          </div>

          <div>
            <label class="pse-label">Téléphone</label>
            <input type="tel" name="telephone" class="pse-input"
                   value="{{ old('telephone', $salon->telephone) }}">
            @error('telephone')<div style="color:#C04A3D;font-size:.72rem;margin-top:.3rem">{{ $message }}</div>@enderror
          </div>
          <div>
            <label class="pse-label">Email</label>
            <input type="email" name="email" class="pse-input"
                   value="{{ old('email', $salon->email) }}">
            @error('email')<div style="color:#C04A3D;font-size:.72rem;margin-top:.3rem">{{ $message }}</div>@enderror
          </div>

          <div>
            <label class="pse-label">Latitude</label>
            <input type="text" name="latitude" class="pse-input"
                   value="{{ old('latitude', $salon->latitude) }}">
            @error('latitude')<div style="color:#C04A3D;font-size:.72rem;margin-top:.3rem">{{ $message }}</div>@enderror
          </div>
          <div>
            <label class="pse-label">Longitude</label>
            <input type="text" name="longitude" class="pse-input"
                   value="{{ old('longitude', $salon->longitude) }}">
            @error('longitude')<div style="color:#C04A3D;font-size:.72rem;margin-top:.3rem">{{ $message }}</div>@enderror
          </div>

          <div style="grid-column:1/-1">
            <label class="pse-label">RIB</label>
            <input type="text" name="rib" class="pse-input"
                   value="{{ old('rib', $salon->rib) }}">
            @error('rib')<div style="color:#C04A3D;font-size:.72rem;margin-top:.3rem">{{ $message }}</div>@enderror
          </div>

          <div style="grid-column:1/-1">
            <label class="pse-label">Description</label>
            <textarea name="description" class="pse-input" rows="4">{{ old('description', $salon->description) }}</textarea>
            @error('description')<div style="color:#C04A3D;font-size:.72rem;margin-top:.3rem">{{ $message }}</div>@enderror
          </div>
        </div>

        <div style="display:flex;gap:.75rem">
          <button type="submit" class="btn-auth" style="display:inline-block;width:auto;padding:.82rem 2.5rem;cursor:pointer;border:none">
            Enregistrer les modifications
          </button>
          <a href="{{ route('admin.salons.show', $salon->id) }}" class="btn-gh" style="padding:.82rem 2rem;display:inline-block;text-decoration:none">Annuler</a>
        </div>
      </form>

    </div>
  </div>
</div>
@endsection
