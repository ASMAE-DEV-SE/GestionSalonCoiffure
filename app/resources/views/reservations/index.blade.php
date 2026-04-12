@extends('layouts.dashboard')
@section('title', 'Mes réservations')

@section('content')

{{-- ── Header client ───────────────────────────────────────── --}}
<div class="client-header">
  <div class="wrap client-header-inner">
    <div class="client-avatar">
      <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->nomComplet()) }}&background=9CAB84&color=fff&size=96"
           alt="{{ auth()->user()->nomComplet() }}">
    </div>
    <div>
      <div class="client-name">{{ auth()->user()->nomComplet() }}</div>
      <div class="client-subtitle">Espace client · Mes réservations</div>
    </div>
    <div class="client-header-actions">
      <a href="{{ route('villes.index') }}" class="btn-header-green">+ Nouvelle réservation</a>
    </div>
  </div>
</div>

{{-- ── Sous-navigation ─────────────────────────────────────── --}}
<div class="client-subnav">
  <div class="client-subnav-inner wrap">
    <a href="{{ route('client.dashboard') }}" class="subnav-tab">Tableau de bord</a>
    <a href="{{ route('client.reservations.index') }}" class="subnav-tab active">Réservations</a>
    <a href="{{ route('client.profil.edit') }}" class="subnav-tab">Mon profil</a>
    <a href="{{ route('client.notifications.index') }}" class="subnav-tab">Notifications</a>
  </div>
</div>

<div class="reservations-layout">
  <div>

    {{-- Filtres --}}
    <div class="filter-tabs">
      @php $statuts = [''=>'Toutes', 'en_attente'=>'En attente', 'confirmee'=>'Confirmées', 'terminee'=>'Terminées', 'annulee'=>'Annulées']; @endphp
      @foreach($statuts as $val => $lbl)
        <a href="{{ request()->fullUrlWithQuery(['statut' => $val]) }}"
           class="filter-tab {{ request('statut', '') === $val ? 'active' : '' }}">
          {{ $lbl }}
        </a>
      @endforeach
    </div>

    {{-- Liste --}}
    @forelse($reservations as $resa)
      <div class="reservation-card {{ in_array($resa->statut, ['terminee','annulee']) ? $resa->statut : '' }}">

        <div class="reservation-card-top">
          <div class="reservation-salon-photo">
            <img src="{{ $resa->salon->photo_url }}" alt="{{ $resa->salon->nom_salon }}">
          </div>
          <div>
            <div class="reservation-salon-name">{{ $resa->salon->nom_salon }}</div>
            <div class="reservation-salon-location">&#128205; {{ $resa->salon->quartier }}, {{ $resa->salon->ville?->nom_ville }}</div>
          </div>
          <div>
            @php
              $pillClass = match($resa->statut) {
                'confirmee'  => 'confirmed',
                'en_attente' => 'pending',
                'terminee'   => 'done',
                'annulee'    => 'cancelled',
                default      => 'pending',
              };
              $pillLabel = match($resa->statut) {
                'confirmee'  => 'Confirmée',
                'en_attente' => 'En attente',
                'terminee'   => 'Terminée',
                'annulee'    => 'Annulée',
                default      => $resa->statut,
              };
            @endphp
            <span class="status-pill {{ $pillClass }}">
              <span class="status-dot"></span>{{ $pillLabel }}
            </span>
          </div>
        </div>

        <div class="reservation-details">
          <div>
            <div class="detail-cell-label">Service</div>
            <div class="detail-cell-value">{{ $resa->service->nom_service }}</div>
          </div>
          <div>
            <div class="detail-cell-label">Date</div>
            <div class="detail-cell-value">{{ $resa->date_heure->translatedFormat('D d M Y') }}</div>
          </div>
          <div>
            <div class="detail-cell-label">Heure</div>
            <div class="detail-cell-value">{{ $resa->date_heure->format('H:i') }}</div>
          </div>
          <div>
            <div class="detail-cell-label">Styliste</div>
            <div class="detail-cell-value">{{ $resa->employe?->nomComplet() ?? 'Au choix' }}</div>
          </div>
        </div>

        <div class="reservation-card-footer">
          <div class="reservation-price">
            {{ $resa->service->prix_format }}
            <span>· {{ $resa->service->duree_formatee }}</span>
          </div>
          <div class="card-actions">
            {{-- Avis si terminée et pas encore d'avis --}}
            @if($resa->statut === 'terminee' && ! $resa->avis)
              <a href="{{ route('avis.create', $resa->id) }}" class="btn-card btn-card-green">&#9733; Laisser un avis</a>
            @endif
            @if($resa->statut === 'terminee' && $resa->avis)
              <span class="btn-card" style="cursor:default;opacity:.6;border-color:var(--border)">Avis publié &#10003;</span>
            @endif
            {{-- Annulation --}}
            @if(in_array($resa->statut, ['en_attente','confirmee']))
              <form method="POST" action="{{ route('reservations.annuler', $resa->id) }}"
                    onsubmit="return confirm('Annuler cette réservation ?')">
                @csrf
                <button type="submit" class="btn-card btn-card-danger">Annuler</button>
              </form>
            @endif
          </div>
        </div>

      </div>
    @empty
      <div style="padding:4rem 0;text-align:center">
        <div style="font-size:3rem;margin-bottom:1rem">&#128197;</div>
        <p style="color:var(--ink-m);font-size:.9rem;margin-bottom:1.5rem">Aucune réservation pour l'instant.</p>
        <a href="{{ route('villes.index') }}" class="btn-ol" style="font-size:.78rem">Trouver un salon</a>
      </div>
    @endforelse

    {{ $reservations->links() }}

  </div>

  {{-- Sidebar --}}
  <div>
    <div class="profile-sidebar-card">
      <div class="sidebar-avatar">
        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->nomComplet()) }}&background=9CAB84&color=fff&size=96" alt="">
      </div>
      <div class="sidebar-name">{{ auth()->user()->nomComplet() }}</div>
      <div class="sidebar-email">{{ auth()->user()->email }}</div>
      @php
        $total    = auth()->user()->reservations()->count();
        $aVenir   = auth()->user()->reservations()->whereIn('statut',['en_attente','confirmee'])->where('date_heure','>=',now())->count();
        $terminees = auth()->user()->reservations()->where('statut','terminee')->count();
      @endphp
      <div class="sidebar-stat"><span class="sidebar-stat-key">Total réservations</span><span class="sidebar-stat-value">{{ $total }}</span></div>
      <div class="sidebar-stat"><span class="sidebar-stat-key">À venir</span><span class="sidebar-stat-value">{{ $aVenir }}</span></div>
      <div class="sidebar-stat"><span class="sidebar-stat-key">Terminées</span><span class="sidebar-stat-value">{{ $terminees }}</span></div>
      <div class="sidebar-cta"><a href="{{ route('villes.index') }}">+ Nouvelle réservation</a></div>
    </div>
  </div>
</div>
@endsection
