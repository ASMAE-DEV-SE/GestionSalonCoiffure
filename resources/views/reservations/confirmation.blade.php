@extends('layouts.app')
@section('title', 'Réservation confirmée')

@section('content')

{{-- ── Stepper (toutes étapes done) ───────────────────────── --}}
<div class="stepper-bar-wrap">
  <div class="stepper-bar">
    <div class="step done"><div class="step-dot">&#10003;</div><div class="step-label">Service</div></div>
    <div class="step done"><div class="step-dot">&#10003;</div><div class="step-label">Créneau</div></div>
    <div class="step done"><div class="step-dot">&#10003;</div><div class="step-label">Vos infos</div></div>
    <div class="step done"><div class="step-dot">&#10003;</div><div class="step-label">Confirmation</div></div>
  </div>
</div>

{{-- ── Hero succès ────────────────────────────────────────── --}}
<div class="confirm-hero">
  <div class="wrap">
    <div class="confirm-pulse"><div class="confirm-check">&#10003;</div></div>
    <h1 class="confirm-title">Réservation <em>effectuée</em> !</h1>
    <p class="confirm-desc">
      Votre rendez-vous a bien été enregistré. Vous receffectuéeevrez une confirmation par email
      et un rappel SMS 24h avant.
    </p>
    <div>
      <span class="confirm-ref-label">Référence de réservation</span>
      <span class="confirm-ref">#SAL-{{ str_pad($reservation->id, 6, '0', STR_PAD_LEFT) }}</span>
    </div>
  </div>
</div>

{{-- ── Layout détail ────────────────────────────────────────── --}}
<div class="confirm-layout">
  <div>

    {{-- Notification email --}}
    <div class="notif-banner">
      <div class="notif-icon">&#9993;</div>
      <div>
        <div class="notif-title">Confirmation envoyée à {{ auth()->user()->email }}</div>
        <p class="notif-text">
          Un récapitulatif complet avec les informations du salon vous a été envoyé par email.
          Vérifiez vos spams si vous ne le trouvez pas.
        </p>
      </div>
    </div>

    {{-- Récap réservation --}}
    <div class="recap-card">
      <div class="recap-head">
        <div class="recap-head-title">Détails de votre réservation</div>
      </div>

      <div class="recap-salon">
        <div class="recap-salon-photo">
          <img src="{{ $reservation->salon->photo_url }}" alt="{{ $reservation->salon->nom_salon }}">
        </div>
        <div>
          <div class="salon-name">{{ $reservation->salon->nom_salon }}</div>
          <div class="salon-address">{{ $reservation->salon->adresse }}, {{ $reservation->salon->quartier }}</div>
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
          <div class="detail-sub">Professionnel(le) certifié(e)</div>
        </div>
      </div>

      <div class="recap-client">
        <div>
          <div class="client-label">Client</div>
          <div class="client-value">{{ auth()->user()->nomComplet() }}</div>
        </div>
        <div>
          <div class="client-label">Statut</div>
          <div class="client-value">
            <span class="bge-wa">En attente de confirmation salon</span>
          </div>
        </div>
      </div>

      <div class="recap-total">
        <div>
          <div class="total-label">Montant à payer au salon</div>
          <div class="total-note">Paiement en espèces ou carte directement sur place</div>
        </div>
        <div class="total-amount">{{ $reservation->service->prix_format }}</div>
      </div>
    </div>

    {{-- Actions --}}
    <div class="confirm-actions">
      <a href="{{ route('client.reservations.index') }}" class="btn-dark">Mes réservations</a>
      <a href="{{ route('salons.index', $reservation->salon->ville->nom_ville) }}" class="btn-outline-green">
        Autres salons à {{ $reservation->salon->ville->nom_ville }}
      </a>
      <a href="{{ route('home') }}" class="btn-outline-light">Retour à l'accueil</a>
    </div>

    {{-- Prochaines étapes --}}
    <div class="next-steps-card">
      <div class="next-steps-head"><div class="next-steps-title">Et maintenant ?</div></div>
      <div class="next-step-item">
        <div class="step-number">1</div>
        <div>
          <div class="step-item-title">Confirmation du salon</div>
          <div class="step-item-desc">Le salon examinera votre demande et vous enverra une confirmation sous 2h.</div>
        </div>
      </div>
      <div class="next-step-item">
        <div class="step-number">2</div>
        <div>
          <div class="step-item-title">Rappel SMS 24h avant</div>
          <div class="step-item-desc">Vous recevrez un SMS de rappel la veille de votre rendez-vous.</div>
        </div>
      </div>
      <div class="next-step-item">
        <div class="step-number">3</div>
        <div>
          <div class="step-item-title">Votre rendez-vous beauté</div>
          <div class="step-item-desc">Présentez-vous 5 min avant l'heure. Le paiement s'effectue sur place.</div>
        </div>
      </div>
      <div class="next-step-item">
        <div class="step-number">4</div>
        <div>
          <div class="step-item-title">Laissez un avis</div>
          <div class="step-item-desc">Après votre visite, partagez votre expérience pour aider la communauté Salonify.</div>
        </div>
      </div>
    </div>

  </div>

  {{-- Sidebar carte --}}
  <div>
    <div class="sidebar-map">
      <div class="map-image">
        <img src="https://images.unsplash.com/photo-1555448248-2571daf6344b?w=400&h=200&fit=crop&q=80" alt="Carte">
        <div class="map-overlay">
          <span class="map-location-label">&#128205; {{ $reservation->salon->quartier }}, {{ $reservation->salon->ville->nom_ville }}</span>
        </div>
      </div>
      <div class="map-body">
        <div class="map-salon-name">{{ $reservation->salon->nom_salon }}</div>
        <div class="map-info-row">
          <span class="map-icon">&#128205;</span>
          <span class="map-info-text">{{ $reservation->salon->adresse }}</span>
        </div>
        @if($reservation->salon->telephone)
          <div class="map-info-row">
            <span class="map-icon">&#128222;</span>
            <span class="map-info-text">{{ $reservation->salon->telephone }}</span>
          </div>
        @endif
        <div class="map-cta">
          <a href="https://maps.google.com/?q={{ urlencode($reservation->salon->adresse . ', ' . $reservation->salon->ville->nom_ville) }}"
             target="_blank">Ouvrir dans Maps</a>
        </div>
      </div>
    </div>

    {{-- Annulation --}}
    <div class="cancellation-box">
      <div class="cancellation-title">Politique d'annulation</div>
      <p class="cancellation-text">
        Annulation gratuite jusqu'à 24h avant votre rendez-vous.
        Au-delà, l'annulation peut entraîner des frais selon le salon.
      </p>
      <div style="margin-top:.9rem">
        @if($reservation->peutEtreAnnulee())
          <form method="POST" action="{{ route('reservations.annuler', $reservation->id) }}"
                onsubmit="return confirm('Confirmer l\'annulation de cette réservation ?')">
            @csrf
            <button type="submit" style="background:none;border:none;font-size:.76rem;font-weight:700;color:#8B2222;text-decoration:underline;cursor:pointer;font-family:var(--fb)">
              Annuler cette réservation
            </button>
          </form>
        @else
          <div style="font-size:.84rem;color:#6E6B66;">Cette réservation n'est plus annulable car elle est à moins de 24h ou le rendez-vous est déjà passé.</div>
        @endif
      </div>
    </div>
  </div>

</div>
@endsection
