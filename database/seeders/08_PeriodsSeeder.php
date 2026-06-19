<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * PeriodsSeeder — Crée les 3 trimestres de l'année scolaire 2025-2026.
 *
 * Dépendances :
 *   - SettingsSeeder (settings.id = 1 doit exister pour settings_id FK)
 *   - SuperAdminSeeder (users.id = 1 pour created_by)
 */
class PeriodsSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer le settings_id (toujours 1 après SettingsSeeder)
        $settings = DB::table('settings')->orderBy('id')->first();
        if (! $settings) {
            $this->command->error('  ✗ Aucun enregistrement dans settings — lancez SettingsSeeder d\'abord.');
            return;
        }
        $settingsId = $settings->id;

        // Récupérer l'id du super admin pour created_by
        $superAdmin = DB::table('users')->where('user_type', 0)->orderBy('id')->first();
        $createdBy  = $superAdmin?->id ?? 1;

        $periods = [
            [
                'name'         => '1er Trimestre',
                'type'         => 'trimestre',
                'order_number' => 1,
                'school_year'  => '2025-2026',
                'start_date'   => '2025-09-01',
                'end_date'     => '2025-12-20',
                'is_current'   => 0,
                'status'       => 0, // Terminé
            ],
            [
                'name'         => '2ème Trimestre',
                'type'         => 'trimestre',
                'order_number' => 2,
                'school_year'  => '2025-2026',
                'start_date'   => '2026-01-06',
                'end_date'     => '2026-03-28',
                'is_current'   => 1,
                'status'       => 1, // En cours
            ],
            [
                'name'         => '3ème Trimestre',
                'type'         => 'trimestre',
                'order_number' => 3,
                'school_year'  => '2025-2026',
                'start_date'   => '2026-04-07',
                'end_date'     => '2026-06-30',
                'is_current'   => 0,
                'status'       => 0, // À venir
            ],
        ];

        $created = 0;
        foreach ($periods as $period) {
            $exists = DB::table('periods')
                ->where('name', $period['name'])
                ->where('school_year', $period['school_year'])
                ->where('is_delete', 0)
                ->exists();

            if (! $exists) {
                DB::table('periods')->insert([
                    'settings_id'  => $settingsId,
                    'name'         => $period['name'],
                    'type'         => $period['type'],
                    'order_number' => $period['order_number'],
                    'school_year'  => $period['school_year'],
                    'start_date'   => $period['start_date'],
                    'end_date'     => $period['end_date'],
                    'is_current'   => $period['is_current'],
                    'status'       => $period['status'],
                    'is_delete'    => 0,
                    'created_by'   => $createdBy,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
                $created++;
            }
        }

        $this->command->info("  ✅ $created période(s) créée(s) (2025-2026).");
    }
}
