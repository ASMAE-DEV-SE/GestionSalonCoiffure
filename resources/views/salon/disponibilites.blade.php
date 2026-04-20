@extends('layouts.dashboard')
@section('title', 'Disponibilités')

@section('content')

@php
  $jours = ['lundi','mardi','mercredi','jeudi','vendredi','samedi','dimanche'];
  $horairesSalon = $salon->horaires ?: [];
@endphp

<div class="dash-page-header">
  <div>
    <div class="dash-greeting">Disponibilités</div>
    <div class="dash-date">Taux d'occupation : {{ $tauxOccupation }}% · CA estimé {{ number_format($caEstime,0,',',' ') }} MAD</div>
  </div>
</div>

{{-- Onglets internes ──────────────────────────────────── --}}
<div style="background:#fff;border-bottom:2px solid var(--border2);padding:0 2.5rem;display:flex;gap:0">
  <button type="button" class="dispo-tab-btn on" data-tab="calendar"
          onclick="switchTab(this,'calendar')"
          style="padding:.9rem 1.1rem;border:none;background:none;font-size:.78rem;font-weight:700;color:var(--ink-h);border-bottom:3px solid var(--p4);cursor:pointer">
    Calendrier
  </button>
  <button type="button" class="dispo-tab-btn" data-tab="horaires"
          onclick="switchTab(this,'horaires')"
          style="padding:.9rem 1.1rem;border:none;background:none;font-size:.78rem;font-weight:700;color:var(--ink-m);border-bottom:3px solid transparent;cursor:pointer">
    Horaires du salon
  </button>
  <button type="button" class="dispo-tab-btn" data-tab="exceptions"
          onclick="switchTab(this,'exceptions')"
          style="padding:.9rem 1.1rem;border:none;background:none;font-size:.78rem;font-weight:700;color:var(--ink-m);border-bottom:3px solid transparent;cursor:pointer">
    Exceptions ({{ $exceptions->count() }})
  </button>
  <button type="button" class="dispo-tab-btn" data-tab="employes"
          onclick="switchTab(this,'employes')"
          style="padding:.9rem 1.1rem;border:none;background:none;font-size:.78rem;font-weight:700;color:var(--ink-m);border-bottom:3px solid transparent;cursor:pointer">
    Équipe
  </button>
</div>

{{-- ───────── Tab : Calendrier ───────── --}}
<div class="dispo-pane" data-pane="calendar" style="padding-top:1.2rem">

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
      <div class="dispo-legend">
        <div class="legend-item"><div class="legend-dot" style="background:var(--p4)"></div>Confirmé</div>
        <div class="legend-item"><div class="legend-dot" style="background:#D4A844"></div>En attente</div>
        <div class="legend-item"><div class="legend-dot" style="background:var(--border2)"></div>Bloqué</div>
      </div>

      <div class="cal-grid">
        <div class="cal-header-row cols-7">
          <div class="cal-corner"></div>
          @foreach($calendrier as $dateStr => $jour)
            <div class="cal-day-head {{ $jour['date']->isToday() ? 'today' : '' }}">
              <div class="cal-day-name">{{ $jour['date']->translatedFormat('D') }}</div>
              <div class="cal-day-num">{{ $jour['date']->format('d') }}</div>
            </div>
          @endforeach
        </div>

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
                      <div class="cal-event-title">{{ $r->client->prenom }} {{ substr($r->client->nom,0,1) }}.</div>
                      <div class="cal-event-sub">{{ $r->service->nom_service }}</div>
                    </div>
                  @endif
                @endforeach
              </div>
            @endforeach
          </div>
        @endforeach
      </div>

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

</div>

{{-- ───────── Tab : Horaires salon ───────── --}}
<div class="dispo-pane" data-pane="horaires" style="display:none;padding:1.5rem 2.5rem">
  <div style="background:#fff;border:2px solid var(--border2);padding:1.6rem;max-width:900px">
    <div style="font-family:var(--fh);font-size:1.1rem;font-weight:700;color:var(--ink-h);margin-bottom:.3rem">
      Horaires hebdomadaires du salon
    </div>
    <div style="font-size:.78rem;color:var(--ink-m);margin-bottom:1.2rem">
      Ces horaires définissent les créneaux proposés aux clients lors de la réservation.
    </div>

    <form method="POST" action="{{ route('salon.disponibilites.horaires') }}">
      @csrf @method('PUT')
      <div style="display:flex;flex-direction:column;gap:.6rem">
        @foreach($jours as $jour)
          @php
            $h      = $horairesSalon[$jour] ?? ['debut'=>'09:00','fin'=>'18:00','ferme'=>false];
            $ferme  = $h['ferme'] ?? false;
            $debut  = $h['debut'] ?? '09:00';
            $fin    = $h['fin']   ?? '18:00';
          @endphp
          <div style="display:grid;grid-template-columns:120px 1fr 1fr 140px;gap:.8rem;align-items:center;padding:.6rem .8rem;background:#FDFAF5;border:1px solid var(--border2)">
            <div style="font-weight:700;color:var(--ink-d);text-transform:capitalize">{{ $jour }}</div>
            <input type="time" name="h_{{ $jour }}_debut" value="{{ $debut }}" class="pse-input" {{ $ferme ? 'disabled' : '' }}>
            <input type="time" name="h_{{ $jour }}_fin"   value="{{ $fin }}"   class="pse-input" {{ $ferme ? 'disabled' : '' }}>
            <label style="display:flex;align-items:center;gap:.4rem;font-size:.8rem;color:var(--ink-m);cursor:pointer">
              <input type="checkbox" name="h_{{ $jour }}_ferme" value="1"
                     {{ $ferme ? 'checked' : '' }}
                     onchange="toggleFerme(this,'h_{{ $jour }}_')">
              Fermé
            </label>
          </div>
        @endforeach
      </div>
      <div style="margin-top:1.2rem;text-align:right">
        <button type="submit" class="btn-add">Enregistrer les horaires</button>
      </div>
    </form>
  </div>
