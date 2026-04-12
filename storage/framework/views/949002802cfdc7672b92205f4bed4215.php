<?php $__env->startSection('title', 'Connexion'); ?>

<?php $__env->startSection('content'); ?>
<div class="auth-page">

  <div class="auth-left">
    <img class="auth-left-bg" src="https://images.unsplash.com/photo-1560066984-138dadb4c035?w=900&q=80" alt="">
    <div class="auth-left-content">
      <div class="auth-left-tag">Bienvenue</div>
      <h1 class="auth-left-title">Votre beauté,<br>notre <em>priorité</em></h1>
      <p class="auth-left-desc">Connectez-vous pour gérer vos réservations, retrouver vos salons favoris et profiter de votre espace personnel Salonify.</p>
      <div class="auth-points">
        <div class="auth-point"><div class="auth-point-icon">&#10003;</div><div class="auth-point-text">Réservations en 3 clics</div></div>
        <div class="auth-point"><div class="auth-point-icon">&#10003;</div><div class="auth-point-text">Rappels SMS automatiques</div></div>
        <div class="auth-point"><div class="auth-point-icon">&#10003;</div><div class="auth-point-text">Historique & avis vérifiés</div></div>
      </div>
    </div>
  </div>

  <div class="auth-right">
    <div class="auth-box">

      <div class="auth-tabs">
        <div class="auth-tab active" onclick="window.location='<?php echo e(route('login')); ?>'">Connexion</div>
        <div class="auth-tab" onclick="window.location='<?php echo e(route('register')); ?>'">Inscription</div>
      </div>

      <div class="auth-form-title">Connexion</div>
      <p class="auth-form-desc">Entrez vos identifiants pour accéder à votre espace.</p>

      <form method="POST" action="<?php echo e(route('login')); ?>">
        <?php echo csrf_field(); ?>

        <div class="form-group">
          <label for="email">Adresse email</label>
          <input id="email" type="email" name="email" class="form-input <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                 value="<?php echo e(old('email')); ?>" placeholder="votre@email.com" required autofocus>
          <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div style="color:#C04A3D;font-size:.76rem;margin-top:.35rem;font-weight:600"><?php echo e($message); ?></div>
          <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="form-group">
          <label for="mot_de_passe">Mot de passe</label>
          <div class="password-wrap">
            <input id="mot_de_passe" type="password" name="mot_de_passe"
                   class="form-input <?php $__errorArgs = ['mot_de_passe'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                   placeholder="••••••••" required>
            <button type="button" class="password-eye"
                    onclick="const i=document.getElementById('mot_de_passe');i.type=i.type==='password'?'text':'password';this.style.color=i.type==='text'?'var(--p4)':''">&#128065;</button>
          </div>
        </div>

        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.4rem">
          <label style="display:flex;align-items:center;gap:.5rem;font-size:.82rem;color:var(--ink-s);cursor:pointer">
            <input type="checkbox" name="remember" style="accent-color:var(--p4)"> Se souvenir de moi
          </label>
          <a href="<?php echo e(route('password.request')); ?>" class="forgot-link" style="margin:0">Mot de passe oublié ?</a>
        </div>

        <button type="submit" class="btn-auth">Se connecter</button>

        <div class="or-divider"><span>ou continuer avec</span></div>
        <a href="<?php echo e(route('auth.google')); ?>" class="btn-social" style="display:flex;align-items:center;justify-content:center;gap:.6rem;text-decoration:none">
          <svg width="18" height="18" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
          Continuer avec Google
        </a>
      </form>

      <div class="switch-text">
        Pas encore de compte ?<a href="<?php echo e(route('register')); ?>">S'inscrire gratuitement</a>
      </div>

    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\ISGA\Projet tuteure\Projet Gestion Salon (PHP Laravel)\salonify\resources\views/auth/connexion.blade.php ENDPATH**/ ?>