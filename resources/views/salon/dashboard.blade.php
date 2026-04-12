@extends('layouts.dashboard')
@section('title', 'Tableau de bord salon')

@section('content')

<div class="dash-page-header">
  <div>
    <div class="dash-greeting">{{ $salon->nom_salon }}</div>
    <div class="dash-date">{{ now()->translatedFormat('l d F Y') }}</div>
  </div>
  <div style="display:flex;gap:.65rem">
    <a href="{{ route('salon.reservations.index') }}" class="btn-new-booking">Toutes les réservations</a>
  </div>
</div>

{{-- KPI ─────────────────────────────────────────────────────── --}}
<div class="kpi-grid" style="margin-bottom:2rem">
  <div class="kpi-card green">
    <div class="kpi-label">RDV aujourd'hui</div>
    <div class="kpi-value">{{ $rdvAujourdhui->count() }}</div>
    <div class="kpi-delta">{{ now()->translatedFormat('l') }}</div>
    <div class="kpi-icon">&#128197;</div>
  </div>
  <div class="kpi-card yellow">
    <div class="kpi-label">En attente</div>
    <div class="kpi-value">{{ $enAttente->count() }}</div>
    <div class="kpi-delta {{ $enAttente->count() > 0 ? '' : '' }}">À confirmer</div>
    <div class="kpi-icon">&#9203;</div>
  </div>
  <div class="kpi-card blue">
    <div class="kpi-label">RDV cette semaine</div>
    <div class="kpi-value">{{ $rdvSemaine }}</div>
    <div class="kpi-delta">{{ now()->translatedFormat('d M') }} – {{ now()->endOfWeek()->translatedFormat('d M') }}</div>
    <div class="kpi-icon">&#9728;</div>
  </div>
  <div class="kpi-card sage">
    <div class="kpi-label">CA semaine (est.)</div>
    <div class="kpi-value" style="font-size:1.6rem">{{ number_format($caSemaine, 0, ',', ' ') }}</div>
    <div class="kpi-delta">MAD · prestations terminées</div>
    <div class="kpi-icon">&#9733;</div>
  </div>
</div>

