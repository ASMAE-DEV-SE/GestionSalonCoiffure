@extends('emails.layout')

@section('content')
<div class="badge">Nouvelle réservation</div>
<div class="greeting">Nouvelle demande de RDV &#128197;</div>

<p class="text">
  Bonjour,<br>
  <strong>{{ $nomClient }}</strong> vient de faire une demande de réservation pour votre salon
  <strong>« {{ $nomSalon }} »</strong>.
</p>

<div class="info-card">
  <div class="info-row">
    <span class="info-label">Client &nbsp &nbsp</span>
    <span class="info-value">{{ $nomClient }}</span>
  </div>
  <div class="info-row">
    <span class="info-label">Téléphone &nbsp &nbsp</span>
    <span class="info-value">{{ $telephoneClient ?: '—' }}</span>
  </div>
  <div class="info-row">
    <span class="info-label">Service demandé &nbsp &nbsp</span>
    <span class="info-value">{{ $nomService }}</span>
  </div>
  <div class="info-row">
    <span class="info-label">Date souhaitée &nbsp &nbsp</span>
    <span class="info-value">{{ $date }}</span>
  </div>
  <div class="info-row">
    <span class="info-label">Heure &nbsp &nbsp</span>
    <span class="info-value">{{ $heure }}</span>
  </div>
  <div class="info-row">
    <span class="info-label">Durée &nbsp &nbsp</span>
    <span class="info-value">{{ $duree }}</span>
  </div>
  @if($notesClient)
  <div class="info-row">
    <span class="info-label">Notes client &nbsp &nbsp</span>
    <span class="info-value">{{ $notesClient }}</span>
  </div>
  @endif
</div>

<div class="alert-box alert-warning">
  &#9203; &nbsp; Cette réservation est <strong>en attente de confirmation</strong>. Confirmez-la rapidement pour rassurer le client.
</div>

<div style="text-align:center">
  <a href="{{ $urlReservation }}" class="btn">Confirmer la réservation</a>
</div>

<p class="text" style="font-size:13px;color:#7A7570;margin-top:12px;text-align:center;">
  Connectez-vous à votre tableau de bord pour gérer cette réservation.
</p>
@endsection
