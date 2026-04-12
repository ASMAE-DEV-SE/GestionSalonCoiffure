<?php $__env->startSection('title', $salon->nom_salon); ?>
<?php $__env->startSection('meta_description', $salon->nom_salon . ' — ' . $salon->quartier . ', ' . $salon->ville->nom_ville . '. Réservez en ligne sur Salonify.'); ?>

<?php $__env->startSection('content'); ?>


<div class="salon-hero">
  <img src="<?php echo e($salon->photo_url); ?>" alt="<?php echo e($salon->nom_salon); ?>">
  <div class="salon-hero-overlay">
    <div class="hero-content">
      <span class="hero-badge">
        <?php echo e($salon->estOuvertMaintenant() ? '● Ouvert maintenant' : '○ Fermé actuellement'); ?>

      </span>
      <h1 class="hero-name"><?php echo e($salon->nom_salon); ?></h1>
      <div class="hero-location">&#128205; <?php echo e($salon->adresse); ?>, <?php echo e($salon->quartier); ?>, <?php echo e($salon->ville->nom_ville); ?></div>
      <div class="hero-rating">
        <span class="hero-stars"><?php echo e(str_repeat('★', round($salon->note_moy))); ?><?php echo e(str_repeat('☆', 5 - round($salon->note_moy))); ?></span>
        <span class="hero-score"><?php echo e(number_format($salon->note_moy, 1)); ?> <span>(<?php echo e($salon->nb_avis); ?> avis)</span></span>
        <span class="bge-ok" style="margin-left:.5rem">Salon vérifié &#10003;</span>
      </div>
    </div>
  </div>
</div>


