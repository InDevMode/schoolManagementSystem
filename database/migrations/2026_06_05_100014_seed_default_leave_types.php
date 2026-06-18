<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Insère les types de congés par défaut s'ils n'existent pas encore.
 */
return new class extends Migration {
    public function up(): void
    {
        $defaults = [
            ['name' => 'Congé annuel',     'description' => 'Congé annuel payé',                      'color' => '#10b981'],
            ['name' => 'Congé maladie',    'description' => 'Arrêt maladie avec justificatif médical', 'color' => '#ef4444'],
            ['name' => 'Congé maternité',  'description' => 'Congé pour naissance',                   'color' => '#8b5cf6'],
            ['name' => 'Congé disciplinaire', 'description' => 'Suspension disciplinaire',            'color' => '#f59e0b'],
            ['name' => 'Absence autorisée','description' => 'Absence exceptionnelle autorisée',       'color' => '#3b82f6'],
        ];

        foreach ($defaults as $lt) {
            $exists = DB::table('leave_types')->where('name', $lt['name'])->first();
            if (!$exists) {
                DB::table('leave_types')->insert([
                    ...$lt,
                    'is_delete'  => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('leave_types')->whereIn('name', [
            'Congé annuel', 'Congé maladie', 'Congé maternité',
            'Congé disciplinaire', 'Absence autorisée',
        ])->delete();
    }
};
