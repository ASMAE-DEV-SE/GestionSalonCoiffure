<?php $__env->startSection('title', 'Réservation confirmée'); ?>

<?php $__env->startSection('content'); ?>


<div class="stepper-bar-wrap">
  <div class="stepper-bar">
    <div class="step done"><div class="step-dot">&#10003;</div><div class="step-label">Service</div></div>
    <div class="step done"><div class="step-dot">&#10003;</div><div class="step-label">Créneau</div></div>
    <div class="step done"><div class="step-dot">&#10003;</div><div class="step-label">Vos infos</div></div>
    <div class="step done"><div class="step-dot">&#10003;</div><div class="step-label">Confirmation</div></div>
  </div>
</div>


<div class="confirm-hero">
  <div class="wrap">
    <div class="confirm-pulse"><div class="confirm-check">&#10003;</div></div>
    <h1 class="confirm-title">Réservation <em>confirmée</em> !</h1>
    <p class="confirm-desc">
      Votre rendez-vous a bien été enregistré. Vous recevrez une confirmation par email
      et un rappel SMS 24h avant.
    </p>
    <div>
      <span class="confirm-ref-label">Référence de réservation</span>
      <span class="confirm-ref">#SAL-<?php echo e(str_pad($reservation->id, 6, '0', STR_PAD_LEFT)); ?></span>
    </div>
  </div>
</div>


