<?php $__env->startSection('title', 'Mon espace'); ?>

<?php $__env->startSection('content'); ?>


<div class="dash-page-header">
  <div>
    <div class="dash-greeting">Bonjour, <?php echo e($user->prenom); ?> &#128075;</div>
    <div class="dash-date"><?php echo e(now()->translatedFormat('l d F Y')); ?></div>
  </div>
  <a href="<?php echo e(route('villes.index')); ?>" class="btn-new-booking">+ Réserver un salon</a>
</div>


<div class="dash-stats-row">
  <div class="dash-stat-card green">
    <div class="dash-stat-val"><?php echo e($stats['total']); ?></div>
    <div class="dash-stat-lbl">Réservations</div>
    <div class="dash-stat-sub">Total</div>
  </div>
  <div class="dash-stat-card sage">
    <div class="dash-stat-val"><?php echo e($stats['a_venir']); ?></div>
    <div class="dash-stat-lbl">À venir</div>
    <div class="dash-stat-sub">Confirmées &amp; en attente</div>
  </div>
  <div class="dash-stat-card cream">
    <div class="dash-stat-val"><?php echo e($stats['terminee']); ?></div>
    <div class="dash-stat-lbl">Terminées</div>
    <div class="dash-stat-sub">Prestations effectuées</div>
  </div>
  <div class="dash-stat-card dark">
    <div class="dash-stat-val"><?php echo e($stats['avis']); ?></div>
    <div class="dash-stat-lbl">Avis publiés</div>
    <div class="dash-stat-sub">Contribution Salonify</div>
  </div>
</div>

