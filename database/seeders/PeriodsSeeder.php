<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * PeriodsSeeder — 3 trimestres 2025-2026 par école + exams liés.
 * UUIDs fixes pour référence croisée dans EvaluationsSeeder.
 */
class PeriodsSeeder extends Seeder
{
    // ── UUIDs fixes par école + trimestre ────────────────────────────────────
    // LMC
    public const LMC_T1 = 'de000001-ee01-4000-8000-000000000001';
    public const LMC_T2 = 'de000001-ee02-4000-8000-000000000002';
    public const LMC_T3 = 'de000001-ee03-4000-8000-000000000003';
    // CSM
    public const CSM_T1 = 'de000001-ee04-4000-8000-000000000004';
    public const CSM_T2 = 'de000001-ee05-4000-8000-000000000005';
    public const CSM_T3 = 'de000001-ee06-4000-8000-000000000006';
    // EPE
    public const EPE_T1 = 'de000001-ee07-4000-8000-000000000007';
    public const EPE_T2 = 'de000001-ee08-4000-8000-000000000008';
    public const EPE_T3 = 'de000001-ee09-4000-8000-000000000009';

    // UUIDs fixes pour les exams
    public const LMC_EXAM1 = 'ea000001-ff01-4000-8000-000000000001';
    public const LMC_EXAM2 = 'ea000001-ff02-4000-8000-000000000002';
    public const CSM_EXAM1 = 'ea000001-ff03-4000-8000-000000000003';
    public const CSM_EXAM2 = 'ea000001-ff04-4000-8000-000000000004';
    public const EPE_EXAM1 = 'ea000001-ff05-4000-8000-000000000005';
    public const EPE_EXAM2 = 'ea000001-ff06-4000-8000-000000000006';

    private array $schoolPeriods = [
        SchoolSeeder::LMC_ID => [
            ['id' => self::LMC_T1, 'name' => '1er Trimestre',  'order' => 1, 'is_current' => 0, 'status' => 1, 'start' => '2025-09-01', 'end' => '2025-12-20'],
            ['id' => self::LMC_T2, 'name' => '2ème Trimestre', 'order' => 2, 'is_current' => 1, 'status' => 1, 'start' => '2026-01-06', 'end' => '2026-03-28'],
            ['id' => self::LMC_T3, 'name' => '3ème Trimestre', 'order' => 3, 'is_current' => 0, 'status' => 0, 'start' => '2026-04-07', 'end' => '2026-06-30'],
        ],
        SchoolSeeder::CSM_ID => [
            ['id' => self::CSM_T1, 'name' => '1er Trimestre',  'order' => 1, 'is_current' => 0, 'status' => 1, 'start' => '2025-09-01', 'end' => '2025-12-20'],
            ['id' => self::CSM_T2, 'name' => '2ème Trimestre', 'order' => 2, 'is_current' => 1, 'status' => 1, 'start' => '2026-01-06', 'end' => '2026-03-28'],
            ['id' => self::CSM_T3, 'name' => '3ème Trimestre', 'order' => 3, 'is_current' => 0, 'status' => 0, 'start' => '2026-04-07', 'end' => '2026-06-30'],
        ],
        SchoolSeeder::EPE_ID => [
            ['id' => self::EPE_T1, 'name' => '1er Trimestre',  'order' => 1, 'is_current' => 0, 'status' => 1, 'start' => '2025-09-01', 'end' => '2025-12-20'],
            ['id' => self::EPE_T2, 'name' => '2ème Trimestre', 'order' => 2, 'is_current' => 1, 'status' => 1, 'start' => '2026-01-06', 'end' => '2026-03-28'],
            ['id' => self::EPE_T3, 'name' => '3ème Trimestre', 'order' => 3, 'is_current' => 0, 'status' => 0, 'start' => '2026-04-07', 'end' => '2026-06-30'],
        ],
    ];

