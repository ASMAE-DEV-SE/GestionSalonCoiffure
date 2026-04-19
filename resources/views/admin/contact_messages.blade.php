@extends('layouts.admin')
@section('title', 'Messages de contact')

@section('content')

<div class="admin-page-header">
  <div>
    <div class="admin-page-title">Messages de contact</div>
    <div class="admin-page-subtitle">{{ $messages->total() }} message(s) reçu(s)</div>
  </div>
</div>

@if($messages->isEmpty())
  <div style="text-align:center;padding:3rem;color:var(--ink-m);font-size:.9rem">
    Aucun message pour le moment.
  </div>
@else
  <div style="display:flex;flex-direction:column;gap:1rem">
    @foreach($messages as $msg)
      <div style="background:#fff;border:1px solid var(--border);padding:1.2rem 1.4rem;position:relative;{{ !$msg->lu ? 'border-left:3px solid var(--p4d)' : '' }}">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;flex-wrap:wrap">
          <div>
            <div style="font-family:var(--fh);font-size:.95rem;font-weight:600;color:var(--ink)">
              {{ $msg->nom }}
              @if(!$msg->lu)
                <span style="background:var(--p4d);color:#fff;font-size:.6rem;font-weight:700;padding:.1rem .4rem;border-radius:4px;margin-left:.4rem;vertical-align:middle">NOUVEAU</span>
              @endif
            </div>
            <div style="font-size:.78rem;color:var(--ink-m);margin-top:.15rem">
              {{ $msg->email }} · {{ $msg->created_at->translatedFormat('d F Y à H\hi') }}
            </div>
            <div style="margin-top:.4rem;font-size:.78rem;font-weight:600;color:var(--p4d);text-transform:uppercase;letter-spacing:.4px">
              {{ $msg->sujet }}
            </div>
          </div>
          <form method="POST" action="{{ route('admin.contact.destroy', $msg->id) }}"
                onsubmit="return confirm('Supprimer ce message ?')">
            @csrf @method('DELETE')
            <button type="submit"
                    style="background:none;border:1px solid #ddd;color:var(--ink-m);font-size:.72rem;padding:.3rem .7rem;cursor:pointer;font-family:var(--fb)">
              Supprimer
            </button>
          </form>
        </div>
        <p style="margin-top:.8rem;font-size:.88rem;color:var(--ink-s);line-height:1.6;white-space:pre-wrap">{{ $msg->message }}</p>
        <a href="mailto:{{ $msg->email }}?subject=Re: {{ urlencode($msg->sujet) }}"
           style="display:inline-block;margin-top:.6rem;font-size:.75rem;font-weight:600;color:var(--p4d);text-decoration:none;letter-spacing:.3px;text-transform:uppercase">
          &#9993; Répondre par email
        </a>
      </div>
    @endforeach
  </div>

  <div style="margin-top:1.5rem">
    {{ $messages->links() }}
  </div>
@endif

@endsection
