@extends('layouts.dashboard')
@section('title', 'Réservations')

@section('content')

<div class="dash-page-header">
  <div><div class="dash-greeting">Réservations</div><div class="dash-date">{{ $salon->nom_salon }}</div></div>
  <div style="display:flex;gap:.5rem;flex-wrap:wrap">
    @foreach([''=>'Toutes','en_attente'=>'En attente','confirmee'=>'Confirmées','terminee'=>'Terminées','annulee'=>'Annulées'] as $v => $l)
      <a href="{{ request()->fullUrlWithQuery(['statut'=>$v]) }}"
         class="tb-filter {{ request('statut','')===$v ? 'on' : '' }}">
        {{ $l }}
        @if($v !== '' && isset($compteurs[$v]) && $compteurs[$v] > 0)
          <span style="background:{{ $v==='en_attente'?'#E8562A':'var(--p4)' }};color:#fff;font-size:.6rem;font-weight:700;padding:.06rem .4rem;border-radius:8px;margin-left:.3rem">{{ $compteurs[$v] }}</span>
        @endif
      </a>
    @endforeach
  </div>
</div>

{{-- Filtre date + employé --}}
<form method="GET" class="toolbar" style="margin-bottom:1.5rem">
  <div class="tb-l">
    <input type="date" name="date" class="tb-search" style="min-width:160px" value="{{ request('date') }}">
    <select name="employe_id" class="tb-search" style="min-width:180px">
      <option value="">Tous les employés</option>
      @foreach($employes as $e)
        <option value="{{ $e->id }}" {{ request('employe_id')==$e->id ? 'selected' : '' }}>{{ $e->nomComplet() }}</option>
      @endforeach
    </select>
    <button type="submit" class="btn-add" style="padding:.6rem 1.1rem;font-size:.76rem">Filtrer</button>
    @if(request()->hasAny(['date','employe_id','statut']))
      <a href="{{ route('salon.reservations.index') }}" class="tb-filter">Réinitialiser</a>
    @endif
  </div>
</form>

{{-- Tableau --}}
<div class="emp-table-card">
  <table class="emp-table">
    <thead><tr>
      <th>Client</th><th>Service</th><th>Date &amp; Heure</th><th>Styliste</th><th>Montant</th><th>Statut</th><th>Actions</th>
    </tr></thead>
    <tbody>
      @forelse($reservations as $r)
        <tr>
          <td>
            <div style="font-weight:700;color:var(--ink-h)">{{ $r->client->nomComplet() }}</div>
            <div style="font-size:.72rem;color:var(--ink-m)">{{ $r->client->telephone }}</div>
          </td>
          <td><div style="font-weight:600">{{ $r->service->nom_service }}</div><div style="font-size:.72rem;color:var(--ink-m)">{{ $r->service->duree_formatee }}</div></td>
          <td><span class="booking-time">{{ $r->date_heure->format('H:i') }}</span><div class="booking-duration">{{ $r->date_heure->translatedFormat('D d M Y') }}</div></td>
          <td>{{ $r->employe?->nomComplet() ?? '—' }}</td>
          <td style="font-family:var(--fh);font-size:1.05rem;font-weight:700">{{ $r->service->prix_format }}</td>
          <td>
            @php $sc = ['en_attente'=>'pending','confirmee'=>'confirmed','terminee'=>'done','annulee'=>'cancelled'][$r->statut] ?? 'pending'; @endphp
            <span class="status-badge {{ $sc }}"><span class="status-dot"></span>{{ ucfirst($r->statut) }}</span>
          </td>
          <td>
            <div class="emp-actions-cell">
              @if($r->statut === 'en_attente')
                <form method="POST" action="{{ route('salon.reservations.confirmer', $r->id) }}">
                  @csrf
                  <button class="tbl-btn tbl-btn-e" title="Confirmer">&#10003;</button>
                </form>
              @endif
              @if(in_array($r->statut,['en_attente','confirmee']))
                <button class="tbl-btn tbl-btn-r"
                        onclick="document.getElementById('annulModal{{ $r->id }}').style.display='flex'"
                        title="Annuler">&#10005;</button>
              @endif
              <a href="{{ route('salon.reservations.show', $r->id) }}" class="tbl-btn tbl-btn-h">&#8594;</a>
            </div>
            {{-- Modal annulation --}}
            <div class="modal-bg" id="annulModal{{ $r->id }}" style="display:none">
              <div class="modal" style="max-width:440px">
                <div class="modal-head"><div class="modal-t">Annuler la réservation</div><button class="modal-close" onclick="document.getElementById('annulModal{{ $r->id }}').style.display='none'">&#10005;</button></div>
                <form method="POST" action="{{ route('salon.reservations.annuler', $r->id) }}">
                  @csrf
                  <div class="modal-body">
                    <p style="font-size:.86rem;color:var(--ink-s);margin-bottom:1rem">Réservation de <strong>{{ $r->client->nomComplet() }}</strong> — {{ $r->date_heure->translatedFormat('D d M Y') }} {{ $r->date_heure->format('H:i') }}</p>
                    <div class="fg"><label>Motif d'annulation *</label>
                      <textarea name="motif" class="fi" rows="3" placeholder="Expliquez le motif..." required></textarea></div>
                  </div>
                  <div class="modal-foot">
                    <button type="button" class="btn-xs btn-xs-e" onclick="document.getElementById('annulModal{{ $r->id }}').style.display='none'">Fermer</button>
                    <button type="submit" class="btn-xs btn-xs-r">Confirmer l'annulation</button>
                  </div>
                </form>
              </div>
            </div>
          </td>
        </tr>
      @empty
        <tr><td colspan="7" style="text-align:center;padding:3rem;color:var(--ink-m)">Aucune réservation.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div style="margin-top:1.5rem">{{ $reservations->links() }}</div>
@endsection
