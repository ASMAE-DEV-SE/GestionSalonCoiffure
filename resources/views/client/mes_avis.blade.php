@extends('layouts.dashboard')
@section('title', 'Mes avis')

@section('content')

{{-- ── Header ─────────────────────────────────────────────── --}}
<div class="client-header">
  <div class="wrap client-header-inner">
    <div class="client-avatar">
      <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->nomComplet()) }}&background=9CAB84&color=fff&size=96"
           alt="{{ auth()->user()->nomComplet() }}">
    </div>
    <div>
      <div class="client-name">{{ auth()->user()->nomComplet() }}</div>
      <div class="client-subtitle">Espace client · Mes avis</div>
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
    <a href="{{ route('client.reservations.index') }}" class="subnav-tab">Réservations</a>
    <a href="{{ route('client.avis.index') }}" class="subnav-tab active">Mes avis</a>
    <a href="{{ route('client.profil.edit') }}" class="subnav-tab">Mon profil</a>
    <a href="{{ route('client.notifications.index') }}" class="subnav-tab">Notifications</a>
  </div>
</div>

<div class="reservations-layout">
  <div>

    {{-- Liste des avis --}}
    @forelse($avis as $a)
      @php
        $salon      = $a->reservation->salon;
        $service    = $a->reservation->service;
        $dateVisite = $a->reservation->date_heure;
      @endphp

      <div class="reservation-card" style="margin-bottom:1.2rem">

        {{-- Salon info --}}
        <div class="reservation-card-top">
          <div class="reservation-salon-photo">
            <img src="{{ $salon->photo_url }}" alt="{{ $salon->nom_salon }}">
          </div>
          <div>
            <div class="reservation-salon-name">
              <a href="{{ route('salons.show', [$salon->ville->nom_ville, $salon->slug]) }}"
                 style="color:inherit;text-decoration:none">{{ $salon->nom_salon }}</a>
            </div>
            <div class="reservation-salon-location">
              &#128205; {{ $salon->quartier }}, {{ $salon->ville->nom_ville }}
            </div>
          </div>
          <div>
            <span style="font-size:1.1rem;color:#C5A96D;letter-spacing:1px">
              {{ str_repeat('★', $a->note) }}{{ str_repeat('☆', 5 - $a->note) }}
            </span>
          </div>
        </div>

        {{-- Détails --}}
        <div class="reservation-details">
          <div>
            <div class="detail-cell-label">Service</div>
            <div class="detail-cell-value">{{ $service->nom_service }}</div>
          </div>
          <div>
            <div class="detail-cell-label">Date de visite</div>
            <div class="detail-cell-value">{{ $dateVisite->translatedFormat('d M Y') }}</div>
          </div>
          <div>
            <div class="detail-cell-label">Avis publié le</div>
            <div class="detail-cell-value">{{ $a->created_at->translatedFormat('d M Y') }}</div>
          </div>
          <div>
            <div class="detail-cell-label">Réponse salon</div>
            <div class="detail-cell-value">
              @if($a->reponse_salon)
                <span style="color:var(--p4d);font-weight:700">&#10003; Reçue</span>
              @else
                <span style="color:var(--ink-m)">En attente</span>
              @endif
            </div>
          </div>
        </div>

        {{-- Commentaire du client --}}
        @if($a->commentaire)
          <div style="padding:.9rem 1.4rem;border-top:1px solid var(--border2);font-size:.84rem;color:var(--ink-s);font-style:italic;line-height:1.6">
            "{{ $a->commentaire }}"
          </div>
        @endif

        {{-- Réponse du salon --}}
        @if($a->reponse_salon)
          <div style="padding:.9rem 1.4rem;background:var(--p1);border-top:1px solid var(--border2)">
            <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--p4d);margin-bottom:.4rem">
              Réponse de {{ $salon->nom_salon }}
            </div>
            <div style="font-size:.83rem;color:var(--ink-s);line-height:1.6">
              {{ $a->reponse_salon }}
            </div>
          </div>
        @endif

      </div>
    @empty
      <div style="padding:4rem 0;text-align:center;border:2px dashed var(--border2)">
        <div style="font-size:2.5rem;margin-bottom:1rem">&#9733;</div>
        <p style="color:var(--ink-m);font-size:.9rem;margin-bottom:1.2rem">
          Vous n'avez pas encore publié d'avis.
        </p>
        <a href="{{ route('villes.index') }}" class="btn-ol" style="font-size:.76rem">
          Trouver un salon
        </a>
      </div>
    @endforelse

    <div style="margin-top:1.2rem">{{ $avis->links() }}</div>

  </div>

  {{-- Sidebar --}}
  <div>
    <div class="profile-sidebar-card">
      <div class="sidebar-avatar">
        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->nomComplet()) }}&background=9CAB84&color=fff&size=96" alt="">
      </div>
      <div class="sidebar-name">{{ auth()->user()->nomComplet() }}</div>
      <div class="sidebar-email">{{ auth()->user()->email }}</div>
      <div class="sidebar-stat">
        <span class="sidebar-stat-key">Avis publiés</span>
        <span class="sidebar-stat-value">{{ $avis->total() }}</span>
      </div>
      <div class="sidebar-stat">
        <span class="sidebar-stat-key">Avec réponse salon</span>
        <span class="sidebar-stat-value">{{ $avis->getCollection()->filter(fn($a) => $a->reponse_salon)->count() }}</span>
      </div>
      <div class="sidebar-cta">
        <a href="{{ route('client.reservations.index') }}">Mes réservations</a>
      </div>
    </div>
  </div>
</div>

@endsection
