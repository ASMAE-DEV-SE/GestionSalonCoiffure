@extends('emails.layout')

@section('content')
<div class="badge" style="background:#8B6914">Rappel RDV</div>
<div class="greeting">Votre RDV est demain &#128337;</div>

<p class="text">
  Bonjour <strong>{{ $prenom }}</strong>,<br>
  Nous vous rappelons que vous avez un rendez-vous prévu <strong>demain</strong>.
</p>

<div class="info-card" style="border-left-color:#D4A844">
  <div class="info-row">
    <span class="info-label">Salon</span>
    <span class="info-value">{{ $nomSalon }}</span>
  </div>
  <div class="info-row">
    <span class="info-label">Adresse</span>
    <span class="info-value">{{ $adresseSalon }}</span>
  </div>
  <div class="info-row">
    <span class="info-label">Service</span>
    <span class="info-value">{{ $nomService }}</span>
  </div>
  <div class="info-row">
    <span class="info-label">&#128197; Date</span>
    <span class="info-value" style="color:#4E5C38;font-size:16px">{{ $date }}</span>
  </div>
  <div class="info-row">
    <span class="info-label">&#8987; Heure</span>
    <span class="info-value" style="color:#4E5C38;font-size:16px">{{ $heure }}</span>
  </div>
  @if($nomEmploye)
  <div class="info-row">
    <span class="info-label">Styliste</span>
    <span class="info-value">{{ $nomEmploye }}</span>
  </div>
  @endif
</div>

<div class="alert-box alert-warning">
  &#128205; &nbsp; <strong>{{ $adresseSalon }}</strong> — Pensez à vérifier l'itinéraire.
</div>

<p class="text">
  En cas d'empêchement, annulez votre réservation au moins <strong>24h à l'avance</strong>
  pour permettre à d'autres clients de prendre ce créneau.
</p>

<div style="text-align:center">
  <a href="{{ $urlReservation }}" class="btn btn-gold">Voir ma réservation</a>
</div>

<p class="text" style="font-size:13px;color:#7A7570;margin-top:12px;text-align:center;">
  Vous recevrez un autre rappel 2h avant votre rendez-vous.
</p>
@endsection
