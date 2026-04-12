<?php $__env->startSection('title', 'Accueil'); ?>
<?php $__env->startSection('meta_description', 'Salonify — Réservez votre salon de beauté au Maroc en ligne. Coiffure, soins, onglerie à Rabat, Casablanca et partout au Maroc.'); ?>

<?php $__env->startSection('content'); ?>


<section class="hero">
  <div class="hero-left">
    <div class="hero-rule"></div>
    <h1 class="hero-title">
      La beauté<br>à portée de<br><em>clic</em>
    </h1>
    <p class="hero-text">
      Réservez votre prochain rendez-vous beauté en quelques secondes.
      Les meilleurs salons de coiffure et instituts du Maroc, disponibles 24h/24.
    </p>
    <div style="display:flex;gap:1rem;flex-wrap:wrap">
      <a href="<?php echo e(route('villes.index')); ?>" class="btn-dk">Trouver un salon</a>
      <?php if(auth()->guard()->guest()): ?>
        <a href="<?php echo e(route('register')); ?>" class="btn-gh">Inscription gratuite</a>
      <?php endif; ?>
    </div>
  </div>
  <div class="hero-right">
    <img src="https://images.unsplash.com/photo-1560066984-138dadb4c035?w=900&q=80" alt="Salon de beauté Maroc">
  </div>
</section>


<div class="search-bar">
  <form action="<?php echo e(route('home')); ?>" method="GET">
    <div class="search-grid">
      <div>
        <div class="search-label">Rechercher</div>
        <input type="text" name="q" class="field" placeholder="Nom de salon, service..." value="<?php echo e(request('q')); ?>">
      </div>
      <div>
        <div class="search-label">Ville</div>
        <select name="ville" class="field">
          <option value="">Toutes les villes</option>
          <?php $__currentLoopData = $villes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ville): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($ville->id); ?>" <?php echo e(request('ville') == $ville->id ? 'selected' : ''); ?>>
              <?php echo e($ville->nom_ville); ?> (<?php echo e($ville->salons_valides_count); ?>)
            </option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <div class="search-label">Service</div>
        <select name="categorie" class="field">
          <option value="">Tous les services</option>
          <option value="Coiffure">Coiffure</option>
          <option value="Couleur">Couleur &amp; Coloration</option>
          <option value="Soins">Soins visage &amp; corps</option>
          <option value="Ongles">Ongles &amp; Onglerie</option>
          <option value="Massage">Massage &amp; Bien-être</option>
        </select>
      </div>
      <button type="submit" class="btn-dk" style="padding:.82rem 2rem">Rechercher</button>
    </div>
  </form>
</div>


<?php if($recherche !== null): ?>
  <div class="wrap" style="padding-top:2.5rem;padding-bottom:1rem">
    <div style="font-size:1rem;font-weight:600;color:var(--ink-s);margin-bottom:1.5rem">
      <?php echo e($recherche->count()); ?> résultat(s) pour votre recherche
    </div>
    <?php if($recherche->isEmpty()): ?>
      <div style="padding:3rem 0;text-align:center;color:var(--ink-m);font-size:.9rem">
        Aucun salon trouvé pour ces critères. <a href="<?php echo e(route('villes.index')); ?>" style="color:var(--p4d);font-weight:700">Parcourir toutes les villes</a>
      </div>
    <?php else: ?>
      <div class="featured-grid" style="margin-bottom:3rem">
        <?php $__currentLoopData = $recherche; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $salon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <a href="<?php echo e(route('salons.show', [$salon->ville->nom_ville, $salon->slug])); ?>" class="card">
            <div class="card-image">
              <img src="<?php echo e($salon->photo_url); ?>" alt="<?php echo e($salon->nom_salon); ?>" style="height:200px">
              <div class="card-category-icon icon-coiffure">&#9986;</div>
            </div>
            <div class="card-body">
              <div class="card-name"><?php echo e($salon->nom_salon); ?></div>
              <div class="card-rating">
                <span class="card-stars"><?php echo e(str_repeat('★', round($salon->note_moy))); ?><?php echo e(str_repeat('☆', 5 - round($salon->note_moy))); ?></span>
                <span class="card-score"><?php echo e(number_format($salon->note_moy, 1)); ?></span>
                <span class="card-reviews">(<?php echo e($salon->nb_avis); ?>)</span>
              </div>
              <div class="card-location">&#128205; <?php echo e($salon->quartier); ?>, <?php echo e($salon->ville->nom_ville); ?></div>
              <button class="btn-view">Voir le salon</button>
            </div>
          </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    <?php endif; ?>
  </div>
<?php else: ?>


