<?php $__env->startSection('title', 'Mes réservations'); ?>

<?php $__env->startSection('content'); ?>


<div class="client-header">
  <div class="wrap client-header-inner">
    <div class="client-avatar">
      <img src="https://ui-avatars.com/api/?name=<?php echo e(urlencode(auth()->user()->nomComplet())); ?>&background=9CAB84&color=fff&size=96"
           alt="<?php echo e(auth()->user()->nomComplet()); ?>">
    </div>
    <div>
      <div class="client-name"><?php echo e(auth()->user()->nomComplet()); ?></div>
      <div class="client-subtitle">Espace client · Mes réservations</div>
    </div>
    <div class="client-header-actions">
      <a href="<?php echo e(route('villes.index')); ?>" class="btn-header-green">+ Nouvelle réservation</a>
    </div>
  </div>
</div>


<div class="client-subnav">
  <div class="client-subnav-inner wrap">
    <a href="<?php echo e(route('client.dashboard')); ?>" class="subnav-tab">Tableau de bord</a>
    <a href="<?php echo e(route('client.reservations.index')); ?>" class="subnav-tab active">Réservations</a>
    <a href="<?php echo e(route('client.profil.edit')); ?>" class="subnav-tab">Mon profil</a>
    <a href="<?php echo e(route('client.notifications.index')); ?>" class="subnav-tab">Notifications</a>
  </div>
</div>

<div class="reservations-layout">
  <div>

    
    <div class="filter-tabs">
      <?php $statuts = [''=>'Toutes', 'en_attente'=>'En attente', 'confirmee'=>'Confirmées', 'terminee'=>'Terminées', 'annulee'=>'Annulées']; ?>
      <?php $__currentLoopData = $statuts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(request()->fullUrlWithQuery(['statut' => $val])); ?>"
           class="filter-tab <?php echo e(request('statut', '') === $val ? 'active' : ''); ?>">
          <?php echo e($lbl); ?>

        </a>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <?php $__empty_1 = true; $__currentLoopData = $reservations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $resa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <div class="reservation-card <?php echo e(in_array($resa->statut, ['terminee','annulee']) ? $resa->statut : ''); ?>">

        <div class="reservation-card-top">
          <div class="reservation-salon-photo">
            <img src="<?php echo e($resa->salon->photo_url); ?>" alt="<?php echo e($resa->salon->nom_salon); ?>">
          </div>
          <div>
            <div class="reservation-salon-name"><?php echo e($resa->salon->nom_salon); ?></div>
            <div class="reservation-salon-location">&#128205; <?php echo e($resa->salon->quartier); ?>, <?php echo e($resa->salon->ville?->nom_ville); ?></div>
          </div>
          <div>
            <?php
              $pillClass = match($resa->statut) {
                'confirmee'  => 'confirmed',
                'en_attente' => 'pending',
                'terminee'   => 'done',
                'annulee'    => 'cancelled',
                default      => 'pending',
              };
              $pillLabel = match($resa->statut) {
                'confirmee'  => 'Confirmée',
                'en_attente' => 'En attente',
                'terminee'   => 'Terminée',
                'annulee'    => 'Annulée',
                default      => $resa->statut,
              };
            ?>
            <span class="status-pill <?php echo e($pillClass); ?>">
              <span class="status-dot"></span><?php echo e($pillLabel); ?>

            </span>
          </div>
        </div>

        <div class="reservation-details">
          <div>
            <div class="detail-cell-label">Service</div>
            <div class="detail-cell-value"><?php echo e($resa->service->nom_service); ?></div>
          </div>
          <div>
            <div class="detail-cell-label">Date</div>
            <div class="detail-cell-value"><?php echo e($resa->date_heure->translatedFormat('D d M Y')); ?></div>
          </div>
          <div>
            <div class="detail-cell-label">Heure</div>
            <div class="detail-cell-value"><?php echo e($resa->date_heure->format('H:i')); ?></div>
          </div>
          <div>
            <div class="detail-cell-label">Styliste</div>
            <div class="detail-cell-value"><?php echo e($resa->employe?->nomComplet() ?? 'Au choix'); ?></div>
          </div>
        </div>

        <div class="reservation-card-footer">
          <div class="reservation-price">
            <?php echo e($resa->service->prix_format); ?>

            <span>· <?php echo e($resa->service->duree_formatee); ?></span>
          </div>
          <div class="card-actions">
            
            <?php if($resa->statut === 'terminee' && ! $resa->avis): ?>
              <a href="<?php echo e(route('avis.create', $resa->id)); ?>" class="btn-card btn-card-green">&#9733; Laisser un avis</a>
            <?php endif; ?>
            <?php if($resa->statut === 'terminee' && $resa->avis): ?>
              <span class="btn-card" style="cursor:default;opacity:.6;border-color:var(--border)">Avis publié &#10003;</span>
            <?php endif; ?>
            
            <?php if(in_array($resa->statut, ['en_attente','confirmee'])): ?>
              <form method="POST" action="<?php echo e(route('reservations.annuler', $resa->id)); ?>"
                    onsubmit="return confirm('Annuler cette réservation ?')">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn-card btn-card-danger">Annuler</button>
              </form>
            <?php endif; ?>
          </div>
        </div>

      </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <div style="padding:4rem 0;text-align:center">
        <div style="font-size:3rem;margin-bottom:1rem">&#128197;</div>
        <p style="color:var(--ink-m);font-size:.9rem;margin-bottom:1.5rem">Aucune réservation pour l'instant.</p>
        <a href="<?php echo e(route('villes.index')); ?>" class="btn-ol" style="font-size:.78rem">Trouver un salon</a>
      </div>
    <?php endif; ?>

    <?php echo e($reservations->links()); ?>


  </div>

  
  <div>
    <div class="profile-sidebar-card">
      <div class="sidebar-avatar">
        <img src="https://ui-avatars.com/api/?name=<?php echo e(urlencode(auth()->user()->nomComplet())); ?>&background=9CAB84&color=fff&size=96" alt="">
      </div>
      <div class="sidebar-name"><?php echo e(auth()->user()->nomComplet()); ?></div>
      <div class="sidebar-email"><?php echo e(auth()->user()->email); ?></div>
      <?php
        $total    = auth()->user()->reservations()->count();
        $aVenir   = auth()->user()->reservations()->whereIn('statut',['en_attente','confirmee'])->where('date_heure','>=',now())->count();
        $terminees = auth()->user()->reservations()->where('statut','terminee')->count();
      ?>
      <div class="sidebar-stat"><span class="sidebar-stat-key">Total réservations</span><span class="sidebar-stat-value"><?php echo e($total); ?></span></div>
      <div class="sidebar-stat"><span class="sidebar-stat-key">À venir</span><span class="sidebar-stat-value"><?php echo e($aVenir); ?></span></div>
      <div class="sidebar-stat"><span class="sidebar-stat-key">Terminées</span><span class="sidebar-stat-value"><?php echo e($terminees); ?></span></div>
      <div class="sidebar-cta"><a href="<?php echo e(route('villes.index')); ?>">+ Nouvelle réservation</a></div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\ISGA\Projet tuteure\Projet Gestion Salon (PHP Laravel)\salonify\resources\views/reservations/index.blade.php ENDPATH**/ ?>