@extends('layouts.app')
@section('title', $salon->nom_salon)
@section('meta_description', $salon->nom_salon . ' — ' . $salon->quartier . ', ' . $salon->ville->nom_ville . '. Réservez en ligne sur Salonify.')

@section('content')

{{-- ══ HERO PHOTO ═══════════════════════════════════════════════ --}}
<div class="salon-hero">
  <img src="{{ $salon->photo_url }}" alt="{{ $salon->nom_salon }}">
  <div class="salon-hero-overlay">
    <div class="hero-content">
      <span class="hero-badge">
        {{ $salon->estOuvertMaintenant() ? '● Ouvert maintenant' : '○ Fermé actuellement' }}
      </span>
      <h1 class="hero-name">{{ $salon->nom_salon }}</h1>
      <div class="hero-location">&#128205; {{ $salon->adresse }}, {{ $salon->quartier }}, {{ $salon->ville->nom_ville }}</div>
      <div class="hero-rating">
        <span class="hero-stars">{{ str_repeat('★', round($salon->note_moy)) }}{{ str_repeat('☆', 5 - round($salon->note_moy)) }}</span>
        <span class="hero-score">{{ number_format($salon->note_moy, 1) }} <span>({{ $salon->nb_avis }} avis)</span></span>
        <span class="bge-ok" style="margin-left:.5rem">Salon vérifié &#10003;</span>
      </div>
    </div>
  </div>
</div>