</div>

{{-- ───────── Tab : Exceptions ───────── --}}
<div class="dispo-pane" data-pane="exceptions" style="display:none;padding:1.5rem 2.5rem">
  <div style="background:#fff;border:2px solid var(--border2);padding:1.6rem;max-width:1000px">
    <div style="font-family:var(--fh);font-size:1.1rem;font-weight:700;color:var(--ink-h);margin-bottom:.3rem">
      Exceptions de disponibilité
    </div>
    <div style="font-size:.78rem;color:var(--ink-m);margin-bottom:1.2rem">
      Ajoutez des fermetures exceptionnelles, jours fériés ou absences ponctuelles (salon entier ou employé précis).
    </div>

    {{-- Formulaire ajout --}}
    <form method="POST" action="{{ route('salon.disponibilites.exceptions.store') }}"
          style="display:grid;grid-template-columns:1.3fr 1fr 1fr 1fr auto;gap:.6rem;align-items:end;padding:1rem;background:#FDFAF5;border:1px dashed var(--p2);margin-bottom:1.2rem">
      @csrf
      <div>
        <label class="pse-label">Concerne</label>
        <select name="employe_id" class="pse-input">
          <option value="">Salon entier</option>
          @foreach($employes as $e)
            <option value="{{ $e->id }}">{{ $e->nomComplet() }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="pse-label">Date</label>
        <input type="date" name="date" class="pse-input" required min="{{ now()->toDateString() }}">
      </div>
      <div>
        <label class="pse-label">Début <span style="text-transform:none;letter-spacing:0;font-weight:400">(vide = fermé)</span></label>
        <input type="time" name="debut" class="pse-input">
      </div>
      <div>
        <label class="pse-label">Fin</label>
        <input type="time" name="fin" class="pse-input">
      </div>
      <button type="submit" class="btn-add">Ajouter</button>
      <div style="grid-column:1 / -1">
        <label style="display:flex;align-items:center;gap:.5rem;font-size:.8rem;color:var(--ink-d);cursor:pointer;margin-top:.2rem">
          <input type="checkbox" name="ferme" value="1" checked> Fermé toute la journée
        </label>
      </div>
      <div style="grid-column:1 / -1">
        <label class="pse-label">Motif <span style="text-transform:none;letter-spacing:0;font-weight:400">(optionnel)</span></label>
        <input type="text" name="motif" class="pse-input" placeholder="Jour férié, formation, congé...">
      </div>
    </form>

    {{-- Liste --}}
    @if($exceptions->isEmpty())
      <div style="padding:2rem;text-align:center;color:var(--ink-m);font-size:.88rem">
        Aucune exception enregistrée.
      </div>
    @else
      <table style="width:100%;border-collapse:collapse">
        <thead>
          <tr style="border-bottom:2px solid var(--border2);text-align:left">
            <th style="padding:.7rem .5rem;font-size:.72rem;text-transform:uppercase;letter-spacing:1px;color:var(--ink-m)">Date</th>
            <th style="padding:.7rem .5rem;font-size:.72rem;text-transform:uppercase;letter-spacing:1px;color:var(--ink-m)">Concerne</th>
            <th style="padding:.7rem .5rem;font-size:.72rem;text-transform:uppercase;letter-spacing:1px;color:var(--ink-m)">Plage</th>
            <th style="padding:.7rem .5rem;font-size:.72rem;text-transform:uppercase;letter-spacing:1px;color:var(--ink-m)">Motif</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @foreach($exceptions as $ex)
            <tr style="border-bottom:1px solid var(--border2)">
              <td style="padding:.7rem .5rem;font-weight:700;color:var(--ink-d)">
                {{ $ex->date->translatedFormat('D d M Y') }}
              </td>
              <td style="padding:.7rem .5rem;color:var(--ink-d)">
                {{ $ex->employe ? $ex->employe->nomComplet() : 'Salon entier' }}
              </td>
              <td style="padding:.7rem .5rem;color:var(--ink-d)">
                @if($ex->ferme)
                  <span style="color:#C04A3D;font-weight:700">Fermé</span>
                @else
                  {{ substr($ex->debut,0,5) }} – {{ substr($ex->fin,0,5) }}
                @endif
              </td>
              <td style="padding:.7rem .5rem;color:var(--ink-m);font-size:.82rem">
                {{ $ex->motif ?: '—' }}
              </td>
              <td style="padding:.7rem .5rem;text-align:right">
                <form method="POST" action="{{ route('salon.disponibilites.exceptions.destroy', $ex->id) }}"
                      onsubmit="return confirm('Supprimer cette exception ?')">
                  @csrf @method('DELETE')
                  <button type="submit" style="background:none;border:none;color:#C04A3D;font-weight:700;cursor:pointer;font-size:.78rem">Supprimer</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>
</div>

{{-- ───────── Tab : Équipe (horaires par employé) ───────── --}}
<div class="dispo-pane" data-pane="employes" style="display:none;padding:1.5rem 2.5rem">
  <div style="max-width:1000px">
    <div style="font-family:var(--fh);font-size:1.1rem;font-weight:700;color:var(--ink-h);margin-bottom:.3rem">
      Horaires de l'équipe
    </div>
    <div style="font-size:.78rem;color:var(--ink-m);margin-bottom:1.2rem">
      Définissez les horaires de travail de chaque employé. Un créneau n'est proposé que si l'employé est disponible.
    </div>

    @forelse($employes as $e)
      @php $he = $e->horaires ?: []; @endphp
      <details style="background:#fff;border:2px solid var(--border2);margin-bottom:.8rem">
        <summary style="display:flex;align-items:center;gap:.8rem;padding:.9rem 1.2rem;cursor:pointer;list-style:none">
          <div style="width:38px;height:38px;border-radius:50%;overflow:hidden;flex-shrink:0">
            <img src="{{ $e->photo_url }}" alt="" style="width:100%;height:100%;object-fit:cover">
          </div>
          <div style="flex:1">
            <div style="font-weight:700;color:var(--ink-d)">{{ $e->nomComplet() }}</div>
            <div style="font-size:.74rem;color:var(--ink-m)">{{ implode(', ', $e->specialites ?? []) ?: '—' }}</div>
          </div>
          <div style="font-size:.72rem;color:var(--ink-m)">Modifier &#9662;</div>
        </summary>

        <form method="POST" action="{{ route('salon.disponibilites.employe.horaires', $e->id) }}"
              style="padding:1rem 1.2rem;border-top:1px solid var(--border2)">
          @csrf @method('PUT')
          <div style="display:flex;flex-direction:column;gap:.5rem">
            @foreach($jours as $jour)
              @php
                $heJ    = $he[$jour] ?? ['debut'=>'09:00','fin'=>'18:00','ferme'=>false];
                $fermeJ = $heJ['ferme'] ?? false;
                $debutJ = $heJ['debut'] ?? '09:00';
                $finJ   = $heJ['fin']   ?? '18:00';
              @endphp
              <div style="display:grid;grid-template-columns:110px 1fr 1fr 120px;gap:.6rem;align-items:center">
                <div style="font-weight:600;color:var(--ink-d);text-transform:capitalize;font-size:.82rem">{{ $jour }}</div>
                <input type="time" name="h_{{ $jour }}_debut" value="{{ $debutJ }}" class="pse-input" {{ $fermeJ ? 'disabled' : '' }}>
                <input type="time" name="h_{{ $jour }}_fin"   value="{{ $finJ }}"   class="pse-input" {{ $fermeJ ? 'disabled' : '' }}>
                <label style="display:flex;align-items:center;gap:.4rem;font-size:.78rem;color:var(--ink-m);cursor:pointer">
                  <input type="checkbox" name="h_{{ $jour }}_ferme" value="1"
                         {{ $fermeJ ? 'checked' : '' }}
                         onchange="toggleFerme(this,'h_{{ $jour }}_')">
                  Repos
                </label>
              </div>
            @endforeach
          </div>
          <div style="margin-top:1rem;text-align:right">
            <button type="submit" class="btn-add">Enregistrer</button>
          </div>
        </form>
      </details>
    @empty
      <div style="padding:2rem;background:#fff;border:2px solid var(--border2);text-align:center;color:var(--ink-m)">
        Aucun employé. Ajoutez-en depuis la page <a href="{{ route('salon.employes.index') }}" style="color:var(--p4d)">Équipe</a>.
      </div>
    @endforelse
  </div>
</div>

@push('scripts')
<script>
function switchTab(btn, tab) {
  document.querySelectorAll('.dispo-tab-btn').forEach(function(b){
    var on = b.dataset.tab === tab;
    b.classList.toggle('on', on);
    b.style.color = on ? 'var(--ink-h)' : 'var(--ink-m)';
    b.style.borderBottomColor = on ? 'var(--p4)' : 'transparent';
  });
  document.querySelectorAll('.dispo-pane').forEach(function(p){
    p.style.display = (p.dataset.pane === tab) ? '' : 'none';
  });
}

function toggleFerme(chk, prefix) {
  var inputs = document.querySelectorAll('input[name="' + prefix + 'debut"], input[name="' + prefix + 'fin"]');
  inputs.forEach(function(i){ i.disabled = chk.checked; });
}
</script>
@endpush
@endsection
