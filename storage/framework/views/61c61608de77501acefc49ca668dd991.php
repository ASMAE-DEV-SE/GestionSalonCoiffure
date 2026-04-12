<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<title><?php echo $__env->yieldContent('title', 'Mon espace'); ?> — Salonify</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,600&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>">
<?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>


<header class="nav" style="position:sticky;top:0;z-index:999">
  <div class="nav-r1">
    <a href="<?php echo e(route('home')); ?>" class="brand">
      <div class="brand-box">
        <img src="<?php echo e(asset('images/logo1.png')); ?>" alt="Salonify" class="logo-img">
      </div>
      <div>
        <div class="brand-name">Salonify</div>
        <div class="brand-sub"><?php echo e(auth()->user()->isSalon() ? 'Espace Salon' : 'Espace Client'); ?></div>
      </div>
    </a>
    <div class="nav-acts" style="margin-left:auto">
      <?php if(auth()->user()->isSalon()): ?>
        <a href="<?php echo e(route('home')); ?>" class="btn-log">Voir le site</a>
      <?php else: ?>
        <a href="<?php echo e(route('villes.index')); ?>" class="btn-log">Trouver un salon</a>
      <?php endif; ?>
      <form method="POST" action="<?php echo e(route('logout')); ?>" style="display:inline">
        <?php echo csrf_field(); ?>
        <button type="submit" class="btn-ins" style="cursor:pointer;border:none">Déconnexion</button>
      </form>
    </div>
  </div>
  <span class="nav-line"></span>

  
  <?php if(auth()->user()->isSalon()): ?>
    
    <div class="dt-inner" style="overflow-x:auto;display:flex;background:linear-gradient(135deg,#1C1A14,#2E3D1E);padding:0 2.5rem">
      <a href="<?php echo e(route('salon.dashboard')); ?>"
         class="dt-tab <?php echo e(request()->routeIs('salon.dashboard') ? 'on' : ''); ?>">Vue générale</a>
      <a href="<?php echo e(route('salon.services.index')); ?>"
         class="dt-tab <?php echo e(request()->routeIs('salon.services.*') ? 'on' : ''); ?>">Services</a>
      <a href="<?php echo e(route('salon.employes.index')); ?>"
         class="dt-tab <?php echo e(request()->routeIs('salon.employes.*') ? 'on' : ''); ?>">Équipe</a>
      <a href="<?php echo e(route('salon.disponibilites.index')); ?>"
         class="dt-tab <?php echo e(request()->routeIs('salon.disponibilites.*') ? 'on' : ''); ?>">Disponibilités</a>
      <a href="<?php echo e(route('salon.reservations.index')); ?>"
         class="dt-tab <?php echo e(request()->routeIs('salon.reservations.*') ? 'on' : ''); ?>">Réservations
        <?php $rdvEnAttente = auth()->user()->salon?->reservations()->where('statut','en_attente')->where('date_heure','>=',now())->count(); ?>
        <?php if($rdvEnAttente > 0): ?>
          <span style="background:#E8562A;color:#fff;font-size:.6rem;font-weight:700;padding:.06rem .4rem;border-radius:8px;margin-left:.3rem"><?php echo e($rdvEnAttente); ?></span>
        <?php endif; ?>
      </a>
      <a href="<?php echo e(route('salon.avis.index')); ?>"
         class="dt-tab <?php echo e(request()->routeIs('salon.avis.*') ? 'on' : ''); ?>">Avis</a>
      <a href="<?php echo e(route('salon.profil.edit')); ?>"
         class="dt-tab <?php echo e(request()->routeIs('salon.profil.*') ? 'on' : ''); ?>">Mon salon</a>
    </div>
  <?php endif; ?>
</header>


<?php $__currentLoopData = ['success','error','warning','info']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
  <?php if(session($type)): ?>
    <div class="flash flash-<?php echo e($type); ?>">
      <?php echo e(session($type)); ?>

      <button class="flash-close" onclick="this.parentElement.remove()">&#10005;</button>
    </div>
  <?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


