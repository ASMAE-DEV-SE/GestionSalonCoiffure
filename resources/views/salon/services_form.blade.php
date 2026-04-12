@extends('layouts.dashboard')
@section('title', isset($service) ? 'Modifier le service' : 'Ajouter un service')

@section('content')

<div class="dash-page-header">
  <div>
    <div class="dash-greeting">{{ isset($service) ? 'Modifier le service' : 'Ajouter un service' }}</div>
    <div class="dash-date">{{ $salon->nom_salon }}</div>
  </div>
  <a href="{{ route('salon.services.index') }}" class="btn-new-booking">&#8592; Services</a>
</div>

<div style="max-width:640px">
  <div class="emp-table-card" style="padding:1.8rem">
    <form method="POST"
          action="{{ isset($service) ? route('salon.services.update', $service->id) : route('salon.services.store') }}">
      @csrf
      @if(isset($service)) @method('PUT') @endif

      <div class="row2m">
        <div class="fg">
          <label>Nom du service *</label>
          <input type="text" name="nom_service" class="fi" required
                 value="{{ old('nom_service', $service->nom_service ?? '') }}"
                 placeholder="Coupe femme">
          @error('nom_service')<div class="fi-error">{{ $message }}</div>@enderror
        </div>
        <div class="fg">
          <label>Catégorie *</label>
          <select name="categorie" class="fi" required>
            @foreach(['Coiffure','Couleur','Soins','Ongles','Massage','Épilation','Barbe','Autre'] as $c)
              <option value="{{ $c }}" {{ old('categorie', $service->categorie ?? '') === $c ? 'selected' : '' }}>{{ $c }}</option>
            @endforeach
          </select>
          @error('categorie')<div class="fi-error">{{ $message }}</div>@enderror
        </div>
      </div>

      <div class="fg">
        <label>Description <span style="font-weight:400;font-size:.78rem;color:var(--ink-m)">(optionnel)</span></label>
        <textarea name="description" class="fi" rows="3"
                  placeholder="Coupe + shampooing inclus...">{{ old('description', $service->description ?? '') }}</textarea>
        @error('description')<div class="fi-error">{{ $message }}</div>@enderror
      </div>

      <div class="row2m">
        <div class="fg">
          <label>Prix (MAD) *</label>
          <input type="number" name="prix" class="fi" required min="0" step="0.01"
                 value="{{ old('prix', $service->prix ?? '') }}" placeholder="120">
          @error('prix')<div class="fi-error">{{ $message }}</div>@enderror
        </div>
        <div class="fg">
          <label>Durée (minutes) *</label>
          <input type="number" name="duree_minu" class="fi" required min="10" max="480"
                 value="{{ old('duree_minu', $service->duree_minu ?? '') }}" placeholder="45">
          @error('duree_minu')<div class="fi-error">{{ $message }}</div>@enderror
        </div>
      </div>

      <div class="fg" style="display:flex;align-items:center;justify-content:space-between;margin-top:.5rem">
        <label style="font-size:.7rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--ink-b)">Service actif</label>
        <label class="toggle">
          <input type="checkbox" name="actif" value="1"
                 {{ old('actif', ($service->actif ?? true) ? '1' : '0') ? 'checked' : '' }}>
          <span class="tgl-sl"></span>
        </label>
      </div>

      <div style="display:flex;gap:.75rem;margin-top:1.8rem">
        <a href="{{ route('salon.services.index') }}" class="btn-xs btn-xs-e" style="padding:.7rem 1.4rem">Annuler</a>
        <button type="submit" class="btn-add">
          {{ isset($service) ? 'Mettre à jour' : 'Créer le service' }}
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
