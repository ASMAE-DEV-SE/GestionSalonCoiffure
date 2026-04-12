@extends('layouts.dashboard')
@section('title', 'Avis clients')

@section('content')

<div class="dash-page-header">
  <div>
    <div class="dash-greeting">Avis clients</div>
    <div class="dash-date">&#9733; {{ number_format($stats['note_moy'], 1) }} · {{ $stats['total'] }} avis · {{ $stats['sans_reponse'] }} sans réponse</div>
  </div>
</div>

{{-- Filtres --}}
<div class="avis-filter-bar" style="margin-bottom:1.5rem">
  @foreach(['' => 'Tous', '1'=>'&#9733;', '2'=>'&#9733;&#9733;', '3'=>'&#9733;&#9733;&#9733;', '4'=>'&#9733;&#9733;&#9733;&#9733;', '5'=>'&#9733;&#9733;&#9733;&#9733;&#9733;'] as $v => $l)
    <a href="{{ request()->fullUrlWithQuery(['note'=>$v]) }}"
       class="avis-filter-tab {{ request('note','')===$v ? 'on' : '' }}">{!! $l !!}</a>
  @endforeach
  <a href="{{ request()->fullUrlWithQuery(['sans_reponse'=>1]) }}"
     class="avis-filter-tab {{ request('sans_reponse') ? 'on' : '' }}">
    Sans réponse <span class="avis-filter-count yellow">{{ $stats['sans_reponse'] }}</span>
  </a>
</div>

@forelse($avis as $a)
  <div class="avis-mod-card {{ $a->aReponse() ? 'approuve' : 'en-attente' }}" style="margin-bottom:1rem">
    <div class="avis-mod-top">
      <div class="avis-mod-author">
        <div class="avis-mod-av">
          <img src="https://ui-avatars.com/api/?name={{ urlencode($a->reservation->client?->nomComplet() ?? 'Client') }}&background=9CAB84&color=fff&size=42" alt="">
        </div>
        <div>
          <div class="avis-mod-name">{{ $a->reservation->client?->prenom ?? 'Client' }} {{ substr($a->reservation->client?->nom ?? '', 0, 1) }}.</div>
          <div class="avis-mod-date">{{ $a->created_at->translatedFormat('d F Y') }}</div>
          <div class="avis-mod-stars">{{ str_repeat('★', $a->note) }}{{ str_repeat('☆', 5-$a->note) }}</div>
        </div>
      </div>
      <div class="avis-mod-meta">
        @if($a->aReponse())
          <span class="signal-badge vert">&#10003; Répondu</span>
        @else
          <span class="signal-badge orange">En attente de réponse</span>
        @endif
      </div>
    </div>

    <div class="avis-mod-body">
      @if($a->commentaire)
        <div class="avis-mod-text">"{{ $a->commentaire }}"</div>
      @endif
      @if($a->aReponse())
        <div style="background:var(--p1);border-left:3px solid var(--p4);padding:.8rem 1rem;margin-top:.8rem;font-size:.82rem;color:var(--ink-s)">
          <strong style="font-size:.7rem;text-transform:uppercase;letter-spacing:.5px;color:var(--p4d)">Votre réponse ·</strong>
          {{ $a->reponse_salon }}
        </div>
      @else
        {{-- Formulaire de réponse inline --}}
        <form method="POST" action="{{ route('salon.avis.repondre', $a->id) }}" style="margin-top:.9rem">
          @csrf
          <textarea name="reponse_salon" class="fi" rows="2" placeholder="Répondez à cet avis..." style="margin-bottom:.5rem"></textarea>
          <button type="submit" class="btn-mod-approve">Publier la réponse</button>
        </form>
      @endif
    </div>

    <div class="avis-mod-foot">
      <div class="avis-mod-signal-by">
        Service : {{ $a->reservation->service->nom_service }} · {{ $a->reservation->date_heure->translatedFormat('d M Y') }}
      </div>
      <div class="avis-mod-actions">
        <form method="POST" action="{{ route('salon.avis.signaler', $a->id) }}"
              onsubmit="const m=prompt('Motif du signalement ?');if(!m)return false;this.querySelector('[name=motif_signalement]').value=m">
          @csrf
          <input type="hidden" name="motif_signalement" value="">
          <button type="submit" class="btn-mod-ignore">Signaler</button>
        </form>
      </div>
    </div>
  </div>
@empty
  <div style="padding:4rem 0;text-align:center;color:var(--ink-m)">Aucun avis pour l'instant.</div>
@endforelse

<div style="margin-top:1.2rem">{{ $avis->links() }}</div>
@endsection
