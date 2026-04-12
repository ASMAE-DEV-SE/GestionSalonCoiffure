@extends('layouts.app')
@section('title', 'Nos villes')
@section('meta_description', 'Trouvez un salon de beauté près de chez vous — Salonify couvre Rabat, Casablanca, Marrakech et toutes les grandes villes du Maroc.')

@section('content')

{{-- ══ HERO VILLES ══════════════════════════════════════════════ --}}
<section class="cities-hero">
  <div class="cities-hero-content wrap">
    <div class="cities-hero-tag">Partout au Maroc</div>
    <h1>Nos <em style="font-style:italic;color:var(--p2)">villes</em></h1>
    <p class="cities-hero-sub">{{ $villes->count() }} villes, {{ $totalSalons }}+ salons référencés sur toute l'étendue du royaume.</p>

    <div class="cities-search-bar">
      <input type="text" id="villeSearch" class="cities-search-input"
             placeholder="&#128269;  Rechercher une ville..."
             oninput="filterVilles(this.value)">
      <button class="cities-search-btn">Rechercher</button>
    </div>

    <div class="cities-stats">
      <div>
        <div class="city-stat-val">{{ $villes->count() }}</div>
        <div class="city-stat-lbl">Villes couvertes</div>
      </div>
      <div>
        <div class="city-stat-val">{{ $totalSalons }}+</div>
        <div class="city-stat-lbl">Salons référencés</div>
      </div>
      <div>
        <div class="city-stat-val">24h/24</div>
        <div class="city-stat-lbl">Réservation en ligne</div>
      </div>
    </div>
  </div>
</section>

{{-- ══ GRILLE DES VILLES ════════════════════════════════════════ --}}
<section class="cities-section" id="villesList">

  {{-- Ville principale en grand (Rabat) --}}
  @php $villesPrincipales = $villes->take(2); $autresVilles = $villes->skip(2); @endphp

  <div class="cities-section-header">
    <h2>Villes <em style="font-style:italic;color:var(--p4d)">populaires</em></h2>
    <span class="cities-section-sub">Les plus demandées sur Salonify</span>
  </div>

  <div class="cities-grid" style="margin-bottom:3rem">
    @foreach($villesPrincipales as $ville)
      <a href="{{ route('salons.index', $ville->nom_ville) }}" class="city-card large">
        <img class="city-card-img"
             src="https://images.unsplash.com/photo-{{ $loop->first ? '1555448248-2571daf6344b' : '1558618666-fcd25c85cd64' }}?w=600&h=320&fit=crop&q=80"
             alt="{{ $ville->nom_ville }}">
        <div class="city-card-overlay">
          <div class="city-card-name">{{ $ville->nom_ville }}</div>
          <div class="city-card-count">{{ $ville->salons_valides_count }} salon{{ $ville->salons_valides_count > 1 ? 's' : '' }} disponibles</div>
        </div>
        @if($loop->first)
          <span class="city-card-featured">&#9733; N°1</span>
        @endif
        <div class="city-card-arrow">&#8594;</div>
      </a>
    @endforeach
  </div>

  <div class="cities-section-header">
    <h2>Toutes les <em style="font-style:italic;color:var(--p4d)">villes</em></h2>
    <span class="cities-section-sub" id="villesCount">{{ $villes->count() }} villes</span>
  </div>

  <div class="cities-grid" id="villesGrid">
    @php
      $photos = [
        '1540541338287-41700207dee6','1539020140153-e479b8c22e70',
        '1558618047-3c8c76ca7d13','1521590832167-7bcbfaa6381f',
        '1507003211169-0a1dd7228f2d','1531746020798-e6953c6e8e04',
        '1580489944761-15a19d654956','1534528741775-53994a69daeb',
        '1494790108377-be9c29b29330','1438761681033-6461ffad8d80',
      ];
    @endphp

    @foreach($villes as $ville)
      <a href="{{ route('salons.index', $ville->nom_ville) }}"
         class="city-card ville-item"
         data-nom="{{ strtolower($ville->nom_ville) }}">
        <img class="city-card-img"
             src="https://images.unsplash.com/photo-{{ $photos[$loop->index % count($photos)] }}?w=400&h=220&fit=crop&q=80"
             alt="{{ $ville->nom_ville }}">
        <div class="city-card-overlay">
          <div class="city-card-name">{{ $ville->nom_ville }}</div>
          <div class="city-card-count">{{ $ville->salons_valides_count }} salon{{ $ville->salons_valides_count > 1 ? 's' : '' }}</div>
        </div>
        <div class="city-card-arrow">&#8594;</div>
      </a>
    @endforeach
  </div>

</section>

@push('scripts')
<script>
function filterVilles(q) {
  const terme  = q.toLowerCase().trim();
  const items  = document.querySelectorAll('.ville-item');
  let visible  = 0;

  items.forEach(item => {
    const match = item.dataset.nom.includes(terme);
    item.style.display = match ? '' : 'none';
    if (match) visible++;
  });

  document.getElementById('villesCount').textContent = visible + ' ville' + (visible > 1 ? 's' : '');
}
</script>
@endpush
@endsection
