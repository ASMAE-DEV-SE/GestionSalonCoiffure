@extends('layouts.app')
@section('title', 'Salons à ' . $villeModel->nom_ville)
@section('meta_description', 'Réservez dans les meilleurs salons de beauté à ' . $villeModel->nom_ville . '. ' . $salons->total() . ' établissements disponibles.')

@section('content')

{{-- ══ BANNIÈRE AUTO-QUARTIER ════════════════════════════════════ --}}
@if($autoQuartier && $quartierActif)
  <div style="background:linear-gradient(90deg,var(--p1),#fff);border-bottom:2px solid var(--p3);padding:.7rem 1.4rem;display:flex;align-items:center;gap:1rem;flex-wrap:wrap">
    <span style="font-size:.85rem;color:var(--p4dd);font-weight:700">
      &#128205; Salons dans votre quartier : <strong>{{ $quartierActif }}</strong>
    </span>
    <span style="font-size:.78rem;color:var(--ink-m)">
      — {{ $salons->total() }} résultat{{ $salons->total() > 1 ? 's' : '' }} trouvé{{ $salons->total() > 1 ? 's' : '' }}
    </span>
    <a href="{{ route('salons.index', $villeModel->nom_ville) }}"
       style="margin-left:auto;font-size:.74rem;color:var(--ink-m);text-decoration:underline;white-space:nowrap">
      &#10005; Voir tous les salons de {{ $villeModel->nom_ville }}
    </a>
  </div>
@endif

{{-- ══ BARRE FILTRES QUARTIERS ══════════════════════════════════ --}}
<div class="quartier-bar">
  <div class="quartier-bar-inner">

    {{-- Bouton GPS discret dans la barre --}}
    <button id="btnGeoSalon" onclick="detecterQuartier()"
            style="display:inline-flex;align-items:center;gap:.4rem;background:var(--p2);border:1.5px solid var(--p3);color:var(--p4dd);padding:.3rem .9rem;border-radius:20px;font-size:.74rem;font-weight:600;cursor:pointer;flex-shrink:0;margin-right:.4rem"
            title="Détecter automatiquement votre quartier">
      <span id="geoSalonIcon">&#128205;</span>
      <span id="geoSalonText">Près de moi</span>
    </button>

    <a href="{{ route('salons.index', $villeModel->nom_ville) }}"
       class="quartier-pill {{ !$quartierActif ? 'active' : '' }}">
      Tous <span class="quartier-count">{{ $salons->total() }}</span>
    </a>
    @foreach($quartiers as $q)
      <a href="{{ route('salons.index', $villeModel->nom_ville) }}?quartier={{ urlencode($q) }}&tri={{ $tri }}"
         class="quartier-pill {{ $quartierActif === $q ? 'active' : '' }}">
        {{ $q }}
      </a>
    @endforeach
  </div>
</div>

