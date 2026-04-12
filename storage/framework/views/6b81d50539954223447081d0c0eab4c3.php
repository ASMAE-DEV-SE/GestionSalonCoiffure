<?php $__env->startSection('content'); ?>
<div class="badge">Nouvelle réservation</div>
<div class="greeting">Nouvelle demande de RDV &#128197;</div>

<p class="text">
  Bonjour,<br>
  <strong><?php echo e($nomClient); ?></strong> vient de faire une demande de réservation pour votre salon
  <strong>« <?php echo e($nomSalon); ?> »</strong>.
</p>

<div class="info-card">
  <div class="info-row">
    <span class="info-label">Client</span>
    <span class="info-value"><?php echo e($nomClient); ?></span>
  </div>
  <div class="info-row">
    <span class="info-label">Téléphone</span>
    <span class="info-value"><?php echo e($telephoneClient ?: '—'); ?></span>
  </div>
  <div class="info-row">
    <span class="info-label">Service demandé</span>
    <span class="info-value"><?php echo e($nomService); ?></span>
  </div>
  <div class="info-row">
    <span class="info-label">Date souhaitée</span>
    <span class="info-value"><?php echo e($date); ?></span>
  </div>
  <div class="info-row">
    <span class="info-label">Heure</span>
    <span class="info-value"><?php echo e($heure); ?></span>
  </div>
  <div class="info-row">
    <span class="info-label">Durée</span>
    <span class="info-value"><?php echo e($duree); ?></span>
  </div>
  <?php if($notesClient): ?>
  <div class="info-row">
    <span class="info-label">Notes client</span>
    <span class="info-value"><?php echo e($notesClient); ?></span>
  </div>
  <?php endif; ?>
</div>

<div class="alert-box alert-warning">
  &#9203; &nbsp; Cette réservation est <strong>en attente de confirmation</strong>. Confirmez-la rapidement pour rassurer le client.
</div>

<div style="text-align:center">
  <a href="<?php echo e($urlReservation); ?>" class="btn">Confirmer la réservation</a>
</div>

<p class="text" style="font-size:13px;color:#7A7570;margin-top:12px;text-align:center;">
  Connectez-vous à votre tableau de bord pour gérer cette réservation.
</p>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('emails.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\ISGA\Projet tuteure\Projet Gestion Salon (PHP Laravel)\salonify\resources\views/emails/nouvelle_reservation.blade.php ENDPATH**/ ?>