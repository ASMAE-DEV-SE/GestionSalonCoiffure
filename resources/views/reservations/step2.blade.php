@extends('layouts.app')
@section('title', 'Réservation — Choix du créneau')

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
    <div class="step done"><div class="step-dot">&#10003;</div><div class="step-label">Service</div></div>
    <div class="step current"><div class="step-dot">2</div><div class="step-label">Créneau</div></div>
    <div class="step"><div class="step-dot">3</div><div class="step-label">Vos infos</div></div>
    <div class="step"><div class="step-dot">4</div><div class="step-label">Confirmation</div></div>
  </div>
</div>

<div class="wizard-two-col">
  <div>

    {{-- Service choisi ──────────────────────────────────────── --}}
    <div class="completed-step">
      <div>
        <div class="completed-step-label">Service choisi</div>
        <div class="completed-step-value">{{ $service->nom_service }}</div>
        <div class="completed-step-meta">{{ $service->duree_formatee }}</div>
      </div>
      <div class="completed-step-right">
        <div class="completed-step-price">{{ $service->prix_format }}</div>
        <a href="{{ route('reservations.step1', $salonModel->slug) }}" class="btn-edit-step">Modifier</a>
      </div>
    </div>

    {{-- Choix styliste ──────────────────────────────────────── --}}
    <div class="emp-select-section">
      <div class="emp-select-head">
        <div>
          <div class="emp-select-title">Choisissez votre styliste</div>
          <div class="emp-select-sub">Optionnel — nous choisirons pour vous si non spécifié</div>
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
        <a href="{{ route('reservations.step1', $salonModel->slug) }}" class="btn-wizard-back">&#8592; Service</a>
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

      <div class="recap-row"><span class="recap-key">Service</span><span class="recap-value">{{ $service->nom_service }}</span></div>
      <div class="recap-row"><span class="recap-key">Durée</span><span class="recap-value">{{ $service->duree_formatee }}</span></div>
      <div class="recap-row"><span class="recap-key">Styliste</span><span id="recapEmp" class="recap-value">Au choix</span></div>
      <div class="recap-row"><span class="recap-key">Date</span><span id="recapDate" class="recap-value" style="color:var(--ink-d)">À choisir</span></div>
      <div class="recap-row"><span class="recap-key">Heure</span><span id="recapTime" class="recap-value" style="color:var(--ink-d)">À choisir</span></div>

      <div class="recap-total-row">
        <span class="recap-total-label">Total</span>
        <span class="recap-total-amount">{{ $service->prix_format }}</span>
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
// Créneaux depuis le serveur
var creneauxData = @json($creneaux);

var selectedEmpId = '';
var selectedDate = null;
var selectedTime = null;
var currentMonth, currentYear;
var months = ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
var monthsFr = ['janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];

var today = new Date();
currentMonth = today.getMonth();
currentYear  = today.getFullYear();

var saveStepUrl = '{{ route('reservations.save-step', $salonModel->slug) }}';
var step3Url    = '{{ route('reservations.step3', $salonModel->slug) }}';
var csrfToken   = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function pad(n) { return n < 10 ? '0' + n : String(n); }
function dateKey(y, m, d) { return y + '-' + pad(m + 1) + '-' + pad(d); }

function buildCal() {
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

  for (var day = 1; day <= days; day++) {
    var d = document.createElement('div');
    var key = dateKey(currentYear, currentMonth, day);
    var isPast = (new Date(currentYear, currentMonth, day) < new Date(today.getFullYear(), today.getMonth(), today.getDate()));
    var isToday = (day === today.getDate() && currentMonth === today.getMonth() && currentYear === today.getFullYear());
    var hasSlots = creneauxData[key] && creneauxData[key].some(function(s){return s.disponible;}) && !isPast;
    var classes = ['cal-day'];
    if (isPast) classes.push('past');
    else if (!hasSlots) classes.push('disabled');
    if (isToday) classes.push('today');
    if (hasSlots) classes.push('has-slots');
    if (key === selectedDate) classes.push('selected');
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
  selectedDate = key;
  selectedTime = null;
  buildCal();

  var jsFull = ['Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche'];
  var jsDate = new Date(year, month, dayNum);
  var wd = jsDate.getDay();
  var wdIdx = (wd === 0) ? 6 : wd - 1;
  var label = jsFull[wdIdx] + ' ' + dayNum + ' ' + monthsFr[month] + ' ' + year;
  document.getElementById('slotsDate').textContent = label;
  document.getElementById('slotsSection').style.display = 'block';

  document.getElementById('recapDate').textContent = dayNum + ' ' + monthsFr[month];
  document.getElementById('recapDate').style.color = '';
  document.getElementById('recapTime').textContent = 'À choisir';
  document.getElementById('recapTime').style.color = 'var(--ink-d)';

  // Construire créneaux
  var slots = creneauxData[key] || [];
  var body = document.getElementById('slotsBody');
  body.innerHTML = '';
  if (slots.length === 0) {
    body.innerHTML = '<p style="color:var(--ink-m);padding:1rem">Aucun créneau disponible ce jour.</p>';
    return;
  }

  var matin = slots.filter(function(s){ return parseInt(s.heure.split(':')[0]) < 13; });
  var apmidi = slots.filter(function(s){ return parseInt(s.heure.split(':')[0]) >= 13; });

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
      el.className = 'time-slot ' + (slot.disponible ? 'available' : 'unavailable');
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
  checkComplete();
}

var selectedDatetime = null;

function selectSlot(el, time, datetime) {
  document.querySelectorAll('.time-slot').forEach(s => s.classList.remove('selected'));
  el.classList.add('selected');
  selectedTime = time;
  selectedDatetime = datetime;
  document.getElementById('recapTime').textContent = time;
  document.getElementById('recapTime').style.color = '';
  checkComplete();
}

function selectEmp(row, id, name) {
  document.querySelectorAll('.emp-select-row').forEach(r => r.classList.remove('selected'));
  row.classList.add('selected');
  selectedEmpId = id;
  document.getElementById('recapEmp').textContent = id ? name : 'Au choix';
}

function checkComplete() {
  var btn = document.getElementById('btnNext3');
  if (selectedDate && selectedTime) {
    btn.style.opacity = '1';
    btn.style.pointerEvents = 'auto';
  } else {
    btn.style.opacity = '.4';
    btn.style.pointerEvents = 'none';
  }
}

function goStep3() {
  if (!selectedDate || !selectedTime || !selectedDatetime) return;
  fetch(saveStepUrl, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
    body: JSON.stringify({ step: 'creneau', date_heure: selectedDatetime, employe_id: selectedEmpId || null })
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

buildCal();
</script>
@endpush
@endsection
