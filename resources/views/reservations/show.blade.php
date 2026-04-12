@extends('layouts.dashboard')
@section('title', 'Détail réservation')

@section('content')

<div class="dash-page-header">
  <div>
    <div class="dash-greeting">Réservation #SAL-{{ str_pad($reservation->id, 6, '0', STR_PAD_LEFT) }}</div>
    <div class="dash-date">{{ $reservation->date_heure->translatedFormat('l d F Y') }} à {{ $reservation->date_heure->format('H:i') }}</div>
  </div>
  <a href="{{ route('client.reservations.index') }}" class="btn-new-booking">&#8592; Mes réservations</a>
</div>

<div style="max-width:820px">

  {{-- Statut ──────────────────────────────────────────────────── --}}
  @php
    $statusColors = [
      'en_attente' => 'pending',
      'confirmee'  => 'confirmed',
      'terminee'   => 'confirmed',
      'annulee'    => 'cancelled',
    ];
    $statusLabels = [
      'en_attente' => 'En attente de confirmation',
      'confirmee'  => 'Confirmée',
      'terminee'   => 'Terminée',
      'annulee'    => 'Annulée',
    ];
  @endphp
  <div style="margin-bottom:2rem;padding:1.2rem 1.6rem;background:var(--p1);border:2px solid var(--p3);display:flex;align-items:center;gap:1rem">
    <span class="status-pill {{ $statusColors[$reservation->statut] ?? 'pending' }}" style="font-size:.86rem">
      <span class="status-dot"></span>{{ $statusLabels[$reservation->statut] ?? $reservation->statut }}
    </span>
    @if($reservation->statut === 'en_attente')
      <span style="font-size:.78rem;color:var(--ink-m)">Le salon confirmera votre réservation sous 2h.</span>
    @endif
  </div>

  {{-- Détails réservation ──────────────────────────────────────── --}}
  <div class="recap-card" style="margin-bottom:1.5rem">
    <div class="recap-head">
      <div class="recap-head-title">Détails</div>
    </div>

    <div class="recap-salon">
      <div class="recap-salon-photo">
        <img src="{{ $reservation->salon->photo_url }}" alt="{{ $reservation->salon->nom_salon }}">
      </div>
      <div>
        <div class="salon-name">{{ $reservation->salon->nom_salon }}</div>
        <div class="salon-address">{{ $reservation->salon->adresse }}, {{ $reservation->salon->quartier }}, {{ $reservation->salon->ville->nom_ville }}</div>
        <div class="salon-rating">&#9733; {{ number_format($reservation->salon->note_moy, 1) }} · {{ $reservation->salon->nb_avis }} avis</div>
      </div>
    </div>

    <div class="recap-details-grid">
      <div class="recap-detail-cell">
        <div class="detail-label">Service</div>
        <div class="detail-value">{{ $reservation->service->nom_service }}</div>
        <div class="detail-sub">{{ $reservation->service->duree_formatee }}</div>
      </div>
      <div class="recap-detail-cell">
        <div class="detail-label">Date &amp; Heure</div>
        <div class="detail-value">{{ $reservation->date_heure->translatedFormat('D d M Y') }}</div>
        <div class="detail-sub">{{ $reservation->date_heure->format('H:i') }}</div>
      </div>
      <div class="recap-detail-cell">
        <div class="detail-label">Styliste</div>
        <div class="detail-value">{{ $reservation->employe?->nomComplet() ?? 'Au choix' }}</div>
      </div>
    </div>

    @if($reservation->notes_client)
      <div style="padding:1rem 1.4rem;background:rgba(197,216,157,.12);border-top:1px solid var(--border2);font-size:.82rem;color:var(--ink-s)">
        <strong>Votre message :</strong> {{ $reservation->notes_client }}
      </div>
    @endif

    <div class="recap-total">
      <div>
        <div class="total-label">Montant à payer au salon</div>
        <div class="total-note">Paiement en espèces ou carte directement sur place</div>
      </div>
      <div class="total-amount">{{ $reservation->service->prix_format }}</div>
    </div>
  </div>

  {{-- Actions ──────────────────────────────────────────────────── --}}
  <div class="confirm-actions" style="margin-bottom:2rem">
    <a href="{{ route('salons.show', [$reservation->salon->ville->nom_ville, $reservation->salon->slug]) }}"
       class="btn-dark">Voir le salon</a>
    @if($reservation->statut === 'terminee' && !$reservation->avis)
      <a href="{{ route('avis.create', $reservation->id) }}" class="btn-outline-green">Laisser un avis &#9733;</a>
    @endif
    @if(in_array($reservation->statut, ['en_attente','confirmee']) && $reservation->peutEtreAnnulee())
      <form method="POST" action="{{ route('reservations.annuler', $reservation->id) }}"
            onsubmit="return confirm('Confirmer l\'annulation ?')" style="display:inline">
        @csrf
        <button type="submit" style="background:none;border:none;font-size:.82rem;font-weight:700;color:#8B2222;text-decoration:underline;cursor:pointer;font-family:var(--fb)">
          Annuler la réservation
        </button>
      </form>
    @endif
  </div>

</div>
@endsection
