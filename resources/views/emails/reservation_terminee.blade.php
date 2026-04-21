@extends('emails.layout')

@section('content')
<div class="badge" style="background:#3A7D44">Réservation terminée</div>
<div class="greeting">Votre rendez-vous est passé ✔</div>

<p class="text">
  Bonjour <strong>{{ $prenom }}</strong>,<br>
  Votre réservation chez <strong>{{ $nomSalon }}</strong> s'est déroulée comme prévu.
</p>

<div class="info-card" style="border-left-color:#3A7D44">
  <div class="info-row">
    <span class="info-label">Salon </span>
    <span class="info-value">{{ $nomSalon }}</span>
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
</div>

<div class="alert-box alert-success">
  ✔ &nbsp; Votre rendez-vous a été automatiquement marqué comme terminé.
</div>

<p class="text">
  Merci d'avoir utilisé Salonify. Vous pouvez laisser un avis si vous le souhaitez.
</p>

<div style="text-align:center">
  <a href="{{ $urlReservation }}" class="btn">Voir ma réservation</a>
</div>
@endsection
