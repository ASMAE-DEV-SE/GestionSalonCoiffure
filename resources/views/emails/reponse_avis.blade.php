@extends('emails.layout')

@section('subject', $nomSalon . ' a répondu à votre avis')

@section('content')
  <div class="greeting">Bonjour {{ $prenomClient }} !</div>

  <p class="text">
    <strong>{{ $nomSalon }}</strong> a répondu à l'avis que vous avez publié sur leur salon.
  </p>

  {{-- Votre avis original --}}
  <div class="info-card">
    <div style="font-size:13px;font-weight:700;color:#7A7570;text-transform:uppercase;letter-spacing:.8px;margin-bottom:10px">
      Votre avis
    </div>
    <div style="font-size:18px;color:#C5A96D;margin-bottom:6px">{{ $etoiles }}</div>
    @if($commentaireClient)
      <p style="font-size:14px;color:#5A5550;font-style:italic;line-height:1.6">
        "{{ $commentaireClient }}"
      </p>
    @endif
  </div>

  {{-- Réponse du salon --}}
  <div style="background:#EEF3E8;border-left:4px solid #4E5C38;border-radius:2px;padding:18px 22px;margin:24px 0">
    <div style="font-size:13px;font-weight:700;color:#4E5C38;text-transform:uppercase;letter-spacing:.8px;margin-bottom:10px">
      Réponse de {{ $nomSalon }}
    </div>
    <p style="font-size:14px;color:#3A4D28;line-height:1.7">
      {{ $reponseSalon }}
    </p>
  </div>

  <p class="text">
    Merci d'avoir partagé votre expérience sur Salonify. Vos avis aident la communauté à choisir les meilleurs salons.
  </p>

  <div style="text-align:center">
    <a href="{{ $urlReservations }}" class="btn">Voir mes réservations</a>
  </div>
@endsection
