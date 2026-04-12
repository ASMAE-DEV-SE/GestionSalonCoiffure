@extends('layouts.admin')
@section('title', $salon->nom_salon)

@section('content')

<div class="admin-page-header">
  <div>
    <div class="admin-page-title">{{ $salon->nom_salon }}</div>
    <div class="admin-page-subtitle">{{ $salon->adresse }}, {{ $salon->quartier }} · {{ $salon->ville->nom_ville }}</div>
  </div>
  <div class="admin-header-actions">
    <a href="{{ route('admin.salons.index') }}" class="btn-admin-ghost">&#8592; Retour</a>
    @if($salon->valide !== 1)
      <form method="POST" action="{{ route('admin.salons.valider', $salon->id) }}" style="display:inline">
        @csrf
        <button type="submit" class="btn-admin-dark">&#10003; Valider ce salon</button>
      </form>
    @endif
    @if($salon->valide !== -1)
      <button class="btn-admin-ghost" style="border-color:#C04A3D;color:#C04A3D"
              onclick="document.getElementById('suspDetail').style.display='flex'">
        Suspendre
      </button>
    @endif
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 280px;gap:2rem;align-items:start">
  <div>

    {{-- Infos générales --}}
    <div class="admin-table-card" style="margin-bottom:1.5rem">
      <div class="admin-table-header"><div class="admin-table-title">Informations</div></div>
      <div style="padding:1.4rem;display:grid;grid-template-columns:1fr 1fr;gap:1rem 2rem">
        @foreach([
          'Nom'          => $salon->nom_salon,
          'Gérant'       => $salon->user->nomComplet() . ' (' . $salon->user->email . ')',
          'Ville'        => $salon->ville->nom_ville,
          'Adresse'      => $salon->adresse . ', ' . $salon->quartier,
          'Téléphone'    => $salon->telephone ?? '—',
          'Email'        => $salon->email ?? '—',
          'Validé le'    => $salon->date_valid?->translatedFormat('d F Y') ?? '—',
          'Inscrit le'   => $salon->created_at->translatedFormat('d F Y'),
        ] as $k => $v)
          <div>
            <div style="font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--ink-m);margin-bottom:.25rem">{{ $k }}</div>
            <div style="font-size:.9rem;font-weight:600;color:var(--ink-h)">{{ $v }}</div>
          </div>
        @endforeach
        @if($salon->description)
          <div style="grid-column:1/-1">
            <div style="font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--ink-m);margin-bottom:.25rem">Description</div>
            <div style="font-size:.88rem;color:var(--ink-s);line-height:1.65">{{ $salon->description }}</div>
          </div>
        @endif
      </div>
    </div>

    {{-- Dernières réservations --}}
    <div class="admin-table-card" style="margin-bottom:1.5rem">
      <div class="admin-table-header">
        <div class="admin-table-title">Réservations récentes</div>
        <span style="font-size:.78rem;color:var(--ink-m)">{{ $stats['reservations'] }} total · {{ $stats['terminées'] }} terminées</span>
      </div>
      <table class="admin-table">
        <thead><tr><th>Client</th><th>Service</th><th>Date</th><th>Statut</th></tr></thead>
        <tbody>
          @forelse($salon->reservations as $r)
            <tr>
              <td><strong>{{ $r->client->nomComplet() }}</strong></td>
              <td>{{ $r->service->nom_service }}</td>
              <td style="font-size:.8rem;color:var(--ink-m)">{{ $r->date_heure->translatedFormat('d M Y H:i') }}</td>
              <td>
                <span class="admin-status-badge {{ match($r->statut) { 'confirmee' => 'asb-ok', 'en_attente' => 'asb-pending', default => 'asb-sus' } }}">
                  {{ ucfirst($r->statut) }}
                </span>
              </td>
            </tr>
          @empty
            <tr><td colspan="4" style="text-align:center;padding:2rem;color:var(--ink-m)">Aucune réservation.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Services --}}
    <div class="admin-table-card">
      <div class="admin-table-header"><div class="admin-table-title">Services ({{ $salon->services->count() }})</div></div>
      <table class="admin-table">
        <thead><tr><th>Service</th><th>Catégorie</th><th>Prix</th><th>Durée</th><th>Actif</th></tr></thead>
        <tbody>
          @foreach($salon->services as $svc)
            <tr>
              <td><strong>{{ $svc->nom_service }}</strong></td>
              <td style="font-size:.8rem">{{ $svc->categorie }}</td>
              <td style="font-family:var(--fh);font-size:1rem;font-weight:700">{{ $svc->prix_format }}</td>
              <td style="font-size:.8rem">{{ $svc->duree_formatee }}</td>
              <td><span class="admin-status-badge {{ $svc->actif ? 'asb-ok' : 'asb-sus' }}">{{ $svc->actif ? 'Oui' : 'Non' }}</span></td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

  </div>

  {{-- Sidebar --}}
  <div>
    <div class="admin-table-card" style="margin-bottom:1.2rem">
      <div class="admin-table-header"><div class="admin-table-title">Statut</div></div>
      <div style="padding:1.2rem">
        <div class="pse-stat-row">
          <span class="pse-stat-k">Statut</span>
          <span class="pse-stat-v">
            @if($salon->estValide()) <span class="valide-badge">&#10003; Validé</span>
            @elseif($salon->estEnAttente()) <span class="en-attente-badge">En attente</span>
            @else <span class="bge-er">Suspendu</span> @endif
          </span>
        </div>
        <div class="pse-stat-row"><span class="pse-stat-k">Note</span><span class="pse-stat-v" style="color:#D4A844">&#9733; {{ number_format($salon->note_moy,1) }}</span></div>
        <div class="pse-stat-row"><span class="pse-stat-k">Avis</span><span class="pse-stat-v">{{ $salon->nb_avis }}</span></div>
        <div class="pse-stat-row"><span class="pse-stat-k">Employés</span><span class="pse-stat-v">{{ $salon->nb_employes }}</span></div>
        <div class="pse-stat-row"><span class="pse-stat-k">Réservations</span><span class="pse-stat-v">{{ $stats['reservations'] }}</span></div>
        <div class="pse-stat-row"><span class="pse-stat-k">Terminées</span><span class="pse-stat-v green">{{ $stats['terminées'] }}</span></div>
        <div class="pse-stat-row"><span class="pse-stat-k">Avis publiés</span><span class="pse-stat-v">{{ $stats['avis'] }}</span></div>
      </div>
    </div>

    {{-- Employés --}}
    @if($salon->employes->count())
      <div class="admin-table-card">
        <div class="admin-table-header"><div class="admin-table-title">Équipe</div></div>
        @foreach($salon->employes as $e)
          <div style="display:flex;align-items:center;gap:.8rem;padding:.8rem 1.2rem;border-bottom:1px solid var(--border2)">
            <div style="width:36px;height:36px;border-radius:50%;overflow:hidden;border:2px solid var(--p3);flex-shrink:0">
              <img src="{{ $e->photo_url }}" alt="{{ $e->nomComplet() }}" style="width:100%;height:100%;object-fit:cover">
            </div>
            <div>
              <div style="font-size:.86rem;font-weight:700;color:var(--ink-h)">{{ $e->nomComplet() }}</div>
              @php $sp = $e->specialites; if (is_string($sp)) $sp = json_decode($sp, true) ?? []; @endphp
              <div style="font-size:.7rem;color:var(--ink-m)">{{ implode(', ', array_slice(is_array($sp) ? $sp : [], 0, 2)) }}</div>
            </div>
            <span class="admin-status-badge {{ $e->actif ? 'asb-ok' : 'asb-sus' }}" style="margin-left:auto">{{ $e->actif ? 'Actif' : 'Inactif' }}</span>
          </div>
        @endforeach
      </div>
    @endif
  </div>
</div>

{{-- Modal suspension --}}
<div class="modal-bg" id="suspDetail" style="display:none">
  <div class="modal" style="max-width:440px">
    <div class="modal-head">
      <div class="modal-t">Suspendre — {{ $salon->nom_salon }}</div>
      <button class="modal-close" onclick="document.getElementById('suspDetail').style.display='none'">&#10005;</button>
    </div>
    <form method="POST" action="{{ route('admin.salons.suspendre', $salon->id) }}">
      @csrf
      <div class="modal-body">
        <div class="fg"><label>Motif *</label>
          <textarea name="motif" class="fi" rows="3" required placeholder="Violation des CGU..."></textarea></div>
      </div>
      <div class="modal-foot">
        <button type="button" class="btn-xs btn-xs-e" onclick="document.getElementById('suspDetail').style.display='none'">Annuler</button>
        <button type="submit" class="btn-xs btn-xs-r">Suspendre</button>
      </div>
    </form>
  </div>
</div>
@endsection
