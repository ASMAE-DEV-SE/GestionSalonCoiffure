#!/bin/sh
set -e

php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan migrate --force

echo "[BOOT] MAIL_MAILER=${MAIL_MAILER} | BREVO_API_KEY set? $([ -n "$BREVO_API_KEY" ] && echo yes || echo NO)"

# Scheduler Laravel en arriere-plan (rappels:envoyer hourly, etc.)
# stderr -> Railway logs via LOG_CHANNEL=stderr
echo "[BOOT] Lancement scheduler Laravel en arriere-plan..."
php artisan schedule:work >&2 &

# Serveur web (process principal - si il meurt, le container redemarre)
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
