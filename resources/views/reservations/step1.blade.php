@extends('layouts.app')
@section('title', 'Réservation — Choix des services')

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
    <div class="step current"><div class="step-dot">1</div><div class="step-label">Services</div></div>
    <div class="step"><div class="step-dot">2</div><div class="step-label">Créneaux</div></div>
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
      <div class="form-card-title">Choisissez vos services</div>
      <div class="form-card-subtitle">Cochez une ou plusieurs prestations proposées par {{ $salonModel->nom_salon }}.</div>

      <div class="svc-select-grid" id="svcGrid">
        @foreach($services as $svc)
          <div class="svc-select-card"
               data-cat="{{ Str::slug($svc->categorie) }}"
               data-id="{{ $svc->id }}"
               data-name="{{ $svc->nom_service }}"
               data-duration="{{ $svc->duree_formatee }}"
               data-price-raw="{{ $svc->prix }}"
               data-price="{{ $svc->prix_format }}"
               onclick="toggleSvc(this)">
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
              <div class="svc-select-check" style="margin-top:.6rem;width:22px;height:22px;border:2px solid var(--border2);display:flex;align-items:center;justify-content:center;font-size:.85rem;font-weight:700;color:#fff;background:#fff"></div>
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
          Choisir les créneaux &#8594;
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

      <div id="recapServicesList" style="padding:.4rem 0;border-bottom:1.5px solid var(--p2);margin-bottom:.8rem;min-height:60px">
        <div style="font-size:.64rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--ink-m);margin-bottom:.5rem">Services sélectionnés</div>
        <div id="recapSvcItems" style="color:var(--ink-m);font-size:.82rem">Aucun service sélectionné</div>
      </div>

      <div class="recap-row"><span class="recap-key">Créneaux</span><span class="recap-value" style="color:var(--ink-d)">À choisir</span></div>

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
var selectedServices = []; // [{id, name, duration, price_raw, price}]
var step2Url    = '{{ route('reservations.step2', $salonModel->slug) }}';
var saveStepUrl = '{{ route('reservations.save-step', $salonModel->slug) }}';
var csrfToken   = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function filterCat(btn, cat) {
  document.querySelectorAll('.svc-cat-tab').forEach(b => b.classList.remove('on'));
  btn.classList.add('on');
  document.querySelectorAll('.svc-select-card').forEach(c => {
    c.style.display = (cat === 'all' || c.dataset.cat === cat) ? '' : 'none';
  });
}

function toggleSvc(card) {
  var id   = card.dataset.id;
  var idx  = selectedServices.findIndex(function(s){ return s.id === id; });
  var check = card.querySelector('.svc-select-check');
  if (idx >= 0) {
    selectedServices.splice(idx, 1);
    card.classList.remove('selected');
    if (check) { check.textContent = ''; check.style.background = '#fff'; check.style.borderColor = 'var(--border2)'; }
  } else {
    selectedServices.push({
      id:        id,
      name:      card.dataset.name,
      duration:  card.dataset.duration,
      price_raw: parseFloat(card.dataset.priceRaw) || 0,
      price:     card.dataset.price,
    });
    card.classList.add('selected');
    if (check) { check.textContent = '\u2713'; check.style.background = 'var(--p4)'; check.style.borderColor = 'var(--p4)'; }
  }
  updateRecap();
}

function updateRecap() {
  var list = document.getElementById('recapSvcItems');
  if (selectedServices.length === 0) {
    list.innerHTML = 'Aucun service sélectionné';
    list.style.color = 'var(--ink-m)';
    document.getElementById('recapTotal').textContent = '—';
  } else {
    var html = '';
    var total = 0;
    selectedServices.forEach(function(s){
      total += s.price_raw;
      html += '<div style="display:flex;justify-content:space-between;padding:.35rem 0;border-top:1px dashed var(--p2);font-size:.82rem">'
            + '<span style="color:var(--ink-d);font-weight:600">' + escapeHtml(s.name) + '</span>'
            + '<span style="color:var(--ink-h);font-weight:700">' + escapeHtml(s.price) + '</span>'
            + '</div>';
    });
    list.innerHTML = html;
    list.style.color = '';
    document.getElementById('recapTotal').textContent = total.toLocaleString('fr-FR') + ' MAD';
  }
  var btn = document.getElementById('btnNext');
  if (selectedServices.length > 0) {
    btn.style.opacity = '1';
    btn.style.pointerEvents = 'auto';
  } else {
    btn.style.opacity = '.4';
    btn.style.pointerEvents = 'none';
  }
}

function escapeHtml(s) {
  return String(s).replace(/[&<>"']/g, function(c){
    return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
  });
}

function goStep2() {
  if (selectedServices.length === 0) return;
  var ids = selectedServices.map(function(s){ return s.id; });
  fetch(saveStepUrl, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
    body: JSON.stringify({ step: 'services', service_ids: ids })
  }).then(() => { window.location.href = step2Url; });
}
</script>
@endpush
@endsection
