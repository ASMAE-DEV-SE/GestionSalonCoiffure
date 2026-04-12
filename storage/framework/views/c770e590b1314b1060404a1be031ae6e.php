<?php $__env->startSection('title', 'Salons à ' . $villeModel->nom_ville); ?>
<?php $__env->startSection('meta_description', 'Réservez dans les meilleurs salons de beauté à ' . $villeModel->nom_ville . '. ' . $salons->total() . ' établissements disponibles.'); ?>

<?php $__env->startSection('content'); ?>


<?php if($autoQuartier && $quartierActif): ?>
  <div style="background:linear-gradient(90deg,var(--p1),#fff);border-bottom:2px solid var(--p3);padding:.7rem 1.4rem;display:flex;align-items:center;gap:1rem;flex-wrap:wrap">
    <span style="font-size:.85rem;color:var(--p4dd);font-weight:700">
      &#128205; Salons dans votre quartier : <strong><?php echo e($quartierActif); ?></strong>
    </span>
    <span style="font-size:.78rem;color:var(--ink-m)">
      — <?php echo e($salons->total()); ?> résultat<?php echo e($salons->total() > 1 ? 's' : ''); ?> trouvé<?php echo e($salons->total() > 1 ? 's' : ''); ?>

    </span>
    <a href="<?php echo e(route('salons.index', $villeModel->nom_ville)); ?>"
       style="margin-left:auto;font-size:.74rem;color:var(--ink-m);text-decoration:underline;white-space:nowrap">
      &#10005; Voir tous les salons de <?php echo e($villeModel->nom_ville); ?>

    </a>
  </div>
<?php endif; ?>


<div class="quartier-bar">
  <div class="quartier-bar-inner">

    
    <button id="btnGeoSalon" onclick="detecterQuartier()"
            style="display:inline-flex;align-items:center;gap:.4rem;background:var(--p2);border:1.5px solid var(--p3);color:var(--p4dd);padding:.3rem .9rem;border-radius:20px;font-size:.74rem;font-weight:600;cursor:pointer;flex-shrink:0;margin-right:.4rem"
            title="Détecter automatiquement votre quartier">
      <span id="geoSalonIcon">&#128205;</span>
      <span id="geoSalonText">Près de moi</span>
    </button>

    <a href="<?php echo e(route('salons.index', $villeModel->nom_ville)); ?>"
       class="quartier-pill <?php echo e(!$quartierActif ? 'active' : ''); ?>">
      Tous <span class="quartier-count"><?php echo e($salons->total()); ?></span>
    </a>
    <?php $__currentLoopData = $quartiers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <a href="<?php echo e(route('salons.index', $villeModel->nom_ville)); ?>?quartier=<?php echo e(urlencode($q)); ?>&tri=<?php echo e($tri); ?>"
         class="quartier-pill <?php echo e($quartierActif === $q ? 'active' : ''); ?>">
        <?php echo e($q); ?>

      </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>
</div>


<div class="search-controls-bar">
  <form class="search-controls-inner" method="GET" action="<?php echo e(route('salons.index', $villeModel->nom_ville)); ?>">
    <div class="search-field-wrap">
      <div class="search-field-icon">&#128269;</div>
      <input type="text" name="q" class="search-field-input"
             placeholder="Rechercher un salon, un service..."
             value="<?php echo e(request('q')); ?>">
    </div>

    <select name="categorie" class="field" style="max-width:200px">
      <option value="">Tous les services</option>
      <?php $__currentLoopData = ['Coiffure','Couleur','Soins','Ongles','Massage','Épilation']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($cat); ?>" <?php echo e(request('categorie') === $cat ? 'selected' : ''); ?>><?php echo e($cat); ?></option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>

    <select name="note_min" class="field" style="max-width:160px">
      <option value="">Toutes les notes</option>
      <option value="4.5" <?php echo e(request('note_min') == '4.5' ? 'selected' : ''); ?>>&#9733; 4.5+</option>
      <option value="4"   <?php echo e(request('note_min') == '4'   ? 'selected' : ''); ?>>&#9733; 4.0+</option>
      <option value="3"   <?php echo e(request('note_min') == '3'   ? 'selected' : ''); ?>>&#9733; 3.0+</option>
    </select>

    <?php if(request('quartier')): ?><input type="hidden" name="quartier" value="<?php echo e(request('quartier')); ?>"><?php endif; ?>

    <button type="submit" class="btn-search">Filtrer</button>

    <div class="sort-controls">
      <span class="sort-label">Trier :</span>
      <?php $__currentLoopData = ['note' => 'Mieux notés', 'avis' => 'Plus d\'avis', 'alpha' => 'A–Z']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(request()->fullUrlWithQuery(['tri' => $val])); ?>"
           class="sort-btn <?php echo e($tri === $val ? 'active' : ''); ?>"><?php echo e($lbl); ?></a>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  </form>
</div>


