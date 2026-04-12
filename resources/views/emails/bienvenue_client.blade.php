@extends('emails.layout')

@section('content')
<div class="badge">Bienvenue !</div>
<div class="greeting">Bonjour {{ $prenom }} &#127881;</div>

<p class="text">
  Bienvenue sur <strong>Salonify</strong> ! Votre compte client a été créé avec succès.
  Vous pouvez dès maintenant réserver vos séances beauté dans les meilleurs salons du Maroc.
</p>

<div class="alert-box alert-success">
  &#10003; &nbsp; Votre adresse email : <strong>{{ $email }}</strong>
</div>

<p class="text">Pour commencer, vérifiez votre adresse email en cliquant sur le bouton ci-dessous :</p>

<div style="text-align:center">
  <a href="{{ $urlVerification }}" class="btn">Vérifier mon email</a>
</div>

<hr class="divider">

<p class="text" style="font-size:13px; color:#7A7570;">
  Si vous n'avez pas créé de compte Salonify, ignorez cet email.
  Ce lien expirera dans <strong>24 heures</strong>.
</p>

<p class="text" style="font-size:13px; color:#7A7570;">
  Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :<br>
  <a href="{{ $urlVerification }}" style="color:#4E5C38;word-break:break-all;">{{ $urlVerification }}</a>
</p>
@endsection
