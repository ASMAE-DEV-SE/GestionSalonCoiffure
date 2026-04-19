@extends('layouts.dashboard')
@section('title', 'Disponibilités')

@section('content')

<div class="dash-page-header">
  <div>
    <div class="dash-greeting">Disponibilités</div>
    <div class="dash-date">Taux d'occupation : {{ $tauxOccupation }}% · CA estimé {{ number_format($caEstime,0,',',' ') }} MAD</div>
  </div>
</div>

{{-- Navigation semaine ──────────────────────────────────── --}}
<div class="dispo-controls" style="background:#fff;border-bottom:2px solid var(--border2);padding:.8rem 0;margin-bottom:1.5rem">
  <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap;padding:0 2.5rem">
    <div class="week-nav">
      <a href="{{ request()->fullUrlWithQuery(['debut' => $debutSemaine->copy()->subWeek()->toDateString()]) }}" class="week-nav-btn">&#8592;</a>
      <div class="week-label">{{ $debutSemaine->translatedFormat('d M') }} – {{ $finSemaine->translatedFormat('d M Y') }}</div>
      <a href="{{ request()->fullUrlWithQuery(['debut' => $debutSemaine->copy()->addWeek()->toDateString()]) }}" class="week-nav-btn">&#8594;</a>
    </div>
    <a href="{{ request()->fullUrlWithQuery(['debut' => now()->startOfWeek()->toDateString()]) }}"
       class="tb-filter" style="font-size:.76rem">Cette semaine</a>

    {{-- Filtre employé --}}
    <div style="margin-left:auto;display:flex;gap:.4rem;flex-wrap:wrap">
      <a href="{{ request()->fullUrlWithQuery(['employe_id'=>'']) }}"
         class="emp-pill {{ !$employeFiltre ? 'on' : '' }}">
        <span class="emp-pill-dot" style="background:var(--p4)"></span>Tous
      </a>
      @foreach($employes as $e)
        <a href="{{ request()->fullUrlWithQuery(['employe_id'=>$e->id]) }}"
           class="emp-pill {{ $employeFiltre==$e->id ? 'on' : '' }}">
          <span class="emp-pill-dot" style="background:hsl({{ ($e->id*67)%360 }},50%,45%)"></span>
          {{ $e->prenom }}
        </a>
      @endforeach
    </div>
  </div>
</div>

<div class="dispo-two-col">
  <div>

    {{-- Légende --}}
    <div class="dispo-legend">
      <div class="legend-item"><div class="legend-dot" style="background:var(--p4)"></div>Confirmé</div>
      <div class="legend-item"><div class="legend-dot" style="background:#D4A844"></div>En attente</div>
      <div class="legend-item"><div class="legend-dot" style="background:var(--border2)"></div>Bloqué</div>
    </div>

    {{-- Calendrier --}}
    <div class="cal-grid">
      {{-- En-tête jours --}}
      <div class="cal-header-row cols-7">
        <div class="cal-corner"></div>
        @foreach($calendrier as $dateStr => $jour)
          <div class="cal-day-head {{ $jour['date']->isToday() ? 'today' : '' }}">
            <div class="cal-day-name">{{ $jour['date']->translatedFormat('D') }}</div>
            <div class="cal-day-num">{{ $jour['date']->format('d') }}</div>
          </div>
        @endforeach
      </div>

      {{-- Créneaux horaires --}}
      @foreach(['09:00','10:00','11:00','12:00','14:00','15:00','16:00','17:00'] as $heure)
        <div class="cal-body-row cols-7">
          <div class="cal-time-cell">{{ $heure }}</div>
          @foreach($calendrier as $dateStr => $jour)
            <div class="cal-slot">
              @foreach($jour['reservations'] as $r)
                @if($r->date_heure->format('H:i') === $heure)
                  @php
                    $evClass = $r->statut === 'confirmee' ? 'ev-confirmed'
                             : ($r->statut === 'en_attente' ? 'ev-pending' : 'ev-blocked');
                  @endphp
                  <div class="cal-event {{ $evClass }}">
                    <div class="cal-event-title">{{ $r->client?->prenom ?? 'Client' }} {{ substr($r->client?->nom ?? '', 0, 1) }}.</div>
                    <div class="cal-event-sub">{{ $r->service?->nom_service ?? 'Service' }}</div>
                  </div>
                @endif
              @endforeach
            </div>
          @endforeach
        </div>
      @endforeach
    </div>

    {{-- Bloquer un créneau --}}
    <div style="margin-top:1.5rem;border:2px solid var(--border2);padding:1.4rem;background:#fff">
      <div style="font-family:var(--fh);font-size:1.1rem;font-weight:700;color:var(--ink-h);margin-bottom:1rem">Bloquer un créneau</div>
      <form method="POST" action="{{ route('salon.disponibilites.bloquer') }}">
        @csrf
        <div style="display:grid;grid-template-columns:1fr 1fr 80px auto;gap:.75rem;align-items:end">
          <div>
            <label class="pse-label">Employé</label>
            <select name="employe_id" class="pse-input" required>
              @foreach($employes as $e)<option value="{{ $e->id }}">{{ $e->nomComplet() }}</option>@endforeach
            </select>
          </div>
          <div>
            <label class="pse-label">Date &amp; heure</label>
            <input type="datetime-local" name="date_heure" class="pse-input" required>
          </div>
          <div>
            <label class="pse-label">Durée (min)</label>
            <input type="number" name="duree" class="pse-input" value="60" min="15" max="480" required>
          </div>
          <button type="submit" class="btn-add" style="padding:.68rem 1rem;font-size:.74rem;white-space:nowrap">Bloquer</button>
        </div>
        <div style="margin-top:.7rem">
          <label class="pse-label">Motif <span style="font-weight:400;text-transform:none;letter-spacing:0">(optionnel)</span></label>
          <input type="text" name="motif" class="pse-input" placeholder="Pause, formation, congé...">
        </div>
      </form>
    </div>

  </div>

  {{-- Sidebar charge par employé --}}
  <div>
    <div class="emp-dispo-sidebar">
      <div class="emp-dispo-head">Charge hebdomadaire</div>
      @forelse($employes as $e)
        @php $nb = collect($calendrier)->sum(fn($j) => $j['reservations']->where('employe_id',$e->id)->count()); @endphp
        <div class="emp-dispo-item">
          <div class="emp-dispo-av">
            <img src="{{ $e->photo_url }}" alt="{{ $e->nomComplet() }}">
          </div>
          <div>
            <div class="emp-dispo-name">{{ $e->nomComplet() }}</div>
            <div class="emp-dispo-rdv">{{ $nb }} RDV cette semaine</div>
          </div>
          <div class="emp-dispo-count">{{ $nb }}</div>
        </div>
      @empty
        <div style="padding:1.2rem;font-size:.82rem;color:var(--ink-m)">Aucun employé.</div>
      @endforelse
    </div>
  </div>
</div>
@endsection
