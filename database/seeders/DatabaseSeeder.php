<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Ordre d'exécution important (respecte les FK).
     *
     * Lancer avec :
     *   php artisan migrate:fresh --seed
     * ou :
     *   php artisan db:seed
     */
    public function run(): void
    {
        $this->call([
            VilleSeeder::class,     // 1. villes (pas de FK)
            AdminSeeder::class,     // 2. users (admin + clients test)
            SalonDemoSeeder::class, // 3. gérants + salons + employes + services + reservations
        ]);

        $this->command->newLine();
        $this->command->info('════════════════════════════════════════');
        $this->command->info('  Salonify — Seeding terminé avec succès');
        $this->command->info('════════════════════════════════════════');
        $this->command->line('  Admin  : admin@salonify.ma / Admin@2026!');
        $this->command->line('  Client : salma.benali@email.com / Client@2026!');
        $this->command->line('  Salon  : contact@elegance-rabat.ma / Salon@2026!');
        $this->command->newLine();
        $this->command->comment('  Pensez à exécuter : php artisan storage:link');
    }
}
