@extends('emails.layout')

@section('content')
<div class="badge" style="background:#C04A3D">Salon suspendu</div>
<div class="greeting">Votre salon a été suspendu &#9888;</div>

<p class="text">
  Bonjour <strong>{{ $prenom }}</strong>,<br>
  Nous vous informons que votre salon <strong>« {{ $nomSalon }} »</strong>
  a été <strong>temporairement suspendu</strong> de notre plateforme.
</p>

<div class="info-card" style="border-left-color:#C04A3D">
  <div class="info-row">
    <span class="info-label">Salon </span>
    <span class="info-value">{{ $nomSalon }}</span>
  </div>
  <div class="info-row">
    <span class="info-label">Statut </span>
    <span class="info-value" style="color:#C04A3D">&#10005; Suspendu</span>
  </div>
  <div class="info-row">
    <span class="info-label">Motif</span>
    <span class="info-value">{{ $motif }}</span>
  </div>
</div>

<div class="alert-box alert-danger">
  &#9888; &nbsp; Votre salon n'est <strong>plus visible par les clients</strong> pendant la période de suspension.
  Les nouvelles réservations ont été désactivées.
</div>

<p class="text">
  Si vous pensez que cette décision est erronée ou souhaitez contester cette suspension,
  veuillez nous contacter à l'adresse <a href="mailto:support@salonify.ma" style="color:#4E5C38">support@salonify.ma</a>
  en mentionnant le nom de votre salon et les détails de votre situation.
</p>

<p class="text" style="font-size:13px;color:#7A7570;">
  L'équipe Salonify reste disponible pour vous accompagner dans la résolution de cette situation.
</p>
@endsection
