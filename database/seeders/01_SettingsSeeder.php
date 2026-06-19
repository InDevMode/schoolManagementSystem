<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * SettingsSeeder — Insère l'enregistrement de configuration global (id=1).
 *
 * La table settings est conservée pour rétrocompatibilité avec PeriodModel
 * et EvaluationSeeder qui font encore référence à settings_id.
 * La table schools est la source de vérité multi-tenant.
 */
class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('settings')->exists()) {
            $this->command->info('  ⏭  Settings déjà présent — ignoré.');
            return;
        }

        DB::table('settings')->insert([
            'school_name' => 'Mon École',
            'school_type' => 'Lycée',
            'address'     => 'Cotonou, Bénin',
            'phone'       => '+229 97 00 00 00',
            'email'       => 'contact@monecole.bj',
            'period_type' => 'trimestre',
            'status'      => 1,
            'is_delete'   => 0,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        $this->command->info('  ✅ Settings (id=1) créé.');
    }
}