<div class="dashboard-layout" style="max-width:100%;margin:0 0 4rem">
  <div>

    {{-- RDV en attente ──────────────────────────────────────── --}}
    @if($enAttente->count())
      <div class="section-header" style="margin-bottom:1rem">
        <h2 style="font-family:var(--fh);font-size:1.4rem;color:var(--ink-h)">
          &#9888; En attente de confirmation
          <span style="font-size:.8rem;font-weight:600;background:#FCECC0;color:#6A3800;padding:.15rem .6rem;margin-left:.5rem">{{ $enAttente->count() }}</span>
        </h2>
      </div>
      <div class="bookings-table" style="margin-bottom:2rem">
        <table>
          <thead><tr>
            <th>Client</th><th>Service</th><th>Date &amp; Heure</th><th>Durée</th><th>Actions</th>
          </tr></thead>
          <tbody>
            @foreach($enAttente as $r)
              <tr>
                <td><strong>{{ $r->client->nomComplet() }}</strong><div class="booking-phone">{{ $r->client->telephone }}</div></td>
                <td>{{ $r->service->nom_service }}</td>
                <td><span class="booking-time">{{ $r->date_heure->format('H:i') }}</span><div class="booking-duration">{{ $r->date_heure->translatedFormat('D d M') }}</div></td>
                <td>{{ $r->service->duree_formatee }}</td>
                <td>
                  <div style="display:flex;gap:.4rem">
                    <form method="POST" action="{{ route('salon.reservations.confirmer', $r->id) }}">
                      @csrf
                      <button class="action-btn" title="Confirmer" style="background:var(--p2);border-color:var(--p4);color:var(--p4dd)">&#10003;</button>
                    </form>
                    <a href="{{ route('salon.reservations.show', $r->id) }}" class="action-btn" title="Détail">&#8594;</a>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif

    {{-- Programme du jour ──────────────────────────────────── --}}
    <div class="section-header">
      <h2 style="font-family:var(--fh);font-size:1.4rem;color:var(--ink-h)">Programme du jour</h2>
      <a href="{{ route('salon.disponibilites.index') }}" class="section-link">Calendrier complet</a>
    </div>

    <div class="bookings-table" style="margin-bottom:2rem">
      <table>
        <thead><tr>
          <th>Heure</th><th>Client</th><th>Service</th><th>Styliste</th><th>Statut</th><th></th>
        </tr></thead>
        <tbody>
          @forelse($rdvAujourdhui as $r)
            <tr>
              <td><span class="booking-time">{{ $r->date_heure->format('H:i') }}</span></td>
              <td><strong>{{ $r->client->nomComplet() }}</strong><div class="booking-phone">{{ $r->client->telephone }}</div></td>
              <td>{{ $r->service->nom_service }}<div class="booking-duration">{{ $r->service->duree_formatee }}</div></td>
              <td>{{ $r->employe?->nomComplet() ?? '—' }}</td>
              <td>
                @php $sc = $r->statut === 'confirmee' ? 'confirmed' : 'pending'; @endphp
                <span class="status-badge {{ $sc }}">
                  <span class="status-dot"></span>
                  {{ $r->statut === 'confirmee' ? 'Confirmé' : 'En attente' }}
                </span>
              </td>
              <td><a href="{{ route('salon.reservations.show', $r->id) }}" class="action-btn">&#8594;</a></td>
            </tr>
          @empty
            <tr><td colspan="6" style="text-align:center;color:var(--ink-m);padding:2rem">Aucun rendez-vous aujourd'hui.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Graphique 7 jours ──────────────────────────────────── --}}
    <div class="chart-card">
      <div class="chart-title">Réservations — 7 derniers jours</div>
      @php $max = collect($chartData)->max('value') ?: 1; @endphp
      <div class="bar-chart">
        @foreach($chartData as $d)
          <div class="bar-column">
            <div class="bar-fill {{ $d['today'] ? 'highlighted' : '' }}"
                 style="height:{{ round($d['value'] / $max * 100) }}%"
                 data-value="{{ $d['value'] }} RDV"></div>
            <div class="bar-day-label">{{ $d['label'] }}</div>
          </div>
        @endforeach
      </div>
    </div>

  </div>

  {{-- Sidebar ─────────────────────────────────────────────── --}}
  <div>

    {{-- Prochains 7 jours --}}
    <div class="team-card" style="margin-bottom:1.5rem">
      <div class="team-card-head"><div class="team-card-title">Prochains RDV</div></div>
      @forelse($prochains->take(5) as $r)
        <div class="team-member">
          <div class="member-avatar">
            <img src="https://ui-avatars.com/api/?name={{ urlencode($r->client->nomComplet()) }}&background=9CAB84&color=fff&size=40" alt="">
          </div>
          <div>
            <div class="member-name">{{ $r->client->nomComplet() }}</div>
            <div class="member-role">{{ $r->service->nom_service }} · {{ $r->date_heure->translatedFormat('D d M') }} {{ $r->date_heure->format('H:i') }}</div>
          </div>
          @if($r->employe)
            <span class="member-bookings">{{ $r->employe->prenom }}</span>
          @endif
        </div>
      @empty
        <div style="padding:1rem 1.4rem;font-size:.82rem;color:var(--ink-m)">Aucun RDV à venir.</div>
      @endforelse
    </div>

    {{-- Top services --}}
    <div class="services-list">
      <div class="services-list-head">Top services</div>
      @forelse($topServices as $t)
        <div class="service-item">
          <div>
            <div class="service-name">{{ $t->service->nom_service }}</div>
            <div class="service-meta">{{ $t->service->duree_formatee }} · {{ $t->service->prix_format }}</div>
          </div>
          <div>
            <div class="service-count">{{ $t->total }}</div>
            <div class="service-percent">
              {{ $totalTerminees > 0 ? round($t->total / $totalTerminees * 100) : 0 }}%
            </div>
            <div class="service-bar-track">
              <div class="service-bar-fill" style="width:{{ $totalTerminees > 0 ? round($t->total / $totalTerminees * 100) : 0 }}%"></div>
            </div>
          </div>
        </div>
      @empty
        <div style="padding:1rem 1.4rem;font-size:.82rem;color:var(--ink-m)">Aucune donnée.</div>
      @endforelse
    </div>

    {{-- Derniers avis --}}
    @if($derniersAvis->count())
      <div class="sidebar-alert" style="margin-top:1.5rem">
        <div class="alert-icon">&#9733;</div>
        <div>
          <div class="alert-title">{{ $derniersAvis->count() }} avis récents</div>
          @foreach($derniersAvis as $a)
            <div class="oa">
              <div class="oa-top">
                <span class="oa-name">{{ $a->reservation->client?->prenom ?? 'Client' }}</span>
                <span class="oa-stars">{{ str_repeat('★',$a->note) }}</span>
              </div>
              @if($a->commentaire)
                <div class="oa-txt">"{{ Str::limit($a->commentaire, 60) }}"</div>
              @endif
            </div>
          @endforeach
          <a href="{{ route('salon.avis.index') }}" class="alert-link">Voir tous les avis</a>
        </div>
      </div>
    @endif

  </div>
</div>
@endsection
