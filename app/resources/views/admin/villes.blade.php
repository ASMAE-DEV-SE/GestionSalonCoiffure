@extends('layouts.admin')
@section('title', 'Gestion des villes')

@section('content')

<div class="admin-page-header">
  <div>
    <div class="admin-page-title">Villes</div>
    <div class="admin-page-subtitle">{{ $villes->total() }} villes enregistrées</div>
  </div>
  <div class="admin-header-actions">
    <button class="btn-admin-dark" onclick="document.getElementById('villeModal').style.display='flex'">+ Ajouter une ville</button>
  </div>
</div>

<div class="admin-table-card">
  <div class="admin-table-header">
    <div class="admin-table-title">Liste des villes</div>
  </div>
  <table class="admin-table">
    <thead><tr>
      <th>Ville</th><th>Région</th><th>Code postal</th><th>Salons inscrits</th><th>Salons validés</th><th>Statut</th><th>Actions</th>
    </tr></thead>
    <tbody>
      @foreach($villes as $v)
        <tr>
          <td><strong>{{ $v->nom_ville }}</strong></td>
          <td style="font-size:.82rem;color:var(--ink-m)">{{ $v->region }}</td>
          <td style="font-size:.82rem">{{ $v->code_postal }}</td>
          <td style="text-align:center;font-weight:700">{{ $v->salons_count }}</td>
          <td style="text-align:center;font-weight:700;color:var(--p4d)">{{ $v->salons_valides_count }}</td>
          <td>
            <span class="admin-status-badge {{ $v->actif ? 'asb-ok' : 'asb-sus' }}">
              {{ $v->actif ? 'Active' : 'Inactive' }}
            </span>
          </td>
          <td>
            <div style="display:flex;gap:.4rem">
              <button class="admin-table-action"
                      onclick="openVilleEdit({{ $v->id }},'{{ addslashes($v->nom_ville) }}','{{ $v->code_postal }}','{{ addslashes($v->region) }}',{{ $v->actif?'true':'false' }})">
                Modifier
              </button>
              <form method="POST" action="{{ route('admin.villes.destroy', $v->id) }}"
                    onsubmit="return confirm('Supprimer {{ $v->nom_ville }} ?')">
                @csrf @method('DELETE')
                <button type="submit" class="admin-table-action" style="color:#C04A3D">&#128465;</button>
              </form>
            </div>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>

<div style="margin-top:1.2rem">{{ $villes->links() }}</div>

{{-- Modal Ajouter / Modifier --}}
<div class="modal-bg" id="villeModal" style="display:none">
  <div class="modal" style="max-width:500px">
    <div class="modal-head">
      <div class="modal-t" id="villeModalTitle">Ajouter une ville</div>
      <button class="modal-close" onclick="closeVilleModal()">&#10005;</button>
    </div>
    <form method="POST" id="villeForm" action="{{ route('admin.villes.store') }}">
      @csrf
      <input type="hidden" name="_method" id="villeMethod" value="POST">
      <div class="modal-body">
        <div class="row2m">
          <div class="fg"><label>Nom de la ville *</label>
            <input type="text" name="nom_ville" id="v_nom" class="fi" placeholder="Kénitra" required></div>
          <div class="fg"><label>Code postal *</label>
            <input type="text" name="code_postal" id="v_cp" class="fi" placeholder="14000" required></div>
        </div>
        <div class="row2m">
          <div class="fg"><label>Région *</label>
            <select name="region" id="v_region" class="fi" required>
              @foreach(['Rabat-Salé-Kénitra','Grand Casablanca-Settat','Marrakech-Safi','Fès-Meknès','Tanger-Tétouan-Al Hoceïma','Oriental','Souss-Massa','Laâyoune-Sakia El Hamra','Guelmim-Oued Noun','Drâa-Tafilalet','Béni Mellal-Khénifra','Dakhla-Oued Ed-Dahab'] as $r)
                <option value="{{ $r }}">{{ $r }}</option>
              @endforeach
            </select>
          </div>
          <div class="fg"><label>Pays</label>
            <input type="text" name="pays" class="fi" value="Maroc" readonly></div>
        </div>
        <div class="fg" style="display:flex;align-items:center;justify-content:space-between;margin-top:.5rem">
          <label style="font-size:.7rem;font-weight:700;letter-spacing:1px;text-transform:uppercase">Ville active</label>
          <label class="toggle"><input type="checkbox" name="actif" id="v_actif" checked><span class="tgl-sl"></span></label>
        </div>
      </div>
      <div class="modal-foot">
        <button type="button" class="btn-xs btn-xs-r" onclick="closeVilleModal()">Annuler</button>
        <button type="submit" class="btn-add">Enregistrer</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
function closeVilleModal() { document.getElementById('villeModal').style.display='none'; }
function openVilleEdit(id, nom, cp, region, actif) {
  document.getElementById('villeModalTitle').textContent = 'Modifier — ' + nom;
  document.getElementById('villeForm').action = '/admin/villes/' + id;
  document.getElementById('villeMethod').value = 'PUT';
  document.getElementById('v_nom').value    = nom;
  document.getElementById('v_cp').value     = cp;
  document.getElementById('v_region').value = region;
  document.getElementById('v_actif').checked = actif;
  document.getElementById('villeModal').style.display = 'flex';
}
document.getElementById('villeModal').addEventListener('click', e => { if(e.target===e.currentTarget) closeVilleModal(); });
</script>
@endpush
@endsection
