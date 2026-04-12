@extends('layouts.dashboard')
@section('title', 'Notifications')

@section('content')

<div class="dash-page-header">
  <div>
    <div class="dash-greeting">Notifications</div>
    <div class="dash-date">{{ $notifications->total() }} notification(s)</div>
  </div>
</div>

@if($notifications->isEmpty())
  <div style="padding:5rem 0;text-align:center;border:2px dashed var(--border2);border-radius:2px">
    <div style="font-size:2.5rem;margin-bottom:1rem">&#9993;</div>
    <p style="color:var(--ink-m);font-size:.95rem">Aucune notification pour l'instant.</p>
  </div>
@else
  <div style="display:flex;flex-direction:column;gap:.75rem;max-width:680px">
    @foreach($notifications as $n)
      @php
        $lu      = $n->estLue();
        $message = $n->donnees['message'] ?? ucfirst(str_replace('_', ' ', $n->type));
        $icons   = [
          'reservation_confirmee' => '&#10003;',
          'reservation_annulee'   => '&#10005;',
          'rappel_24h'            => '&#128337;',
          'rappel_2h'             => '&#128337;',
          'nouvelle_reservation'  => '&#128197;',
          'salon_valide'          => '&#9733;',
          'salon_suspendu'        => '&#9888;',
        ];
        $colors = [
          'reservation_confirmee' => 'var(--p4)',
          'reservation_annulee'   => '#C04A3D',
          'rappel_24h'            => '#D4A844',
          'rappel_2h'             => '#D4A844',
          'salon_valide'          => 'var(--p4)',
          'salon_suspendu'        => '#C04A3D',
        ];
        $icon  = $icons[$n->type]  ?? '&#9993;';
        $color = $colors[$n->type] ?? 'var(--p4)';
      @endphp
      <div style="display:flex;align-items:flex-start;gap:1rem;padding:1rem 1.3rem;background:{{ $lu ? '#fff' : 'var(--p1)' }};border:1.5px solid {{ $lu ? 'var(--border2)' : 'var(--p3)' }};position:relative">
        {{-- Indicateur non-lu --}}
        @if(!$lu)
          <div style="position:absolute;top:.75rem;right:.75rem;width:8px;height:8px;border-radius:50%;background:{{ $color }}"></div>
        @endif

        {{-- Icône --}}
        <div style="width:38px;height:38px;border-radius:50%;background:{{ $color }};color:#fff;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0">
          {!! $icon !!}
        </div>

        {{-- Contenu --}}
        <div style="flex:1;min-width:0">
          <div style="font-size:.88rem;font-weight:{{ $lu ? '500' : '700' }};color:var(--ink-h);line-height:1.5;margin-bottom:.3rem">
            {{ $message }}
          </div>
          <div style="font-size:.72rem;color:var(--ink-m)">
            {{ $n->cree_le->diffForHumans() }} · {{ $n->cree_le->translatedFormat('d F Y à H:i') }}
          </div>
        </div>

        {{-- Action marquer lu --}}
        @if(!$lu)
          <form method="POST" action="{{ route('client.notifications.lu', $n->id) }}" style="flex-shrink:0">
            @csrf
            <button type="submit" style="background:none;border:none;font-size:.7rem;color:var(--ink-m);cursor:pointer;text-decoration:underline;padding:0;font-family:var(--fb)">
              Marquer lu
            </button>
          </form>
        @endif
      </div>
    @endforeach
  </div>

  <div style="margin-top:1.5rem">{{ $notifications->links() }}</div>
@endif

@endsection