<div class="dash-two-col">
  <div>

    
    <div class="dash-section-title">
      Prochain rendez-vous
      <a href="<?php echo e(route('client.reservations.index')); ?>" class="dash-section-link">Voir tout</a>
    </div>

    <?php if($prochainRdv): ?>
      <div style="border:2px solid var(--p3);background:var(--p1);padding:1.4rem 1.6rem;margin-bottom:1.8rem">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem">
          <div style="display:flex;align-items:center;gap:1.2rem">
            <div class="upcoming-date-block">
              <div class="upcoming-day"><?php echo e($prochainRdv->date_heure->format('d')); ?></div>
              <div class="upcoming-month"><?php echo e($prochainRdv->date_heure->translatedFormat('M')); ?></div>
            </div>
            <div>
              <div style="font-family:var(--fh);font-size:1.2rem;font-weight:700;color:var(--ink-h)"><?php echo e($prochainRdv->salon->nom_salon); ?></div>
              <div style="font-size:.82rem;color:var(--ink-m);margin-top:.18rem"><?php echo e($prochainRdv->service->nom_service); ?></div>
              <div style="font-size:.76rem;color:var(--ink-s);margin-top:.25rem">
                &#128337; <?php echo e($prochainRdv->service->duree_formatee); ?>

                <?php if($prochainRdv->employe): ?> · <?php echo e($prochainRdv->employe->nomComplet()); ?> <?php endif; ?>
              </div>
            </div>
          </div>
          <div style="text-align:right">
            <div style="font-family:var(--fh);font-size:1.6rem;font-weight:700;color:var(--p4)"><?php echo e($prochainRdv->date_heure->format('H:i')); ?></div>
            <?php $pillLabel = $prochainRdv->statut === 'confirmee' ? 'Confirmée' : 'En attente'; ?>
            <span class="status-pill <?php echo e($prochainRdv->statut === 'confirmee' ? 'confirmed' : 'pending'); ?>" style="margin-top:.4rem">
              <span class="status-dot"></span><?php echo e($pillLabel); ?>

            </span>
          </div>
        </div>
        <div style="display:flex;gap:.6rem;margin-top:1.2rem">
          <a href="<?php echo e(route('salons.show', [$prochainRdv->salon->ville->nom_ville, $prochainRdv->salon->slug])); ?>"
             class="btn-apt-primary" style="flex:1;text-decoration:none">Voir le salon</a>
          <form method="POST" action="<?php echo e(route('reservations.annuler', $prochainRdv->id)); ?>"
                onsubmit="return confirm('Annuler ce rendez-vous ?')">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn-apt-ghost">Annuler</button>
          </form>
        </div>
      </div>
    <?php else: ?>
      <div style="padding:2rem;text-align:center;border:2px dashed var(--border2);margin-bottom:1.8rem">
        <div style="font-size:2rem;margin-bottom:.6rem">&#128197;</div>
        <p style="color:var(--ink-m);font-size:.88rem;margin-bottom:1rem">Aucun rendez-vous à venir.</p>
        <a href="<?php echo e(route('villes.index')); ?>" class="btn-ol" style="font-size:.76rem">Trouver un salon</a>
      </div>
    <?php endif; ?>

    
    <?php if($rdvAVenir->count()): ?>
      <div class="dash-section-title" style="margin-bottom:.8rem">Prochains rendez-vous</div>
      <?php $__currentLoopData = $rdvAVenir; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rdv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="upcoming-card">
          <div class="upcoming-date-block">
            <div class="upcoming-day"><?php echo e($rdv->date_heure->format('d')); ?></div>
            <div class="upcoming-month"><?php echo e($rdv->date_heure->translatedFormat('M')); ?></div>
          </div>
          <div>
            <div class="upcoming-salon-name"><?php echo e($rdv->salon->nom_salon); ?></div>
            <div class="upcoming-service"><?php echo e($rdv->service->nom_service); ?></div>
          </div>
          <div class="upcoming-time"><?php echo e($rdv->date_heure->format('H:i')); ?></div>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>

    
    <?php if($rdvSansAvis->count()): ?>
      <div class="dash-section-title" style="margin-top:2rem;margin-bottom:.8rem">
        Laissez un avis &#9733;
      </div>
      <?php $__currentLoopData = $rdvSansAvis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rdv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="review-prompt">
          <div class="recap-salon-photo" style="width:44px;height:44px;flex-shrink:0;overflow:hidden;border:1.5px solid var(--border2)">
            <img src="<?php echo e($rdv->salon->photo_url); ?>" alt="" style="width:100%;height:100%;object-fit:cover">
          </div>
          <div>
            <div class="review-prompt-salon"><?php echo e($rdv->salon->nom_salon); ?></div>
            <div class="review-prompt-service"><?php echo e($rdv->service->nom_service); ?> · <?php echo e($rdv->date_heure->translatedFormat('d M Y')); ?></div>
          </div>
          <a href="<?php echo e(route('avis.create', $rdv->id)); ?>" style="margin-left:auto;font-size:.72rem;font-weight:700;color:var(--p4d);text-decoration:underline;white-space:nowrap">
            Évaluer &#8594;
          </a>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>

  </div>

  
  <div>

    
    <?php if($notifications->count()): ?>
      <div class="sidebar-widget">
        <div class="widget-title">Notifications récentes</div>
        <div class="widget-body" style="padding:0">
          <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notif): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div style="padding:.8rem 1.2rem;border-bottom:1px solid var(--border2);font-size:.8rem;color:var(--ink-s)">
              <div style="font-weight:700;color:var(--ink-h);margin-bottom:.2rem"><?php echo e($notif->donnees['salon'] ?? 'Salonify'); ?></div>
              <div><?php echo e($notif->donnees['service'] ?? ''); ?></div>
              <div style="font-size:.7rem;color:var(--ink-m);margin-top:.2rem"><?php echo e($notif->cree_le->diffForHumans()); ?></div>
            </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          <div style="padding:.7rem 1.2rem">
            <a href="<?php echo e(route('client.notifications.index')); ?>" style="font-size:.74rem;font-weight:700;color:var(--p4d);text-decoration:underline">Toutes les notifications</a>
          </div>
        </div>
      </div>
    <?php endif; ?>

    
    <?php if($derniersRdv->count()): ?>
      <div class="sidebar-widget">
        <div class="widget-title">Dernières visites</div>
        <div class="widget-body" style="padding:0">
          <?php $__currentLoopData = $derniersRdv; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rdv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="history-row">
              <div class="history-salon-photo">
                <img src="<?php echo e($rdv->salon->photo_url); ?>" alt="">
              </div>
              <div>
                <div class="history-salon-name"><?php echo e($rdv->salon->nom_salon); ?></div>
                <div class="history-service"><?php echo e($rdv->service->nom_service); ?></div>
              </div>
              <div class="history-date"><?php echo e($rdv->date_heure->format('d/m/Y')); ?></div>
            </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>
    <?php endif; ?>

  </div>
