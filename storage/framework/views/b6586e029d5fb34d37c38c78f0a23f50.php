<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $__env->yieldContent('subject', 'Salonify'); ?></title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { background: #F5F2EE; font-family: 'Helvetica Neue', Arial, sans-serif; color: #3A3530; }
    .wrapper { max-width: 600px; margin: 32px auto; background: #fff; border-radius: 4px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,.08); }
    .header { background: #4E5C38; padding: 32px 40px; text-align: center; }
    .header .logo { color: #fff; font-size: 26px; font-weight: 700; letter-spacing: 1px; }
    .header .logo span { color: #C5A96D; }
    .header .tagline { color: #C2C9AC; font-size: 12px; margin-top: 4px; letter-spacing: 2px; text-transform: uppercase; }
    .body { padding: 40px; }
    .greeting { font-size: 22px; font-weight: 700; color: #2C2A24; margin-bottom: 16px; }
    .text { font-size: 15px; line-height: 1.7; color: #5A5550; margin-bottom: 16px; }
    .info-card { background: #F5F2EE; border-left: 4px solid #4E5C38; border-radius: 2px; padding: 18px 22px; margin: 24px 0; }
    .info-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #E8E4DF; font-size: 14px; }
    .info-row:last-child { border-bottom: none; }
    .info-label { color: #7A7570; font-weight: 500; }
    .info-value { color: #2C2A24; font-weight: 600; text-align: right; }
    .btn { display: inline-block; background: #4E5C38; color: #fff !important; text-decoration: none; padding: 14px 32px; border-radius: 2px; font-weight: 700; font-size: 14px; margin: 24px 0; letter-spacing: .5px; }
    .btn-danger { background: #C04A3D; }
    .btn-gold { background: #8B6914; }
    .alert-box { border-radius: 2px; padding: 14px 18px; margin: 20px 0; font-size: 14px; }
    .alert-success { background: #EEF3E8; border: 1px solid #9CAB84; color: #3A4D28; }
    .alert-danger  { background: #FDECEA; border: 1px solid #D98080; color: #6B1F1B; }
    .alert-warning { background: #FEF6E4; border: 1px solid #D4A844; color: #6A3800; }
    .divider { border: none; border-top: 1px solid #EEE; margin: 28px 0; }
    .footer { background: #F5F2EE; padding: 24px 40px; text-align: center; border-top: 1px solid #E8E4DF; }
    .footer p { font-size: 12px; color: #9A9590; line-height: 1.6; }
    .footer a { color: #4E5C38; text-decoration: none; }
    .social { margin: 12px 0; }
    .badge { display: inline-block; background: #4E5C38; color: #fff; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 10px; margin-bottom: 12px; }
  </style>
</head>
<body>
<div class="wrapper">
  <div class="header">
    <div class="logo">Salon<span>ify</span></div>
    <div class="tagline">Votre plateforme beauté au Maroc</div>
  </div>
  <div class="body">
    <?php echo $__env->yieldContent('content'); ?>
  </div>
  <div class="footer">
    <p>
      Cet email a été envoyé automatiquement par <strong>Salonify</strong>.<br>
      Merci de ne pas répondre à cet email.
    </p>
    <p style="margin-top:10px;">
      &copy; <?php echo e(date('Y')); ?> Salonify · Maroc &nbsp;|&nbsp;
      <a href="#">Conditions d'utilisation</a> &nbsp;|&nbsp;
      <a href="#">Contact</a>
    </p>
  </div>
</div>
</body>
</html>
<?php /**PATH F:\ISGA\Projet tuteure\Projet Gestion Salon (PHP Laravel)\salonify\resources\views/emails/layout.blade.php ENDPATH**/ ?>