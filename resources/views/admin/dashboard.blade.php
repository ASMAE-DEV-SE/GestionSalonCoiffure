@extends('layouts.admin')
@section('title', 'Tableau de bord admin')

@section('content')

<div class="admin-page-header">
  <div>
    <div class="admin-page-title">Tableau de bord</div>
    <div class="admin-page-subtitle">Vue d'ensemble de la plateforme Salonify — {{ now()->translatedFormat('l d F Y') }}</div>
  </div>
  <div class="admin-header-actions">
    <a href="{{ route('admin.statistiques.export') }}" class="btn-admin-ghost">&#128196; Export CSV</a>
    <a href="{{ route('admin.statistiques.index') }}" class="btn-admin-dark">&#9830; Statistiques</a>
  </div>
</div>

{{-- Alertes ─────────────────────────────────────────────── --}}
@foreach($alertes as $alerte)
  <div class="flash flash-{{ $alerte['type'] }}" style="margin-bottom:1rem">
    {{ $alerte['message'] }}
    <a href="{{ $alerte['route'] }}" style="font-weight:700;text-decoration:underline;margin-left:.5rem">Voir &#8594;</a>
  </div>
@endforeach

{{-- KPI ─────────────────────────────────────────────────── --}}
<div class="admin-kpi-grid">
  <div class="admin-kpi-card green">
    <div class="admin-kpi-top">
      <div class="admin-kpi-label">Salons validés</div>
      <div class="admin-kpi-icon kpi-icon-green">&#9986;</div>
    </div>
    <div class="admin-kpi-value">{{ $kpi['salons_valides'] }}</div>
    <div class="admin-kpi-trend trend-neutral">/ {{ $kpi['salons_total'] }} inscrits</div>
  </div>
  <div class="admin-kpi-card gold">
    <div class="admin-kpi-top">
      <div class="admin-kpi-label">En attente validation</div>
      <div class="admin-kpi-icon kpi-icon-gold">&#9203;</div>
    </div>
    <div class="admin-kpi-value">{{ $kpi['salons_attente'] }}</div>
    <div class="admin-kpi-trend {{ $kpi['salons_attente'] > 0 ? 'trend-down' : 'trend-neutral' }}">
      {{ $kpi['salons_attente'] > 0 ? 'Action requise' : 'Aucune action' }}
    </div>
  </div>
  <div class="admin-kpi-card blue">
    <div class="admin-kpi-top">
      <div class="admin-kpi-label">Utilisateurs</div>
      <div class="admin-kpi-icon kpi-icon-blue">&#9786;</div>
    </div>
    <div class="admin-kpi-value">{{ $kpi['users_total'] }}</div>
    <div class="admin-kpi-trend trend-neutral">{{ $kpi['users_clients'] }} clients</div>
  </div>
  <div class="admin-kpi-card green" style="--before-bg:var(--p4)">
    <div class="admin-kpi-top">
      <div class="admin-kpi-label">Réservations ce mois</div>
      <div class="admin-kpi-icon kpi-icon-green">&#128197;</div>
    </div>
    <div class="admin-kpi-value">{{ $kpi['resa_ce_mois'] }}</div>
    <div class="admin-kpi-trend trend-neutral">/ {{ $kpi['reservations'] }} total</div>
  </div>
</div>

