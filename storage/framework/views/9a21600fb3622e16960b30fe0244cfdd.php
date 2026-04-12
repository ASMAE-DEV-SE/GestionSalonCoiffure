<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<meta name="description" content="<?php echo $__env->yieldContent('meta_description', 'Salonify — Réservez votre salon de beauté au Maroc'); ?>">
<title><?php echo $__env->yieldContent('title', 'Salonify'); ?> — Réservation Beauté</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,600&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>">
<?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="<?php echo e($bodyClass ?? ''); ?>">


<header class="nav">
  <div class="nav-r1">
    <a href="<?php echo e(route('home')); ?>" class="brand">
      <div class="brand-box">
        <img src="<?php echo e(asset('images/logo1.png')); ?>" alt="Salonify" class="logo-img">
      </div>
      <div>
        <div class="brand-name">Salonify</div>
        <div class="brand-sub">Reservation Beaute</div>
      </div>
    </a>

    
    <?php if(auth()->guard()->check()): ?>
    <div style="margin-left:auto;margin-right:1rem;position:relative;display:flex;align-items:center;gap:.75rem">
      <a href="<?php echo e(route('client.notifications.index')); ?>" class="notif-btn" style="position:relative">
        &#9993;
        <?php $nbNotifs = auth()->user()->notificationsNonLues()->count(); ?>
        <?php if($nbNotifs > 0): ?>
          <div class="notif-dot"></div>
        <?php endif; ?>
      </a>
      <span style="font-size:.84rem;font-weight:600;color:var(--ink-s)">
        <?php echo e(auth()->user()->prenom); ?>

      </span>
    </div>
    <?php endif; ?>

    <div class="nav-acts">
      <?php if(auth()->guard()->guest()): ?>
        <a href="<?php echo e(route('login')); ?>" class="btn-log">Connexion</a>
        <a href="<?php echo e(route('register')); ?>" class="btn-ins">Inscription</a>
      <?php else: ?>
        <?php if(auth()->user()->isSalon()): ?>
          <a href="<?php echo e(route('salon.dashboard')); ?>" class="btn-log">Mon salon</a>
        <?php elseif(auth()->user()->isAdmin()): ?>
          <a href="<?php echo e(route('admin.dashboard')); ?>" class="btn-log">Admin</a>
        <?php else: ?>
          <a href="<?php echo e(route('client.dashboard')); ?>" class="btn-log">Mon espace</a>
        <?php endif; ?>
        <form method="POST" action="<?php echo e(route('logout')); ?>" style="display:inline">
          <?php echo csrf_field(); ?>
          <button type="submit" class="btn-ins" style="cursor:pointer;border:none">Déconnexion</button>
        </form>
      <?php endif; ?>
    </div>

    <button class="burger" id="brg"><span></span><span></span><span></span></button>
  </div>

  <span class="nav-line"></span>

  <div class="nav-r2">
    <a href="<?php echo e(route('home')); ?>"
       class="<?php echo e(request()->routeIs('home') ? 'on' : ''); ?>">Accueil</a>
    <a href="<?php echo e(route('villes.index')); ?>"
       class="<?php echo e(request()->routeIs('villes.*') ? 'on' : ''); ?>">Emplacements</a>
    <?php if(auth()->guard()->check()): ?>
      <?php if(auth()->user()->isClient()): ?>
        <a href="<?php echo e(route('client.reservations.index')); ?>"
           class="<?php echo e(request()->routeIs('client.reservations.*') ? 'on' : ''); ?>">Mes réservations</a>
      <?php endif; ?>
    <?php endif; ?>
    <a href="<?php echo e(route('contact.show')); ?>"
       class="<?php echo e(request()->routeIs('contact.*') ? 'on' : ''); ?>">Contact</a>
  </div>

  
  <nav class="mob" id="mm">
    <a href="<?php echo e(route('home')); ?>">Accueil</a>
    <a href="<?php echo e(route('villes.index')); ?>">Emplacements</a>
    <a href="<?php echo e(route('contact.show')); ?>">Contact</a>
    <?php if(auth()->guard()->check()): ?>
      <a href="<?php echo e(route('client.reservations.index')); ?>">Mes réservations</a>
    <?php endif; ?>
    <div class="mob-cta">
      <?php if(auth()->guard()->guest()): ?>
        <a href="<?php echo e(route('login')); ?>" class="mc">Connexion</a>
        <a href="<?php echo e(route('register')); ?>" class="mi">Inscription</a>
      <?php else: ?>
        <form method="POST" action="<?php echo e(route('logout')); ?>">
          <?php echo csrf_field(); ?>
          <button type="submit" class="mi" style="width:100%;border:none;cursor:pointer">
            Déconnexion
          </button>
        </form>
      <?php endif; ?>
    </div>
  </nav>
</header>


<?php if(session()->hasAny(['success','error','warning','info'])): ?>
  <div class="flash-zone">
    <?php $__currentLoopData = ['success','error','warning','info']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <?php if(session($type)): ?>
        <div class="flash flash-<?php echo e($type); ?>">
          <?php echo e(session($type)); ?>

          <button class="flash-close" onclick="this.parentElement.remove()">&#10005;</button>
        </div>
      <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>
<?php endif; ?>


<main>
  <?php echo $__env->yieldContent('content'); ?>
</main>


<footer class="foot">
  <div class="foot-bar">
    <div class="fl">hello@Salonify.ma</div>
    <div class="fm">
      <div class="fm-t">Pour plus d'informations</div>
      <div class="fm-r"></div>
    </div>
    <div class="fr">+(212) 7 63456-7890</div>
  </div>
</footer>

<script>
// Burger menu
const brg = document.getElementById('brg');
const mm  = document.getElementById('mm');
if (brg) brg.addEventListener('click', () => mm.classList.toggle('open'));

// Fermer les flash automatiquement après 5s
document.querySelectorAll('.flash').forEach(f => {
  setTimeout(() => f.remove(), 5000);
});
</script>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH F:\ISGA\Projet tuteure\Projet Gestion Salon (PHP Laravel)\salonify\resources\views/layouts/app.blade.php ENDPATH**/ ?>