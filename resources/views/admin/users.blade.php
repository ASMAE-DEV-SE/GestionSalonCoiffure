@extends('layouts.admin')
@section('title', 'Utilisateurs')

@section('content')

<div class="admin-page-header">
  <div>
    <div class="admin-page-title">Utilisateurs</div>
    <div class="admin-page-subtitle">
      {{ $compteurs['total'] }} inscrits · {{ $compteurs['clients'] }} clients · {{ $compteurs['salons'] }} gérants · {{ $compteurs['admins'] }} admins
    </div>
  </div>
  <div class="admin-header-actions">
    <a href="{{ route('admin.users.create') }}" class="btn-admin-dark">+ Nouvel utilisateur</a>
  </div>
</div>

{{-- Filtres ─────────────────────────────────────────────── --}}
<form method="GET" style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;margin-bottom:1.8rem">
  <input type="text" name="q" class="tb-search" placeholder="&#128269; Nom, email..." value="{{ request('q') }}" style="min-width:260px">
  @foreach([''=>'Tous','client'=>'Clients','salon'=>'Salons','admin'=>'Admins'] as $v => $l)
    <a href="{{ request()->fullUrlWithQuery(['role'=>$v,'page'=>1]) }}"
       class="tb-filter {{ request('role','')===$v ? 'on' : '' }}">{{ $l }}</a>
  @endforeach
  @foreach([''=>'Tous emails','1'=>'Email vérifié','0'=>'Non vérifié'] as $v => $l)
    <a href="{{ request()->fullUrlWithQuery(['verifie'=>$v,'page'=>1]) }}"
       class="tb-filter {{ request('verifie','')===$v ? 'on' : '' }}">{{ $l }}</a>
  @endforeach
  <button type="submit" class="btn-add" style="padding:.6rem 1.1rem;font-size:.76rem">Filtrer</button>
</form>

<div class="admin-table-card">
  <div class="admin-table-header">
    <div class="admin-table-title">{{ $users->total() }} utilisateur(s)</div>
  </div>
  <table class="admin-table">
    <thead><tr><th>Utilisateur</th><th>Rôle</th><th>Téléphone</th><th>Email vérifié</th><th>Inscrit le</th><th>Actions</th></tr></thead>
    <tbody>
      @forelse($users as $u)
        <tr>
          <td>
            <strong>{{ $u->nomComplet() }}</strong>
            <div style="font-size:.72rem;color:var(--ink-m)">{{ $u->email }}</div>
          </td>
          <td>
            <span class="admin-status-badge {{ match($u->role) { 'admin'=>'asb-sus','salon'=>'asb-pending',default=>'asb-ok' } }}">
              {{ ucfirst($u->role) }}
            </span>
          </td>
          <td style="font-size:.82rem">{{ $u->telephone ?? '—' }}</td>
          <td>
            @if($u->email_verifie_le)
              <span class="admin-status-badge asb-ok">&#10003; {{ $u->email_verifie_le->format('d/m/Y') }}</span>
            @else
              <span class="admin-status-badge asb-sus">Non vérifié</span>
            @endif
          </td>
          <td style="font-size:.78rem;color:var(--ink-m)">{{ $u->created_at->translatedFormat('d M Y') }}</td>
          <td>
            <div style="display:flex;gap:.5rem">
              <a href="{{ route('admin.users.show', $u->id) }}" class="admin-table-action">Voir</a>
              <a href="{{ route('admin.users.edit', $u->id) }}" class="admin-table-action">Modifier</a>
              @if($u->isSalon() && $u->salon)
                <a href="{{ route('admin.salons.show', $u->salon->id) }}" class="admin-table-action" style="color:var(--p4d)">Salon</a>
              @endif
              @if($u->id !== auth()->id())
                <form method="POST" action="{{ route('admin.users.destroy', $u->id) }}"
                      onsubmit="return confirm('Supprimer {{ addslashes($u->nomComplet()) }} ?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="admin-table-action" style="color:#C04A3D">&#128465;</button>
                </form>
              @endif
            </div>
          </td>
        </tr>
      @empty
        <tr><td colspan="6" style="text-align:center;padding:2.5rem;color:var(--ink-m)">Aucun utilisateur trouvé.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div style="margin-top:1.2rem">{{ $users->links() }}</div>
@endsection
