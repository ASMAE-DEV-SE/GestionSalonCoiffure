<?php $__env->startSection('title', 'Tableau de bord salon'); ?>

<?php $__env->startSection('content'); ?>

<div class="dash-page-header">
  <div>
    <div class="dash-greeting"><?php echo e($salon->nom_salon); ?></div>
    <div class="dash-date"><?php echo e(now()->translatedFormat('l d F Y')); ?></div>
  </div>
  <div style="display:flex;gap:.65rem">
    <a href="<?php echo e(route('salon.reservations.index')); ?>" class="btn-new-booking">Toutes les réservations</a>
  </div>
</div>


<div class="kpi-grid" style="margin-bottom:2rem">
  <div class="kpi-card green">
    <div class="kpi-label">RDV aujourd'hui</div>
    <div class="kpi-value"><?php echo e($rdvAujourdhui->count()); ?></div>
    <div class="kpi-delta"><?php echo e(now()->translatedFormat('l')); ?></div>
    <div class="kpi-icon">&#128197;</div>
  </div>
  <div class="kpi-card yellow">
    <div class="kpi-label">En attente</div>
    <div class="kpi-value"><?php echo e($enAttente->count()); ?></div>
    <div class="kpi-delta <?php echo e($enAttente->count() > 0 ? '' : ''); ?>">À confirmer</div>
    <div class="kpi-icon">&#9203;</div>
  </div>
  <div class="kpi-card blue">
    <div class="kpi-label">RDV cette semaine</div>
    <div class="kpi-value"><?php echo e($rdvSemaine); ?></div>
    <div class="kpi-delta"><?php echo e(now()->translatedFormat('d M')); ?> – <?php echo e(now()->endOfWeek()->translatedFormat('d M')); ?></div>
    <div class="kpi-icon">&#9728;</div>
  </div>
  <div class="kpi-card sage">
    <div class="kpi-label">CA semaine (est.)</div>
    <div class="kpi-value" style="font-size:1.6rem"><?php echo e(number_format($caSemaine, 0, ',', ' ')); ?></div>
    <div class="kpi-delta">MAD · prestations terminées</div>
    <div class="kpi-icon">&#9733;</div>
  </div>
</div>