{{-- Deux colonnes ───────────────────────────────────────── --}}
<div class="admin-two-col">

  {{-- Salons en attente ──────────────────────────────────── --}}
  <div class="admin-table-card">
    <div class="admin-table-header">
      <div class="admin-table-title">Salons en attente de validation</div>
      <a href="{{ route('admin.salons.index') }}?statut=attente" class="admin-table-link">Voir tous</a>
    </div>
    <table class="admin-table">
      <thead><tr>
        <th>Salon</th><th>Gérant</th><th>Ville</th><th>Inscrit le</th><th>Action</th>
      </tr></thead>
      <tbody>
        @forelse($salonsEnAttente as $s)
          <tr>
            <td><strong>{{ $s->nom_salon }}</strong><div style="font-size:.72rem;color:var(--ink-m)">{{ $s->quartier }}</div></td>
            <td>{{ $s->user->nomComplet() }}<div style="font-size:.72rem;color:var(--ink-m)">{{ $s->user->email }}</div></td>
            <td>{{ $s->ville->nom_ville }}</td>
            <td style="font-size:.78rem;color:var(--ink-m)">{{ $s->created_at->translatedFormat('d M Y') }}</td>
            <td>
              <div style="display:flex;gap:.4rem">
                <form method="POST" action="{{ route('admin.salons.valider', $s->id) }}">
                  @csrf
                  <button class="admin-action-btn aab-green" style="padding:.36rem .85rem">&#10003; Valider</button>
                </form>
                <a href="{{ route('admin.salons.show', $s->id) }}" class="admin-action-btn aab-green" style="padding:.36rem .85rem;text-decoration:none">&#8594;</a>
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--ink-m);font-size:.88rem">
            &#10003; Aucun salon en attente de validation.
          </td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Actions système ────────────────────────────────────── --}}
  <div>
    <div class="admin-action-panel" style="margin-bottom:1.5rem">
      <div class="admin-action-header">Actions rapides</div>
      @if($kpi['salons_attente'] > 0)
        <div class="admin-action-item">
          <div class="admin-action-dot dot-alert"></div>
          <div class="admin-action-text">
            <div class="admin-action-title">{{ $kpi['salons_attente'] }} salon(s) à valider</div>
            <div class="admin-action-sub">En attente depuis l'inscription</div>
          </div>
          <a href="{{ route('admin.salons.index') }}?statut=attente" class="admin-action-btn aab-green">Traiter</a>
        </div>
      @endif
      @if($kpi['avis_sans_reponse'] > 0)
        <div class="admin-action-item">
          <div class="admin-action-dot dot-warn"></div>
          <div class="admin-action-text">
            <div class="admin-action-title">{{ $kpi['avis_sans_reponse'] }} avis sans réponse</div>
            <div class="admin-action-sub">Les salons n'ont pas encore répondu</div>
          </div>
          <a href="{{ route('admin.avis.index') }}?sans_reponse=1" class="admin-action-btn aab-green">Voir</a>
        </div>
      @endif
      <div class="admin-action-item">
        <div class="admin-action-dot dot-info"></div>
        <div class="admin-action-text">
          <div class="admin-action-title">{{ $kpi['villes_actives'] }} villes actives</div>
          <div class="admin-action-sub">Couverture géographique</div>
        </div>
        <a href="{{ route('admin.villes.index') }}" class="admin-action-btn aab-green">Gérer</a>
      </div>
    </div>

    {{-- Graphique réservations --}}
    <div class="admin-table-card">
      <div class="admin-table-header">
        <div class="admin-table-title">Réservations — 6 mois</div>
        <a href="{{ route('admin.statistiques.index') }}" class="admin-table-link">Détails</a>
      </div>
      <div style="padding:1.4rem">
        @php $maxV = collect($chartResa)->max('value') ?: 1; @endphp
        <div style="display:flex;align-items:flex-end;gap:.4rem;height:80px;border-bottom:1px solid var(--border2);padding-bottom:.5rem;margin-bottom:.6rem">
          @foreach($chartResa as $d)
            <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:.3rem">
              <div style="width:100%;background:var(--p{{ $loop->last ? '4' : '3' }});border-radius:2px 2px 0 0;min-height:4px"
                   style="height:{{ round($d['value']/$maxV*100) }}%"></div>
            </div>
          @endforeach
        </div>
        <div style="display:flex;gap:.4rem">
          @foreach($chartResa as $d)
            <div style="flex:1;text-align:center;font-size:.62rem;color:var(--ink-m);font-weight:600">{{ $d['label'] }}</div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Derniers inscrits + Top villes --}}
<div class="admin-two-col-equal">

  <div class="admin-table-card">
    <div class="admin-table-header">
      <div class="admin-table-title">Derniers inscrits</div>
      <a href="{{ route('admin.users.index') }}" class="admin-table-link">Voir tous</a>
    </div>
    <table class="admin-table">
      <thead><tr><th>Utilisateur</th><th>Rôle</th><th>Email vérifié</th><th>Inscrit le</th></tr></thead>
      <tbody>
        @foreach($derniersUsers as $u)
          <tr>
            <td><strong>{{ $u->nomComplet() }}</strong><div style="font-size:.72rem;color:var(--ink-m)">{{ $u->email }}</div></td>
            <td><span class="admin-status-badge {{ $u->role==='salon' ? 'asb-pending' : 'asb-ok' }}">{{ ucfirst($u->role) }}</span></td>
            <td>
              @if($u->email_verifie_le)
                <span class="admin-status-badge asb-ok">&#10003;</span>
              @else
                <span class="admin-status-badge asb-sus">Non vérifié</span>
              @endif
            </td>
            <td style="font-size:.78rem;color:var(--ink-m)">{{ $u->created_at->diffForHumans() }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="admin-table-card">
    <div class="admin-table-header">
      <div class="admin-table-title">Top villes</div>
      <a href="{{ route('admin.villes.index') }}" class="admin-table-link">Gérer</a>
    </div>
    <div style="padding:1.4rem">
      @foreach($topVilles as $v)
        <div class="coverage-row">
          <span class="coverage-label">{{ $v->nom_ville }}</span>
          <div class="coverage-track">
            <div class="coverage-fill" style="width:{{ $topVilles->first()->nb_resa > 0 ? round($v->nb_resa / $topVilles->first()->nb_resa * 100) : 0 }}%"></div>
          </div>
          <span class="coverage-count">{{ $v->nb_resa }}</span>
        </div>
      @endforeach
    </div>
  </div>

</div>
@endsection
