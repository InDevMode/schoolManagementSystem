<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * DatabaseSeeder — Orchestrateur.
 * Ordre : respecte toutes les dépendances FK.
 * Toutes les tables remplies, aucune colonne nullable laissée vide.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('╔══════════════════════════════════════════════════╗');
        $this->command->info('║   School Management System — Seed complet (UUID) ║');
        $this->command->info('╚══════════════════════════════════════════════════╝');
        $this->command->info('');

        $seeders = [
            '1/10  Settings'         => SettingsSeeder::class,
            '2/10  Schools'          => SchoolSeeder::class,
            '3/10  Roles & Perms'    => RolesAndPermissionsSeeder::class,
            '4/10  Super Admin'      => SuperAdminSeeder::class,
            '5/10  Jours semaine'    => WeekSeeder::class,
            '6/10  Types congés'     => LeaveTypesSeeder::class,
            '7/10  Users & Classes'  => MultiSchoolSeeder::class,
            '8/10  Périodes & Exams' => PeriodsSeeder::class,
            '9/10  Staff & Événements' => StaffAndEventsSeeder::class,
            '10/10 Évaluations'      => EvaluationsSeeder::class,
        ];

        foreach ($seeders as $label => $class) {
            $this->command->info("► $label");
            $this->call($class);
            $this->command->info('');
        }

        $this->command->info('╔══════════════════════════════════════════════════════════╗');
        $this->command->info('║  ✅  Seed terminé avec succès !                          ║');
        $this->command->info('║                                                          ║');
        $this->command->info('║  Super Admin  : schoolmanagementsystem00@gmail.com / SuperAdmin@2025  ║');
        $this->command->info('║  Admin LMC    : admin@lmc.bj     / Admin@LMC2025        ║');
        $this->command->info('║  Admin CSM    : admin@csm.bj     / Admin@CSM2025        ║');
        $this->command->info('║  Admin EPE    : admin@epe.bj     / Admin@EPE2025        ║');
        $this->command->info('║  Profs        : prof1@lmc.bj     / Prof@1234            ║');
        $this->command->info('║  Apprenants   : eleve1@lmc.bj    / Eleve@1234           ║');
        $this->command->info('║  Parents      : parent1@lmc.bj   / Parent@1234          ║');
        $this->command->info('╚══════════════════════════════════════════════════════════╝');
    }
}
