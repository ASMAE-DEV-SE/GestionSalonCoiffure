@extends('emails.layout')

@section('content')
<div class="badge">Nouveau salon</div>
<div class="greeting">Bonjour {{ $prenom }} &#127381;</div>

<p class="text">
  Bienvenue sur <strong>Salonify</strong> ! Votre demande d'inscription pour le salon
  <strong>« {{ $nomSalon }} »</strong> a bien été reçue.
</p>

<div class="info-card">
  <div class="info-row">
    <span class="info-label">Nom du salon</span>
    <span class="info-value">{{ $nomSalon }}</span>
  </div>
  <div class="info-row">
    <span class="info-label">Adresse</span>
    <span class="info-value">{{ $adresse }}</span>
  </div>
  <div class="info-row">
    <span class="info-label">Statut</span>
    <span class="info-value" style="color:#D4A844">&#9203; En attente de validation</span>
  </div>
</div>

<div class="alert-box alert-warning">
  &#9888; &nbsp; Votre salon est actuellement <strong>en attente de validation</strong> par notre équipe.
  Vous recevrez un email dès que votre salon sera validé (généralement sous 24 à 48h).
</div>

<p class="text">En attendant, vérifiez votre adresse email :</p>

<div style="text-align:center">
  <a href="{{ $urlVerification }}" class="btn">Vérifier mon email</a>
</div>

<hr class="divider">

<p class="text" style="font-size:13px; color:#7A7570;">
  Ce lien de vérification expirera dans <strong>24 heures</strong>.
</p>
@endsection