    private array $schoolExams = [
        SchoolSeeder::LMC_ID => [
            ['id' => self::LMC_EXAM1, 'period_id' => self::LMC_T1, 'name' => 'Examens 1er Trimestre LMC',  'start' => '2025-12-10', 'end' => '2025-12-20', 'status' => 'completed'],
            ['id' => self::LMC_EXAM2, 'period_id' => self::LMC_T2, 'name' => 'Examens 2ème Trimestre LMC', 'start' => '2026-03-20', 'end' => '2026-03-28', 'status' => 'in_progress'],
        ],
        SchoolSeeder::CSM_ID => [
            ['id' => self::CSM_EXAM1, 'period_id' => self::CSM_T1, 'name' => 'Examens 1er Trimestre CSM',  'start' => '2025-12-10', 'end' => '2025-12-20', 'status' => 'completed'],
            ['id' => self::CSM_EXAM2, 'period_id' => self::CSM_T2, 'name' => 'Examens 2ème Trimestre CSM', 'start' => '2026-03-20', 'end' => '2026-03-28', 'status' => 'in_progress'],
        ],
        SchoolSeeder::EPE_ID => [
            ['id' => self::EPE_EXAM1, 'period_id' => self::EPE_T1, 'name' => 'Examens 1er Trimestre EPE',  'start' => '2025-12-10', 'end' => '2025-12-20', 'status' => 'completed'],
            ['id' => self::EPE_EXAM2, 'period_id' => self::EPE_T2, 'name' => 'Examens 2ème Trimestre EPE', 'start' => '2026-03-20', 'end' => '2026-03-28', 'status' => 'in_progress'],
        ],
    ];

