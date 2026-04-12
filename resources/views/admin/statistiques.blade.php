@extends('layouts.admin')
@section('title', 'Statistiques')

@section('content')

<div class="admin-page-header">
  <div>
    <div class="admin-page-title">Statistiques &amp; Rapports</div>
    <div class="admin-page-subtitle">
      Du {{ $debut->translatedFormat('d F Y') }} au {{ $fin->translatedFormat('d F Y') }}
    </div>
  </div>
  <div class="admin-header-actions">
    <a href="{{ route('admin.statistiques.export') }}?debut={{ $debut->toDateString() }}&fin={{ $fin->toDateString() }}"
       class="btn-admin-ghost">&#128196; Export CSV</a>
  </div>
</div>

{{-- Sélecteur de période --}}
<form method="GET" style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;margin-bottom:2rem">
  @foreach(['7' => '7 jours', '30' => '30 jours', '90' => '3 mois', '180' => '6 mois', '365' => '1 an'] as $j => $l)
    <a href="{{ route('admin.statistiques.index') }}?debut={{ now()->subDays($j)->toDateString() }}&fin={{ now()->toDateString() }}"
       class="period-btn {{ request('debut') === now()->subDays($j)->toDateString() ? 'on' : '' }}">{{ $l }}</a>
  @endforeach
  <div class="period-sep"></div>
  <input type="date" name="debut" class="tb-search" value="{{ $debut->toDateString() }}">
  <input type="date" name="fin"   class="tb-search" value="{{ $fin->toDateString() }}">
  <button type="submit" class="btn-add" style="padding:.5rem 1.1rem;font-size:.76rem">Appliquer</button>
</form>

{{-- KPI ─────────────────────────────────────────────────── --}}
<div class="admin-kpi-grid" style="margin-bottom:2rem">
  <div class="admin-kpi-card green">
    <div class="admin-kpi-top"><div class="admin-kpi-label">Réservations</div><div class="admin-kpi-icon kpi-icon-green">&#128197;</div></div>
    <div class="admin-kpi-value">{{ number_format($kpi['total_resa']) }}</div>
    <div class="admin-kpi-trend trend-neutral">Sur la période</div>
  </div>
  <div class="admin-kpi-card gold">
    <div class="admin-kpi-top"><div class="admin-kpi-label">CA estimé</div><div class="admin-kpi-icon kpi-icon-gold">&#9733;</div></div>
    <div class="admin-kpi-value" style="font-size:1.5rem">{{ number_format($kpi['ca_total'], 0, ',', ' ') }}</div>
    <div class="admin-kpi-trend trend-neutral">MAD</div>
  </div>
  <div class="admin-kpi-card blue">
    <div class="admin-kpi-top"><div class="admin-kpi-label">Inscriptions clients</div><div class="admin-kpi-icon kpi-icon-blue">&#9786;</div></div>
    <div class="admin-kpi-value">{{ $kpi['inscriptions'] }}</div>
    <div class="admin-kpi-trend trend-neutral">Nouveaux comptes</div>
  </div>
  <div class="admin-kpi-card red">
    <div class="admin-kpi-top"><div class="admin-kpi-label">Taux d'annulation</div><div class="admin-kpi-icon kpi-icon-red">&#10005;</div></div>
    <div class="admin-kpi-value">{{ $kpi['taux_annul'] }}%</div>
    <div class="admin-kpi-trend trend-neutral">Note moy. &#9733; {{ $kpi['note_moy'] }}</div>
  </div>
</div>