{{-- ══ LAYOUT PRINCIPAL ══════════════════════════════════════════ --}}
<div class="salon-layout">

  <div>
    {{-- ── Onglets ────────────────────────────────────────────── --}}
    <div class="salon-tabs">
      <button class="salon-tab active" onclick="showTab('services',this)">Services</button>
      <button class="salon-tab" onclick="showTab('equipe',this)">Équipe</button>
      <button class="salon-tab" onclick="showTab('avis',this)">Avis ({{ $salon->nb_avis }})</button>
      <button class="salon-tab" onclick="showTab('infos',this)">Informations</button>
    </div>

    {{-- ── SERVICES ───────────────────────────────────────────── --}}
    <div id="tab-services">
      @foreach($servicesByCategorie as $categorie => $services)
        <h2 class="section-title">{{ $categorie }}</h2>
        <div class="services-grid" style="margin-bottom:2rem">
          @foreach($services as $svc)
            <div class="service-row" onclick="selectService({{ $svc->id }}, '{{ $svc->nom_service }}', {{ $svc->duree_minu }}, {{ $svc->prix }})">
              <div>
                <div class="service-row-name">{{ $svc->nom_service }}</div>
                <div class="service-row-duration">&#128337; {{ $svc->duree_formatee }}</div>
                @if($svc->description)
                  <div style="font-size:.74rem;color:var(--ink-m);margin-top:.2rem">{{ Str::limit($svc->description, 60) }}</div>
                @endif
              </div>
              <div>
                <div class="service-row-price">{{ $svc->prix_format }}</div>
                <span class="service-row-cta">Réserver &#8594;</span>
              </div>
            </div>
          @endforeach
        </div>
      @endforeach
    </div>

    {{-- ── ÉQUIPE ─────────────────────────────────────────────── --}}
    <div id="tab-equipe" style="display:none">
      <h2 class="section-title">Notre équipe</h2>
      @if($salon->employesActifs->isEmpty())
        <p style="color:var(--ink-m);font-size:.9rem;padding:2rem 0">Informations sur l'équipe à venir.</p>
      @else
        <div class="team-grid">
          @foreach($salon->employesActifs as $emp)
            <div class="team-member">
              <div class="team-member-photo">
                <img src="{{ $emp->photo_url }}" alt="{{ $emp->nomComplet() }}">
              </div>
              <div class="team-member-name">{{ $emp->nomComplet() }}</div>
              <div class="team-member-role">
                @php $specs = $emp->specialites; if (is_string($specs)) $specs = json_decode($specs, true) ?? []; @endphp
                {{ is_array($specs) ? implode(', ', $specs) : $specs }}
              </div>
            </div>
          @endforeach
        </div>
      @endif
    </div>

    {{-- ── AVIS ───────────────────────────────────────────────── --}}
    <div id="tab-avis" style="display:none">
      <h2 class="section-title">Avis clients</h2>

      {{-- Résumé --}}
      @if($salon->nb_avis > 0)
        <div class="reviews-summary">
          <div>
            <div class="reviews-big-score">{{ number_format($salon->note_moy, 1) }}</div>
            <div class="reviews-stars-large">{{ str_repeat('★', round($salon->note_moy)) }}{{ str_repeat('☆', 5 - round($salon->note_moy)) }}</div>
            <div class="reviews-total">{{ $salon->nb_avis }} avis vérifiés</div>
          </div>
          <div style="flex:1">
            @for($i = 5; $i >= 1; $i--)
              <div class="rating-bar-row">
                <span class="rating-bar-label">{{ $i }}&#9733;</span>
                <div class="rating-bar-track">
                  <div class="rating-bar-fill {{ $i <= 2 ? 'low' : '' }}"
                       style="width:{{ $distribution[$i]['pct'] }}%"></div>
                </div>
                <span class="rating-bar-pct">{{ $distribution[$i]['pct'] }}%</span>
              </div>
            @endfor
          </div>
        </div>
      @endif

      {{-- Liste avis --}}
      <div class="reviews-list">
        @forelse($avis as $a)
          <div class="review-card">
            <div class="review-top">
              <div class="review-avatar">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($a->reservation->client?->nomComplet() ?? 'Client') }}&background=9CAB84&color=fff&size=42"
                     alt="Client">
              </div>
              <div>
                <div class="review-author-name">{{ $a->reservation->client?->prenom ?? 'Client' }} {{ substr($a->reservation->client?->nom ?? '', 0, 1) }}.</div>
                <div class="review-date">{{ $a->created_at->translatedFormat('d F Y') }}</div>
              </div>
              <div class="review-stars">{{ str_repeat('★', $a->note) }}{{ str_repeat('☆', 5 - $a->note) }}</div>
            </div>
            @if($a->commentaire)
              <p class="review-text">"{{ $a->commentaire }}"</p>
            @endif
            @if($a->reponse_salon)
              <div style="background:var(--p1);border-left:3px solid var(--p4);padding:.8rem 1rem;margin-top:.8rem;font-size:.82rem;color:var(--ink-s)">
                <strong style="font-size:.7rem;text-transform:uppercase;letter-spacing:.8px;color:var(--p4d)">Réponse du salon ·</strong>
                {{ $a->reponse_salon }}
              </div>
            @endif
          </div>
        @empty
          <p style="color:var(--ink-m);font-size:.9rem;padding:2rem 0">Aucun avis pour ce salon. Soyez le premier !</p>
        @endforelse
      </div>
    </div>

    {{-- ── INFOS PRATIQUES ────────────────────────────────────── --}}
    <div id="tab-infos" style="display:none">
      <h2 class="section-title">Informations pratiques</h2>
      <div class="practical-info">
        <div class="practical-info-title">Coordonnées &amp; Horaires</div>
        <div class="info-row">
          <div class="info-icon">&#128205;</div>
          <div><div class="info-value">{{ $salon->adresse }}</div><div class="info-label">{{ $salon->quartier }}, {{ $salon->ville->nom_ville }}</div></div>
        </div>
        @if($salon->telephone)
          <div class="info-row">
            <div class="info-icon">&#128222;</div>
            <div><div class="info-value">{{ $salon->telephone }}</div><div class="info-label">Téléphone</div></div>
          </div>
        @endif
        @if($salon->email)
          <div class="info-row">
            <div class="info-icon">&#9993;</div>
            <div><div class="info-value">{{ $salon->email }}</div><div class="info-label">Email</div></div>
          </div>
        @endif

        @if($salon->horaires)
          <div style="margin-top:1.4rem">
            <div class="practical-info-title">Horaires d'ouverture</div>
            @foreach(['lundi','mardi','mercredi','jeudi','vendredi','samedi','dimanche'] as $j)
              @if(isset($salon->horaires[$j]))
                @php $h = $salon->horaires[$j]; $today = strtolower(now()->translatedFormat('l')); @endphp
                <div class="info-row" style="border-bottom:1px solid var(--border2);padding:.4rem 0;{{ $j === $today ? 'background:rgba(197,216,157,.12);' : '' }}">
                  <span style="font-size:.82rem;font-weight:{{ $j === $today ? '700' : '500' }};color:{{ $j === $today ? 'var(--p4d)' : 'var(--ink-b)' }};width:90px;display:inline-block;text-transform:capitalize">{{ $j }}</span>
                  <span style="font-size:.8rem;font-weight:700;color:{{ ($h['ferme'] ?? true) ? '#C04A3D' : 'var(--ink-h)' }}">
                    {{ ($h['ferme'] ?? true) ? 'Fermé' : ($h['debut'] . ' – ' . $h['fin']) }}
                  </span>
                </div>
              @endif
            @endforeach
          </div>
        @endif
      </div>
    </div>

  </div>

  {{-- ── SIDEBAR RÉSERVATION ──────────────────────────────────── --}}
  <aside>
    <div class="booking-card">
      <div class="booking-card-title">Réserver</div>
      <div class="booking-card-subtitle" id="selectedServiceName">Choisissez un service ci-dessous</div>

      <div id="bookingServiceInfo" style="display:none;background:var(--p1);border:1.5px solid var(--p2);padding:.9rem 1rem;margin-bottom:1rem">
        <div style="font-size:.84rem;font-weight:700;color:var(--ink-h)" id="bkSvcName"></div>
        <div style="display:flex;justify-content:space-between;margin-top:.35rem">
          <span style="font-size:.76rem;color:var(--ink-m)" id="bkSvcDuration"></span>
          <span style="font-family:var(--fh);font-size:1.2rem;font-weight:700;color:var(--ink-h)" id="bkSvcPrice"></span>
        </div>
      </div>

      @auth
        @if(auth()->user()->isClient())
          <a id="btnReserver" href="{{ route('reservations.step1', $salon->slug) }}"
             class="btn-book-full" style="display:block;text-align:center;text-decoration:none">
            Choisir un créneau
          </a>
        @else
          <p style="font-size:.8rem;color:var(--ink-m);text-align:center">Connectez-vous en tant que client pour réserver.</p>
        @endif
      @else
        <a href="{{ route('login') }}?redirect={{ url()->current() }}" class="btn-book-full" style="display:block;text-align:center;text-decoration:none">
          Se connecter pour réserver
        </a>
        <div style="text-align:center;margin-top:.7rem;font-size:.76rem;color:var(--ink-m)">
          Pas de compte ? <a href="{{ route('register') }}" style="color:var(--p4d);font-weight:700">Inscription gratuite</a>
        </div>
      @endauth

      <div class="booking-total" style="margin-top:1.2rem">
        <span class="booking-total-label">Paiement</span>
        <span style="font-size:.82rem;color:var(--ink-s);font-weight:600">Au salon &#10003;</span>
      </div>
      <div style="font-size:.74rem;color:var(--ink-m);text-align:center">
        Réservation gratuite — Annulation libre 24h avant
      </div>
    </div>

    {{-- Infos rapides --}}
    <div class="practical-info" style="margin-top:1.2rem">
      <div class="practical-info-title">Informations</div>
      <div class="info-row">
        <div class="info-icon">&#128197;</div>
        <div><div class="info-value">{{ $salon->nb_employes }} coiffeur{{ $salon->nb_employes > 1 ? 's' : '' }}</div></div>
      </div>
      <div class="info-row">
        <div class="info-icon">&#9733;</div>
        <div><div class="info-value">{{ number_format($salon->note_moy, 1) }} / 5</div><div class="info-label">{{ $salon->nb_avis }} avis vérifiés</div></div>
      </div>
      <div class="info-row">
        <div class="info-icon">&#128222;</div>
        <div><div class="info-value">{{ $salon->telephone ?? 'N/A' }}</div></div>
      </div>
    </div>
  </aside>

