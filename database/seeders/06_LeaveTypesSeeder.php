<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * LeaveTypesSeeder — Insère les types de congés par défaut.
 *
 * Dépendances : aucune (school_id nullable)
 */
class LeaveTypesSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Congé annuel',        'description' => 'Congé annuel payé',                           'color' => '#10b981'],
            ['name' => 'Congé maladie',        'description' => 'Arrêt maladie avec justificatif médical',     'color' => '#ef4444'],
            ['name' => 'Congé maternité',      'description' => 'Congé pour naissance ou adoption',            'color' => '#8b5cf6'],
            ['name' => 'Congé disciplinaire',  'description' => 'Suspension disciplinaire',                    'color' => '#f59e0b'],
            ['name' => 'Absence autorisée',    'description' => 'Absence exceptionnelle autorisée',            'color' => '#3b82f6'],
            ['name' => 'Congé sans solde',     'description' => 'Absence sans traitement',                     'color' => '#6b7280'],
            ['name' => 'Congé exceptionnel',   'description' => 'Événement familial exceptionnel',             'color' => '#f97316'],
        ];

        foreach ($types as $type) {
            $exists = DB::table('leave_types')
                ->where('name', $type['name'])
                ->where('is_delete', 0)
                ->exists();

            if (! $exists) {
                DB::table('leave_types')->insert([
                    'school_id'   => null,
                    'name'        => $type['name'],
                    'description' => $type['description'],
                    'color'       => $type['color'],
                    'is_delete'   => 0,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        }

        $this->command->info('  ✅ ' . count($types) . ' types de congés insérés/vérifiés.');
    }
}