<?php if(auth()->user()->isClient()): ?>
<div class="client-dash-wrap">

  <aside class="client-sidebar">
    
    <div class="client-sidebar-profile">
      <div class="sidebar-avatar-wrap">
        <img src="https://ui-avatars.com/api/?name=<?php echo e(urlencode(auth()->user()->nomComplet())); ?>&background=9CAB84&color=fff&size=96"
             class="sidebar-avatar-img" alt="<?php echo e(auth()->user()->nomComplet()); ?>">
        <div class="sidebar-avatar-status"></div>
      </div>
      <div class="sidebar-client-name"><?php echo e(auth()->user()->nomComplet()); ?></div>
      <div class="sidebar-client-email"><?php echo e(auth()->user()->email); ?></div>
      <div class="sidebar-level-badge">&#9733; Client Salonify</div>
    </div>

    
    <nav class="sidebar-nav">
      <span class="sidebar-nav-section-label">Mon espace</span>
      <a href="<?php echo e(route('client.dashboard')); ?>"
         class="sidebar-nav-link <?php echo e(request()->routeIs('client.dashboard') ? 'active' : ''); ?>">
        <div class="sidebar-nav-icon">&#9962;</div>Tableau de bord
      </a>
      <a href="<?php echo e(route('client.reservations.index')); ?>"
         class="sidebar-nav-link <?php echo e(request()->routeIs('client.reservations.*') ? 'active' : ''); ?>">
        <div class="sidebar-nav-icon">&#128197;</div>Mes réservations
        <?php $aVenir = auth()->user()->reservations()->whereIn('statut',['en_attente','confirmee'])->where('date_heure','>=',now())->count(); ?>
        <?php if($aVenir > 0): ?>
          <span class="sidebar-nav-badge"><?php echo e($aVenir); ?></span>
        <?php endif; ?>
      </a>
      <a href="<?php echo e(route('client.profil.edit')); ?>"
         class="sidebar-nav-link <?php echo e(request()->routeIs('client.profil.*') ? 'active' : ''); ?>">
        <div class="sidebar-nav-icon">&#9786;</div>Mon profil
      </a>
      <a href="<?php echo e(route('client.notifications.index')); ?>"
         class="sidebar-nav-link <?php echo e(request()->routeIs('client.notifications.*') ? 'active' : ''); ?>">
        <div class="sidebar-nav-icon">&#9993;</div>Notifications
        <?php $nbNotif = auth()->user()->notificationsNonLues()->count(); ?>
        <?php if($nbNotif > 0): ?>
          <span class="sidebar-nav-badge"><?php echo e($nbNotif); ?></span>
        <?php endif; ?>
      </a>

      <div class="sidebar-divider"></div>
      <span class="sidebar-nav-section-label">Découvrir</span>
      <a href="<?php echo e(route('villes.index')); ?>" class="sidebar-nav-link">
        <div class="sidebar-nav-icon">&#127968;</div>Salons près de moi
      </a>
    </nav>

    <div class="sidebar-logout">
      <form method="POST" action="<?php echo e(route('logout')); ?>">
        <?php echo csrf_field(); ?>
        <button type="submit" class="btn-logout">&#10006; Déconnexion</button>
      </form>
    </div>
  </aside>

  <main class="client-main">
    <?php echo $__env->yieldContent('content'); ?>
  </main>

</div>


<?php else: ?>
<main style="max-width:1200px;margin:2.2rem auto 4rem;padding:0 2.5rem">
  <?php echo $__env->yieldContent('content'); ?>
</main>
<?php endif; ?>

<script>
document.querySelectorAll('.flash').forEach(f => setTimeout(() => f.remove(), 5000));
</script>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH F:\ISGA\Projet tuteure\Projet Gestion Salon (PHP Laravel)\salonify\resources\views/layouts/dashboard.blade.php ENDPATH**/ ?>