<div class="salon-layout">

  <div>
    
    <div class="salon-tabs">
      <button class="salon-tab active" onclick="showTab('services',this)">Services</button>
      <button class="salon-tab" onclick="showTab('equipe',this)">Équipe</button>
      <button class="salon-tab" onclick="showTab('avis',this)">Avis (<?php echo e($salon->nb_avis); ?>)</button>
      <button class="salon-tab" onclick="showTab('infos',this)">Informations</button>
    </div>

    
    <div id="tab-services">
      <?php $__currentLoopData = $servicesByCategorie; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categorie => $services): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <h2 class="section-title"><?php echo e($categorie); ?></h2>
        <div class="services-grid" style="margin-bottom:2rem">
          <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $svc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="service-row" onclick="selectService(<?php echo e($svc->id); ?>, '<?php echo e($svc->nom_service); ?>', <?php echo e($svc->duree_minu); ?>, <?php echo e($svc->prix); ?>)">
              <div>
                <div class="service-row-name"><?php echo e($svc->nom_service); ?></div>
                <div class="service-row-duration">&#128337; <?php echo e($svc->duree_formatee); ?></div>
                <?php if($svc->description): ?>
                  <div style="font-size:.74rem;color:var(--ink-m);margin-top:.2rem"><?php echo e(Str::limit($svc->description, 60)); ?></div>
                <?php endif; ?>
              </div>
              <div>
                <div class="service-row-price"><?php echo e($svc->prix_format); ?></div>
                <span class="service-row-cta">Réserver &#8594;</span>
              </div>
            </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <div id="tab-equipe" style="display:none">
      <h2 class="section-title">Notre équipe</h2>
      <?php if($salon->employesActifs->isEmpty()): ?>
        <p style="color:var(--ink-m);font-size:.9rem;padding:2rem 0">Informations sur l'équipe à venir.</p>
      <?php else: ?>
        <div class="team-grid">
          <?php $__currentLoopData = $salon->employesActifs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="team-member">
              <div class="team-member-photo">
                <img src="<?php echo e($emp->photo_url); ?>" alt="<?php echo e($emp->nomComplet()); ?>">
              </div>
              <div class="team-member-name"><?php echo e($emp->nomComplet()); ?></div>
              <div class="team-member-role">
                <?php $specs = $emp->specialites; if (is_string($specs)) $specs = json_decode($specs, true) ?? []; ?>
                <?php echo e(is_array($specs) ? implode(', ', $specs) : $specs); ?>

              </div>
            </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      <?php endif; ?>
    </div>

    
    <div id="tab-avis" style="display:none">
      <h2 class="section-title">Avis clients</h2>

      
      <?php if($salon->nb_avis > 0): ?>
        <div class="reviews-summary">
          <div>
            <div class="reviews-big-score"><?php echo e(number_format($salon->note_moy, 1)); ?></div>
            <div class="reviews-stars-large"><?php echo e(str_repeat('★', round($salon->note_moy))); ?><?php echo e(str_repeat('☆', 5 - round($salon->note_moy))); ?></div>
            <div class="reviews-total"><?php echo e($salon->nb_avis); ?> avis vérifiés</div>
          </div>
          <div style="flex:1">
            <?php for($i = 5; $i >= 1; $i--): ?>
              <div class="rating-bar-row">
                <span class="rating-bar-label"><?php echo e($i); ?>&#9733;</span>
                <div class="rating-bar-track">
                  <div class="rating-bar-fill <?php echo e($i <= 2 ? 'low' : ''); ?>"
                       style="width:<?php echo e($distribution[$i]['pct']); ?>%"></div>
                </div>
                <span class="rating-bar-pct"><?php echo e($distribution[$i]['pct']); ?>%</span>
              </div>
            <?php endfor; ?>
          </div>
        </div>
      <?php endif; ?>

      
      <div class="reviews-list">
        <?php $__empty_1 = true; $__currentLoopData = $avis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <div class="review-card">
            <div class="review-top">
              <div class="review-avatar">
                <img src="https://ui-avatars.com/api/?name=<?php echo e(urlencode($a->reservation->client?->nomComplet() ?? 'Client')); ?>&background=9CAB84&color=fff&size=42"
                     alt="Client">
              </div>
              <div>
                <div class="review-author-name"><?php echo e($a->reservation->client?->prenom ?? 'Client'); ?> <?php echo e(substr($a->reservation->client?->nom ?? '', 0, 1)); ?>.</div>
                <div class="review-date"><?php echo e($a->created_at->translatedFormat('d F Y')); ?></div>
              </div>
              <div class="review-stars"><?php echo e(str_repeat('★', $a->note)); ?><?php echo e(str_repeat('☆', 5 - $a->note)); ?></div>
            </div>
            <?php if($a->commentaire): ?>
              <p class="review-text">"<?php echo e($a->commentaire); ?>"</p>
            <?php endif; ?>
            <?php if($a->reponse_salon): ?>
              <div style="background:var(--p1);border-left:3px solid var(--p4);padding:.8rem 1rem;margin-top:.8rem;font-size:.82rem;color:var(--ink-s)">
                <strong style="font-size:.7rem;text-transform:uppercase;letter-spacing:.8px;color:var(--p4d)">Réponse du salon ·</strong>
                <?php echo e($a->reponse_salon); ?>

              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <p style="color:var(--ink-m);font-size:.9rem;padding:2rem 0">Aucun avis pour ce salon. Soyez le premier !</p>
        <?php endif; ?>
      </div>
    </div>

    
    <div id="tab-infos" style="display:none">
      <h2 class="section-title">Informations pratiques</h2>
      <div class="practical-info">
        <div class="practical-info-title">Coordonnées &amp; Horaires</div>
        <div class="info-row">
          <div class="info-icon">&#128205;</div>
          <div><div class="info-value"><?php echo e($salon->adresse); ?></div><div class="info-label"><?php echo e($salon->quartier); ?>, <?php echo e($salon->ville->nom_ville); ?></div></div>
        </div>
        <?php if($salon->telephone): ?>
          <div class="info-row">
            <div class="info-icon">&#128222;</div>
            <div><div class="info-value"><?php echo e($salon->telephone); ?></div><div class="info-label">Téléphone</div></div>
          </div>
        <?php endif; ?>
        <?php if($salon->email): ?>
          <div class="info-row">
            <div class="info-icon">&#9993;</div>
            <div><div class="info-value"><?php echo e($salon->email); ?></div><div class="info-label">Email</div></div>
          </div>
        <?php endif; ?>

        <?php if($salon->horaires): ?>
          <div style="margin-top:1.4rem">
            <div class="practical-info-title">Horaires d'ouverture</div>
            <?php $__currentLoopData = ['lundi','mardi','mercredi','jeudi','vendredi','samedi','dimanche']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <?php if(isset($salon->horaires[$j])): ?>
                <?php $h = $salon->horaires[$j]; $today = strtolower(now()->translatedFormat('l')); ?>
                <div class="info-row" style="border-bottom:1px solid var(--border2);padding:.4rem 0;<?php echo e($j === $today ? 'background:rgba(197,216,157,.12);' : ''); ?>">
                  <span style="font-size:.82rem;font-weight:<?php echo e($j === $today ? '700' : '500'); ?>;color:<?php echo e($j === $today ? 'var(--p4d)' : 'var(--ink-b)'); ?>;width:90px;display:inline-block;text-transform:capitalize"><?php echo e($j); ?></span>
                  <span style="font-size:.8rem;font-weight:700;color:<?php echo e(($h['ferme'] ?? true) ? '#C04A3D' : 'var(--ink-h)'); ?>">
                    <?php echo e(($h['ferme'] ?? true) ? 'Fermé' : ($h['debut'] . ' – ' . $h['fin'])); ?>

                  </span>
                </div>
              <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

  </div>

  
  <aside>
    <div class="booking-card">
      <div class="booking-card-title">Réserver</div>
      <div class="booking-card-subtitle" id="selectedServiceName">Choisissez un service ci-dessous</div>

      <div id="bookingServiceInfo" style="display:none;background:var(--p1);border:1.5px solid var(--p2);padding:.9rem 1rem;margin-bottom:1rem">
        <div style="font-size:.84rem;font-weight:700;color:var(--ink-h)" id="bkSvcName"></div>
        <div style="display:flex;justify-content:space-between;margin-top:.35rem">
          <span style="font-size:.76rem;color:var(--ink-m)" id="bkSvcDuration"></span>
          <span style="font-family:var(--fh);font-size:1.2rem;font-weight:700;color:var(--ink-h)" id="bkSvcPrice"></span>
        </div>
      </div>

      <?php if(auth()->guard()->check()): ?>
        <?php if(auth()->user()->isClient()): ?>
          <a id="btnReserver" href="<?php echo e(route('reservations.step1', $salon->slug)); ?>"
             class="btn-book-full" style="display:block;text-align:center;text-decoration:none">
            Choisir un créneau
          </a>
        <?php else: ?>
          <p style="font-size:.8rem;color:var(--ink-m);text-align:center">Connectez-vous en tant que client pour réserver.</p>
        <?php endif; ?>
      <?php else: ?>
        <a href="<?php echo e(route('login')); ?>?redirect=<?php echo e(url()->current()); ?>" class="btn-book-full" style="display:block;text-align:center;text-decoration:none">
          Se connecter pour réserver
        </a>
        <div style="text-align:center;margin-top:.7rem;font-size:.76rem;color:var(--ink-m)">
          Pas de compte ? <a href="<?php echo e(route('register')); ?>" style="color:var(--p4d);font-weight:700">Inscription gratuite</a>
        </div>
      <?php endif; ?>

      <div class="booking-total" style="margin-top:1.2rem">
        <span class="booking-total-label">Paiement</span>
        <span style="font-size:.82rem;color:var(--ink-s);font-weight:600">Au salon &#10003;</span>
      </div>
      <div style="font-size:.74rem;color:var(--ink-m);text-align:center">
        Réservation gratuite — Annulation libre 24h avant
      </div>
    </div>

    
    <div class="practical-info" style="margin-top:1.2rem">
      <div class="practical-info-title">Informations</div>
      <div class="info-row">
        <div class="info-icon">&#128197;</div>
        <div><div class="info-value"><?php echo e($salon->nb_employes); ?> coiffeur<?php echo e($salon->nb_employes > 1 ? 's' : ''); ?></div></div>
      </div>
      <div class="info-row">
        <div class="info-icon">&#9733;</div>
        <div><div class="info-value"><?php echo e(number_format($salon->note_moy, 1)); ?> / 5</div><div class="info-label"><?php echo e($salon->nb_avis); ?> avis vérifiés</div></div>
      </div>
      <div class="info-row">
        <div class="info-icon">&#128222;</div>
        <div><div class="info-value"><?php echo e($salon->telephone ?? 'N/A'); ?></div></div>
      </div>
    </div>
  </aside>