<div class="stats-bar">
  <div class="stats-grid wrap">
    <div style="padding:1.2rem 0">
      <div class="stat-value"><?php echo e($stats['salons']); ?>+</div>
      <div class="stat-label">Salons référencés</div>
    </div>
    <div style="padding:1.2rem 0;border-left:1px solid rgba(255,255,255,.18);border-right:1px solid rgba(255,255,255,.18)">
      <div class="stat-value"><?php echo e($stats['villes']); ?></div>
      <div class="stat-label">Villes couvertes</div>
    </div>
    <div style="padding:1.2rem 0">
      <div class="stat-value"><?php echo e(number_format($stats['reservations'])); ?>+</div>
      <div class="stat-label">Réservations effectuées</div>
    </div>
  </div>
</div>


<?php if($salonsFeatured->count()): ?>
<section style="padding:5rem 0;background:#fff">
  <div class="wrap">
    <div style="display:flex;align-items:baseline;justify-content:space-between;margin-bottom:2.5rem">
      <div>
        <div class="rule" style="margin-bottom:.9rem"></div>
        <h2 style="font-family:var(--fh);font-size:2.1rem;color:var(--ink-h)">Salons en <em style="font-style:italic;color:var(--p4d)">vedette</em></h2>
      </div>
      <a href="<?php echo e(route('villes.index')); ?>" style="font-size:.78rem;font-weight:700;color:var(--p4d);text-decoration:underline;text-transform:uppercase;letter-spacing:.5px">Voir tous les salons</a>
    </div>
    <div class="featured-grid">
      <?php $__currentLoopData = $salonsFeatured; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $salon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('salons.show', [$salon->ville->nom_ville, $salon->slug])); ?>" class="card">
          <div class="card-image">
            <img src="<?php echo e($salon->photo_url); ?>" alt="<?php echo e($salon->nom_salon); ?>" style="height:200px">
            <span class="card-price-tag">À partir de <?php echo e(number_format($salon->servicesActifs->min('prix') ?? 0, 0, ',', ' ')); ?> MAD</span>
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
              <span class="verified-dot"></span><?php echo e($salon->quartier); ?>, <?php echo e($salon->ville->nom_ville); ?>

            </div>
            <p class="card-desc"><?php echo e(Str::limit($salon->description, 80)); ?></p>
            <button class="btn-view">Réserver maintenant</button>
          </div>
        </a>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  </div>
</section>
<?php endif; ?>


<section class="districts" style="padding:4rem 0">
  <div class="wrap">
    <h2 class="districts-title">Explorez par <em style="font-style:italic;color:var(--p4d)">ville</em></h2>
    <div class="districts-grid">
      <?php $__currentLoopData = $villes->take(10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ville): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('salons.index', $ville->nom_ville)); ?>" class="district-cell" style="text-decoration:none">
          <div class="district-name"><?php echo e($ville->nom_ville); ?></div>
          <div class="district-count"><?php echo e($ville->salons_valides_count); ?> salon<?php echo e($ville->salons_valides_count > 1 ? 's' : ''); ?></div>
        </a>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  </div>
</section>


<section class="features">
  <div class="wrap">
    <h2 class="features-title">Comment ça <em style="font-style:italic;color:var(--p4d)">marche ?</em></h2>
    <div class="features-grid">
      <div style="text-align:center">
        <div class="feature-icon">&#128269;</div>
        <div class="feature-name">1. Trouvez votre salon</div>
        <p class="feature-desc">Cherchez parmi les meilleurs salons de votre ville, filtrez par service, quartier ou note.</p>
      </div>
      <div style="text-align:center">
        <div class="feature-icon">&#128197;</div>
        <div class="feature-name">2. Choisissez votre créneau</div>
        <p class="feature-desc">Sélectionnez votre service et l'horaire qui vous convient directement en ligne, 24h/24.</p>
      </div>
      <div style="text-align:center">
        <div class="feature-icon">&#10003;</div>
        <div class="feature-name">3. Confirmé instantanément</div>
        <p class="feature-desc">Recevez la confirmation par email et SMS. Le paiement s'effectue directement au salon.</p>
      </div>
    </div>
  </div>
</section>


<div class="cta-block">
  <div class="cta-image">
    <img src="https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=800&q=80" alt="Gérant de salon">
  </div>
  <div class="cta-text">
    <h2 class="cta-title">Vous êtes un<br>professionnel ?</h2>
    <p class="cta-desc">Rejoignez Salonify et développez votre clientèle. Inscription gratuite, aucune commission sur les réservations.</p>
    <a href="<?php echo e(route('register')); ?>?role=salon" class="btn-wh">Inscrire mon salon</a>
  </div>
</div>

<?php endif; ?> 

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\ISGA\Projet tuteure\Projet Gestion Salon (PHP Laravel)\salonify\resources\views/public/accueil.blade.php ENDPATH**/ ?>