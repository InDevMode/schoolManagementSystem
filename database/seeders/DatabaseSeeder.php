<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * DatabaseSeeder — Orchestrateur principal.
 *
 * Ordre d'exécution respectant les dépendances :
 *
 *  1. SettingsSeeder          → settings (id=1) requis par periods.settings_id
 *  2. SchoolSeeder            → 3 écoles de démo
 *  3. RolesAndPermissionsSeeder → rôles + permissions Spatie
 *  4. SuperAdminSeeder        → compte super admin global
 *  5. WeekSeeder              → jours de la semaine (pour l'emploi du temps)
 *  6. LeaveTypesSeeder        → types de congés par défaut
 *  7. MultiSchoolSeeder       → users (admin/prof/élèves/parents) + classes + matières
 *  8. PeriodsSeeder           → 3 trimestres 2025-2026
 *  9. StaffAndEventsSeeder    → fiches personnel, congés, événements
 * 10. EvaluationsSeeder       → évaluations + notes de démo
 *
 * Commandes utiles :
 *   php artisan db:seed                       — exécute tout
 *   php artisan db:seed --class=SchoolSeeder  — exécute un seeder précis
 *   php artisan migrate:fresh --seed          — repart de zéro (dev)
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('╔══════════════════════════════════════════════╗');
        $this->command->info('║        School Management System — Seed       ║');
        $this->command->info('╚══════════════════════════════════════════════╝');
        $this->command->info('');

        $seeders = [
            '1/10  Settings'        => SettingsSeeder::class,
            '2/10  Schools'         => SchoolSeeder::class,
            '3/10  Roles & Perms'   => RolesAndPermissionsSeeder::class,
            '4/10  Super Admin'     => SuperAdminSeeder::class,
            '5/10  Week days'       => WeekSeeder::class,
            '6/10  Leave Types'     => LeaveTypesSeeder::class,
            '7/10  Users & Classes' => MultiSchoolSeeder::class,
            '8/10  Periods'         => PeriodsSeeder::class,
            '9/10  Staff & Events'  => StaffAndEventsSeeder::class,
            '10/10 Evaluations'     => EvaluationsSeeder::class,
        ];

        foreach ($seeders as $label => $class) {
            $this->command->info("► $label");
            $this->call($class);
            $this->command->info('');
        }

        $this->command->info('╔══════════════════════════════════════════════╗');
        $this->command->info('║  ✅  Seed terminé avec succès !              ║');
        $this->command->info('║                                              ║');
        $this->command->info('║  Super Admin : superadmin@sms.local          ║');
        $this->command->info('║  Mot de passe : SuperAdmin@2025              ║');
        $this->command->info('╚══════════════════════════════════════════════╝');
        $this->command->info('');
    }
}
