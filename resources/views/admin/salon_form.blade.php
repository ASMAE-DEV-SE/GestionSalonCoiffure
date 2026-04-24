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

      <form method="POST" action="{{ route('admin.salons.update', $salon->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Photo principale --}}
        <div style="margin-bottom:1.6rem;padding:1rem;border:1.5px solid var(--border2);border-radius:10px;display:flex;gap:1.2rem;align-items:center">
          <div style="width:120px;height:80px;border-radius:8px;overflow:hidden;flex-shrink:0;border:1px solid var(--border2)">
            <img src="{{ $salon->photo_url }}" alt="{{ $salon->nom_salon }}" style="width:100%;height:100%;object-fit:cover">
          </div>
          <div style="flex:1">
            <label class="pse-label">Photo principale</label>
            <input type="file" name="photo" accept="image/jpeg,image/png,image/webp" class="pse-input" style="font-size:.8rem">
            <div style="font-size:.72rem;color:var(--ink-m);margin-top:.3rem">JPG, PNG ou WebP · max 5 Mo. Laisser vide pour conserver l'actuelle.</div>
            @error('photo')<div style="color:#C04A3D;font-size:.72rem;margin-top:.3rem">{{ $message }}</div>@enderror
          </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem 1.4rem;margin-bottom:1.2rem">
          <div style="grid-column:1/-1">
            <label class="pse-label">Nom du salon *</label>
            <input type="text" name="nom_salon" class="pse-input"
                   value="{{ old('nom_salon', $salon->nom_salon) }}" required>
            @error('nom_salon')<div style="color:#C04A3D;font-size:.72rem;margin-top:.3rem">{{ $message }}</div>@enderror
          </div>

          <div>
            <label class="pse-label">Statut *</label>
            <select name="valide" class="pse-input" required>
              @foreach([1 => 'Validé', 0 => 'En attente', -1 => 'Suspendu'] as $v => $l)
                <option value="{{ $v }}" {{ (string) old('valide', $salon->valide) === (string) $v ? 'selected' : '' }}>{{ $l }}</option>
              @endforeach
            </select>
            @error('valide')<div style="color:#C04A3D;font-size:.72rem;margin-top:.3rem">{{ $message }}</div>@enderror
          </div>
          <div></div>

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

        {{-- Horaires --}}
        <div style="border-top:1.5px solid var(--border2);padding-top:1.2rem;margin-bottom:1.5rem">
          <div style="font-size:.7rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--ink-m);margin-bottom:1rem">
            Horaires d'ouverture
          </div>
          <table style="width:100%;border-collapse:collapse">
            @foreach(['lundi','mardi','mercredi','jeudi','vendredi','samedi','dimanche'] as $j)
              @php $h = $salon->horaires[$j] ?? ['debut'=>'09:00','fin'=>'18:00','ferme'=>false]; @endphp
              <tr style="border-bottom:1px solid var(--border2)">
                <td style="padding:.5rem .6rem;font-size:.85rem;font-weight:600;color:var(--ink-h);width:110px;text-transform:capitalize">{{ $j }}</td>
                <td style="padding:.5rem .4rem">
                  <input type="time" name="h_{{ $j }}_debut" class="pse-input" value="{{ old('h_'.$j.'_debut', $h['debut'] ?? '09:00') }}" style="max-width:130px">
                </td>
                <td style="padding:.5rem .4rem;font-size:.8rem;color:var(--ink-m)">à</td>
                <td style="padding:.5rem .4rem">
                  <input type="time" name="h_{{ $j }}_fin" class="pse-input" value="{{ old('h_'.$j.'_fin', $h['fin'] ?? '18:00') }}" style="max-width:130px">
                </td>
                <td style="padding:.5rem .6rem">
                  <label style="display:inline-flex;align-items:center;gap:.4rem;font-size:.8rem;color:var(--ink-s);cursor:pointer">
                    <input type="hidden" name="h_{{ $j }}_ferme" value="0">
                    <input type="checkbox" name="h_{{ $j }}_ferme" value="1" {{ old('h_'.$j.'_ferme', ($h['ferme'] ?? false)) ? 'checked' : '' }}
                           style="width:16px;height:16px;cursor:pointer">
                    Fermé
                  </label>
                </td>
              </tr>
            @endforeach
          </table>
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
