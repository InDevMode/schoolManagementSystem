<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LeaveTypesSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Congé annuel',       'description' => 'Congé annuel payé légal',                 'color' => '#10b981'],
            ['name' => 'Congé maladie',       'description' => 'Arrêt maladie avec certificat médical',   'color' => '#ef4444'],
            ['name' => 'Congé maternité',     'description' => 'Congé maternité / paternité',             'color' => '#8b5cf6'],
            ['name' => 'Congé disciplinaire', 'description' => 'Suspension disciplinaire',                'color' => '#f59e0b'],
            ['name' => 'Absence autorisée',   'description' => 'Absence exceptionnelle avec autorisation','color' => '#3b82f6'],
            ['name' => 'Congé sans solde',    'description' => 'Absence non rémunérée',                   'color' => '#6b7280'],
            ['name' => 'Congé exceptionnel',  'description' => 'Événement familial (mariage, décès...)',  'color' => '#f97316'],
        ];

        foreach ($types as $type) {
            if (!DB::table('leave_types')->where('name', $type['name'])->where('is_delete', 0)->exists()) {
                DB::table('leave_types')->insert([
                    'id'          => (string) Str::uuid(),
                    'school_id'   => SchoolSeeder::LMC_ID,
                    'name'        => $type['name'],
                    'description' => $type['description'],
                    'color'       => $type['color'],
                    'is_delete'   => 0,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        }

        $this->command->info('  ✅ 7 types de congés insérés.');
    }
}