<div class="dashboard-layout" style="max-width:100%;margin:0 0 4rem">
  <div>

    
    <?php if($enAttente->count()): ?>
      <div class="section-header" style="margin-bottom:1rem">
        <h2 style="font-family:var(--fh);font-size:1.4rem;color:var(--ink-h)">
          &#9888; En attente de confirmation
          <span style="font-size:.8rem;font-weight:600;background:#FCECC0;color:#6A3800;padding:.15rem .6rem;margin-left:.5rem"><?php echo e($enAttente->count()); ?></span>
        </h2>
      </div>
      <div class="bookings-table" style="margin-bottom:2rem">
        <table>
          <thead><tr>
            <th>Client</th><th>Service</th><th>Date &amp; Heure</th><th>Durée</th><th>Actions</th>
          </tr></thead>
          <tbody>
            <?php $__currentLoopData = $enAttente; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <tr>
                <td><strong><?php echo e($r->client->nomComplet()); ?></strong><div class="booking-phone"><?php echo e($r->client->telephone); ?></div></td>
                <td><?php echo e($r->service->nom_service); ?></td>
                <td><span class="booking-time"><?php echo e($r->date_heure->format('H:i')); ?></span><div class="booking-duration"><?php echo e($r->date_heure->translatedFormat('D d M')); ?></div></td>
                <td><?php echo e($r->service->duree_formatee); ?></td>
                <td>
                  <div style="display:flex;gap:.4rem">
                    <form method="POST" action="<?php echo e(route('salon.reservations.confirmer', $r->id)); ?>">
                      <?php echo csrf_field(); ?>
                      <button class="action-btn" title="Confirmer" style="background:var(--p2);border-color:var(--p4);color:var(--p4dd)">&#10003;</button>
                    </form>
                    <a href="<?php echo e(route('salon.reservations.show', $r->id)); ?>" class="action-btn" title="Détail">&#8594;</a>
                  </div>
                </td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>

    
    <div class="section-header">
      <h2 style="font-family:var(--fh);font-size:1.4rem;color:var(--ink-h)">Programme du jour</h2>
      <a href="<?php echo e(route('salon.disponibilites.index')); ?>" class="section-link">Calendrier complet</a>
    </div>

    <div class="bookings-table" style="margin-bottom:2rem">
      <table>
        <thead><tr>
          <th>Heure</th><th>Client</th><th>Service</th><th>Styliste</th><th>Statut</th><th></th>
        </tr></thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $rdvAujourdhui; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><span class="booking-time"><?php echo e($r->date_heure->format('H:i')); ?></span></td>
              <td><strong><?php echo e($r->client->nomComplet()); ?></strong><div class="booking-phone"><?php echo e($r->client->telephone); ?></div></td>
              <td><?php echo e($r->service->nom_service); ?><div class="booking-duration"><?php echo e($r->service->duree_formatee); ?></div></td>
              <td><?php echo e($r->employe?->nomComplet() ?? '—'); ?></td>
              <td>
                <?php $sc = $r->statut === 'confirmee' ? 'confirmed' : 'pending'; ?>
                <span class="status-badge <?php echo e($sc); ?>">
                  <span class="status-dot"></span>
                  <?php echo e($r->statut === 'confirmee' ? 'Confirmé' : 'En attente'); ?>

                </span>
              </td>
              <td><a href="<?php echo e(route('salon.reservations.show', $r->id)); ?>" class="action-btn">&#8594;</a></td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="6" style="text-align:center;color:var(--ink-m);padding:2rem">Aucun rendez-vous aujourd'hui.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    
    <div class="chart-card">
      <div class="chart-title">Réservations — 7 derniers jours</div>
      <?php $max = collect($chartData)->max('value') ?: 1; ?>
      <div class="bar-chart">
        <?php $__currentLoopData = $chartData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <div class="bar-column">
            <div class="bar-fill <?php echo e($d['today'] ? 'highlighted' : ''); ?>"
                 style="height:<?php echo e(round($d['value'] / $max * 100)); ?>%"
                 data-value="<?php echo e($d['value']); ?> RDV"></div>
            <div class="bar-day-label"><?php echo e($d['label']); ?></div>
          </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    </div>

  </div>

  
  <div>

    
    <div class="team-card" style="margin-bottom:1.5rem">
      <div class="team-card-head"><div class="team-card-title">Prochains RDV</div></div>
      <?php $__empty_1 = true; $__currentLoopData = $prochains->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="team-member">
          <div class="member-avatar">
            <img src="https://ui-avatars.com/api/?name=<?php echo e(urlencode($r->client->nomComplet())); ?>&background=9CAB84&color=fff&size=40" alt="">
          </div>
          <div>
            <div class="member-name"><?php echo e($r->client->nomComplet()); ?></div>
            <div class="member-role"><?php echo e($r->service->nom_service); ?> · <?php echo e($r->date_heure->translatedFormat('D d M')); ?> <?php echo e($r->date_heure->format('H:i')); ?></div>
          </div>
          <?php if($r->employe): ?>
            <span class="member-bookings"><?php echo e($r->employe->prenom); ?></span>
          <?php endif; ?>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div style="padding:1rem 1.4rem;font-size:.82rem;color:var(--ink-m)">Aucun RDV à venir.</div>
      <?php endif; ?>
    </div>

    
    <div class="services-list">
      <div class="services-list-head">Top services</div>
      <?php $__empty_1 = true; $__currentLoopData = $topServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="service-item">
          <div>
            <div class="service-name"><?php echo e($t->service->nom_service); ?></div>
            <div class="service-meta"><?php echo e($t->service->duree_formatee); ?> · <?php echo e($t->service->prix_format); ?></div>
          </div>
          <div>
            <div class="service-count"><?php echo e($t->total); ?></div>
            <div class="service-percent">
              <?php echo e($totalTerminees > 0 ? round($t->total / $totalTerminees * 100) : 0); ?>%
            </div>
            <div class="service-bar-track">
              <div class="service-bar-fill" style="width:<?php echo e($totalTerminees > 0 ? round($t->total / $totalTerminees * 100) : 0); ?>%"></div>
            </div>
          </div>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div style="padding:1rem 1.4rem;font-size:.82rem;color:var(--ink-m)">Aucune donnée.</div>
      <?php endif; ?>
    </div>

    
    <?php if($derniersAvis->count()): ?>
      <div class="sidebar-alert" style="margin-top:1.5rem">
        <div class="alert-icon">&#9733;</div>
        <div>
          <div class="alert-title"><?php echo e($derniersAvis->count()); ?> avis récents</div>
          <?php $__currentLoopData = $derniersAvis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="oa">
              <div class="oa-top">
                <span class="oa-name"><?php echo e($a->reservation->client?->prenom ?? 'Client'); ?></span>
                <span class="oa-stars"><?php echo e(str_repeat('★',$a->note)); ?></span>
              </div>
              <?php if($a->commentaire): ?>
                <div class="oa-txt">"<?php echo e(Str::limit($a->commentaire, 60)); ?>"</div>
              <?php endif; ?>
            </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          <a href="<?php echo e(route('salon.avis.index')); ?>" class="alert-link">Voir tous les avis</a>
        </div>
      </div>
    <?php endif; ?>

  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\ISGA\Projet tuteure\Projet Gestion Salon (PHP Laravel)\salonify\resources\views/salon/dashboard.blade.php ENDPATH**/ ?>