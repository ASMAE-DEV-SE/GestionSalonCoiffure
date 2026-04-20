@extends('layouts.app')
@section('title', 'Réservation — Choix des créneaux')

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
    <div class="step done"><div class="step-dot">&#10003;</div><div class="step-label">Services</div></div>
    <div class="step current"><div class="step-dot">2</div><div class="step-label">Créneaux</div></div>
    <div class="step"><div class="step-dot">3</div><div class="step-label">Vos infos</div></div>
    <div class="step"><div class="step-dot">4</div><div class="step-label">Confirmation</div></div>
  </div>
</div>

<div class="wizard-two-col">
  <div>

    {{-- Onglets services ────────────────────────────────────── --}}
    <div class="form-card" style="padding:1rem 1.2rem;margin-bottom:1rem">
      <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--ink-m);margin-bottom:.7rem">
        Prestations à planifier ({{ $services->count() }})
      </div>
      <div id="svcTabs" style="display:flex;flex-wrap:wrap;gap:.5rem">
        @foreach($services as $idx => $svc)
          <button type="button"
                  class="svc-tab-btn {{ $idx === 0 ? 'active' : '' }}"
                  data-svc-id="{{ $svc->id }}"
                  onclick="switchService('{{ $svc->id }}')"
                  style="padding:.6rem .9rem;border:2px solid var(--border2);background:{{ $idx === 0 ? 'var(--p4)' : '#fff' }};color:{{ $idx === 0 ? '#fff' : 'var(--ink-d)' }};font-size:.78rem;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:.5rem">
            <span class="svc-tab-check" style="display:none;color:#fff;font-weight:700">&#10003;</span>
            <span>{{ $svc->nom_service }}</span>
            <span style="opacity:.8;font-size:.7rem">({{ $svc->duree_formatee }})</span>
          </button>
        @endforeach
      </div>
    </div>

    {{-- Choix styliste ──────────────────────────────────────── --}}
    <div class="emp-select-section">
      <div class="emp-select-head">
        <div>
          <div class="emp-select-title">Choisissez votre styliste</div>
          <div class="emp-select-sub">Pour la prestation en cours — optionnel</div>
        </div>
      </div>

      <div class="emp-select-row selected" id="empAny" onclick="selectEmp(this,'','Pas de préférence')">
        <div style="width:46px;height:46px;border-radius:50%;background:var(--border2);display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0;border:2.5px solid var(--border)">&#9786;</div>
        <div>
          <div class="emp-select-name">Pas de préférence</div>
          <div class="emp-select-role">Premier disponible selon votre créneau</div>
        </div>
        <div class="emp-select-check">&#10003;</div>
      </div>

      @foreach($employes as $emp)
        <div class="emp-select-row" id="emp-{{ $emp->id }}" onclick="selectEmp(this,'{{ $emp->id }}','{{ $emp->nomComplet() }}')">
          <div class="emp-select-av">
            <img src="{{ $emp->photo_url }}" alt="{{ $emp->nomComplet() }}">
          </div>
          <div>
            <div class="emp-select-name">{{ $emp->nomComplet() }}</div>
            <div class="emp-select-role">{{ implode(', ', $emp->specialites ?? []) }}</div>
          </div>
          <div class="emp-select-check">&#10003;</div>
        </div>
      @endforeach
    </div>

    {{-- Calendrier ─────────────────────────────────────────── --}}
    <div class="cal-wrap">
      <div class="cal-nav">
        <button class="cal-nav-btn" onclick="prevMonth()">&#8592;</button>
        <div class="cal-nav-month" id="calTitle"></div>
        <button class="cal-nav-btn" onclick="nextMonth()">&#8594;</button>
      </div>
      <div class="cal-weekdays">
        <div class="cal-wd">Lun</div>
        <div class="cal-wd">Mar</div>
        <div class="cal-wd">Mer</div>
        <div class="cal-wd">Jeu</div>
        <div class="cal-wd">Ven</div>
        <div class="cal-wd we">Sam</div>
        <div class="cal-wd we">Dim</div>
      </div>
      <div class="cal-days" id="calDays"></div>
      <div class="cal-legend">
        <div class="cal-legend-item"><div class="cal-legend-dot" style="background:var(--p4)"></div>Créneaux disponibles</div>
        <div class="cal-legend-item"><div class="cal-legend-dot" style="background:var(--border2)"></div>Complet / Fermé</div>
        <div class="cal-legend-item"><div class="cal-legend-dot" style="background:var(--ink-h)"></div>Aujourd'hui</div>
      </div>
    </div>

    {{-- Créneaux du jour ────────────────────────────────────── --}}
    <div class="slots-section" id="slotsSection" style="display:none">
      <div class="slots-head">
        <div>
          <div class="slots-title">Créneaux disponibles</div>
          <div class="slots-date-lbl" id="slotsDate"></div>
        </div>
      </div>
      <div class="slots-body" id="slotsBody"></div>
    </div>

    <div class="form-card" style="padding:1.6rem;margin-top:1.5rem">
      <div class="wizard-navigation">
        <a href="{{ route('reservations.step1', $salonModel->slug) }}" class="btn-wizard-back">&#8592; Services</a>
        <button class="btn-wizard-confirm" id="btnNext3"
                style="opacity:.4;pointer-events:none;border:none;cursor:pointer"
                onclick="goStep3()">
          Mes informations &#8594;
        </button>
      </div>
    </div>

  </div>

  {{-- Recap sidebar ──────────────────────────────────────────── --}}
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
          <div class="recap-salon-location">{{ $salonModel->quartier }}, {{ $salonModel->ville->nom_ville }}</div>
        </div>
      </div>

      <div id="recapSelections" style="padding:.3rem 0"></div>

      <div class="recap-total-row">
        <span class="recap-total-label">Total</span>
        <span id="recapTotal" class="recap-total-amount">
          {{ number_format($services->sum('prix'), 0, ',', ' ') }} MAD
        </span>
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
// Données depuis le serveur
var creneauxByService = @json($creneauxParService);
var servicesMeta = @json($servicesMeta);

