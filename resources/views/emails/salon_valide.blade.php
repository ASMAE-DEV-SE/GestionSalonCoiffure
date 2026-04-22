@extends('emails.layout')

@section('content')
<div class="badge">Salon validé</div>
<div class="greeting">Félicitations &#9733; Votre salon est en ligne !</div>

<p class="text">
  Bonjour <strong>{{ $prenom }}</strong>,<br>
  Excellente nouvelle ! Votre salon <strong>« {{ $nomSalon }} »</strong>
  vient d'être <strong>validé</strong> par notre équipe et est maintenant visible sur Salonify.
</p>

<div class="info-card">
  <div class="info-row">
    <span class="info-label">Salon &nbsp &nbsp</span>
    <span class="info-value">{{ $nomSalon }}</span>
  </div>
  <div class="info-row">
    <span class="info-label">Adresse &nbsp &nbsp</span>
    <span class="info-value">{{ $adresse }}</span>
  </div>
  <div class="info-row">
    <span class="info-label">Statut &nbsp &nbsp</span> 
    <span class="info-value" style="color:#4E5C38">&#10003; Validé</span>
  </div>
  <div class="info-row">
    <span class="info-label">Date de validation &nbsp &nbsp</span>
    <span class="info-value">{{ $dateValidation }}</span>
  </div>
</div>

<div class="alert-box alert-success">
  &#10003; &nbsp; Votre salon est maintenant <strong>public et accessible</strong> aux clients sur notre plateforme.
</div>

<p class="text">Prochaines étapes pour optimiser votre profil :</p>
<ul style="font-size:14px;line-height:2;color:#5A5550;margin-left:20px;margin-bottom:20px">
  <li>&#128248; Ajoutez une belle photo de votre salon</li>
  <li>&#9999; Complétez la description et les horaires</li>
  <li>&#9997; Ajoutez vos services avec prix et durées</li>
  <li>&#128101; Créez les profils de vos employés</li>
</ul>

<div style="text-align:center">
  <a href="{{ $urlDashboard }}" class="btn">Accéder à mon tableau de bord</a>
</div>
@endsection
