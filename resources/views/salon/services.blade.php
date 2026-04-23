@extends('layouts.dashboard')
@section('title', 'Gestion des services')

@section('content')

<div class="dash-page-header">
  <div>
    <div class="dash-greeting">Services</div>
    <div class="dash-date">{{ $salon->nom_salon }}</div>
  </div>
  <button class="btn-new-booking" onclick="openModal()">+ Ajouter un service</button>
</div>

{{-- Filtres catégories ──────────────────────────────────── --}}
<div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:2rem">
  <button class="tb-filter on" onclick="filterCat(this,'all')">Tous</button>
  @foreach($categories as $cat)
    <button class="tb-filter" onclick="filterCat(this,'{{ Str::slug($cat) }}')">{{ $cat }}</button>
  @endforeach
</div>

{{-- Grille services ─────────────────────────────────────── --}}
@foreach($services as $categorie => $liste)
  <div style="margin-bottom:2.5rem" data-cat-section="{{ Str::slug($categorie) }}">
    <h2 style="font-family:var(--fh);font-size:1.3rem;color:var(--ink-h);margin-bottom:1rem;padding-bottom:.5rem;border-bottom:2px solid var(--border2)">
      {{ $categorie }} <span style="font-size:.78rem;font-weight:600;color:var(--ink-m)">({{ $liste->count() }})</span>
    </h2>
    <div class="svc-grid">
      @foreach($liste as $svc)
        <div class="svc-card {{ !$svc->actif ? 'inactive' : '' }}" data-cat="{{ Str::slug($categorie) }}">
          <div class="svc-img">
            <img src="{{ $svc->photo_url }}" alt="{{ $svc->nom_service }}" loading="lazy">
            @if(!$svc->has_image)
              <span class="svc-img-default" title="Image par défaut (catégorie)">Par défaut</span>
            @endif
          </div>
          <div class="svc-cat">{{ $svc->categorie }}</div>
          @if(!$svc->actif)<span class="badge-off">Inactif</span>@endif
          <div class="svc-body">
            <div class="svc-nm">{{ $svc->nom_service }}</div>
            @if($svc->description)
              <div class="svc-desc">{{ $svc->description }}</div>
            @endif
            <div class="svc-meta">
              <div class="svc-m">&#128337; <span>{{ $svc->duree_formatee }}</span></div>
            </div>
            <div class="svc-price">{{ number_format($svc->prix, 0, ',', ' ') }}<small> MAD</small></div>
          </div>
          <div class="svc-foot">
            <div class="svc-stats">
              <span class="svc-stat">RDV : <span>{{ $svc->reservations()->count() }}</span></span>
            </div>
            <div class="svc-actions">
              <button class="btn-xs btn-xs-e"
                      onclick='openEdit(@json($svc->id), @json($svc->nom_service), @json($svc->description ?? ""), @json((float)$svc->prix), @json((int)$svc->duree_minu), @json($svc->categorie), @json((bool)$svc->actif), @json($svc->has_image ? $svc->photo_url : null))'>
                Modifier
              </button>
              <form method="POST" action="{{ route('salon.services.destroy', $svc->id) }}"
                    onsubmit="return confirm('Supprimer ce service ?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn-xs btn-xs-r">Supprimer</button>
              </form>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
@endforeach

@if($services->isEmpty())
  <div style="padding:4rem 0;text-align:center;border:2px dashed var(--border2)">
    <div style="font-size:2.5rem;margin-bottom:1rem">&#9986;</div>
    <p style="color:var(--ink-m);margin-bottom:1.2rem">Aucun service pour l'instant.</p>
    <button class="btn-new-booking" onclick="openModal()">+ Ajouter votre premier service</button>
  </div>
@endif

