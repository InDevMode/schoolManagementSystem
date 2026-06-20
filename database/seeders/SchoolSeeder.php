<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * SchoolSeeder — Insère 3 écoles de démonstration.
 *
 * Ces écoles sont utilisées par MultiSchoolSeeder pour rattacher
 * les utilisateurs, classes et matières de chaque établissement.
 */
class SchoolSeeder extends Seeder
{
    public function run(): void
    {
        $schools = [
            [
                'school_name'  => 'Lycée Moderne de Cotonou',
                'school_type'  => 'Lycée',
                'school_code'  => 'lmc-cotonou',
                'address'      => 'Avenue Jean-Paul II, Cotonou, Bénin',
                'phone'        => '0022997000001',
                'email'        => 'contact@lmc.bj',
                'uai_number'   => 'BJ-LMC-001',
                'academic_year'=> '2025-2026',
                'period_type'  => 'trimestre',
                'status'       => 1,
                'is_delete'    => 0,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'school_name'  => 'Collège Saint-Michel',
                'school_type'  => 'Collège',
                'school_code'  => 'csm-abomey',
                'address'      => 'Rue du Commerce, Abomey-Calavi, Bénin',
                'phone'        => '0022997000002',
                'email'        => 'contact@csm.bj',
                'uai_number'   => 'BJ-CSM-002',
                'academic_year'=> '2025-2026',
                'period_type'  => 'trimestre',
                'status'       => 1,
                'is_delete'    => 0,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'school_name'  => 'École Primaire Les Étoiles',
                'school_type'  => 'École Primaire',
                'school_code'  => 'epe-parakou',
                'address'      => 'Quartier Zongo, Parakou, Bénin',
                'phone'        => '0022997000003',
                'email'        => 'contact@epe.bj',
                'uai_number'   => 'BJ-EPE-003',
                'academic_year'=> '2025-2026',
                'period_type'  => 'trimestre',
                'status'       => 1,
                'is_delete'    => 0,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ];

        foreach ($schools as $school) {
            $exists = DB::table('schools')->where('school_code', $school['school_code'])->exists();
            if (! $exists) {
                DB::table('schools')->insert($school);
                $this->command->info("  ✅ École créée : {$school['school_name']}");
            } else {
                $this->command->info("  ⏭  École déjà présente : {$school['school_name']}");
            }
        }
    }
}