</div>

<?php $__env->startPush('scripts'); ?>
<script>
function showTab(id, btn) {
  ['services','equipe','avis','infos'].forEach(t => {
    document.getElementById('tab-' + t).style.display = 'none';
  });
  document.querySelectorAll('.salon-tab').forEach(b => b.classList.remove('active'));
  document.getElementById('tab-' + id).style.display = 'block';
  btn.classList.add('active');
}

function selectService(id, nom, duree, prix) {
  document.getElementById('bkSvcName').textContent     = nom;
  document.getElementById('bkSvcDuration').textContent = Math.floor(duree / 60) > 0
    ? Math.floor(duree/60) + 'h' + (duree%60 > 0 ? (duree%60) : '') : duree + ' min';
  document.getElementById('bkSvcPrice').textContent    = prix.toLocaleString('fr-FR') + ' MAD';
  document.getElementById('bookingServiceInfo').style.display = 'block';
  document.getElementById('selectedServiceName').textContent  = 'Service sélectionné';

  const btn = document.getElementById('btnReserver');
  if (btn) btn.href = btn.href.split('?')[0] + '?service_id=' + id;

  window.scrollTo({ top: document.querySelector('.booking-card').getBoundingClientRect().top + window.scrollY - 120, behavior: 'smooth' });
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\ISGA\Projet tuteure\Projet Gestion Salon (PHP Laravel)\salonify\resources\views/salons/show.blade.php ENDPATH**/ ?>