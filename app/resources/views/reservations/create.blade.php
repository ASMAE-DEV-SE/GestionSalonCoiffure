@extends('layouts.app')
@section('title', 'Réservation — Vos informations')

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
    <div class="step done"><div class="step-dot">&#10003;</div><div class="step-label">Créneau</div></div>
    <div class="step current"><div class="step-dot">3</div><div class="step-label">Vos infos</div></div>
    <div class="step"><div class="step-dot">4</div><div class="step-label">Confirmation</div></div>
  </div>
</div>

<div class="wizard-layout">
  <div>

    {{-- Étapes complétées --}}
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

    <div class="completed-step" style="margin-bottom:1.5rem">
      <div>
        <div class="completed-step-label">Créneau choisi</div>
        <div class="completed-step-value">
          {{ \Carbon\Carbon::parse($sessionData['date_heure'])->translatedFormat('l d F Y') }}
        </div>
        <div class="completed-step-meta">
          {{ \Carbon\Carbon::parse($sessionData['date_heure'])->format('H:i') }}
          @if($employe) · {{ $employe->nomComplet() }} @endif
        </div>
      </div>
      <div class="completed-step-right">
        <a href="{{ route('reservations.step2', $salonModel->slug) }}" class="btn-edit-step">Modifier</a>
      </div>
    </div>

    {{-- Formulaire informations --}}
    <div class="form-card">
      <div class="form-card-title">Vos informations</div>
      <div class="form-card-subtitle">Vérifiez vos coordonnées avant de confirmer la réservation.</div>

      <form method="POST" action="{{ route('reservations.store', $salonModel->slug) }}" id="reservationForm">
        @csrf
        <input type="hidden" name="service_id"    value="{{ $service->id }}">
        <input type="hidden" name="employe_id"    value="{{ $employe?->id }}">
        <input type="hidden" name="date_heure"    value="{{ $sessionData['date_heure'] }}">
        <input type="hidden" name="duree_minutes" value="{{ $service->duree_minu }}">

        <div class="row-two-col">
          <div class="form-group">
            <label>Prénom</label>
            <input type="text" class="form-input" value="{{ $user->prenom }}" readonly
                   style="background:#FDFAF5;color:var(--ink-m)">
          </div>
          <div class="form-group">
            <label>Nom</label>
            <input type="text" class="form-input" value="{{ $user->nom }}" readonly
                   style="background:#FDFAF5;color:var(--ink-m)">
          </div>
        </div>

        <div class="row-two-col">
          <div class="form-group">
            <label>Email</label>
            <input type="email" class="form-input" value="{{ $user->email }}" readonly
                   style="background:#FDFAF5;color:var(--ink-m)">
          </div>
          <div class="form-group">
            <label>Téléphone</label>
            <input type="tel" class="form-input" value="{{ $user->telephone }}" readonly
                   style="background:#FDFAF5;color:var(--ink-m)">
          </div>
        </div>

        <div class="form-group">
          <label>Message pour le salon <span class="optional">(optionnel)</span></label>
          <textarea name="notes_client" class="form-input" rows="3"
                    placeholder="Ex : cheveux longs, allergie à certains produits...">{{ old('notes_client') }}</textarea>
          @error('notes_client')
            <div style="color:#C04A3D;font-size:.76rem;margin-top:.35rem">{{ $message }}</div>
          @enderror
        </div>

        {{-- Récap total --}}
        <div class="total-box">
          <div class="total-row">
            <span class="total-key">{{ $service->nom_service }}</span>
            <span class="total-value">{{ $service->prix_format }}</span>
          </div>
          <div class="total-row">
            <span class="total-key">Frais de réservation</span>
            <span class="total-value free">Gratuit</span>
          </div>
          <div class="total-final">
            <span class="total-final-label">Total à payer au salon</span>
            <span class="total-final-amount">{{ $service->prix_format }}</span>
          </div>
        </div>

        {{-- CGV --}}
        <div class="checkbox-row">
          <input type="checkbox" id="cgv" required>
          <label for="cgv">
            J'accepte les <a href="#" style="color:var(--p4d)">conditions générales</a>
            et la politique d'annulation (gratuite jusqu'à 24h avant le RDV).
          </label>
        </div>

        <div class="wizard-navigation">
          <a href="{{ route('reservations.step2', $salonModel->slug) }}" class="btn-wizard-back">&#8592; Créneau</a>
          <button type="submit" class="btn-wizard-confirm">Confirmer la réservation &#10003;</button>
        </div>
      </form>
    </div>

  </div>

  {{-- Récap sidebar --}}
  <div class="recap-sidebar">
    <div class="recap-header">
      <div class="recap-header-title">Récapitulatif</div>
    </div>
    <div class="recap-body">
      <div class="recap-salon">
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
      <div class="recap-row">
        <span class="recap-key">Date</span>
        <span class="recap-value">{{ \Carbon\Carbon::parse($sessionData['date_heure'])->translatedFormat('D d M') }}</span>
      </div>
      <div class="recap-row">
        <span class="recap-key">Heure</span>
        <span class="recap-value">{{ \Carbon\Carbon::parse($sessionData['date_heure'])->format('H:i') }}</span>
      </div>
      @if($employe)
        <div class="recap-row"><span class="recap-key">Styliste</span><span class="recap-value">{{ $employe->nomComplet() }}</span></div>
      @endif
      <div class="recap-total-row">
        <span class="recap-total-label">Total</span>
        <span class="recap-total-amount">{{ $service->prix_format }}</span>
      </div>
      <div style="margin-top:1rem;padding:.9rem;background:rgba(197,216,157,.15);border:1px solid var(--p2);font-size:.74rem;color:var(--ink-s);line-height:1.7">
        &#128197; Paiement au salon · Annulation gratuite 24h avant
      </div>
    </div>
  </div>
</div>
@endsection
