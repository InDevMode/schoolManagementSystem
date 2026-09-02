<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WeekSeeder extends Seeder
{
    public const WEEK_IDS = [
        1 => 'b0000001-0001-4000-8000-000000000001',
        2 => 'b0000001-0002-4000-8000-000000000002',
        3 => 'b0000001-0003-4000-8000-000000000003',
        4 => 'b0000001-0004-4000-8000-000000000004',
        5 => 'b0000001-0005-4000-8000-000000000005',
        6 => 'b0000001-0006-4000-8000-000000000006',
    ];

    public function run(): void
    {
        if (DB::table('week')->count() > 0) {
            $this->command->info('  ⏭  Jours déjà présents.');
            return;
        }

        $days = [
            [1, 'Lundi'],
            [2, 'Mardi'],
            [3, 'Mercredi'],
            [4, 'Jeudi'],
            [5, 'Vendredi'],
            [6, 'Samedi'],
        ];

        foreach ($days as [$num, $name]) {
            DB::table('week')->insert([
                'id'         => self::WEEK_IDS[$num],
                'name'       => $name,
                'day'        => $num,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('  ✅ 6 jours de semaine insérés.');
    }
}
