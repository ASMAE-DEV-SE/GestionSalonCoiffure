@extends('layouts.dashboard')
@section('title', 'Mon salon')

@section('content')

<div class="dash-page-header">
  <div>
    <div class="dash-greeting">Mon salon</div>
    <div class="dash-date">{{ $salon->nom_salon }} · {{ $salon->libelleStatut() }}</div>
  </div>
</div>

<form method="POST" action="{{ route('salon.profil.update') }}" enctype="multipart/form-data">
  @csrf @method('PUT')

  <div style="display:grid;grid-template-columns:1fr 300px;gap:2rem;align-items:start">
    <div>

      {{-- Photo principale --}}
      <div class="pse-card" style="margin-bottom:1.5rem">
        <div class="pse-card-head"><div class="pse-card-title">Photo principale</div></div>
        <div class="pse-photo-hero">
          <img src="{{ $salon->photo_url }}" alt="{{ $salon->nom_salon }}" style="width:100%;height:100%;object-fit:cover">
          <div class="pse-photo-overlay">
            <div class="pse-photo-overlay-ic">&#128247;</div>
            <div class="pse-photo-overlay-t">Changer la photo</div>
          </div>
        </div>
        <div style="padding:1rem 1.4rem">
          <input type="file" name="photo" accept="image/jpeg,image/png,image/webp" class="fi" style="font-size:.8rem">
          <div style="font-size:.72rem;color:var(--ink-m);margin-top:.4rem">Format JPG, PNG ou WebP · Max 5 Mo</div>
        </div>
      </div>

      {{-- Informations générales --}}
      <div class="pse-card" style="margin-bottom:1.5rem">
        <div class="pse-card-head"><div class="pse-card-title">Informations générales</div></div>
        <div class="pse-card-body">
          <div class="pse-fields-grid">
            <div>
              <label class="pse-label">Nom du salon *</label>
              <input type="text" name="nom_salon" class="pse-input" value="{{ old('nom_salon',$salon->nom_salon) }}" required>
              @error('nom_salon')<div style="color:#C04A3D;font-size:.72rem;margin-top:.3rem">{{ $message }}</div>@enderror
            </div>
            <div>
              <label class="pse-label">Ville *</label>
              <select name="ville_id" class="pse-input" required>
                @foreach($villes as $v)
                  <option value="{{ $v->id }}" {{ old('ville_id',$salon->ville_id) == $v->id ? 'selected' : '' }}>{{ $v->nom_ville }}</option>
                @endforeach
              </select>
            </div>
            <div class="pse-field-full">
              <label class="pse-label">Adresse *</label>
              <input type="text" name="adresse" class="pse-input" value="{{ old('adresse',$salon->adresse) }}" required>
            </div>
            <div>
              <label class="pse-label">Quartier</label>
              <input type="text" name="quartier" class="pse-input" value="{{ old('quartier',$salon->quartier) }}">
            </div>
            <div>
              <label class="pse-label">Code postal</label>
              <input type="text" name="code_postal" class="pse-input" value="{{ old('code_postal',$salon->code_postal) }}">
            </div>
            <div>
              <label class="pse-label">Téléphone</label>
              <input type="tel" name="telephone" class="pse-input" value="{{ old('telephone',$salon->telephone) }}">
            </div>
            <div>
              <label class="pse-label">Email</label>
              <input type="email" name="email" class="pse-input" value="{{ old('email',$salon->email) }}">
            </div>
            <div class="pse-field-full">
              <label class="pse-label">Description</label>
              <textarea name="description" class="pse-input" rows="3">{{ old('description',$salon->description) }}</textarea>
            </div>
          </div>
        </div>
      </div>

      {{-- Horaires --}}
      <div class="pse-card" style="margin-bottom:1.5rem">
        <div class="pse-card-head"><div class="pse-card-title">Horaires d'ouverture</div></div>
        <div class="pse-card-body">
          <table class="horaires-table">
            @foreach(['lundi','mardi','mercredi','jeudi','vendredi','samedi','dimanche'] as $j)
              @php $h = $salon->horaires[$j] ?? ['debut'=>'09:00','fin'=>'18:00','ferme'=>false]; @endphp
              <tr class="hor-row {{ ($h['ferme']??false) ? 'fermee' : '' }}" id="hor-row-{{ $j }}">
                <td class="hor-day {{ now()->translatedFormat('l') === ucfirst($j) ? 'today-d' : '' }}">{{ ucfirst($j) }}</td>
                <td><input type="time" name="h_{{ $j }}_debut" class="hor-input" value="{{ $h['debut'] ?? '09:00' }}"></td>
                <td><input type="time" name="h_{{ $j }}_fin"   class="hor-input" value="{{ $h['fin']   ?? '18:00' }}"></td>
                <td class="hor-closed-toggle">
                  <label class="toggle" title="Fermé">
                    <input type="checkbox" name="h_{{ $j }}_ferme" {{ ($h['ferme']??false) ? 'checked' : '' }}
                           onchange="toggleFerme('{{ $j }}', this.checked)">
                    <span class="tgl-sl"></span>
                  </label>
                </td>
              </tr>
            @endforeach
          </table>
        </div>
      </div>

    </div>

    {{-- Sidebar infos --}}
    <div>
      <div class="pse-card" style="margin-bottom:1.2rem">
        <div class="pse-card-head"><div class="pse-card-title">Statut du salon</div></div>
        <div class="pse-card-body">
          <div class="pse-stat-row"><span class="pse-stat-k">Statut</span>
            <span class="pse-stat-v">
              @if($salon->estValide()) <span class="valide-badge">&#10003; Validé</span>
              @elseif($salon->estEnAttente()) <span class="en-attente-badge">&#8987; En attente</span>
              @else <span class="bge-er">Suspendu</span> @endif
            </span>
          </div>
          <div class="pse-stat-row"><span class="pse-stat-k">Note</span>
            <span class="pse-stat-v gold"><span class="note-stars">{{ str_repeat('★',round($salon->note_moy)) }}</span> {{ number_format($salon->note_moy,1) }}</span></div>
          <div class="pse-stat-row"><span class="pse-stat-k">Avis</span><span class="pse-stat-v">{{ $salon->nb_avis }}</span></div>
          <div class="pse-stat-row"><span class="pse-stat-k">Employés</span><span class="pse-stat-v">{{ $salon->nb_employes }}</span></div>
        </div>
      </div>

      <div style="position:sticky;top:80px">
        <button type="submit" class="btn-auth" style="display:block;width:100%;text-align:center;cursor:pointer;border:none;margin-bottom:.75rem">
          Enregistrer les modifications
        </button>
        <a href="{{ route('salon.dashboard') }}" class="btn-gh" style="display:block;text-align:center">Annuler</a>
      </div>
    </div>
  </div>
</form>

@push('scripts')
<script>
function toggleFerme(jour, ferme) {
  const row = document.getElementById('hor-row-' + jour);
  row.classList.toggle('fermee', ferme);
}
</script>
@endpush
@endsection