<div class="confirm-layout">
  <div>

    
    <div class="notif-banner">
      <div class="notif-icon">&#9993;</div>
      <div>
        <div class="notif-title">Confirmation envoyée à <?php echo e(auth()->user()->email); ?></div>
        <p class="notif-text">
          Un récapitulatif complet avec les informations du salon vous a été envoyé par email.
          Vérifiez vos spams si vous ne le trouvez pas.
        </p>
      </div>
    </div>

    
    <div class="recap-card">
      <div class="recap-head">
        <div class="recap-head-title">Détails de votre réservation</div>
      </div>

      <div class="recap-salon">
        <div class="recap-salon-photo">
          <img src="<?php echo e($reservation->salon->photo_url); ?>" alt="<?php echo e($reservation->salon->nom_salon); ?>">
        </div>
        <div>
          <div class="salon-name"><?php echo e($reservation->salon->nom_salon); ?></div>
          <div class="salon-address"><?php echo e($reservation->salon->adresse); ?>, <?php echo e($reservation->salon->quartier); ?></div>
          <div class="salon-rating">&#9733; <?php echo e(number_format($reservation->salon->note_moy, 1)); ?> · <?php echo e($reservation->salon->nb_avis); ?> avis</div>
        </div>
      </div>

      <div class="recap-details-grid">
        <div class="recap-detail-cell">
          <div class="detail-label">Service</div>
          <div class="detail-value"><?php echo e($reservation->service->nom_service); ?></div>
          <div class="detail-sub"><?php echo e($reservation->service->duree_formatee); ?></div>
        </div>
        <div class="recap-detail-cell">
          <div class="detail-label">Date &amp; Heure</div>
          <div class="detail-value"><?php echo e($reservation->date_heure->translatedFormat('D d M Y')); ?></div>
          <div class="detail-sub"><?php echo e($reservation->date_heure->format('H:i')); ?></div>
        </div>
        <div class="recap-detail-cell">
          <div class="detail-label">Styliste</div>
          <div class="detail-value"><?php echo e($reservation->employe?->nomComplet() ?? 'Au choix'); ?></div>
          <div class="detail-sub">Professionnel(le) certifié(e)</div>
        </div>
      </div>

      <div class="recap-client">
        <div>
          <div class="client-label">Client</div>
          <div class="client-value"><?php echo e(auth()->user()->nomComplet()); ?></div>
        </div>
        <div>
          <div class="client-label">Statut</div>
          <div class="client-value">
            <span class="bge-wa">En attente de confirmation salon</span>
          </div>
        </div>
      </div>

      <div class="recap-total">
        <div>
          <div class="total-label">Montant à payer au salon</div>
          <div class="total-note">Paiement en espèces ou carte directement sur place</div>
        </div>
        <div class="total-amount"><?php echo e($reservation->service->prix_format); ?></div>
      </div>
    </div>

    
    <div class="confirm-actions">
      <a href="<?php echo e(route('client.reservations.index')); ?>" class="btn-dark">Mes réservations</a>
      <a href="<?php echo e(route('salons.index', $reservation->salon->ville->nom_ville)); ?>" class="btn-outline-green">
        Autres salons à <?php echo e($reservation->salon->ville->nom_ville); ?>

      </a>
      <a href="<?php echo e(route('home')); ?>" class="btn-outline-light">Retour à l'accueil</a>
    </div>

    
    <div class="next-steps-card">
      <div class="next-steps-head"><div class="next-steps-title">Et maintenant ?</div></div>
      <div class="next-step-item">
        <div class="step-number">1</div>
        <div>
          <div class="step-item-title">Confirmation du salon</div>
          <div class="step-item-desc">Le salon examinera votre demande et vous enverra une confirmation sous 2h.</div>
        </div>
      </div>
      <div class="next-step-item">
        <div class="step-number">2</div>
        <div>
          <div class="step-item-title">Rappel SMS 24h avant</div>
          <div class="step-item-desc">Vous recevrez un SMS de rappel la veille de votre rendez-vous.</div>
        </div>
      </div>
      <div class="next-step-item">
        <div class="step-number">3</div>
        <div>
          <div class="step-item-title">Votre rendez-vous beauté</div>
          <div class="step-item-desc">Présentez-vous 5 min avant l'heure. Le paiement s'effectue sur place.</div>
        </div>
      </div>
      <div class="next-step-item">
        <div class="step-number">4</div>
        <div>
          <div class="step-item-title">Laissez un avis</div>
          <div class="step-item-desc">Après votre visite, partagez votre expérience pour aider la communauté Salonify.</div>
        </div>
      </div>
    </div>

  </div>

  
  <div>
    <div class="sidebar-map">
      <div class="map-image">
        <img src="https://images.unsplash.com/photo-1555448248-2571daf6344b?w=400&h=200&fit=crop&q=80" alt="Carte">
        <div class="map-overlay">
          <span class="map-location-label">&#128205; <?php echo e($reservation->salon->quartier); ?>, <?php echo e($reservation->salon->ville->nom_ville); ?></span>
        </div>
      </div>
      <div class="map-body">
        <div class="map-salon-name"><?php echo e($reservation->salon->nom_salon); ?></div>
        <div class="map-info-row">
          <span class="map-icon">&#128205;</span>
          <span class="map-info-text"><?php echo e($reservation->salon->adresse); ?></span>
        </div>
        <?php if($reservation->salon->telephone): ?>
          <div class="map-info-row">
            <span class="map-icon">&#128222;</span>
            <span class="map-info-text"><?php echo e($reservation->salon->telephone); ?></span>
          </div>
        <?php endif; ?>
        <div class="map-cta">
          <a href="https://maps.google.com/?q=<?php echo e(urlencode($reservation->salon->adresse . ', ' . $reservation->salon->ville->nom_ville)); ?>"
             target="_blank">Ouvrir dans Maps</a>
        </div>
      </div>
    </div>

    
    <div class="cancellation-box">
      <div class="cancellation-title">Politique d'annulation</div>
      <p class="cancellation-text">
        Annulation gratuite jusqu'à 24h avant votre rendez-vous.
        Au-delà, l'annulation peut entraîner des frais selon le salon.
      </p>
      <div style="margin-top:.9rem">
        <form method="POST" action="<?php echo e(route('reservations.annuler', $reservation->id)); ?>"
              onsubmit="return confirm('Confirmer l\'annulation de cette réservation ?')">
          <?php echo csrf_field(); ?>
          <button type="submit" style="background:none;border:none;font-size:.76rem;font-weight:700;color:#8B2222;text-decoration:underline;cursor:pointer;font-family:var(--fb)">
            Annuler cette réservation
          </button>
        </form>
      </div>
    </div>
  </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\ISGA\Projet tuteure\Projet Gestion Salon (PHP Laravel)\salonify\resources\views/reservations/confirmation.blade.php ENDPATH**/ ?>