    public function run(): void
    {
        $settings  = DB::table('settings')->orderBy('id')->first();
        $settingsId = $settings?->id ?? 1;

        $superAdmin = DB::table('users')->where('user_type', 0)->orderBy('created_at')->first();
        $createdBy  = $superAdmin?->id ?? SuperAdminSeeder::SUPER_ADMIN_ID;

        $created = 0;

        foreach ($this->schoolPeriods as $schoolId => $periods) {
            $school = DB::table('schools')->where('id', $schoolId)->first();
            $adminUser = DB::table('users')->where('school_id', $schoolId)->where('user_type', 1)->first();
            $adminId = $adminUser?->id ?? $createdBy;

            foreach ($periods as $p) {
                if (!DB::table('periods')->where('id', $p['id'])->exists()) {
                    DB::table('periods')->insert([
                        'id'           => $p['id'],
                        'settings_id'  => $settingsId,
                        'school_id'    => $schoolId,
                        'name'         => $p['name'],
                        'type'         => 'trimestre',
                        'order_number' => $p['order'],
                        'school_year'  => '2025-2026',
                        'start_date'   => $p['start'],
                        'end_date'     => $p['end'],
                        'is_current'   => $p['is_current'],
                        'status'       => $p['status'],
                        'is_delete'    => 0,
                        'created_by'   => $adminId,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);
                    $created++;
                }
            }

            // Exams pour cette école
            foreach ($this->schoolExams[$schoolId] as $e) {
                if (!DB::table('exams')->where('id', $e['id'])->exists()) {
                    DB::table('exams')->insert([
                        'id'         => $e['id'],
                        'name'       => $e['name'],
                        'period_id'  => $e['period_id'],
                        'start_date' => $e['start'],
                        'end_date'   => $e['end'],
                        'status'     => $e['status'],
                        'is_delete'  => 0,
                        'created_by' => $adminId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Schedules — planning des salles d'examen (table schedules)
        $this->seedSchedules();

        $this->command->info("  ✅ $created périodes créées (3 écoles × 3 trimestres) + exams + schedules.");
    }

    private function seedSchedules(): void
    {
        $examSubjectPairs = [
            ['exam_id' => self::LMC_EXAM1, 'class_id' => MultiSchoolSeeder::LMC_CLASS1_ID, 'subject_id' => MultiSchoolSeeder::SUBJ_MATHS_ID,   'date' => '2025-12-10', 'start' => '08:00', 'end' => '10:00', 'room' => 'Salle A101', 'full_marks' => '20', 'passing_marks' => '10'],
            ['exam_id' => self::LMC_EXAM1, 'class_id' => MultiSchoolSeeder::LMC_CLASS1_ID, 'subject_id' => MultiSchoolSeeder::SUBJ_FRENCH_ID,   'date' => '2025-12-11', 'start' => '08:00', 'end' => '10:00', 'room' => 'Salle A101', 'full_marks' => '20', 'passing_marks' => '10'],
            ['exam_id' => self::LMC_EXAM1, 'class_id' => MultiSchoolSeeder::LMC_CLASS2_ID, 'subject_id' => MultiSchoolSeeder::SUBJ_PHYS_ID,     'date' => '2025-12-12', 'start' => '10:30', 'end' => '12:30', 'room' => 'Salle B201', 'full_marks' => '20', 'passing_marks' => '10'],
            ['exam_id' => self::LMC_EXAM2, 'class_id' => MultiSchoolSeeder::LMC_CLASS1_ID, 'subject_id' => MultiSchoolSeeder::SUBJ_SVT_ID,      'date' => '2026-03-20', 'start' => '08:00', 'end' => '10:00', 'room' => 'Salle A102', 'full_marks' => '20', 'passing_marks' => '10'],
            ['exam_id' => self::CSM_EXAM1, 'class_id' => MultiSchoolSeeder::CSM_CLASS1_ID, 'subject_id' => MultiSchoolSeeder::SUBJ_MATHS_ID,   'date' => '2025-12-10', 'start' => '08:00', 'end' => '10:00', 'room' => 'Salle C101', 'full_marks' => '20', 'passing_marks' => '10'],
            ['exam_id' => self::CSM_EXAM1, 'class_id' => MultiSchoolSeeder::CSM_CLASS2_ID, 'subject_id' => MultiSchoolSeeder::SUBJ_ENGLISH_ID,  'date' => '2025-12-11', 'start' => '10:30', 'end' => '12:30', 'room' => 'Salle C102', 'full_marks' => '20', 'passing_marks' => '10'],
            ['exam_id' => self::EPE_EXAM1, 'class_id' => MultiSchoolSeeder::EPE_CLASS1_ID, 'subject_id' => MultiSchoolSeeder::SUBJ_CALCUL_ID,  'date' => '2025-12-10', 'start' => '08:00', 'end' => '09:30', 'room' => 'Salle P101', 'full_marks' => '20', 'passing_marks' => '10'],
            ['exam_id' => self::EPE_EXAM1, 'class_id' => MultiSchoolSeeder::EPE_CLASS2_ID, 'subject_id' => MultiSchoolSeeder::SUBJ_LECTURE_ID,  'date' => '2025-12-11', 'start' => '09:45', 'end' => '11:15', 'room' => 'Salle P102', 'full_marks' => '20', 'passing_marks' => '10'],
        ];

        foreach ($examSubjectPairs as $s) {
            $exists = DB::table('schedules')
                ->where('exam_id', $s['exam_id'])
                ->where('class_id', $s['class_id'])
                ->where('subject_id', $s['subject_id'])
                ->exists();
            if (!$exists) {
                DB::table('schedules')->insert([
                    'id'            => (string) Str::uuid(),
                    'exam_id'       => $s['exam_id'],
                    'class_id'      => $s['class_id'],
                    'subject_id'    => $s['subject_id'],
                    'exam_date'     => $s['date'],
                    'start_time'    => $s['start'],
                    'end_time'      => $s['end'],
                    'room_number'   => $s['room'],
                    'full_marks'    => $s['full_marks'],
                    'passing_marks' => $s['passing_marks'],
                    'is_delete'     => 0,
                    'created_by'    => MultiSchoolSeeder::LMC_ADMIN_ID,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        }
    }
}
