@extends('layouts.admin')
@section('title', 'Gestion des salons')

@section('content')

<div class="admin-page-header">
  <div>
    <div class="admin-page-title">Salons</div>
    <div class="admin-page-subtitle">
      {{ $compteurs['valides'] }} validés · {{ $compteurs['attente'] }} en attente · {{ $compteurs['suspendus'] }} suspendus
    </div>
  </div>
  <div class="admin-header-actions">
    <a href="{{ route('admin.statistiques.export') }}" class="btn-admin-ghost">Export CSV</a>
  </div>
</div>

{{-- Filtres ─────────────────────────────────────────────── --}}
<form method="GET" style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;margin-bottom:1.8rem">
  <input type="text" name="q" class="tb-search" placeholder="&#128269; Rechercher un salon..." value="{{ request('q') }}" style="min-width:240px">
  @foreach([''=>'Tous','valide'=>'Validés','attente'=>'En attente','suspendu'=>'Suspendus'] as $v => $l)
    <a href="{{ request()->fullUrlWithQuery(['statut'=>$v,'page'=>1]) }}"
       class="tb-filter {{ request('statut','')===$v ? 'on' : '' }}">
      {{ $l }}
      @if($v==='attente' && $compteurs['attente']>0)
        <span style="background:#E8562A;color:#fff;font-size:.6rem;font-weight:700;padding:.06rem .4rem;border-radius:8px;margin-left:.3rem">{{ $compteurs['attente'] }}</span>
      @endif
    </a>
  @endforeach
  <select name="ville_id" class="tb-search" style="max-width:180px">
    <option value="">Toutes les villes</option>
    @foreach($villes as $v)<option value="{{ $v->id }}" {{ request('ville_id')==$v->id?'selected':'' }}>{{ $v->nom_ville }}</option>@endforeach
  </select>
  <button type="submit" class="btn-add" style="padding:.6rem 1.1rem;font-size:.76rem">Filtrer</button>
</form>

{{-- Tableau ─────────────────────────────────────────────── --}}
<div class="admin-table-card">
  <div class="admin-table-header">
    <div class="admin-table-title">{{ $salons->total() }} salon(s)</div>
  </div>
  <table class="admin-table">
    <thead><tr>
      <th>Salon</th><th>Gérant</th><th>Ville</th><th>Note</th><th>Réservations</th><th>Statut</th><th>Actions</th>
    </tr></thead>
    <tbody>
      @forelse($salons as $s)
        <tr>
          <td>
            <strong>{{ $s->nom_salon }}</strong>
            <div style="font-size:.72rem;color:var(--ink-m)">{{ $s->adresse }}, {{ $s->quartier }}</div>
          </td>
          <td>
            {{ $s->user->nomComplet() }}
            <div style="font-size:.72rem;color:var(--ink-m)">{{ $s->user->email }}</div>
          </td>
          <td>{{ $s->ville->nom_ville }}</td>
          <td>
            @if($s->nb_avis > 0)
              <span style="color:#D4A844;font-weight:700">&#9733; {{ number_format($s->note_moy,1) }}</span>
              <div style="font-size:.7rem;color:var(--ink-m)">{{ $s->nb_avis }} avis</div>
            @else
              <span style="color:var(--ink-d);font-size:.78rem">—</span>
            @endif
          </td>
          <td style="font-weight:700;text-align:center">{{ $s->reservations()->count() }}</td>
          <td>
            @php $sc = match($s->valide) { 1 => 'asb-ok', 0 => 'asb-pending', default => 'asb-sus' }; @endphp
            <span class="admin-status-badge {{ $sc }}">{{ $s->libelleStatut() }}</span>
          </td>
          <td>
            <div style="display:flex;gap:.4rem;flex-wrap:wrap">
              <a href="{{ route('admin.salons.show', $s->id) }}" class="admin-table-action">Voir</a>
              @if($s->valide !== 1)
                <form method="POST" action="{{ route('admin.salons.valider', $s->id) }}">
                  @csrf
                  <button type="submit" class="admin-table-action" style="color:var(--p4d)">&#10003; Valider</button>
                </form>
              @endif
              @if($s->valide !== -1)
                <button class="admin-table-action" style="color:#C04A3D"
                        onclick="document.getElementById('suspModal{{ $s->id }}').style.display='flex'">
                  Suspendre
                </button>
              @endif
              <form method="POST" action="{{ route('admin.salons.destroy', $s->id) }}"
                    onsubmit="return confirm('Supprimer définitivement {{ addslashes($s->nom_salon) }} ?')">
                @csrf @method('DELETE')
                <button type="submit" class="admin-table-action" style="color:#C04A3D">&#128465;</button>
              </form>
            </div>

            {{-- Modal suspension --}}
            <div class="modal-bg" id="suspModal{{ $s->id }}" style="display:none">
              <div class="modal" style="max-width:440px">
                <div class="modal-head">
                  <div class="modal-t">Suspendre — {{ $s->nom_salon }}</div>
                  <button class="modal-close" onclick="document.getElementById('suspModal{{ $s->id }}').style.display='none'">&#10005;</button>
                </div>
                <form method="POST" action="{{ route('admin.salons.suspendre', $s->id) }}">
                  @csrf
                  <div class="modal-body">
                    <div class="fg"><label>Motif de suspension *</label>
                      <textarea name="motif" class="fi" rows="3" placeholder="Violation des CGU, signalements répétés..." required></textarea></div>
                  </div>
                  <div class="modal-foot">
                    <button type="button" class="btn-xs btn-xs-e" onclick="document.getElementById('suspModal{{ $s->id }}').style.display='none'">Annuler</button>
                    <button type="submit" class="btn-xs btn-xs-r">Confirmer la suspension</button>
                  </div>
                </form>
              </div>
            </div>
          </td>
        </tr>
      @empty
        <tr><td colspan="7" style="text-align:center;padding:2.5rem;color:var(--ink-m)">Aucun salon trouvé.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div style="margin-top:1.2rem">{{ $salons->links() }}</div>
@endsection
