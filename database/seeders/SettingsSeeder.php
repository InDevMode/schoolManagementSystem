<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('settings')->exists()) {
            $this->command->info('  ⏭  Settings déjà présent.');
            return;
        }

        DB::table('settings')->insert([
            'school_name'      => 'School Management System',
            'school_type'      => 'Multi-établissements',
            'address'          => 'Avenue Jean-Paul II, Cotonou, Bénin',
            'phone'            => '+229 97 00 00 00',
            'email'            => 'contact@sms.bj',
            'uai_number'       => 'BJ-SMS-000',
            'period_type'      => 'trimestre',
            'auth_bg_type'     => 'gradient',
            'auth_bg_value'    => 'linear-gradient(145deg, #5b21b6 0%, #7c3aed 50%, #6d28d9 100%)',
            'auth_bg_label'    => 'School Management System',
            'auth_bg_overlay'  => 'rgba(0,0,0,0.35)',
            'logo'             => 'logo.png',
            'favicon'          => 'favicon.png',
            'status'           => 1,
            'is_delete'        => 0,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        $this->command->info('  ✅ Settings (id=1) créé.');
    }
}
