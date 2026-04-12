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
      <button class="cities-search-btn" onclick="filterVilles(document.getElementById('villeSearch').value)">Rechercher</button>
    </div>

    {{-- Bouton géolocalisation --}}
    <div style="margin-top:1.2rem">
      <button id="btnGeo" onclick="detecterPosition()"
              style="display:inline-flex;align-items:center;gap:.6rem;background:rgba(255,255,255,.15);border:1.5px solid rgba(255,255,255,.4);color:#fff;padding:.65rem 1.4rem;border-radius:2px;font-size:.85rem;font-weight:600;cursor:pointer;transition:.2s;backdrop-filter:blur(4px)"
              onmouseover="this.style.background='rgba(255,255,255,.25)'"
              onmouseout="this.style.background='rgba(255,255,255,.15)'">
        <span id="geoIcon">&#128205;</span>
        <span id="geoText">Détecter ma position</span>
      </button>
      <div id="geoMsg" style="margin-top:.6rem;font-size:.78rem;color:rgba(255,255,255,.75);min-height:1.2em"></div>
    </div>

    {{-- Ville de l'utilisateur connecté --}}
    @auth
      @if($villeUtilisateur)
        <div style="margin-top:1rem;padding:.6rem 1.2rem;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.25);border-radius:2px;display:inline-block;font-size:.82rem;color:#fff">
          &#9733; Votre ville enregistrée :
          <a href="{{ route('salons.index', $villeUtilisateur->nom_ville) }}"
             style="color:#C5A96D;font-weight:700;text-decoration:none">
            {{ $villeUtilisateur->nom_ville }} &#8594;
          </a>
          @if(Auth::user()->quartier)
            <span style="color:rgba(255,255,255,.65)"> · {{ Auth::user()->quartier }}</span>
          @endif
        </div>
      @endif
    @endauth

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

  <div class="cities-section-header">
    <h2>Villes <em style="font-style:italic;color:var(--p4d)">populaires</em></h2>
    <span class="cities-section-sub">Les plus demandées sur Salonify</span>
  </div>

  @php $villesPrincipales = $villes->take(2); $autresVilles = $villes->skip(2); @endphp

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
// ── Filtrage texte ─────────────────────────────────────────────
function filterVilles(q) {
  const terme = q.toLowerCase().trim();
  const items = document.querySelectorAll('.ville-item');
  let visible = 0;
  items.forEach(item => {
    const match = item.dataset.nom.includes(terme);
    item.style.display = match ? '' : 'none';
    if (match) visible++;
  });
  document.getElementById('villesCount').textContent = visible + ' ville' + (visible !== 1 ? 's' : '');
}

// ── Géolocalisation ────────────────────────────────────────────
function detecterPosition() {
  const btn     = document.getElementById('btnGeo');
  const icon    = document.getElementById('geoIcon');
  const text    = document.getElementById('geoText');
  const msg     = document.getElementById('geoMsg');

  if (!navigator.geolocation) {
    msg.textContent = 'La géolocalisation n\'est pas supportée par votre navigateur.';
    return;
  }

  // État chargement
  btn.disabled = true;
  icon.textContent = '⏳';
  text.textContent = 'Localisation en cours…';
  msg.textContent  = '';

  navigator.geolocation.getCurrentPosition(
    async (position) => {
      const lat = position.coords.latitude;
      const lng = position.coords.longitude;

      msg.textContent = `Position détectée (${lat.toFixed(4)}, ${lng.toFixed(4)}). Identification de la ville…`;

      try {
        // Nominatim OpenStreetMap — reverse geocoding gratuit
        const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&accept-language=fr&zoom=12`;
        const res  = await fetch(url, {
          headers: { 'Accept-Language': 'fr' }
        });
        const data = await res.json();

        const adresse  = data.address || {};
        const cityNom  = adresse.city || adresse.town || adresse.municipality || adresse.village || '';
        const quartier = adresse.suburb || adresse.neighbourhood || adresse.quarter || '';

        if (!cityNom) {
          afficherErreurGeo('Impossible de déterminer votre ville. Veuillez la sélectionner manuellement.', btn, icon, text);
          return;
        }

        // Chercher la correspondance dans nos villes
        const villeItems = document.querySelectorAll('.ville-item');
        let redirect     = null;

        const normalise = s => s.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        const cityNomN = normalise(cityNom);

        villeItems.forEach(item => {
          const nomDbN = normalise(item.dataset.nom);

          if (nomDbN.includes(cityNomN) || cityNomN.includes(nomDbN)) {
            let href = item.getAttribute('href');
            if (quartier) href += '?quartier=' + encodeURIComponent(quartier) + '&auto=1';
            redirect = href;
          }
        });

        if (redirect) {
          msg.innerHTML = `<span style="color:#9CAB84">✔ Ville détectée : <strong>${cityNom}</strong>${quartier ? ' · ' + quartier : ''}. Redirection…</span>`;
          setTimeout(() => { window.location.href = redirect; }, 900);
        } else {
          // Aucune correspondance exacte — filtrer la liste
          msg.innerHTML = `<span style="color:#D4A844">Ville détectée : <strong>${cityNom}</strong>. Sélectionnez ci-dessous.</span>`;
          filterVilles(cityNom.split(' ')[0]);
          document.getElementById('villeSearch').value = cityNom;
          document.getElementById('villesList').scrollIntoView({ behavior: 'smooth' });
          resetBtn(btn, icon, text);
        }

      } catch (e) {
        afficherErreurGeo('Erreur réseau. Vérifiez votre connexion internet.', btn, icon, text);
      }
    },
    (error) => {
      const messages = {
        1: 'Accès à la localisation refusé. Autorisez-le dans les paramètres de votre navigateur.',
        2: 'Position introuvable. Vérifiez votre connexion GPS.',
        3: 'Délai dépassé. Réessayez.',
      };
      afficherErreurGeo(messages[error.code] || 'Erreur de géolocalisation.', btn, icon, text);
    },
    { timeout: 10000, maximumAge: 60000, enableHighAccuracy: false }
  );
}

function nomGeoN(s) {
  return s.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
}

function afficherErreurGeo(msg, btn, icon, text) {
  document.getElementById('geoMsg').innerHTML = `<span style="color:#E8562A">${msg}</span>`;
  resetBtn(btn, icon, text);
}

function resetBtn(btn, icon, text) {
  btn.disabled    = false;
  icon.textContent = '📍';
  text.textContent = 'Détecter ma position';
}
</script>
@endpush
@endsection