{{-- ══ BARRE RECHERCHE + TRI ═════════════════════════════════════ --}}
<div class="search-controls-bar">
  <form class="search-controls-inner" method="GET" action="{{ route('salons.index', $villeModel->nom_ville) }}">
    <div class="search-field-wrap">
      <div class="search-field-icon">&#128269;</div>
      <input type="text" name="q" class="search-field-input"
             placeholder="Rechercher un salon, un service..."
             value="{{ request('q') }}">
    </div>

    <select name="categorie" class="field" style="max-width:200px">
      <option value="">Tous les services</option>
      @foreach(['Coiffure','Couleur','Soins','Ongles','Massage','Épilation'] as $cat)
        <option value="{{ $cat }}" {{ request('categorie') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
      @endforeach
    </select>

    <select name="note_min" class="field" style="max-width:160px">
      <option value="">Toutes les notes</option>
      <option value="4.5" {{ request('note_min') == '4.5' ? 'selected' : '' }}>&#9733; 4.5+</option>
      <option value="4"   {{ request('note_min') == '4'   ? 'selected' : '' }}>&#9733; 4.0+</option>
      <option value="3"   {{ request('note_min') == '3'   ? 'selected' : '' }}>&#9733; 3.0+</option>
    </select>

    @if(request('quartier'))<input type="hidden" name="quartier" value="{{ request('quartier') }}">@endif

    <button type="submit" class="btn-search">Filtrer</button>

    <div class="sort-controls">
      <span class="sort-label">Trier :</span>
      @foreach(['note' => 'Mieux notés', 'avis' => 'Plus d\'avis', 'alpha' => 'A–Z'] as $val => $lbl)
        <a href="{{ request()->fullUrlWithQuery(['tri' => $val]) }}"
           class="sort-btn {{ $tri === $val ? 'active' : '' }}">{{ $lbl }}</a>
      @endforeach
    </div>
  </form>
</div>

{{-- ══ RÉSULTATS ════════════════════════════════════════════════ --}}
<div class="salons-content">

  <div class="results-count-bar">
    <div class="results-label">
      <strong>{{ $salons->total() }}</strong> salon{{ $salons->total() > 1 ? 's' : '' }}
      à <strong>{{ $villeModel->nom_ville }}</strong>
      @if(request('quartier')) · {{ request('quartier') }} @endif
    </div>
    <div style="font-size:.78rem;color:var(--ink-m)">
      Page {{ $salons->currentPage() }} / {{ $salons->lastPage() }}
    </div>
  </div>

  @if($salons->isEmpty())
    <div style="padding:5rem 0;text-align:center">
      <div style="font-size:2.5rem;margin-bottom:1rem">&#128269;</div>
      <p style="color:var(--ink-m);font-size:.9rem">Aucun salon ne correspond à vos critères.</p>
      <a href="{{ route('salons.index', $villeModel->nom_ville) }}" style="color:var(--p4d);font-weight:700;font-size:.84rem;text-decoration:underline">
        Voir tous les salons de {{ $villeModel->nom_ville }}
      </a>
    </div>
  @else
    <div class="featured-grid">
      @foreach($salons as $salon)
        <a href="{{ route('salons.show', [$villeModel->nom_ville, $salon->slug]) }}" class="card">
          <div class="card-image">
            <img src="{{ $salon->photo_url }}" alt="{{ $salon->nom_salon }}" style="height:200px">
            @if($salon->servicesActifs->count())
              <span class="card-price-tag">
                À partir de {{ number_format($salon->servicesActifs->min('prix'), 0, ',', ' ') }} MAD
              </span>
            @endif
            <div class="card-category-icon icon-coiffure">&#9986;</div>
          </div>
          <div class="card-body">
            <div class="card-name">{{ $salon->nom_salon }}</div>
            <div class="card-rating">
              @if($salon->real_nb_avis > 0)
                <span class="card-stars">{{ str_repeat('★', (int) round($salon->real_note_moy)) }}{{ str_repeat('☆', 5 - (int) round($salon->real_note_moy)) }}</span>
                <span class="card-score">{{ number_format($salon->real_note_moy, 1) }}</span>
                <span class="card-reviews">({{ $salon->real_nb_avis }} avis)</span>
              @else
                <span class="card-reviews" style="color:var(--ink-m)">Aucun avis</span>
              @endif
            </div>
            <div class="card-location">
              <span class="verified-dot"></span>{{ $salon->quartier }}, {{ $villeModel->nom_ville }}
            </div>
            <p class="card-desc">{{ Str::limit($salon->description, 80) }}</p>
            <button class="btn-view">Voir &amp; Réserver</button>
          </div>
        </a>
      @endforeach
    </div>

    {{-- Pagination --}}
    @if($salons->hasPages())
      <div class="pagination">
        @if($salons->onFirstPage())
          <span class="page-btn" style="opacity:.4">&#8592;</span>
        @else
          <a href="{{ $salons->previousPageUrl() }}" class="page-btn">&#8592;</a>
        @endif

        @foreach($salons->getUrlRange(1, $salons->lastPage()) as $page => $url)
          <a href="{{ $url }}" class="page-btn {{ $page === $salons->currentPage() ? 'active' : '' }}">
            {{ $page }}
          </a>
        @endforeach

        @if($salons->hasMorePages())
          <a href="{{ $salons->nextPageUrl() }}" class="page-btn">&#8594;</a>
        @else
          <span class="page-btn" style="opacity:.4">&#8594;</span>
        @endif
      </div>
    @endif
  @endif

</div>

@push('scripts')
<script>
// URL de base pour la ville courante
const baseUrl = "{{ route('salons.index', $villeModel->nom_ville) }}";

function detecterQuartier() {
  const btn  = document.getElementById('btnGeoSalon');
  const icon = document.getElementById('geoSalonIcon');
  const text = document.getElementById('geoSalonText');

  if (!navigator.geolocation) {
    alert('La géolocalisation n\'est pas supportée par votre navigateur.');
    return;
  }

  btn.disabled    = true;
  icon.textContent = '⏳';
  text.textContent = '…';

  navigator.geolocation.getCurrentPosition(
    async (position) => {
      const lat = position.coords.latitude;
      const lng = position.coords.longitude;

      try {
        const res  = await fetch(
          `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&accept-language=fr&zoom=16`,
          { headers: { 'Accept-Language': 'fr' } }
        );
        const data = await res.json();
        const addr = data.address || {};

        // Quartier : suburb → neighbourhood → quarter → road → city_district
        const quartier = addr.suburb || addr.neighbourhood || addr.quarter
                      || addr.city_district || addr.road || '';

        if (quartier) {
          // Rediriger avec le quartier détecté (marqué auto=1)
          window.location.href = baseUrl + '?quartier=' + encodeURIComponent(quartier) + '&auto=1&tri={{ $tri }}';
        } else {
          alert('Quartier introuvable pour votre position. Sélectionnez-le manuellement.');
          resetGeoBtn(btn, icon, text);
        }
      } catch (e) {
        alert('Erreur réseau. Vérifiez votre connexion internet.');
        resetGeoBtn(btn, icon, text);
      }
    },
    (err) => {
      const msgs = {
        1: 'Accès refusé. Autorisez la localisation dans votre navigateur.',
        2: 'Position introuvable.',
        3: 'Délai dépassé. Réessayez.',
      };
      alert(msgs[err.code] || 'Erreur de géolocalisation.');
      resetGeoBtn(btn, icon, text);
    },
    { timeout: 10000, maximumAge: 60000, enableHighAccuracy: true }
  );
}

function resetGeoBtn(btn, icon, text) {
  btn.disabled     = false;
  icon.textContent = '📍';
  text.textContent = 'Près de moi';
}
</script>
@endpush

@endsection
