@extends('emails.layout')

@section('subject', 'Nouvel avis sur ' . $nomSalon)

@section('content')
  <div class="greeting">Un nouvel avis vient d'être publié !</div>

  <p class="text">
    <strong>{{ $nomClient }}</strong> vient de laisser un avis sur <strong>{{ $nomSalon }}</strong>.
  </p>

  {{-- Carte avis --}}
  <div class="info-card">
    <div style="font-size:13px;font-weight:700;color:#7A7570;text-transform:uppercase;letter-spacing:.8px;margin-bottom:10px">
      Note du client
    </div>
    <div style="font-size:22px;color:#C5A96D;margin-bottom:10px">{{ $etoiles }} <span style="font-size:14px;color:#5A5550">({{ $note }}/5)</span></div>

    <div style="font-size:13px;color:#7A7570;margin-bottom:4px">
      Service : <strong style="color:#3A4D28">{{ $nomService }}</strong>
      @if($dateRdv)
        &nbsp;·&nbsp; {{ $dateRdv }}
      @endif
    </div>

    @if($commentaire)
      <p style="font-size:14px;color:#5A5550;font-style:italic;line-height:1.6;margin-top:14px;padding-top:12px;border-top:1px solid #E5DED4">
        "{{ $commentaire }}"
      </p>
    @endif
  </div>

  <p class="text">
    Répondre à vos clients renforce votre image professionnelle et rassure les futurs visiteurs.
  </p>

  <div style="text-align:center">
    <a href="{{ $urlRepondre }}" class="btn">Répondre à cet avis</a>
  </div>
@endsection
