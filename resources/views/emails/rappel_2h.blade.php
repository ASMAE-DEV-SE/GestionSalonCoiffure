@extends('emails.layout')

@section('content')
<div class="badge" style="background:#8B6914">Rappel — Dans 2h</div>
<div class="greeting">Votre RDV est dans 2h &#9201;</div>

<p class="text">
  Bonjour <strong>{{ $prenom }}</strong>,<br>
  C'est bientôt l'heure ! Votre rendez-vous chez <strong>{{ $nomSalon }}</strong>
  est prévu dans <strong>2 heures</strong>.
</p>

<div class="info-card" style="border-left-color:#D4A844">
  <div class="info-row">
    <span class="info-label">Salon &nbsp &nbsp</span>
    <span class="info-value">{{ $nomSalon }}</span>
  </div>
  <div class="info-row">
    <span class="info-label">Adresse &nbsp &nbsp</span>
    <span class="info-value">{{ $adresseSalon }}</span>
  </div>
  <div class="info-row">
    <span class="info-label">Service &nbsp &nbsp</span>
    <span class="info-value">{{ $nomService }}</span>
  </div>
  <div class="info-row">
    <span class="info-label">&#8987; Heure &nbsp &nbsp</span>
    <span class="info-value" style="color:#C04A3D;font-size:18px;font-weight:800">{{ $heure }}</span>
  </div>
  @if($nomEmploye)
  <div class="info-row">
    <span class="info-label">Styliste &nbsp &nbsp</span>
    <span class="info-value">{{ $nomEmploye }}</span>
  </div>
  @endif
</div>

<p class="text" style="text-align:center; font-size:18px; font-weight:700; color:#2C2A24;">
  &#128205; {{ $adresseSalon }}
</p>

<div style="text-align:center">
  <a href="{{ $urlReservation }}" class="btn btn-gold">Voir ma réservation</a>
</div>

<p class="text" style="font-size:13px;color:#7A7570;margin-top:16px;">
  Nous vous souhaitons une excellente séance beauté ! &#10024;
</p>
@endsection
