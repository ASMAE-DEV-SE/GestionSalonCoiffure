@extends('layouts.dashboard')
@section('title', "Gestion de l'équipe")

@section('content')

<div class="dash-page-header">
  <div>
    <div class="dash-greeting">Équipe</div>
    <div class="dash-date">{{ $salon->nom_salon }} · {{ $employes->count() }} membre(s)</div>
  </div>
  <button class="btn-new-booking" onclick="openEmpModal()">+ Ajouter un(e) employé(e)</button>
</div>

{{-- Grille employés ─────────────────────────────────────── --}}
@if($employes->isEmpty())
  <div style="padding:4rem 0;text-align:center;border:2px dashed var(--border2)">
    <div style="font-size:2.5rem;margin-bottom:1rem">&#9786;</div>
    <p style="color:var(--ink-m);margin-bottom:1.2rem">Aucun employé. Ajoutez votre équipe.</p>
    <button class="btn-new-booking" onclick="openEmpModal()">+ Ajouter un(e) employé(e)</button>
  </div>
@else
  <div class="emp-grid">
    @foreach($employes as $emp)
      <div class="emp-card">
        <div class="emp-head">
          <div class="emp-status {{ $emp->actif ? 'es-on' : 'es-off' }}"></div>
          <div class="emp-av">
            <img src="{{ $emp->photo_url }}" alt="{{ $emp->nomComplet() }}">
          </div>
          <div class="emp-nm">{{ $emp->nomComplet() }}</div>
          <div class="emp-role">{{ implode(', ', array_slice($emp->specialites ?? [], 0, 2)) }}</div>
        </div>
        <div class="emp-body">
          <div class="emp-svc">
            @foreach($emp->specialites ?? [] as $spe)
              <span class="svc-tag">{{ $spe }}</span>
            @endforeach
          </div>
          <div class="emp-stats">
            <div class="emp-st">
              <div class="emp-st-v">{{ $emp->reservationsAVenir()->count() }}</div>
              <div class="emp-st-l">À venir</div>
            </div>
            <div class="emp-st">
              <div class="emp-st-v">{{ $emp->reservations()->where('statut','terminee')->count() }}</div>
              <div class="emp-st-l">Terminées</div>
            </div>
          </div>
          <div class="emp-horaires">
            @php
              $jours = ['lun','mar','mer','jeu','ven','sam','dim'];
              $joursLong = ['lundi','mardi','mercredi','jeudi','vendredi','samedi','dimanche'];
              $ouvert = [];
              foreach($joursLong as $i => $j) {
                if(isset($emp->horaires[$j]) && !($emp->horaires[$j]['ferme'] ?? true)) {
                  $ouvert[] = $jours[$i];
                }
              }
            @endphp
            {{ count($ouvert) > 0 ? implode(' · ', $ouvert) : 'Horaires non définis' }}
          </div>
        </div>
        <div class="emp-foot">
          <button class="tbl-btn tbl-btn-e"
                  onclick="openEditEmp({{ $emp->id }}, '{{ $emp->prenom }}', '{{ $emp->nom }}', '{{ $emp->email }}', '{{ $emp->tel }}')">
            Modifier
          </button>
          <form method="POST" action="{{ route('salon.employes.destroy', $emp->id) }}"
                onsubmit="return confirm('Retirer {{ $emp->nomComplet() }} de l\'équipe ?')">
            @csrf @method('DELETE')
            <button type="submit" class="tbl-btn tbl-btn-r">Retirer</button>
          </form>
        </div>
      </div>
    @endforeach
  </div>
@endif

{{-- Modal Employé ──────────────────────────────────────── --}}
<div class="modal-bg" id="empModal" style="display:none">
  <div class="modal" style="max-width:620px">
    <div class="modal-head">
      <div class="modal-t" id="empModalTitle">Ajouter un(e) employé(e)</div>
      <button class="modal-close" onclick="closeEmpModal()">&#10005;</button>
    </div>
    <form method="POST" id="empForm" action="{{ route('salon.employes.store') }}" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="_method" id="empMethod" value="POST">
      <div class="modal-body">
        <div class="row2m">
          <div class="fg"><label>Prénom *</label><input type="text" name="prenom" id="ep_prenom" class="fi" required></div>
          <div class="fg"><label>Nom *</label><input type="text" name="nom" id="ep_nom" class="fi" required></div>
        </div>
        <div class="row2m">
          <div class="fg"><label>Email</label><input type="email" name="email" id="ep_email" class="fi"></div>
          <div class="fg"><label>Téléphone</label><input type="tel" name="tel" id="ep_tel" class="fi"></div>
        </div>
        <div class="fg"><label>Spécialités</label>
          <div class="ge-modal-spe">
            @foreach(['Coupe femme','Coupe homme','Coloration','Mèches','Balayage','Brushing','Lissage','Soin visage','Manucure','Pédicure','Massage','Épilation'] as $spe)
              <label class="spe-check">
                <input type="checkbox" name="specialites[]" value="{{ $spe }}"> {{ $spe }}
              </label>
            @endforeach
          </div>
        </div>
        <div class="fg"><label>Photo <span style="font-weight:400;font-size:.78rem;color:var(--ink-m)">(max 2 Mo)</span></label>
          <input type="file" name="photo" class="fi" accept="image/jpeg,image/png,image/webp"></div>
        <div class="fg" style="display:flex;align-items:center;justify-content:space-between;margin-top:.5rem">
          <label style="font-size:.7rem;font-weight:700;letter-spacing:1px;text-transform:uppercase">Actif</label>
          <label class="toggle"><input type="checkbox" name="actif" checked><span class="tgl-sl"></span></label>
        </div>
      </div>
      <div class="modal-foot">
        <button type="button" class="btn-xs btn-xs-r" onclick="closeEmpModal()">Annuler</button>
        <button type="submit" class="btn-add">Enregistrer</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
function openEmpModal() {
  document.getElementById('empModalTitle').textContent = 'Ajouter un(e) employé(e)';
  document.getElementById('empForm').action = '{{ route('salon.employes.store') }}';
  document.getElementById('empMethod').value = 'POST';
  ['prenom','nom','email','tel'].forEach(f => document.getElementById('ep_' + f).value = '');
  document.getElementById('empModal').style.display = 'flex';
}
function openEditEmp(id, prenom, nom, email, tel) {
  document.getElementById('empModalTitle').textContent = 'Modifier — ' + prenom + ' ' + nom;
  document.getElementById('empForm').action = '/salon/employes/' + id;
  document.getElementById('empMethod').value = 'PUT';
  document.getElementById('ep_prenom').value = prenom;
  document.getElementById('ep_nom').value    = nom;
  document.getElementById('ep_email').value  = email;
  document.getElementById('ep_tel').value    = tel;
  document.getElementById('empModal').style.display = 'flex';
}
function closeEmpModal() { document.getElementById('empModal').style.display = 'none'; }
document.getElementById('empModal').addEventListener('click', e => { if(e.target === e.currentTarget) closeEmpModal(); });
</script>
@endpush
@endsection
