<?php

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Tâches planifiées — Salonify
|--------------------------------------------------------------------------
| Activer le cron sur le serveur :
|   * * * * * cd /votre/chemin && php artisan schedule:run >> /dev/null 2>&1
|
| Tester en local :
|   php artisan schedule:work   (surveille et exécute toutes les minutes)
|   php artisan rappels:envoyer --dry-run
*/

// ── Rappels SMS toutes les heures (24h et 2h avant RDV) ──────
Schedule::command('rappels:envoyer')
    ->hourly()
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/rappels.log'));

// ── Nettoyage des tokens expirés chaque nuit à 3h ────────────
Schedule::command('auth:clear-resets')
    ->dailyAt('03:00');

// ── Nettoyage des sessions expirées ──────────────────────────
// (si SESSION_DRIVER=database)
// Schedule::command('session:gc')->daily();