<div class="stat-two-col">

  {{-- Réservations par mois --}}
  <div class="stat-chart-card">
    <div class="stat-chart-head">
      <div><div class="stat-chart-title">Réservations par mois</div><div class="stat-chart-sub">Évolution sur la période</div></div>
    </div>
    <div class="stat-chart-body">
      @php $maxR = collect($resaParMois)->max('value') ?: 1; @endphp
      <div style="display:flex;align-items:flex-end;gap:.5rem;height:120px;border-bottom:1px solid var(--border2);padding-bottom:.5rem;margin-bottom:.8rem">
        @foreach($resaParMois as $m)
          <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:.3rem" title="{{ $m['value'] }} RDV">
            <div style="width:100%;background:var(--p{{ $loop->last ? '4' : '3' }});border-radius:2px 2px 0 0;min-height:4px;height:{{ round($m['value']/$maxR*100) }}%"></div>
            <div style="font-size:.6rem;font-weight:700;color:var(--ink-m);white-space:nowrap">{{ $m['label'] }}</div>
          </div>
        @endforeach
      </div>
      <div class="stat-metric-row" style="grid-template-columns:repeat(3,1fr);border-top:none;padding:.5rem 0 0">
        <div class="stat-metric"><div class="stat-metric-val">{{ collect($resaParMois)->sum('value') }}</div><div class="stat-metric-lbl">Total RDV</div></div>
        <div class="stat-metric"><div class="stat-metric-val">{{ number_format(collect($resaParMois)->sum('ca'),0,',',' ') }}</div><div class="stat-metric-lbl">CA MAD</div></div>
        <div class="stat-metric"><div class="stat-metric-val">{{ $kpi['taux_annul'] }}%</div><div class="stat-metric-lbl">Annulation</div></div>
      </div>
    </div>
  </div>

  {{-- Distribution notes --}}
  <div class="stat-chart-card">
    <div class="stat-chart-head">
      <div><div class="stat-chart-title">Satisfaction clients</div><div class="stat-chart-sub">Distribution des notes</div></div>
    </div>
    <div class="stat-chart-body">
      <div style="text-align:center;padding:.8rem 0;margin-bottom:1rem">
        <div style="font-family:var(--fh);font-size:3rem;font-weight:700;color:var(--ink-h);line-height:1">{{ $kpi['note_moy'] }}</div>
        <div style="color:#D4A844;font-size:1.1rem;letter-spacing:2px;margin:.3rem 0">
          {{ str_repeat('★', (int)round($kpi['note_moy'])) }}{{ str_repeat('☆', 5 - (int)round($kpi['note_moy'])) }}
        </div>
        <div style="font-size:.72rem;color:var(--ink-m);font-weight:600">Note moyenne globale</div>
      </div>
      <div class="bar-h-chart">
        @foreach(array_reverse(array_keys($distNotes), true) as $note)
          <div class="bar-h-row">
            <span class="bar-h-label" style="width:50px">{{ $note }}&#9733;</span>
            <div class="bar-h-track">
              <div class="bar-h-fill" style="width:{{ $distNotes[$note]['pct'] }}%;background:{{ ['','#C04A3D','#E8562A','#D4A844','#9CAB84','#89986D'][$note] }}">
                <span class="bar-h-val">{{ $distNotes[$note]['pct'] }}%</span>
              </div>
            </div>
            <span class="bar-h-count">{{ $distNotes[$note]['count'] }}</span>
          </div>
        @endforeach
      </div>
    </div>
  </div>

</div>

{{-- Top salons + Par ville --}}
<div class="stat-two-col" style="margin-bottom:2rem">

  <div class="stat-chart-card">
    <div class="stat-chart-head">
      <div><div class="stat-chart-title">Top salons</div><div class="stat-chart-sub">Réservations terminées sur la période</div></div>
    </div>
    <div class="stat-chart-body">
      @php $maxS = $topSalons->max('nb_resa') ?: 1; @endphp
      <div class="bar-h-chart">
        @foreach($topSalons as $s)
          <div class="bar-h-row">
            <span class="bar-h-label">{{ Str::limit($s->nom_salon, 14) }}</span>
            <div class="bar-h-track">
              <div class="bar-h-fill" style="width:{{ round($s->nb_resa/$maxS*100) }}%"><span class="bar-h-val">{{ $s->nb_resa }}</span></div>
            </div>
            <span class="bar-h-count">{{ $s->nb_resa }}</span>
          </div>
        @endforeach
      </div>
    </div>
  </div>

  <div class="stat-chart-card">
    <div class="stat-chart-head">
      <div><div class="stat-chart-title">Répartition par catégorie</div><div class="stat-chart-sub">Services les plus réservés</div></div>
    </div>
    <div class="stat-chart-body">
      @php
        $totalCat = $parCategorie->sum('total') ?: 1;
        $colors   = ['#89986D','#D4A844','#4A7C9E','#C5843C','#9E6B8A','#7B9E87','#4E5C38'];
      @endphp
      <div class="bar-h-chart">
        @foreach($parCategorie as $i => $c)
          <div class="bar-h-row">
            <span class="bar-h-label">{{ Str::limit($c->categorie, 12) }}</span>
            <div class="bar-h-track">
              <div class="bar-h-fill" style="width:{{ round($c->total/$totalCat*100) }}%;background:{{ $colors[$i%count($colors)] }}">
                <span class="bar-h-val">{{ round($c->total/$totalCat*100) }}%</span>
              </div>
            </div>
            <span class="bar-h-count">{{ $c->total }}</span>
          </div>
        @endforeach
      </div>
    </div>
  </div>

</div>

{{-- Tableau par ville --}}
<div class="stat-chart-card">
  <div class="stat-chart-head">
    <div><div class="stat-chart-title">Performance par ville</div><div class="stat-chart-sub">Réservations sur la période</div></div>
    <a href="{{ route('admin.statistiques.export') }}?debut={{ $debut->toDateString() }}&fin={{ $fin->toDateString() }}"
       class="export-btn" style="font-size:.7rem;padding:.3rem .8rem">CSV</a>
  </div>
  <div class="stat-chart-body" style="padding:0">
    <table class="geo-table">
      <thead><tr><th>Ville</th><th>Réservations</th><th>Part</th></tr></thead>
      <tbody>
        @php $totalV = $parVille->sum('nb_resa') ?: 1; @endphp
        @foreach($parVille as $v)
          <tr>
            <td><strong>{{ $v->nom_ville }}</strong></td>
            <td><div class="geo-bar-inline" style="width:{{ round($v->nb_resa/$totalV*60) }}px"></div>{{ $v->nb_resa }}</td>
            <td>{{ round($v->nb_resa/$totalV*100) }}%</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
