@extends('layouts.dashboard')
@section('title', 'Réservation #' . $reservation->id)

@section('content')

<div class="dash-page-header">
  <div>
    <div class="dash-greeting">Réservation #SAL-{{ str_pad($reservation->id, 6, '0', STR_PAD_LEFT) }}</div>
    <div class="dash-date">{{ $reservation->date_heure->translatedFormat('l d F Y') }} à {{ $reservation->date_heure->format('H:i') }}</div>
  </div>
  <a href="{{ route('salon.reservations.index') }}" class="btn-new-booking">&#8592; Réservations</a>
</div>

@php
  $statusColors = ['en_attente'=>'pending','confirmee'=>'confirmed','terminee'=>'done','annulee'=>'cancelled'];
  $statusLabels = ['en_attente'=>'En attente','confirmee'=>'Confirmée','terminee'=>'Terminée','annulee'=>'Annulée'];
@endphp

{{-- Statut ──────────────────────────────────────────────── --}}
<div style="margin-bottom:1.8rem;padding:1rem 1.4rem;background:var(--p1);border:2px solid var(--p2);display:flex;align-items:center;gap:1rem;flex-wrap:wrap">
  <span class="status-badge {{ $statusColors[$reservation->statut] ?? 'pending' }}" style="font-size:.86rem">
    <span class="status-dot"></span>{{ $statusLabels[$reservation->statut] ?? $reservation->statut }}
  </span>
  @if($reservation->statut === 'en_attente')
    <span style="font-size:.78rem;color:var(--ink-m)">En attente de votre confirmation.</span>
  @elseif($reservation->statut === 'annulee' && $reservation->motif_annul)
    <span style="font-size:.78rem;color:#C04A3D">Motif : {{ $reservation->motif_annul }}</span>
  @endif

  {{-- Actions rapides --}}
  <div style="margin-left:auto;display:flex;gap:.6rem;flex-wrap:wrap">
    @if($reservation->statut === 'en_attente')
      <form method="POST" action="{{ route('salon.reservations.confirmer', $reservation->id) }}">
        @csrf
        <button class="btn-new-booking" style="font-size:.8rem;padding:.5rem 1.1rem">&#10003; Confirmer</button>
      </form>
    @endif
    @if($reservation->statut === 'confirmee')
      <form method="POST" action="{{ route('salon.reservations.terminer', $reservation->id) }}">
        @csrf
        <button class="btn-new-booking" style="font-size:.8rem;padding:.5rem 1.1rem">&#10003; Marquer terminée</button>
      </form>
    @endif
    @if(in_array($reservation->statut, ['en_attente','confirmee']))
      <button class="btn-xs btn-xs-r" style="font-size:.8rem;padding:.5rem 1.1rem"
              onclick="document.getElementById('annulModal').style.display='flex'">
        &#10005; Annuler
      </button>
    @endif
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 280px;gap:2rem;align-items:start">

  <div>
    {{-- Détails réservation ──────────────────────────────── --}}
    <div class="emp-table-card" style="margin-bottom:1.5rem">
      <div style="padding:1rem 1.4rem;border-bottom:2px solid var(--border2)">
        <div style="font-family:var(--fh);font-size:1.1rem;font-weight:700;color:var(--ink-h)">Détails</div>
      </div>
      <div style="padding:1.4rem;display:grid;grid-template-columns:1fr 1fr;gap:1.2rem 2rem">
        <div>
          <div style="font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--ink-m);margin-bottom:.25rem">Service</div>
          <div style="font-size:.95rem;font-weight:700;color:var(--ink-h)">{{ $reservation->service->nom_service }}</div>
          <div style="font-size:.78rem;color:var(--ink-m)">{{ $reservation->service->duree_formatee }}</div>
        </div>
        <div>
          <div style="font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--ink-m);margin-bottom:.25rem">Date &amp; Heure</div>
          <div style="font-size:.95rem;font-weight:700;color:var(--ink-h)">{{ $reservation->date_heure->translatedFormat('D d M Y') }}</div>
          <div style="font-size:.78rem;color:var(--ink-m)">{{ $reservation->date_heure->format('H:i') }}</div>
        </div>
        <div>
          <div style="font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--ink-m);margin-bottom:.25rem">Styliste assigné</div>
          <div style="font-size:.95rem;font-weight:700;color:var(--ink-h)">{{ $reservation->employe?->nomComplet() ?? 'Au choix' }}</div>
        </div>
        <div>
          <div style="font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--ink-m);margin-bottom:.25rem">Montant</div>
          <div style="font-family:var(--fh);font-size:1.4rem;font-weight:700;color:var(--ink-h)">{{ $reservation->service->prix_format }}</div>
        </div>
      </div>
      @if($reservation->notes_client)
        <div style="padding:1rem 1.4rem;background:rgba(197,216,157,.12);border-top:1px solid var(--border2);font-size:.82rem;color:var(--ink-s)">
          <strong>Message du client :</strong> {{ $reservation->notes_client }}
        </div>
      @endif
    </div>

    {{-- Avis client ──────────────────────────────────────── --}}
    @if($reservation->avis)
      <div class="emp-table-card" style="margin-bottom:1.5rem">
        <div style="padding:1rem 1.4rem;border-bottom:2px solid var(--border2)">
          <div style="font-family:var(--fh);font-size:1.1rem;font-weight:700;color:var(--ink-h)">Avis client</div>
        </div>
        <div style="padding:1.4rem">
          <div style="display:flex;align-items:center;gap:.6rem;margin-bottom:.8rem">
            <span style="color:#D4A844;font-size:1.1rem">{{ str_repeat('★', $reservation->avis->note) }}{{ str_repeat('☆', 5-$reservation->avis->note) }}</span>
            <span style="font-size:.78rem;color:var(--ink-m)">{{ $reservation->avis->created_at->translatedFormat('d F Y') }}</span>
          </div>
          @if($reservation->avis->commentaire)
            <p style="font-size:.88rem;color:var(--ink-s);font-style:italic;margin-bottom:1rem">"{{ $reservation->avis->commentaire }}"</p>
          @endif
          @if($reservation->avis->reponse_salon)
            <div style="background:var(--p1);border-left:3px solid var(--p4);padding:.75rem 1rem;font-size:.82rem;color:var(--ink-s)">
              <strong style="font-size:.68rem;text-transform:uppercase;color:var(--p4d)">Votre réponse ·</strong> {{ $reservation->avis->reponse_salon }}
            </div>
          @else
            <form method="POST" action="{{ route('salon.avis.repondre', $reservation->avis->id) }}" style="margin-top:.8rem">
              @csrf
              <div class="fg"><label style="font-size:.74rem;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--ink-m)">Répondre à cet avis</label>
                <textarea name="reponse_salon" class="fi" rows="3" placeholder="Merci pour votre avis..." required minlength="10"></textarea></div>
              <button type="submit" class="btn-add" style="margin-top:.6rem;font-size:.8rem">Publier la réponse</button>
            </form>
          @endif
        </div>
      </div>
    @endif
  </div>

  {{-- Sidebar client ──────────────────────────────────────── --}}
  <div>
    <div class="emp-table-card">
      <div style="padding:1rem 1.4rem;border-bottom:2px solid var(--border2)">
        <div style="font-family:var(--fh);font-size:1.1rem;font-weight:700;color:var(--ink-h)">Client</div>
      </div>
      <div style="padding:1.4rem">
        <div style="display:flex;align-items:center;gap:.8rem;margin-bottom:1.2rem">
          <img src="https://ui-avatars.com/api/?name={{ urlencode($reservation->client->nomComplet()) }}&background=9CAB84&color=fff&size=52"
               style="width:48px;height:48px;border-radius:50%;border:2px solid var(--p3)" alt="">
          <div>
            <div style="font-weight:700;color:var(--ink-h)">{{ $reservation->client->nomComplet() }}</div>
            <div style="font-size:.74rem;color:var(--ink-m)">{{ $reservation->client->email }}</div>
          </div>
        </div>
        @if($reservation->client->telephone)
          <div style="font-size:.82rem;color:var(--ink-s);margin-bottom:.5rem">&#128222; {{ $reservation->client->telephone }}</div>
        @endif
        @php $nbRdv = $reservation->client->reservations()->where('salon_id', $salon->id)->count(); @endphp
        <div style="font-size:.78rem;color:var(--ink-m)">{{ $nbRdv }} réservation(s) dans votre salon</div>
      </div>
    </div>
  </div>