</div>


<?php if($salonsProches->count()): ?>
  <div style="margin-top:2.5rem;margin-bottom:3rem">
    <div class="dash-section-title" style="margin-bottom:1.2rem">
      &#128205; Salons près de vous
      <?php if($user->quartier): ?>
        <span style="font-size:.72rem;font-weight:500;color:var(--ink-m);margin-left:.5rem">
          — <?php echo e($user->quartier); ?><?php echo e($user->ville ? ', '.$user->ville->nom_ville : ''); ?>

        </span>
      <?php elseif($user->ville): ?>
        <span style="font-size:.72rem;font-weight:500;color:var(--ink-m);margin-left:.5rem">
          — <?php echo e($user->ville->nom_ville); ?>

        </span>
      <?php endif; ?>
      <a href="<?php echo e(route('client.profil.edit')); ?>" class="dash-section-link" style="font-size:.7rem">
        Modifier mon quartier
      </a>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:1rem">
      <?php $__currentLoopData = $salonsProches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('salons.show', [$s->ville->nom_ville, $s->slug])); ?>"
           style="display:block;text-decoration:none;border:1.5px solid var(--border2);background:#fff;overflow:hidden;transition:box-shadow .2s"
           onmouseover="this.style.boxShadow='0 4px 16px rgba(0,0,0,.1)'"
           onmouseout="this.style.boxShadow='none'">
          <div style="height:120px;overflow:hidden;background:var(--p1)">
            <img src="<?php echo e($s->photo_url); ?>" alt="<?php echo e($s->nom_salon); ?>"
                 style="width:100%;height:100%;object-fit:cover">
          </div>
          <div style="padding:.9rem 1rem">
            <div style="font-family:var(--fh);font-size:.95rem;font-weight:700;color:var(--ink-h);margin-bottom:.2rem">
              <?php echo e($s->nom_salon); ?>

            </div>
            <div style="font-size:.72rem;color:var(--ink-m);margin-bottom:.5rem">
              &#128205; <?php echo e($s->quartier ?: $s->adresse); ?>

            </div>
            <div style="display:flex;align-items:center;justify-content:space-between">
              <?php if($s->nb_avis > 0): ?>
                <span style="font-size:.78rem;font-weight:700;color:#D4A844">
                  &#9733; <?php echo e(number_format($s->note_moy, 1)); ?>

                  <span style="font-weight:400;color:var(--ink-m)">(<?php echo e($s->nb_avis); ?>)</span>
                </span>
              <?php else: ?>
                <span style="font-size:.72rem;color:var(--ink-d)">Nouveau</span>
              <?php endif; ?>
              <?php if($s->estOuvertMaintenant()): ?>
                <span style="font-size:.65rem;font-weight:700;background:var(--p2);color:var(--p4dd);padding:.1rem .5rem">Ouvert</span>
              <?php endif; ?>
            </div>
          </div>
        </a>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  </div>
<?php else: ?>
  
  <?php if(!$user->ville_id): ?>
    <div style="margin-top:2rem;padding:1.5rem 2rem;border:2px dashed var(--border2);text-align:center;background:var(--p1)">
      <div style="font-size:1.6rem;margin-bottom:.5rem">&#128205;</div>
      <p style="color:var(--ink-m);font-size:.88rem;margin-bottom:1rem">
        Définissez votre ville et quartier pour découvrir les salons près de chez vous.
      </p>
      <a href="<?php echo e(route('client.profil.edit')); ?>" class="btn-ol" style="font-size:.76rem">
        Compléter mon profil
      </a>
    </div>
  <?php endif; ?>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\ISGA\Projet tuteure\Projet Gestion Salon (PHP Laravel)\salonify\resources\views/client/dashboard.blade.php ENDPATH**/ ?>