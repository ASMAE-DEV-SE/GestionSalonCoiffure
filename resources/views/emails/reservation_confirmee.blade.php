@extends('emails.layout')

@section('content')
<div class="badge">Réservation confirmée</div>
<div class="greeting">Votre RDV est confirmé &#10003;</div>

<p class="text">
  Bonne nouvelle, <strong>{{ $prenom }}</strong> !
  Votre réservation chez <strong>{{ $nomSalon }}</strong> a été <strong>confirmée</strong>.
</p>

<div class="info-card">
  <div class="info-row">
    <span class="info-label">Salon </span>
    <span class="info-value">{{ $nomSalon }}</span>
  </div>
  <div class="info-row">
    <span class="info-label">Adresse </span>
    <span class="info-value">{{ $adresseSalon }}</span>
  </div>
  <div class="info-row">
    <span class="info-label">Service </span>
    <span class="info-value">{{ $nomService }}</span>
  </div>
  <div class="info-row">
    <span class="info-label">Date </span>
    <span class="info-value">{{ $date }}</span>
  </div>
  <div class="info-row">
    <span class="info-label">Heure </span>
    <span class="info-value">{{ $heure }}</span>
  </div>
  @if($nomEmploye)
  <div class="info-row">
    <span class="info-label">Styliste </span>
    <span class="info-value">{{ $nomEmploye }}</span>
  </div>
  @endif
  <div class="info-row">
    <span class="info-label">Durée </span>
    <span class="info-value">{{ $duree }}</span>
  </div>
</div>

<div class="alert-box alert-success">
  &#10003; &nbsp; Votre place est réservée. Soyez à l'heure pour profiter pleinement de votre séance !
</div>

<p class="text">
  En cas d'empêchement, pensez à annuler votre réservation au moins <strong>24h à l'avance</strong>
  depuis votre espace client.
</p>

<div style="text-align:center">
  <a href="{{ $urlReservation }}" class="btn">Voir ma réservation</a>
</div>

<p class="text" style="font-size:13px;color:#7A7570;margin-top:16px;">
  &#128337; Vous recevrez un rappel 24h et 2h avant votre rendez-vous.
</p>
@endsection
