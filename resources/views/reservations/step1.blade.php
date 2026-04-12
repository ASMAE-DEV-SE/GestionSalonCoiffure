@extends('layouts.app')
@section('title', 'Réservation — Choix du service')

@section('content')

<div class="booking-page-header">
  <div class="wrap">
    <h1>Nouvelle réservation</h1>
    <div class="booking-page-subtitle">
      {{ $salonModel->nom_salon }} &nbsp;·&nbsp; {{ $salonModel->quartier }}, {{ $salonModel->ville->nom_ville }}
      &nbsp;·&nbsp; &#9733; {{ number_format($salonModel->note_moy, 1) }}
    </div>
  </div>
</div>

{{-- ── Stepper ─────────────────────────────────────────────── --}}
<div class="stepper-bar-wrap">
  <div class="stepper-bar">
    <div class="step current"><div class="step-dot">1</div><div class="step-label">Service</div></div>
    <div class="step"><div class="step-dot">2</div><div class="step-label">Créneau</div></div>
    <div class="step"><div class="step-dot">3</div><div class="step-label">Vos infos</div></div>
    <div class="step"><div class="step-dot">4</div><div class="step-label">Confirmation</div></div>
  </div>
</div>

{{-- ── Filtre catégorie ─────────────────────────────────────── --}}
<div class="svc-cat-bar">
  <div class="svc-cat-bar-inner">
    <button class="svc-cat-tab on" onclick="filterCat(this,'all')">
      Tous<span class="svc-cat-count">{{ $services->count() }}</span>
    </button>
    @foreach($categories as $cat)
      <button class="svc-cat-tab" onclick="filterCat(this,'{{ Str::slug($cat) }}')">
        {{ $cat }}<span class="svc-cat-count">{{ $services->where('categorie', $cat)->count() }}</span>
      </button>
    @endforeach
  </div>
</div>

<div class="wizard-layout">
  <div>
    <div class="form-card" style="padding:1.8rem">
      <div class="form-card-title">Choisissez votre service</div>
      <div class="form-card-subtitle">Sélectionnez une prestation proposée par {{ $salonModel->nom_salon }}.</div>

      <div class="svc-select-grid" id="svcGrid">
        @foreach($services as $svc)
          <div class="svc-select-card"
               data-cat="{{ Str::slug($svc->categorie) }}"
               data-id="{{ $svc->id }}"
               data-name="{{ $svc->nom_service }}"
               data-duration="{{ $svc->duree_formatee }}"
               data-price="{{ $svc->prix_format }}"
               onclick="selectSvc(this)">
            <div class="svc-select-left">
              <div class="svc-select-cat">{{ $svc->categorie }}</div>
              <div class="svc-select-name">{{ $svc->nom_service }}</div>
              @if($svc->description)
                <div class="svc-select-desc">{{ Str::limit($svc->description, 80) }}</div>
              @endif
              <div class="svc-select-meta">
                <span class="svc-select-duration">&#128337; {{ $svc->duree_formatee }}</span>
              </div>
            </div>
            <div class="svc-select-right">
              <div class="svc-select-price">{{ $svc->prix }}</div>
              <div class="svc-select-price-lbl">MAD</div>
            </div>
          </div>
        @endforeach
      </div>

      <div class="wizard-navigation">
        <a href="{{ route('salons.show', [$salonModel->ville->nom_ville, $salonModel->slug]) }}"
           class="btn-wizard-back">&#8592; Retour au salon</a>
        <button class="btn-wizard-confirm" id="btnNext"
                style="opacity:.4;pointer-events:none;border:none;cursor:pointer"
                onclick="goStep2()">
          Choisir un créneau &#8594;
        </button>
      </div>
    </div>
  </div>

  {{-- Récap sidebar ──────────────────────────────────────────── --}}
  <div class="recap-sidebar">
    <div class="recap-header">
      <div class="recap-header-title">Votre réservation</div>
    </div>
    <div class="recap-body">
      <div class="recap-salon" style="margin-bottom:1.2rem;padding-bottom:1.2rem;border-bottom:1.5px solid var(--p2)">
        <div class="recap-salon-photo">
          <img src="{{ $salonModel->photo_url }}" alt="{{ $salonModel->nom_salon }}">
        </div>
        <div>
          <div class="recap-salon-name">{{ $salonModel->nom_salon }}</div>
          <div class="recap-salon-location">{{ $salonModel->quartier }}, {{ $salonModel->ville->nom_ville }} &nbsp;·&nbsp; &#9733; {{ number_format($salonModel->note_moy, 1) }}</div>
        </div>
      </div>

      <div id="recapService" style="padding:.8rem 0;border-bottom:1.5px solid var(--p2);margin-bottom:.8rem">
        <div style="font-size:.64rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--ink-m);margin-bottom:.3rem">Service sélectionné</div>
        <div id="recapSvcName" style="font-size:.96rem;font-weight:700;color:var(--p4d)">—</div>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-top:.3rem">
          <span id="recapSvcDuration" style="font-size:.76rem;color:var(--ink-m)">—</span>
          <span id="recapSvcPrice" style="font-family:var(--fh);font-size:1.3rem;font-weight:700;color:var(--ink-h)">—</span>
        </div>
      </div>

      <div class="recap-row"><span class="recap-key">Créneau</span><span class="recap-value" style="color:var(--ink-d)">À choisir</span></div>

      <div class="recap-total-row">
        <span class="recap-total-label">Total</span>
        <span id="recapTotal" class="recap-total-amount">—</span>
      </div>

      <div style="margin-top:1.2rem;padding:1rem;background:rgba(197,216,157,.15);border:1px solid var(--p2)">
        <div style="font-size:.72rem;color:var(--ink-s);line-height:1.7">
          &#128197; Réservation gratuite — paiement au salon<br>
          &#10003; Annulation libre jusqu'à 24h avant
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
var selectedServiceId = null;
var step2Url = '{{ route('reservations.step2', $salonModel->slug) }}';
var saveStepUrl = '{{ route('reservations.save-step', $salonModel->slug) }}';
var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function filterCat(btn, cat) {
  document.querySelectorAll('.svc-cat-tab').forEach(b => b.classList.remove('on'));
  btn.classList.add('on');
  document.querySelectorAll('.svc-select-card').forEach(c => {
    c.style.display = (cat === 'all' || c.dataset.cat === cat) ? '' : 'none';
  });
}

function selectSvc(card) {
  document.querySelectorAll('.svc-select-card').forEach(c => c.classList.remove('selected'));
  card.classList.add('selected');
  selectedServiceId = card.dataset.id;

  document.getElementById('recapSvcName').textContent    = card.dataset.name;
  document.getElementById('recapSvcDuration').textContent = card.dataset.duration;
  document.getElementById('recapSvcPrice').textContent   = card.dataset.price;
  document.getElementById('recapTotal').textContent      = card.dataset.price;

  const btn = document.getElementById('btnNext');
  btn.style.opacity = '1';
  btn.style.pointerEvents = 'auto';
}

function goStep2() {
  if (!selectedServiceId) return;
  fetch(saveStepUrl, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
    body: JSON.stringify({ step: 'service_id', service_id: selectedServiceId })
  }).then(() => { window.location.href = step2Url; });
}
</script>
@endpush
@endsection
