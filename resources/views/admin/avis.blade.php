@extends('layouts.admin')
@section('title', 'Modération des avis')

@section('content')

<div class="admin-page-header">
  <div>
    <div class="admin-page-title">Modération des avis</div>
    <div class="admin-page-subtitle">
      {{ $stats['total'] }} avis · Note moy. &#9733; {{ $stats['note_moy'] }} · {{ $stats['sans_reponse'] }} sans réponse salon
    </div>
  </div>
</div>

{{-- Filtres ─────────────────────────────────────────────── --}}
<div style="display:flex;align-items:center;gap:.6rem;flex-wrap:wrap;margin-bottom:1.5rem">
  @foreach(['' => 'Tous', '5'=>'&#9733;&#9733;&#9733;&#9733;&#9733;','4'=>'&#9733;&#9733;&#9733;&#9733;','3'=>'&#9733;&#9733;&#9733;','2'=>'&#9733;&#9733;','1'=>'&#9733;'] as $v => $l)
    <a href="{{ request()->fullUrlWithQuery(['note'=>$v,'page'=>1]) }}"
       class="avis-filter-tab {{ request('note','')===$v ? 'on' : '' }}">{!! $l !!}</a>
  @endforeach
  <a href="{{ request()->fullUrlWithQuery(['sans_reponse'=>request('sans_reponse')?null:1,'page'=>1]) }}"
     class="avis-filter-tab {{ request('sans_reponse') ? 'on' : '' }}">
    Sans réponse <span class="avis-filter-count yellow">{{ $stats['sans_reponse'] }}</span>
  </a>
  <select onchange="window.location=this.value" class="tb-search" style="max-width:200px">
    <option value="{{ request()->fullUrlWithQuery(['salon_id'=>'']) }}">Tous les salons</option>
    @foreach($salons as $s)
      <option value="{{ request()->fullUrlWithQuery(['salon_id'=>$s->id]) }}"
              {{ request('salon_id')==$s->id?'selected':'' }}>{{ $s->nom_salon }}</option>
    @endforeach
  </select>
</div>

@forelse($avis as $a)
  <div class="avis-mod-card {{ $a->note <= 2 ? 'signale' : ($a->aReponse() ? 'approuve' : 'en-attente') }}" style="margin-bottom:1rem">
    <div class="avis-mod-top">
      <div class="avis-mod-author">
        <div class="avis-mod-av">
          <img src="https://ui-avatars.com/api/?name={{ urlencode($a->reservation->client?->nomComplet() ?? 'C') }}&background=9CAB84&color=fff&size=42" alt="">
        </div>
        <div>
          <div class="avis-mod-name">{{ $a->reservation->client?->nomComplet() ?? 'Client supprimé' }}</div>
          <div class="avis-mod-date">{{ $a->created_at->translatedFormat('d F Y') }}</div>
          <div class="avis-mod-stars">{{ str_repeat('★',$a->note) }}{{ str_repeat('☆',5-$a->note) }}</div>
        </div>
      </div>
      <div class="avis-mod-meta">
        @if($a->note <= 2)
          <span class="signal-badge rouge">Note faible</span>
        @elseif($a->aReponse())
          <span class="signal-badge vert">&#10003; Répondu</span>
        @else
          <span class="signal-badge orange">Sans réponse</span>
        @endif
      </div>
    </div>

    <div class="avis-mod-body">
      <div class="avis-mod-salon">
        <div class="avis-mod-salon-ph">
          <img src="{{ $a->reservation->salon->photo_url }}" alt="">
        </div>
        <div class="avis-mod-salon-name">
          <a href="{{ route('admin.salons.show', $a->reservation->salon->id) }}" style="color:var(--p4d)">
            {{ $a->reservation->salon->nom_salon }}
          </a>
          · {{ $a->reservation->service->nom_service }}
        </div>
      </div>
      @if($a->commentaire)
        <div class="avis-mod-text">"{{ $a->commentaire }}"</div>
      @endif
      @if($a->aReponse())
        <div style="background:var(--p1);border-left:3px solid var(--p4);padding:.75rem 1rem;margin-top:.8rem;font-size:.82rem;color:var(--ink-s)">
          <strong style="font-size:.68rem;text-transform:uppercase;letter-spacing:.5px;color:var(--p4d)">Réponse salon ·</strong> {{ $a->reponse_salon }}
        </div>
      @endif
    </div>

    <div class="avis-mod-foot">
      <div class="avis-mod-signal-by">ID #{{ $a->id }} · RDV {{ $a->reservation->date_heure->translatedFormat('d M Y') }}</div>
      <div class="avis-mod-actions">
        <form method="POST" action="{{ route('admin.avis.approuver', $a->id) }}">
          @csrf
          <button type="submit" class="btn-mod-approve">&#10003; Maintenir</button>
        </form>
        <form method="POST" action="{{ route('admin.avis.destroy', $a->id) }}"
              onsubmit="return confirm('Supprimer définitivement cet avis ?')">
          @csrf @method('DELETE')
          <button type="submit" class="btn-mod-delete">&#128465; Supprimer</button>
        </form>
      </div>
    </div>
  </div>
@empty
  <div style="padding:4rem 0;text-align:center;color:var(--ink-m)">&#10003; Aucun avis pour ces critères.</div>
@endforelse

<div style="margin-top:1.2rem">{{ $avis->links() }}</div>
@endsection
