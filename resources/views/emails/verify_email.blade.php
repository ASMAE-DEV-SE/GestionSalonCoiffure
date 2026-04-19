@extends('emails.layout')

@section('subject', 'Vérifiez votre adresse email')

@section('content')
  <div class="greeting">Une dernière étape !</div>

  <p class="text">
    Merci de vous être inscrit(e) sur <strong>Salonify</strong>. Pour activer votre compte et commencer à réserver dans les meilleurs salons de beauté au Maroc, veuillez confirmer votre adresse email.
  </p>

  <div style="text-align:center;margin:32px 0">
    <a href="{{ $url }}" class="btn" style="font-size:15px;padding:16px 40px">
      ✓ Vérifier mon adresse email
    </a>
  </div>

  <div class="alert-box alert-warning" style="text-align:center;font-size:13px">
    Ce lien expire dans <strong>24 heures</strong>. Passé ce délai, vous devrez en demander un nouveau.
  </div>

  <hr class="divider">

  <p class="text" style="font-size:13px;color:#7A7570">
    Si vous n'avez pas créé de compte sur Salonify, ignorez simplement cet email — aucune action n'est requise.
  </p>

  <p class="text" style="font-size:12px;color:#9A9590;word-break:break-all">
    Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :<br>
    <a href="{{ $url }}" style="color:#4E5C38">{{ $url }}</a>
  </p>
@endsection