<div class="salons-content">

  <div class="results-count-bar">
    <div class="results-label">
      <strong><?php echo e($salons->total()); ?></strong> salon<?php echo e($salons->total() > 1 ? 's' : ''); ?>

      à <strong><?php echo e($villeModel->nom_ville); ?></strong>
      <?php if(request('quartier')): ?> · <?php echo e(request('quartier')); ?> <?php endif; ?>
    </div>
    <div style="font-size:.78rem;color:var(--ink-m)">
      Page <?php echo e($salons->currentPage()); ?> / <?php echo e($salons->lastPage()); ?>

    </div>
  </div>

  <?php if($salons->isEmpty()): ?>
    <div style="padding:5rem 0;text-align:center">
      <div style="font-size:2.5rem;margin-bottom:1rem">&#128269;</div>
      <p style="color:var(--ink-m);font-size:.9rem">Aucun salon ne correspond à vos critères.</p>
      <a href="<?php echo e(route('salons.index', $villeModel->nom_ville)); ?>" style="color:var(--p4d);font-weight:700;font-size:.84rem;text-decoration:underline">
        Voir tous les salons de <?php echo e($villeModel->nom_ville); ?>

      </a>
    </div>
  <?php else: ?>
    <div class="featured-grid">
      <?php $__currentLoopData = $salons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $salon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('salons.show', [$villeModel->nom_ville, $salon->slug])); ?>" class="card">
          <div class="card-image">
            <img src="<?php echo e($salon->photo_url); ?>" alt="<?php echo e($salon->nom_salon); ?>" style="height:200px">
            <?php if($salon->servicesActifs->count()): ?>
              <span class="card-price-tag">
                À partir de <?php echo e(number_format($salon->servicesActifs->min('prix'), 0, ',', ' ')); ?> MAD
              </span>
            <?php endif; ?>
            <div class="card-category-icon icon-coiffure">&#9986;</div>
          </div>
          <div class="card-body">
            <div class="card-name"><?php echo e($salon->nom_salon); ?></div>
            <div class="card-rating">
              <span class="card-stars"><?php echo e(str_repeat('★', round($salon->note_moy))); ?><?php echo e(str_repeat('☆', 5 - round($salon->note_moy))); ?></span>
              <span class="card-score"><?php echo e(number_format($salon->note_moy, 1)); ?></span>
              <span class="card-reviews">(<?php echo e($salon->nb_avis); ?> avis)</span>
            </div>
            <div class="card-location">
              <span class="verified-dot"></span><?php echo e($salon->quartier); ?>, <?php echo e($villeModel->nom_ville); ?>

            </div>
            <p class="card-desc"><?php echo e(Str::limit($salon->description, 80)); ?></p>
            <button class="btn-view">Voir &amp; Réserver</button>
          </div>
        </a>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <?php if($salons->hasPages()): ?>
      <div class="pagination">
        <?php if($salons->onFirstPage()): ?>
          <span class="page-btn" style="opacity:.4">&#8592;</span>
        <?php else: ?>
          <a href="<?php echo e($salons->previousPageUrl()); ?>" class="page-btn">&#8592;</a>
        <?php endif; ?>

        <?php $__currentLoopData = $salons->getUrlRange(1, $salons->lastPage()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <a href="<?php echo e($url); ?>" class="page-btn <?php echo e($page === $salons->currentPage() ? 'active' : ''); ?>">
            <?php echo e($page); ?>

          </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <?php if($salons->hasMorePages()): ?>
          <a href="<?php echo e($salons->nextPageUrl()); ?>" class="page-btn">&#8594;</a>
        <?php else: ?>
          <span class="page-btn" style="opacity:.4">&#8594;</span>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  <?php endif; ?>

</div>

<?php $__env->startPush('scripts'); ?>
<script>
// URL de base pour la ville courante
const baseUrl = "<?php echo e(route('salons.index', $villeModel->nom_ville)); ?>";

function detecterQuartier() {
  const btn  = document.getElementById('btnGeoSalon');
  const icon = document.getElementById('geoSalonIcon');
  const text = document.getElementById('geoSalonText');

  if (!navigator.geolocation) {
    alert('La géolocalisation n\'est pas supportée par votre navigateur.');
    return;
  }

  btn.disabled    = true;
  icon.textContent = '⏳';
  text.textContent = '…';

  navigator.geolocation.getCurrentPosition(
    async (position) => {
      const lat = position.coords.latitude;
      const lng = position.coords.longitude;

      try {
        const res  = await fetch(
          `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&accept-language=fr&zoom=16`,
          { headers: { 'Accept-Language': 'fr' } }
        );
        const data = await res.json();
        const addr = data.address || {};

        // Quartier : suburb → neighbourhood → quarter → road → city_district
        const quartier = addr.suburb || addr.neighbourhood || addr.quarter
                      || addr.city_district || addr.road || '';

        if (quartier) {
          // Rediriger avec le quartier détecté (marqué auto=1)
          window.location.href = baseUrl + '?quartier=' + encodeURIComponent(quartier) + '&auto=1&tri=<?php echo e($tri); ?>';
        } else {
          alert('Quartier introuvable pour votre position. Sélectionnez-le manuellement.');
          resetGeoBtn(btn, icon, text);
        }
      } catch (e) {
        alert('Erreur réseau. Vérifiez votre connexion internet.');
        resetGeoBtn(btn, icon, text);
      }
    },
    (err) => {
      const msgs = {
        1: 'Accès refusé. Autorisez la localisation dans votre navigateur.',
        2: 'Position introuvable.',
        3: 'Délai dépassé. Réessayez.',
      };
      alert(msgs[err.code] || 'Erreur de géolocalisation.');
      resetGeoBtn(btn, icon, text);
    },
    { timeout: 10000, maximumAge: 60000, enableHighAccuracy: true }
  );
}

function resetGeoBtn(btn, icon, text) {
  btn.disabled     = false;
  icon.textContent = '📍';
  text.textContent = 'Près de moi';
}
</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\ISGA\Projet tuteure\Projet Gestion Salon (PHP Laravel)\salonify\resources\views/salons/index.blade.php ENDPATH**/ ?>