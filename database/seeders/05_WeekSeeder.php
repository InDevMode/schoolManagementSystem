<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * WeekSeeder — Insère les 6 jours ouvrables de la semaine.
 *
 * Dépendances : aucune
 */
class WeekSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('week')->count() > 0) {
            $this->command->info('  ⏭  Jours de semaine déjà présents — ignoré.');
            return;
        }

        $days = [
            ['name' => 'Lundi',    'day' => 1],
            ['name' => 'Mardi',    'day' => 2],
            ['name' => 'Mercredi', 'day' => 3],
            ['name' => 'Jeudi',    'day' => 4],
            ['name' => 'Vendredi', 'day' => 5],
            ['name' => 'Samedi',   'day' => 6],
        ];

        foreach ($days as $day) {
            DB::table('week')->insert([
                'name'       => $day['name'],
                'day'        => $day['day'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('  ✅ 6 jours de semaine insérés.');
    }
}
