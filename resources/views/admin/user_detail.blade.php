@extends('layouts.admin')
@section('title', $user->nomComplet())

@section('content')

<div class="admin-page-header">
  <div>
    <div class="admin-page-title">{{ $user->nomComplet() }}</div>
    <div class="admin-page-subtitle">
      {{ $user->email }}
      &nbsp;·&nbsp;
      <span class="admin-status-badge {{ match($user->role) { 'admin'=>'asb-sus','salon'=>'asb-pending',default=>'asb-ok' } }}">
        {{ ucfirst($user->role) }}
      </span>
    </div>
  </div>
  <div class="admin-header-actions">
    <a href="{{ route('admin.users.index') }}" class="btn-admin-ghost">&#8592; Retour</a>
    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn-admin-dark">&#9998; Modifier</a>
    @if($user->isSalon() && $user->salon)
      <a href="{{ route('admin.salons.show', $user->salon->id) }}" class="btn-admin-ghost" style="color:var(--p4d)">
        &#128341; Voir le salon
      </a>
    @endif
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 300px;gap:2rem;align-items:start">

  {{-- ── Colonne principale ────────────────────────────────── --}}
  <div>

    {{-- Informations personnelles --}}
    <div class="admin-table-card" style="margin-bottom:1.5rem">
      <div class="admin-table-header"><div class="admin-table-title">Informations personnelles</div></div>
      <div style="padding:1.4rem;display:grid;grid-template-columns:1fr 1fr;gap:1rem 2rem">
        @foreach([
          'Prénom'         => $user->prenom,
          'Nom'            => $user->nom,
          'Email'          => $user->email,
          'Téléphone'      => $user->telephone ?? '—',
          'Rôle'           => ucfirst($user->role),
          'Ville'          => $user->ville?->nom_ville ?? '—',
          'Inscrit le'     => $user->created_at->translatedFormat('d F Y'),
          'Email vérifié'  => $user->email_verifie_le ? $user->email_verifie_le->translatedFormat('d F Y') : 'Non vérifié',
        ] as $k => $v)
          <div>
            <div style="font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--ink-m);margin-bottom:.25rem">{{ $k }}</div>
            <div style="font-size:.9rem;font-weight:600;color:var(--ink-h)">{{ $v }}</div>
          </div>
        @endforeach
      </div>
    </div>

    {{-- Réservations (clients) --}}
    @if($user->isClient())
      <div class="admin-table-card" style="margin-bottom:1.5rem">
        <div class="admin-table-header">
          <div class="admin-table-title">Réservations</div>
          <span style="font-size:.78rem;color:var(--ink-m)">
            {{ $stats['reservations'] }} total · {{ $stats['terminees'] }} terminées · {{ $stats['annulees'] }} annulées
          </span>
        </div>
        <table class="admin-table">
          <thead><tr><th>Salon</th><th>Service</th><th>Date</th><th>Statut</th></tr></thead>
          <tbody>
            @forelse($user->reservations->sortByDesc('date_heure')->take(20) as $r)
              <tr>
                <td>
                  <strong>{{ $r->salon->nom_salon }}</strong>
                  <div style="font-size:.72rem;color:var(--ink-m)">{{ $r->salon->ville->nom_ville ?? '' }}</div>
                </td>
                <td>{{ $r->service->nom_service }}</td>
                <td style="font-size:.8rem;color:var(--ink-m)">{{ $r->date_heure->translatedFormat('d M Y H:i') }}</td>
                <td>
                  <span class="admin-status-badge {{ match($r->statut) {
                    'confirmee' => 'asb-ok',
                    'en_attente' => 'asb-pending',
                    'terminee' => 'asb-ok',
                    default => 'asb-sus'
                  } }}">{{ ucfirst(str_replace('_', ' ', $r->statut)) }}</span>
                </td>
              </tr>
            @empty
              <tr><td colspan="4" style="text-align:center;padding:2rem;color:var(--ink-m)">Aucune réservation.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    @endif

    {{-- Salon du gérant --}}
    @if($user->isSalon() && $user->salon)
      @php $salon = $user->salon; @endphp
      <div class="admin-table-card" style="margin-bottom:1.5rem">
        <div class="admin-table-header">
          <div class="admin-table-title">Salon géré : {{ $salon->nom_salon }}</div>
          <a href="{{ route('admin.salons.show', $salon->id) }}" class="admin-table-action" style="color:var(--p4d)">Voir le détail complet</a>
        </div>
        <div style="padding:1.4rem;display:grid;grid-template-columns:1fr 1fr;gap:1rem 2rem">
          @foreach([
            'Nom du salon'  => $salon->nom_salon,
            'Adresse'       => $salon->adresse . ', ' . $salon->quartier,
            'Ville'         => $salon->ville?->nom_ville ?? '—',
            'Téléphone'     => $salon->telephone ?? '—',
            'Note moyenne'  => number_format($salon->note_moy, 1) . ' / 5 (' . $salon->nb_avis . ' avis)',
            'Statut'        => $salon->libelleStatut(),
          ] as $k => $v)
            <div>
              <div style="font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--ink-m);margin-bottom:.25rem">{{ $k }}</div>
              <div style="font-size:.9rem;font-weight:600;color:var(--ink-h)">{{ $v }}</div>
            </div>
          @endforeach
        </div>
      </div>

      {{-- Services du salon --}}
      <div class="admin-table-card" style="margin-bottom:1.5rem">
        <div class="admin-table-header"><div class="admin-table-title">Services ({{ $salon->services->count() }})</div></div>
        <table class="admin-table">
          <thead><tr><th>Service</th><th>Catégorie</th><th>Prix</th><th>Durée</th><th>Actif</th></tr></thead>
          <tbody>
            @forelse($salon->services as $svc)
              <tr>
                <td><strong>{{ $svc->nom_service }}</strong></td>
                <td style="font-size:.8rem">{{ $svc->categorie }}</td>
                <td style="font-family:var(--fh);font-size:1rem;font-weight:700">{{ $svc->prix_format }}</td>
                <td style="font-size:.8rem">{{ $svc->duree_formatee }}</td>
                <td><span class="admin-status-badge {{ $svc->actif ? 'asb-ok' : 'asb-sus' }}">{{ $svc->actif ? 'Oui' : 'Non' }}</span></td>
              </tr>
            @empty
              <tr><td colspan="5" style="text-align:center;padding:1.5rem;color:var(--ink-m)">Aucun service.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- Équipe du salon --}}
      @if($salon->employes->count())
        <div class="admin-table-card" style="margin-bottom:1.5rem">
          <div class="admin-table-header"><div class="admin-table-title">Équipe ({{ $salon->employes->count() }})</div></div>
          @foreach($salon->employes as $e)
            <div style="display:flex;align-items:center;gap:.8rem;padding:.8rem 1.2rem;border-bottom:1px solid var(--border2)">
              <div style="width:36px;height:36px;border-radius:50%;overflow:hidden;border:2px solid var(--p3);flex-shrink:0">
                <img src="{{ $e->photo_url }}" alt="{{ $e->nomComplet() }}" style="width:100%;height:100%;object-fit:cover">
              </div>
              <div>
                <div style="font-size:.86rem;font-weight:700;color:var(--ink-h)">{{ $e->nomComplet() }}</div>
                @php $sp = $e->specialites; if (is_string($sp)) $sp = json_decode($sp, true) ?? []; @endphp
                <div style="font-size:.7rem;color:var(--ink-m)">{{ implode(', ', array_slice(is_array($sp) ? $sp : [], 0, 3)) }}</div>
              </div>
              <span class="admin-status-badge {{ $e->actif ? 'asb-ok' : 'asb-sus' }}" style="margin-left:auto">
                {{ $e->actif ? 'Actif' : 'Inactif' }}
              </span>
            </div>
          @endforeach
        </div>
      @endif
    @endif

    {{-- Notifications --}}
    @if($user->notifications->count())
      <div class="admin-table-card">
        <div class="admin-table-header">
          <div class="admin-table-title">Notifications ({{ $user->notifications->count() }})</div>
        </div>
        <table class="admin-table">
          <thead><tr><th>Type</th><th>Message</th><th>Date</th><th>Lu</th></tr></thead>
          <tbody>
            @foreach($user->notifications->sortByDesc('cree_le')->take(15) as $n)
              <tr>
                <td style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px">
                  {{ str_replace('_', ' ', $n->type) }}
                </td>
                <td style="font-size:.82rem;color:var(--ink-s)">{{ $n->donnees['message'] ?? '—' }}</td>
                <td style="font-size:.75rem;color:var(--ink-m)">{{ $n->cree_le?->translatedFormat('d M Y H:i') ?? '—' }}</td>
                <td>
                  @if($n->lu_le)
                    <span class="admin-status-badge asb-ok" style="font-size:.65rem">Lu</span>
                  @else
                    <span class="admin-status-badge asb-pending" style="font-size:.65rem">Non lu</span>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif

  </div>

  {{-- ── Sidebar ───────────────────────────────────────────── --}}
  <div>
    <div class="admin-table-card" style="margin-bottom:1.2rem">
      <div class="admin-table-header"><div class="admin-table-title">Résumé</div></div>
      <div style="padding:1.2rem">
        <div class="pse-stat-row">
          <span class="pse-stat-k">Rôle</span>
          <span class="pse-stat-v">
            <span class="admin-status-badge {{ match($user->role) { 'admin'=>'asb-sus','salon'=>'asb-pending',default=>'asb-ok' } }}">
              {{ ucfirst($user->role) }}
            </span>
          </span>
        </div>
        <div class="pse-stat-row">
          <span class="pse-stat-k">Email vérifié</span>
          <span class="pse-stat-v">
            @if($user->email_verifie_le)
              <span style="color:#3A7D44;font-weight:700">&#10003; Oui</span>
            @else
              <span style="color:#C04A3D;font-weight:700">&#10005; Non</span>
            @endif
          </span>
        </div>
        <div class="pse-stat-row">
          <span class="pse-stat-k">Inscrit le</span>
          <span class="pse-stat-v">{{ $user->created_at->translatedFormat('d M Y') }}</span>
        </div>

        @if($user->isClient())
          <div style="border-top:1px solid var(--border2);margin:.8rem 0"></div>
          <div class="pse-stat-row"><span class="pse-stat-k">Réservations</span><span class="pse-stat-v">{{ $stats['reservations'] }}</span></div>
          <div class="pse-stat-row"><span class="pse-stat-k">Confirmées</span><span class="pse-stat-v">{{ $stats['confirmees'] }}</span></div>
          <div class="pse-stat-row"><span class="pse-stat-k">Terminées</span><span class="pse-stat-v green">{{ $stats['terminees'] }}</span></div>
          <div class="pse-stat-row"><span class="pse-stat-k">Annulées</span><span class="pse-stat-v" style="color:#C04A3D">{{ $stats['annulees'] }}</span></div>
          <div class="pse-stat-row"><span class="pse-stat-k">Notifications</span><span class="pse-stat-v">{{ $stats['notifications'] }}</span></div>
        @elseif($user->isSalon() && $user->salon)
          <div style="border-top:1px solid var(--border2);margin:.8rem 0"></div>
          <div class="pse-stat-row"><span class="pse-stat-k">Réservations</span><span class="pse-stat-v">{{ $stats['reservations'] }}</span></div>
          <div class="pse-stat-row"><span class="pse-stat-k">Services</span><span class="pse-stat-v">{{ $stats['services'] }}</span></div>
          <div class="pse-stat-row"><span class="pse-stat-k">Employés</span><span class="pse-stat-v">{{ $stats['employes'] }}</span></div>
          <div class="pse-stat-row"><span class="pse-stat-k">Avis reçus</span><span class="pse-stat-v">{{ $stats['avis'] }}</span></div>
        @endif
      </div>
    </div>

    {{-- Actions rapides --}}
    <div class="admin-table-card">
      <div class="admin-table-header"><div class="admin-table-title">Actions</div></div>
      <div style="padding:1.2rem;display:flex;flex-direction:column;gap:.6rem">
        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn-admin-dark" style="text-align:center;display:block">
          &#9998; Modifier le compte
        </a>
        @if($user->isSalon() && $user->salon)
          <a href="{{ route('admin.salons.show', $user->salon->id) }}" class="btn-admin-ghost" style="text-align:center;display:block">
            &#128341; Gérer le salon
          </a>
        @endif
        @if($user->id !== auth()->id())
          <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}"
                onsubmit="return confirm('Supprimer définitivement {{ addslashes($user->nomComplet()) }} ?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn-admin-ghost" style="width:100%;color:#C04A3D;border-color:#C04A3D">
              &#128465; Supprimer le compte
            </button>
          </form>
        @endif
      </div>
    </div>
  </div>

</div>

@endsection