</div>

@push('scripts')
<script>
function showTab(id, btn) {
  ['services','equipe','avis','infos'].forEach(t => {
    document.getElementById('tab-' + t).style.display = 'none';
  });
  document.querySelectorAll('.salon-tab').forEach(b => b.classList.remove('active'));
  document.getElementById('tab-' + id).style.display = 'block';
  btn.classList.add('active');
}

function selectService(id, nom, duree, prix) {
  document.getElementById('bkSvcName').textContent     = nom;
  document.getElementById('bkSvcDuration').textContent = Math.floor(duree / 60) > 0
    ? Math.floor(duree/60) + 'h' + (duree%60 > 0 ? (duree%60) : '') : duree + ' min';
  document.getElementById('bkSvcPrice').textContent    = prix.toLocaleString('fr-FR') + ' MAD';
  document.getElementById('bookingServiceInfo').style.display = 'block';
  document.getElementById('selectedServiceName').textContent  = 'Service sélectionné';

  const btn = document.getElementById('btnReserver');
  if (btn) btn.href = btn.href.split('?')[0] + '?service_id=' + id;

  window.scrollTo({ top: document.querySelector('.booking-card').getBoundingClientRect().top + window.scrollY - 120, behavior: 'smooth' });
}
</script>
@endpush
@endsection