// selections: { [service_id]: { date_heure, employe_id, employe_name } }
var selections = {};
var currentServiceId = servicesMeta[0].id;
var currentMonth, currentYear;
var months   = ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
var monthsFr = ['janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
var today    = new Date();
currentMonth = today.getMonth();
currentYear  = today.getFullYear();

var saveStepUrl = '{{ route('reservations.save-step', $salonModel->slug) }}';
var step3Url    = '{{ route('reservations.step3', $salonModel->slug) }}';
var creneauxUrlBase = @json(url('/reservations/' . $salonModel->slug . '/creneaux'));
var csrfToken   = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
var loadedMonths = {};

function pad(n) { return n < 10 ? '0' + n : String(n); }
function dateKey(y, m, d) { return y + '-' + pad(m + 1) + '-' + pad(d); }

function currentCreneaux() {
  return creneauxByService[currentServiceId] || {};
}

function loadCreneauxForCurrentMonth() {
  var monthKey = currentYear + '-' + pad(currentMonth + 1);
  if (!loadedMonths[currentServiceId]) loadedMonths[currentServiceId] = {};
  if (loadedMonths[currentServiceId][monthKey]) return;

  loadedMonths[currentServiceId][monthKey] = true;
  var url = creneauxUrlBase + '/' + encodeURIComponent(currentServiceId)
    + '?annee=' + encodeURIComponent(currentYear)
    + '&mois=' + encodeURIComponent(currentMonth + 1);

  fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(function (r) { return r.ok ? r.json() : {}; })
    .then(function (data) {
      creneauxByService[currentServiceId] = Object.assign(
        {},
        creneauxByService[currentServiceId] || {},
        data || {}
      );
      buildCal();
    })
    .catch(function () {
      // Ne pas casser la page si une requête échoue : l'utilisateur peut changer de mois/service.
    });
}

function switchService(id) {
  currentServiceId = String(id);
  document.querySelectorAll('.svc-tab-btn').forEach(function(b){
    var active = b.dataset.svcId === currentServiceId;
    var done   = !!selections[b.dataset.svcId];
    b.style.background = active ? 'var(--p4)' : (done ? 'var(--p2)' : '#fff');
    b.style.color      = active ? '#fff' : 'var(--ink-d)';
    b.classList.toggle('active', active);
    var check = b.querySelector('.svc-tab-check');
    if (check) check.style.display = done ? 'inline' : 'none';
  });

  // Restaurer la sélection employé de ce service
  var sel = selections[currentServiceId] || {};
  highlightEmp(sel.employe_id || '');

  buildCal();
  document.getElementById('slotsSection').style.display = 'none';
}

function buildCal() {
  loadCreneauxForCurrentMonth();
  var data = currentCreneaux();
  document.getElementById('calTitle').textContent = months[currentMonth] + ' ' + currentYear;
  var grid = document.getElementById('calDays');
  grid.innerHTML = '';
  var first = new Date(currentYear, currentMonth, 1).getDay();
  first = (first === 0) ? 6 : first - 1;
  var days = new Date(currentYear, currentMonth + 1, 0).getDate();
  var prevDays = new Date(currentYear, currentMonth, 0).getDate();

  for (var i = 0; i < first; i++) {
    var d = document.createElement('div');
    d.className = 'cal-day other-month past';
    d.innerHTML = '<span class="cal-day-num">' + (prevDays - first + 1 + i) + '</span>';
    grid.appendChild(d);
  }

  var selDate = (selections[currentServiceId] && selections[currentServiceId].date_heure)
              ? selections[currentServiceId].date_heure.substring(0, 10) : null;

  for (var day = 1; day <= days; day++) {
    var d = document.createElement('div');
    var key = dateKey(currentYear, currentMonth, day);
    var isPast = (new Date(currentYear, currentMonth, day) < new Date(today.getFullYear(), today.getMonth(), today.getDate()));
    var isToday = (day === today.getDate() && currentMonth === today.getMonth() && currentYear === today.getFullYear());
    var hasSlots = data[key] && data[key].some(function(s){return s.disponible;}) && !isPast;
    var classes = ['cal-day'];
    if (isPast) classes.push('past');
    else if (!hasSlots) classes.push('disabled');
    if (isToday) classes.push('today');
    if (hasSlots) classes.push('has-slots');
    if (key === selDate) classes.push('selected');
    d.className = classes.join(' ');
    d.innerHTML = '<span class="cal-day-num">' + day + '</span>' + (hasSlots ? '<span class="cal-day-dot"></span>' : '');
    if (hasSlots) {
      (function(k, dn, m, y) {
        d.addEventListener('click', function() { selectDate(k, dn, m, y); });
      })(key, day, currentMonth, currentYear);
    }
    grid.appendChild(d);
  }

  var total = first + days;
  var remaining = total % 7 === 0 ? 0 : 7 - (total % 7);
  for (var i = 1; i <= remaining; i++) {
    var d = document.createElement('div');
    d.className = 'cal-day other-month past';
    d.innerHTML = '<span class="cal-day-num">' + i + '</span>';
    grid.appendChild(d);
  }
}

function selectDate(key, dayNum, month, year) {
  buildCalHighlight(key);

  var jsFull = ['Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche'];
  var jsDate = new Date(year, month, dayNum);
  var wd = jsDate.getDay();
  var wdIdx = (wd === 0) ? 6 : wd - 1;
  var label = jsFull[wdIdx] + ' ' + dayNum + ' ' + monthsFr[month] + ' ' + year;
  document.getElementById('slotsDate').textContent = label;
  document.getElementById('slotsSection').style.display = 'block';

  var data = currentCreneaux();
  var slots = data[key] || [];
  var body = document.getElementById('slotsBody');
  body.innerHTML = '';
  if (slots.length === 0) {
    body.innerHTML = '<p style="color:var(--ink-m);padding:1rem">Aucun créneau disponible ce jour.</p>';
    return;
  }

  var matin = slots.filter(function(s){ return parseInt(s.heure.split(':')[0]) < 13; });
  var apmidi = slots.filter(function(s){ return parseInt(s.heure.split(':')[0]) >= 13; });

  var currentDt = selections[currentServiceId] ? selections[currentServiceId].date_heure : null;

  function makeSlots(list, label) {
    if (!list.length) return;
    var lbl = document.createElement('div');
    lbl.className = 'slots-period-label';
    lbl.textContent = label;
    body.appendChild(lbl);
    var grid = document.createElement('div');
    grid.className = 'slots-grid';
    list.forEach(function(slot) {
      var el = document.createElement('div');
      var isSel = currentDt === slot.datetime;
      el.className = 'time-slot ' + (slot.disponible ? 'available' : 'unavailable') + (isSel ? ' selected' : '');
      el.textContent = slot.heure;
      if (slot.disponible) {
        el.onclick = function() { selectSlot(el, slot.heure, slot.datetime); };
      }
      grid.appendChild(el);
    });
    body.appendChild(grid);
  }

  makeSlots(matin, 'Matin');
  makeSlots(apmidi, 'Après-midi');
}

function buildCalHighlight(key) {
  document.querySelectorAll('.cal-day').forEach(function(d){ d.classList.remove('selected'); });
  // sélection sera visuellement mise à jour au prochain buildCal via selections[currentServiceId]
  if (!selections[currentServiceId]) selections[currentServiceId] = {};
  // mémoriser la date provisoire (recalée à l'heure)
}

function selectSlot(el, time, datetime) {
  document.querySelectorAll('.time-slot').forEach(s => s.classList.remove('selected'));
  el.classList.add('selected');

  var prevEmp = selections[currentServiceId] ? selections[currentServiceId].employe_id : '';
  var prevEmpName = selections[currentServiceId] ? selections[currentServiceId].employe_name : '';
  selections[currentServiceId] = {
    date_heure:    datetime,
    employe_id:    prevEmp || '',
    employe_name:  prevEmpName || '',
  };
  refreshTabs();
  refreshRecap();
  checkComplete();
  buildCal(); // pour refléter la sélection du jour
}

function selectEmp(row, id, name) {
  highlightEmp(id);
  if (!selections[currentServiceId]) selections[currentServiceId] = { date_heure: '', employe_id: '', employe_name: '' };
  selections[currentServiceId].employe_id   = id;
  selections[currentServiceId].employe_name = id ? name : '';
  refreshRecap();
}

function highlightEmp(id) {
  document.querySelectorAll('.emp-select-row').forEach(r => r.classList.remove('selected'));
  if (!id) {
    document.getElementById('empAny').classList.add('selected');
  } else {
    var row = document.getElementById('emp-' + id);
    if (row) row.classList.add('selected');
  }
}

function refreshTabs() {
  document.querySelectorAll('.svc-tab-btn').forEach(function(b){
    var sid  = b.dataset.svcId;
    var done = !!(selections[sid] && selections[sid].date_heure);
    var active = sid === currentServiceId;
    b.style.background = active ? 'var(--p4)' : (done ? 'var(--p2)' : '#fff');
    var check = b.querySelector('.svc-tab-check');
    if (check) check.style.display = done ? 'inline' : 'none';
  });
}

function refreshRecap() {
  var box = document.getElementById('recapSelections');
  var html = '';
  var total = 0;
  servicesMeta.forEach(function(m){
    var s = selections[m.id];
    var when = (s && s.date_heure)
      ? formatDt(s.date_heure)
      : '<span style="color:var(--ink-m)">À choisir</span>';
    var emp  = (s && s.employe_name) ? s.employe_name : 'Au choix';
    total += m.prix;
    html += '<div style="padding:.6rem 0;border-bottom:1px dashed var(--p2)">'
          + '<div style="display:flex;justify-content:space-between;font-size:.82rem">'
          + '<span style="font-weight:700;color:var(--ink-d)">' + escapeHtml(m.name) + '</span>'
          + '<span style="color:var(--ink-h);font-weight:700">' + escapeHtml(m.price) + '</span>'
          + '</div>'
          + '<div style="font-size:.72rem;color:var(--ink-m);margin-top:.2rem">' + when + ' · ' + escapeHtml(emp) + '</div>'
          + '</div>';
  });
  box.innerHTML = html;
  document.getElementById('recapTotal').textContent = total.toLocaleString('fr-FR') + ' MAD';
}

function formatDt(iso) {
  var d = new Date(iso.replace(' ', 'T'));
  if (isNaN(d)) return iso;
  return pad(d.getDate()) + ' ' + monthsFr[d.getMonth()] + ' · ' + pad(d.getHours()) + ':' + pad(d.getMinutes());
}

function escapeHtml(s) {
  return String(s).replace(/[&<>"']/g, function(c){
    return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
  });
}

function checkComplete() {
  var allSet = servicesMeta.every(function(m){ return selections[m.id] && selections[m.id].date_heure; });
  var btn = document.getElementById('btnNext3');
  if (allSet) {
    btn.style.opacity = '1';
    btn.style.pointerEvents = 'auto';
  } else {
    btn.style.opacity = '.4';
    btn.style.pointerEvents = 'none';
  }
}

function goStep3() {
  var payload = servicesMeta.map(function(m){
    var s = selections[m.id] || {};
    return {
      service_id: m.id,
      date_heure: s.date_heure || '',
      employe_id: s.employe_id || null,
    };
  });
  if (payload.some(function(p){ return !p.date_heure; })) return;

  fetch(saveStepUrl, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
    body: JSON.stringify({ step: 'creneaux', selections: payload })
  }).then(() => { window.location.href = step3Url; });
}

function prevMonth() {
  currentMonth--;
  if (currentMonth < 0) { currentMonth = 11; currentYear--; }
  buildCal();
}
function nextMonth() {
  currentMonth++;
  if (currentMonth > 11) { currentMonth = 0; currentYear++; }
  buildCal();
}

refreshRecap();
buildCal();
</script>
@endpush
@endsection
