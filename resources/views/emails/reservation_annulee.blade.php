@extends('emails.layout')

@section('content')
<div class="badge" style="background:#C04A3D">Réservation annulée</div>
<div class="greeting">Votre RDV a été annulé &#10005;</div>

<p class="text">
  Bonjour <strong>{{ $prenom }}</strong>,<br>
  Votre réservation chez <strong>{{ $nomSalon }}</strong> a malheureusement été annulée.
</p>

<div class="info-card" style="border-left-color:#C04A3D">
  <div class="info-row">
    <span class="info-label">Salon &nbsp &nbsp</span>
    <span class="info-value">{{ $nomSalon }}</span>
  </div>
  <div class="info-row">
    <span class="info-label">Service &nbsp &nbsp</span>
    <span class="info-value">{{ $nomService }}</span>
  </div>
  <div class="info-row">
    <span class="info-label">Date &nbsp &nbsp</span>
    <span class="info-value">{{ $date }}</span>
  </div>
  <div class="info-row">
    <span class="info-label">Heure &nbsp &nbsp</span>
    <span class="info-value">{{ $heure }}</span>
  </div>
  <div class="info-row">
    <span class="info-label">Annulée par &nbsp &nbsp</span>
    <span class="info-value">{{ $annuleePar === 'salon' ? $nomSalon : 'Vous' }}</span>
  </div>
  @if($motif)
  <div class="info-row">
    <span class="info-label">Motif &nbsp &nbsp</span> 
    <span class="info-value">{{ $motif }}</span>
  </div>
  @endif
</div>

<div class="alert-box alert-danger">
  &#10005; &nbsp; Cette réservation a été annulée et ne peut plus être modifiée.
</div>

<p class="text">
  Vous pouvez prendre un nouveau rendez-vous quand vous le souhaitez depuis votre espace client.
</p>

<div style="text-align:center">
  <a href="{{ $urlSalons }}" class="btn btn-gold">Réserver à nouveau</a>
</div>
@endsection