{{-- Modal Ajouter/Modifier ──────────────────────────────── --}}
<div class="modal-bg" id="svcModal" style="display:none">
  <div class="modal">
    <div class="modal-head">
      <div class="modal-t" id="modalTitle">Ajouter un service</div>
      <button class="modal-close" onclick="closeModal()">&#10005;</button>
    </div>
    <form method="POST" id="svcForm" action="{{ route('salon.services.store') }}" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="_method" id="formMethod" value="POST">
      <input type="hidden" name="image_supprimer" id="f_img_supprimer" value="0">
      <div class="modal-body">

        {{-- Upload image --}}
        <div class="fg">
          <label style="font-size:.7rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--ink-b);margin-bottom:.5rem;display:block">
            Image du service <span style="font-weight:400;font-size:.78rem;color:var(--ink-m);text-transform:none;letter-spacing:0">(optionnel — JPG / PNG / WEBP, 5 Mo max)</span>
          </label>
          <div class="svc-upload">
            <div class="svc-upload-preview" id="f_img_preview">
              <img id="f_img_preview_img" src="" alt="" style="display:none">
              <div class="svc-upload-placeholder" id="f_img_placeholder">
                <div style="font-size:2rem;margin-bottom:.2rem">&#128247;</div>
                <div style="font-size:.78rem;color:var(--ink-m)">Aucune image — une image par défaut sera utilisée</div>
              </div>
            </div>
            <div class="svc-upload-actions">
              <label class="btn-xs btn-xs-e" style="cursor:pointer;display:inline-block">
                <span id="f_img_btn_label">Choisir une image</span>
                <input type="file" name="image" id="f_img" accept="image/jpeg,image/png,image/webp" style="display:none" onchange="previewImage(this)">
              </label>
              <button type="button" class="btn-xs btn-xs-r" id="f_img_remove_btn" style="display:none" onclick="removeImage()">Retirer l'image</button>
            </div>
          </div>
        </div>

        <div class="row2m">
          <div class="fg"><label>Nom du service *</label>
            <input type="text" name="nom_service" id="f_nom" class="fi" placeholder="Coupe femme" required></div>
          <div class="fg"><label>Catégorie *</label>
            <select name="categorie" id="f_cat" class="fi" required>
              @foreach(['Coiffure','Couleur','Soins','Ongles','Massage','Épilation','Barbe','Autre'] as $c)
                <option value="{{ $c }}">{{ $c }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="fg"><label>Description <span style="font-weight:400;font-size:.78rem;color:var(--ink-m)">(optionnel)</span></label>
          <textarea name="description" id="f_desc" class="fi" rows="2" placeholder="Coupe + shampooing inclus..."></textarea></div>
        <div class="row2m">
          <div class="fg"><label>Prix (MAD) *</label>
            <input type="number" name="prix" id="f_prix" class="fi" placeholder="120" min="0" step="0.01" required></div>
          <div class="fg"><label>Durée (minutes) *</label>
            <input type="number" name="duree_minu" id="f_duree" class="fi" placeholder="45" min="10" max="480" required></div>
        </div>
        <div class="fg" style="display:flex;align-items:center;justify-content:space-between;margin-top:.5rem">
          <label style="font-size:.7rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--ink-b)">Service actif</label>
          <label class="toggle"><input type="checkbox" name="actif" id="f_actif" checked><span class="tgl-sl"></span></label>
        </div>
      </div>
      <div class="modal-foot">
        <button type="button" class="btn-xs btn-xs-r" onclick="closeModal()">Annuler</button>
        <button type="submit" class="btn-add">Enregistrer</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
function resetImagePreview() {
  const img  = document.getElementById('f_img_preview_img');
  const ph   = document.getElementById('f_img_placeholder');
  const rm   = document.getElementById('f_img_remove_btn');
  const file = document.getElementById('f_img');
  const lbl  = document.getElementById('f_img_btn_label');
  img.src = ''; img.style.display = 'none';
  ph.style.display = 'flex';
  rm.style.display = 'none';
  file.value = '';
  lbl.textContent = 'Choisir une image';
  document.getElementById('f_img_supprimer').value = '0';
}
function showImagePreview(url, isExisting) {
  const img = document.getElementById('f_img_preview_img');
  const ph  = document.getElementById('f_img_placeholder');
  const rm  = document.getElementById('f_img_remove_btn');
  const lbl = document.getElementById('f_img_btn_label');
  img.src = url; img.style.display = 'block';
  ph.style.display = 'none';
  rm.style.display = 'inline-block';
  lbl.textContent = isExisting ? 'Remplacer l\'image' : 'Changer d\'image';
}
function previewImage(input) {
  if (!input.files || !input.files[0]) return;
  const reader = new FileReader();
  reader.onload = e => showImagePreview(e.target.result, false);
  reader.readAsDataURL(input.files[0]);
  document.getElementById('f_img_supprimer').value = '0';
}
function removeImage() {
  document.getElementById('f_img').value = '';
  document.getElementById('f_img_supprimer').value = '1';
  resetImagePreview();
  document.getElementById('f_img_supprimer').value = '1';
}

function openModal() {
  document.getElementById('modalTitle').textContent = 'Ajouter un service';
  document.getElementById('svcForm').action = '{{ route('salon.services.store') }}';
  document.getElementById('formMethod').value = 'POST';
  ['nom','desc','prix','duree'].forEach(f => document.getElementById('f_' + f).value = '');
  document.getElementById('f_actif').checked = true;
  resetImagePreview();
  document.getElementById('svcModal').style.display = 'flex';
}
function openEdit(id, nom, desc, prix, duree, cat, actif, imageUrl) {
  document.getElementById('modalTitle').textContent = 'Modifier — ' + nom;
  document.getElementById('svcForm').action = '/salon/services/' + id;
  document.getElementById('formMethod').value = 'PUT';
  document.getElementById('f_nom').value   = nom;
  document.getElementById('f_desc').value  = desc;
  document.getElementById('f_prix').value  = prix;
  document.getElementById('f_duree').value = duree;
  document.getElementById('f_cat').value   = cat;
  document.getElementById('f_actif').checked = actif;
  resetImagePreview();
  if (imageUrl) { showImagePreview(imageUrl, true); }
  document.getElementById('svcModal').style.display = 'flex';
}
function closeModal() { document.getElementById('svcModal').style.display = 'none'; }
document.getElementById('svcModal').addEventListener('click', e => { if(e.target === e.currentTarget) closeModal(); });

function filterCat(btn, cat) {
  document.querySelectorAll('.tb-filter').forEach(b => b.classList.remove('on'));
  btn.classList.add('on');
  document.querySelectorAll('.svc-card').forEach(c => {
    c.closest('[data-cat-section]').style.display = '';
    c.style.display = (cat === 'all' || c.dataset.cat === cat) ? '' : 'none';
  });
}
</script>
@endpush
@endsection