</div>

{{-- Modal annulation ──────────────────────────────────────── --}}
<div class="modal-bg" id="annulModal" style="display:none">
  <div class="modal" style="max-width:440px">
    <div class="modal-head">
      <div class="modal-t">Annuler cette réservation</div>
      <button class="modal-close" onclick="document.getElementById('annulModal').style.display='none'">&#10005;</button>
    </div>
    <form method="POST" action="{{ route('salon.reservations.annuler', $reservation->id) }}">
      @csrf
      <div class="modal-body">
        <p style="font-size:.86rem;color:var(--ink-s);margin-bottom:1rem">
          Réservation de <strong>{{ $reservation->client->nomComplet() }}</strong> —
          {{ $reservation->date_heure->translatedFormat('D d M Y') }} {{ $reservation->date_heure->format('H:i') }}
        </p>
        <div class="fg">
          <label>Motif d'annulation *</label>
          <textarea name="motif" class="fi" rows="3" placeholder="Indisponibilité, fermeture exceptionnelle..." required></textarea>
        </div>
      </div>
      <div class="modal-foot">
        <button type="button" class="btn-xs btn-xs-e" onclick="document.getElementById('annulModal').style.display='none'">Fermer</button>
        <button type="submit" class="btn-xs btn-xs-r">Confirmer l'annulation</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
document.getElementById('annulModal').addEventListener('click', e => {
  if (e.target === e.currentTarget) e.currentTarget.style.display = 'none';
});
</script>
@endpush
@endsection
