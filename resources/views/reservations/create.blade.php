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
    <div class="step done"><div class="step-dot">&#10003;</div><div class="step-label">Services</div></div>
    <div class="step done"><div class="step-dot">&#10003;</div><div class="step-label">Créneaux</div></div>
    <div class="step current"><div class="step-dot">3</div><div class="step-label">Vos infos</div></div>
    <div class="step"><div class="step-dot">4</div><div class="step-label">Confirmation</div></div>
  </div>
</div>

<div class="wizard-layout">
  <div>

    {{-- Liste des prestations choisies --}}
    <div class="form-card" style="padding:1.4rem;margin-bottom:1.5rem">
      <div class="form-card-title">Récapitulatif des prestations</div>
      <div style="margin-top:1rem">
        @foreach($items as $item)
          <div style="display:flex;justify-content:space-between;align-items:flex-start;padding:.9rem 0;border-bottom:1.5px solid var(--border2)">
            <div>
              <div style="font-weight:700;color:var(--ink-d);font-size:.94rem">{{ $item['service']->nom_service }}</div>
              <div style="font-size:.76rem;color:var(--ink-m);margin-top:.25rem">
                {{ \Carbon\Carbon::parse($item['date_heure'])->translatedFormat('l d F Y') }}
                · {{ \Carbon\Carbon::parse($item['date_heure'])->format('H:i') }}
                · {{ $item['service']->duree_formatee }}
                @if($item['employe']) · {{ $item['employe']->nomComplet() }} @else · Styliste au choix @endif
              </div>
            </div>
            <div style="font-family:var(--fh);font-weight:700;color:var(--ink-h);font-size:1.1rem">
              {{ $item['service']->prix_format }}
            </div>
          </div>
        @endforeach
      </div>
      <div style="text-align:right;margin-top:.8rem">
        <a href="{{ route('reservations.step2', $salonModel->slug) }}" class="btn-edit-step">Modifier</a>
      </div>
    </div>

    {{-- Formulaire informations --}}
    <div class="form-card">
      <div class="form-card-title">Vos informations</div>
      <div class="form-card-subtitle">Vérifiez vos coordonnées avant de confirmer la réservation.</div>

      <form method="POST" action="{{ route('reservations.store', $salonModel->slug) }}" id="reservationForm">
        @csrf

        @foreach($items as $i => $item)
          <input type="hidden" name="selections[{{ $i }}][service_id]" value="{{ $item['service']->id }}">
          <input type="hidden" name="selections[{{ $i }}][employe_id]" value="{{ $item['employe']?->id }}">
          <input type="hidden" name="selections[{{ $i }}][date_heure]" value="{{ $item['date_heure'] }}">
        @endforeach

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
          @foreach($items as $item)
            <div class="total-row">
              <span class="total-key">{{ $item['service']->nom_service }}</span>
              <span class="total-value">{{ $item['service']->prix_format }}</span>
            </div>
          @endforeach
          <div class="total-row">
            <span class="total-key">Frais de réservation</span>
            <span class="total-value free">Gratuit</span>
          </div>
          <div class="total-final">
            <span class="total-final-label">Total à payer au salon</span>
            <span class="total-final-amount">{{ number_format($total, 0, ',', ' ') }} MAD</span>
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
          <a href="{{ route('reservations.step2', $salonModel->slug) }}" class="btn-wizard-back">&#8592; Créneaux</a>
          <button type="submit" class="btn-wizard-confirm">
            Confirmer {{ $items->count() > 1 ? 'les réservations' : 'la réservation' }} &#10003;
          </button>
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

      @foreach($items as $item)
        <div style="padding:.7rem 0;border-top:1.5px dashed var(--p2)">
          <div style="display:flex;justify-content:space-between;font-size:.84rem">
            <span style="font-weight:700;color:var(--ink-d)">{{ $item['service']->nom_service }}</span>
            <span style="color:var(--ink-h);font-weight:700">{{ $item['service']->prix_format }}</span>
          </div>
          <div style="font-size:.72rem;color:var(--ink-m);margin-top:.2rem">
            {{ \Carbon\Carbon::parse($item['date_heure'])->translatedFormat('D d M') }}
            · {{ \Carbon\Carbon::parse($item['date_heure'])->format('H:i') }}
            @if($item['employe']) · {{ $item['employe']->nomComplet() }} @endif
          </div>
        </div>
      @endforeach

      <div class="recap-total-row">
        <span class="recap-total-label">Total</span>
        <span class="recap-total-amount">{{ number_format($total, 0, ',', ' ') }} MAD</span>
      </div>
      <div style="margin-top:1rem;padding:.9rem;background:rgba(197,216,157,.15);border:1px solid var(--p2);font-size:.74rem;color:var(--ink-s);line-height:1.7">
        &#128197; Paiement au salon · Annulation gratuite 24h avant
      </div>
    </div>
  </div>
</div>
@endsection
