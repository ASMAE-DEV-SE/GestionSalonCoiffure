<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use Illuminate\Console\Command;

/**
 * Commande : php artisan rappels:envoyer
 *
 * À planifier dans routes/console.php ou via cron :
 *   * * * * * cd /chemin/du/projet && php artisan schedule:run >> /dev/null 2>&1
 *
 * La commande :
 *   1. Envoie les SMS de rappel 24h avant le RDV
 *   2. Envoie les SMS de rappel 2h avant le RDV
 *   3. Marque les réservations comme rappel_24h=true / rappel_2h=true
 */
class EnvoyerRappels extends Command
{
    protected $signature   = 'rappels:envoyer {--dry-run : Afficher sans envoyer}';
    protected $description = 'Envoyer les rappels SMS 24h et 2h avant les réservations confirmées';

    public function __construct(private NotificationService $notifService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('⚠ Mode dry-run — aucun SMS ne sera envoyé.');
        }

        $this->info('Salonify — Rappels SMS · ' . now()->format('Y-m-d H:i:s'));
        $this->line(str_repeat('─', 50));

        // ── Rappels 24h ───────────────────────────────────────
        $this->line('');
        $this->line('📨 Rappels 24h...');

        $nb24h = $dryRun
            ? \App\Models\Reservation::rappel24h()->count()
            : $this->notifService->envoyerRappels24h();

        $this->info("   ✓ {$nb24h} rappel(s) 24h traité(s).");

        // ── Rappels 2h ────────────────────────────────────────
        $this->line('');
        $this->line('📨 Rappels 2h...');

        $nb2h = $dryRun
            ? \App\Models\Reservation::rappel2h()->count()
            : $this->notifService->envoyerRappels2h();

        $this->info("   ✓ {$nb2h} rappel(s) 2h traité(s).");

        // ── Expirations & terminaisons automatiques ─────────────
        $this->line('');
        $this->line('⏳ Traitement des réservations passées...');

        $nbExpirees = $dryRun
            ? \App\Models\Reservation::where('statut', 'en_attente')->where('date_heure', '<', now())->count()
            : $this->notifService->annulerReservationsExpirees();

        $this->info("   ✓ {$nbExpirees} réservation(s) expirée(s) annulée(s).\n");

        $nbTerminees = $dryRun
            ? \App\Models\Reservation::where('statut', 'confirmee')->where('date_heure', '<', now())->count()
            : $this->notifService->terminerReservationsPassees();

        $this->info("   ✓ {$nbTerminees} réservation(s) terminée(s).");

        $this->line('');
        $this->line(str_repeat('─', 50));
        $this->info('Terminé. Total : ' . ($nb24h + $nb2h + $nbExpirees + $nbTerminees) . ' actions traitées.');

        return Command::SUCCESS;
    }
}
