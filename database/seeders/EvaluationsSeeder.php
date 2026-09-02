<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * EvaluationsSeeder — Évaluations, notes, bulletins complets.
 * Toutes les colonnes nullable renseignées.
 * Max ~20 évaluations, ~20 notes, ~20 bulletins.
 */
class EvaluationsSeeder extends Seeder
{
    // ── UUIDs fixes des évaluations ──────────────────────────────────────────
    // LMC — Classe 1 (3ème A)
    public const E_LMC_C1_T1_MATHS_INT  = 'eb000001-e001-4000-8000-000000000001';
    public const E_LMC_C1_T1_MATHS_DS   = 'eb000001-e002-4000-8000-000000000002';
    public const E_LMC_C1_T1_FRENCH_INT = 'eb000001-e003-4000-8000-000000000003';
    public const E_LMC_C1_T1_FRENCH_DS  = 'eb000001-e004-4000-8000-000000000004';
    public const E_LMC_C1_T2_MATHS_INT  = 'eb000001-e005-4000-8000-000000000005';
    public const E_LMC_C1_T2_FRENCH_INT = 'eb000001-e006-4000-8000-000000000006';
    // LMC — Classe 2 (2nde B)
    public const E_LMC_C2_T1_MATHS_INT  = 'eb000001-e007-4000-8000-000000000007';
    public const E_LMC_C2_T1_PHYS_DS    = 'eb000001-e008-4000-8000-000000000008';
    // CSM — Classe 1 (5ème C)
    public const E_CSM_C1_T1_MATHS_INT  = 'eb000001-e009-4000-8000-000000000009';
    public const E_CSM_C1_T1_FRENCH_DS  = 'eb000001-e010-4000-8000-000000000010';
    public const E_CSM_C1_T2_MATHS_INT  = 'eb000001-e011-4000-8000-000000000011';
    // CSM — Classe 2 (4ème D)
    public const E_CSM_C2_T1_ENG_INT    = 'eb000001-e012-4000-8000-000000000012';
    // EPE — Classe 1 (CE2)
    public const E_EPE_C1_T1_CALC_INT   = 'eb000001-e013-4000-8000-000000000013';
    public const E_EPE_C1_T1_LECT_DS    = 'eb000001-e014-4000-8000-000000000014';
    // EPE — Classe 2 (CM1)
    public const E_EPE_C2_T1_CALC_INT   = 'eb000001-e015-4000-8000-000000000015';
    public const E_EPE_C2_T1_FRENCH_DS  = 'eb000001-e016-4000-8000-000000000016';

    public function run(): void
    {
        $this->command->info('  🌱 Évaluations, notes, bulletins...');

        $this->seedEvaluations();
        $this->seedGrades();
        $this->seedMarksGrade();
        $this->seedBulletins();

        $this->command->info('  ✅ Évaluations + notes + bulletins créés.');
    }

    // ── Définition des évaluations ────────────────────────────────────────────

    private function evaluationDefs(): array
    {
        return [
            // ── LMC Classe 1 ─────────────────────────────────────────────────
            [
                'id' => self::E_LMC_C1_T1_MATHS_INT,
                'school_id' => SchoolSeeder::LMC_ID,
                'exam_id'   => PeriodsSeeder::LMC_EXAM1,
                'class_id'  => MultiSchoolSeeder::LMC_CLASS1_ID,
                'subject_id'=> MultiSchoolSeeder::SUBJ_MATHS_ID,
                'teacher_id'=> MultiSchoolSeeder::LMC_PROF1_ID,
                'period_id' => PeriodsSeeder::LMC_T1,
                'created_by'=> MultiSchoolSeeder::LMC_PROF1_ID,
                'type' => 'interrogation', 'coefficient' => 1, 'max_score' => 20.00,
                'eval_date' => '2025-10-15', 'title' => 'Interrogation Maths Oct — 3ème A',
                'status' => 'validated',
            ],
            [
                'id' => self::E_LMC_C1_T1_MATHS_DS,
                'school_id' => SchoolSeeder::LMC_ID,
                'exam_id'   => PeriodsSeeder::LMC_EXAM1,
                'class_id'  => MultiSchoolSeeder::LMC_CLASS1_ID,
                'subject_id'=> MultiSchoolSeeder::SUBJ_MATHS_ID,
                'teacher_id'=> MultiSchoolSeeder::LMC_PROF1_ID,
                'period_id' => PeriodsSeeder::LMC_T1,
                'created_by'=> MultiSchoolSeeder::LMC_PROF1_ID,
                'type' => 'devoir_surveille', 'coefficient' => 2, 'max_score' => 20.00,
                'eval_date' => '2025-11-20', 'title' => 'Devoir Surveillé Maths Nov — 3ème A',
                'status' => 'validated',
            ],
            [
                'id' => self::E_LMC_C1_T1_FRENCH_INT,
                'school_id' => SchoolSeeder::LMC_ID,
                'exam_id'   => PeriodsSeeder::LMC_EXAM1,
                'class_id'  => MultiSchoolSeeder::LMC_CLASS1_ID,
                'subject_id'=> MultiSchoolSeeder::SUBJ_FRENCH_ID,
                'teacher_id'=> MultiSchoolSeeder::LMC_PROF2_ID,
                'period_id' => PeriodsSeeder::LMC_T1,
                'created_by'=> MultiSchoolSeeder::LMC_PROF2_ID,
                'type' => 'interrogation', 'coefficient' => 1, 'max_score' => 20.00,
                'eval_date' => '2025-10-20', 'title' => 'Interrogation Français Oct — 3ème A',
                'status' => 'validated',
            ],
            [
                'id' => self::E_LMC_C1_T1_FRENCH_DS,
                'school_id' => SchoolSeeder::LMC_ID,
                'exam_id'   => PeriodsSeeder::LMC_EXAM1,
                'class_id'  => MultiSchoolSeeder::LMC_CLASS1_ID,
                'subject_id'=> MultiSchoolSeeder::SUBJ_FRENCH_ID,
                'teacher_id'=> MultiSchoolSeeder::LMC_PROF2_ID,
                'period_id' => PeriodsSeeder::LMC_T1,
                'created_by'=> MultiSchoolSeeder::LMC_PROF2_ID,
                'type' => 'devoir_surveille', 'coefficient' => 2, 'max_score' => 20.00,
                'eval_date' => '2025-11-25', 'title' => 'Devoir Surveillé Français Nov — 3ème A',
                'status' => 'validated',
            ],
            [
                'id' => self::E_LMC_C1_T2_MATHS_INT,
                'school_id' => SchoolSeeder::LMC_ID,
                'exam_id'   => PeriodsSeeder::LMC_EXAM2,
                'class_id'  => MultiSchoolSeeder::LMC_CLASS1_ID,
                'subject_id'=> MultiSchoolSeeder::SUBJ_MATHS_ID,
                'teacher_id'=> MultiSchoolSeeder::LMC_PROF1_ID,
                'period_id' => PeriodsSeeder::LMC_T2,
                'created_by'=> MultiSchoolSeeder::LMC_PROF1_ID,
                'type' => 'interrogation', 'coefficient' => 1, 'max_score' => 20.00,
                'eval_date' => '2026-02-10', 'title' => 'Interrogation Maths Fév — 3ème A',
                'status' => 'closed',
            ],
            [
                'id' => self::E_LMC_C1_T2_FRENCH_INT,
                'school_id' => SchoolSeeder::LMC_ID,
                'exam_id'   => PeriodsSeeder::LMC_EXAM2,
                'class_id'  => MultiSchoolSeeder::LMC_CLASS1_ID,
                'subject_id'=> MultiSchoolSeeder::SUBJ_FRENCH_ID,
                'teacher_id'=> MultiSchoolSeeder::LMC_PROF2_ID,
                'period_id' => PeriodsSeeder::LMC_T2,
                'created_by'=> MultiSchoolSeeder::LMC_PROF2_ID,
                'type' => 'interrogation', 'coefficient' => 1, 'max_score' => 20.00,
                'eval_date' => '2026-02-15', 'title' => 'Interrogation Français Fév — 3ème A',
                'status' => 'open',
            ],
            // ── LMC Classe 2 ─────────────────────────────────────────────────
            [
                'id' => self::E_LMC_C2_T1_MATHS_INT,
                'school_id' => SchoolSeeder::LMC_ID,
                'exam_id'   => PeriodsSeeder::LMC_EXAM1,
                'class_id'  => MultiSchoolSeeder::LMC_CLASS2_ID,
                'subject_id'=> MultiSchoolSeeder::SUBJ_MATHS_ID,
                'teacher_id'=> MultiSchoolSeeder::LMC_PROF1_ID,
                'period_id' => PeriodsSeeder::LMC_T1,
                'created_by'=> MultiSchoolSeeder::LMC_PROF1_ID,
                'type' => 'interrogation', 'coefficient' => 1, 'max_score' => 20.00,
                'eval_date' => '2025-10-16', 'title' => 'Interrogation Maths Oct — 2nde B',
                'status' => 'validated',
            ],
            [
                'id' => self::E_LMC_C2_T1_PHYS_DS,
                'school_id' => SchoolSeeder::LMC_ID,
                'exam_id'   => PeriodsSeeder::LMC_EXAM1,
                'class_id'  => MultiSchoolSeeder::LMC_CLASS2_ID,
                'subject_id'=> MultiSchoolSeeder::SUBJ_PHYS_ID,
                'teacher_id'=> MultiSchoolSeeder::LMC_PROF1_ID,
                'period_id' => PeriodsSeeder::LMC_T1,
                'created_by'=> MultiSchoolSeeder::LMC_PROF1_ID,
                'type' => 'devoir_surveille', 'coefficient' => 2, 'max_score' => 20.00,
                'eval_date' => '2025-11-18', 'title' => 'DS Physique-Chimie Nov — 2nde B',
                'status' => 'validated',
            ],
            // ── CSM Classe 1 ─────────────────────────────────────────────────
            [
                'id' => self::E_CSM_C1_T1_MATHS_INT,
                'school_id' => SchoolSeeder::CSM_ID,
                'exam_id'   => PeriodsSeeder::CSM_EXAM1,
                'class_id'  => MultiSchoolSeeder::CSM_CLASS1_ID,
                'subject_id'=> MultiSchoolSeeder::SUBJ_MATHS_ID,
                'teacher_id'=> MultiSchoolSeeder::CSM_PROF1_ID,
                'period_id' => PeriodsSeeder::CSM_T1,
                'created_by'=> MultiSchoolSeeder::CSM_PROF1_ID,
                'type' => 'interrogation', 'coefficient' => 1, 'max_score' => 20.00,
                'eval_date' => '2025-10-14', 'title' => 'Interrogation Maths Oct — 5ème C',
                'status' => 'validated',
            ],
            [
                'id' => self::E_CSM_C1_T1_FRENCH_DS,
                'school_id' => SchoolSeeder::CSM_ID,
                'exam_id'   => PeriodsSeeder::CSM_EXAM1,
                'class_id'  => MultiSchoolSeeder::CSM_CLASS1_ID,
                'subject_id'=> MultiSchoolSeeder::SUBJ_FRENCH_ID,
                'teacher_id'=> MultiSchoolSeeder::CSM_PROF2_ID,
                'period_id' => PeriodsSeeder::CSM_T1,
                'created_by'=> MultiSchoolSeeder::CSM_PROF2_ID,
                'type' => 'devoir_surveille', 'coefficient' => 2, 'max_score' => 20.00,
                'eval_date' => '2025-11-19', 'title' => 'DS Français Nov — 5ème C',
                'status' => 'validated',
            ],
            [
                'id' => self::E_CSM_C1_T2_MATHS_INT,
                'school_id' => SchoolSeeder::CSM_ID,
                'exam_id'   => PeriodsSeeder::CSM_EXAM2,
                'class_id'  => MultiSchoolSeeder::CSM_CLASS1_ID,
                'subject_id'=> MultiSchoolSeeder::SUBJ_MATHS_ID,
                'teacher_id'=> MultiSchoolSeeder::CSM_PROF1_ID,
                'period_id' => PeriodsSeeder::CSM_T2,
                'created_by'=> MultiSchoolSeeder::CSM_PROF1_ID,
                'type' => 'interrogation', 'coefficient' => 1, 'max_score' => 20.00,
                'eval_date' => '2026-02-12', 'title' => 'Interrogation Maths Fév — 5ème C',
                'status' => 'open',
            ],
            // ── CSM Classe 2 ─────────────────────────────────────────────────
            [
                'id' => self::E_CSM_C2_T1_ENG_INT,
                'school_id' => SchoolSeeder::CSM_ID,
                'exam_id'   => PeriodsSeeder::CSM_EXAM1,
                'class_id'  => MultiSchoolSeeder::CSM_CLASS2_ID,
                'subject_id'=> MultiSchoolSeeder::SUBJ_ENGLISH_ID,
                'teacher_id'=> MultiSchoolSeeder::CSM_PROF2_ID,
                'period_id' => PeriodsSeeder::CSM_T1,
                'created_by'=> MultiSchoolSeeder::CSM_PROF2_ID,
                'type' => 'interrogation', 'coefficient' => 1, 'max_score' => 20.00,
                'eval_date' => '2025-10-22', 'title' => 'Interrogation Anglais Oct — 4ème D',
                'status' => 'validated',
            ],
            // ── EPE Classe 1 ─────────────────────────────────────────────────
            [
                'id' => self::E_EPE_C1_T1_CALC_INT,
                'school_id' => SchoolSeeder::EPE_ID,
                'exam_id'   => PeriodsSeeder::EPE_EXAM1,
                'class_id'  => MultiSchoolSeeder::EPE_CLASS1_ID,
                'subject_id'=> MultiSchoolSeeder::SUBJ_CALCUL_ID,
                'teacher_id'=> MultiSchoolSeeder::EPE_PROF1_ID,
                'period_id' => PeriodsSeeder::EPE_T1,
                'created_by'=> MultiSchoolSeeder::EPE_PROF1_ID,
                'type' => 'interrogation', 'coefficient' => 1, 'max_score' => 20.00,
                'eval_date' => '2025-10-10', 'title' => 'Interrogation Calcul Oct — CE2',
                'status' => 'validated',
            ],
            [
                'id' => self::E_EPE_C1_T1_LECT_DS,
                'school_id' => SchoolSeeder::EPE_ID,
                'exam_id'   => PeriodsSeeder::EPE_EXAM1,
                'class_id'  => MultiSchoolSeeder::EPE_CLASS1_ID,
                'subject_id'=> MultiSchoolSeeder::SUBJ_LECTURE_ID,
                'teacher_id'=> MultiSchoolSeeder::EPE_PROF1_ID,
                'period_id' => PeriodsSeeder::EPE_T1,
                'created_by'=> MultiSchoolSeeder::EPE_PROF1_ID,
                'type' => 'devoir_surveille', 'coefficient' => 2, 'max_score' => 20.00,
                'eval_date' => '2025-11-14', 'title' => 'DS Lecture Nov — CE2',
                'status' => 'validated',
            ],
            // ── EPE Classe 2 ─────────────────────────────────────────────────
            [
                'id' => self::E_EPE_C2_T1_CALC_INT,
                'school_id' => SchoolSeeder::EPE_ID,
                'exam_id'   => PeriodsSeeder::EPE_EXAM1,
                'class_id'  => MultiSchoolSeeder::EPE_CLASS2_ID,
                'subject_id'=> MultiSchoolSeeder::SUBJ_CALCUL_ID,
                'teacher_id'=> MultiSchoolSeeder::EPE_PROF2_ID,
                'period_id' => PeriodsSeeder::EPE_T1,
                'created_by'=> MultiSchoolSeeder::EPE_PROF2_ID,
                'type' => 'interrogation', 'coefficient' => 1, 'max_score' => 20.00,
                'eval_date' => '2025-10-13', 'title' => 'Interrogation Calcul Oct — CM1',
                'status' => 'validated',
            ],
            [
                'id' => self::E_EPE_C2_T1_FRENCH_DS,
                'school_id' => SchoolSeeder::EPE_ID,
                'exam_id'   => PeriodsSeeder::EPE_EXAM1,
                'class_id'  => MultiSchoolSeeder::EPE_CLASS2_ID,
                'subject_id'=> MultiSchoolSeeder::SUBJ_FRENCH_ID,
                'teacher_id'=> MultiSchoolSeeder::EPE_PROF2_ID,
                'period_id' => PeriodsSeeder::EPE_T1,
                'created_by'=> MultiSchoolSeeder::EPE_PROF2_ID,
                'type' => 'travail_maison', 'coefficient' => 1, 'max_score' => 20.00,
                'eval_date' => '2025-11-05', 'title' => 'Travail Maison Français Nov — CM1',
                'status' => 'validated',
            ],
        ];
    }

    // ── Insérer les évaluations ───────────────────────────────────────────────

    private function seedEvaluations(): void
    {
        foreach ($this->evaluationDefs() as $e) {
            if (!DB::table('evaluations')->where('id', $e['id'])->exists()) {
                DB::table('evaluations')->insert([
                    'id'          => $e['id'],
                    'school_id'   => $e['school_id'],
                    'exam_id'     => $e['exam_id'],
                    'class_id'    => $e['class_id'],
                    'subject_id'  => $e['subject_id'],
                    'teacher_id'  => $e['teacher_id'],
                    'period_id'   => $e['period_id'],
                    'type'        => $e['type'],
                    'coefficient' => $e['coefficient'],
                    'max_score'   => $e['max_score'],
                    'eval_date'   => $e['eval_date'],
                    'title'       => $e['title'],
                    'status'      => $e['status'],
                    'is_delete'   => 0,
                    'created_by'  => $e['created_by'],
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        }
        $count = count($this->evaluationDefs());
        $this->command->info("    ✅ $count évaluations insérées.");
    }

    // ── Notes (grades) ────────────────────────────────────────────────────────

    private function seedGrades(): void
    {
        // Map eval_id → liste de [student_id, score, observation]
        $gradeMap = $this->buildGradeMap();

        $count = 0;
        foreach ($gradeMap as $evalId => $entries) {
            $eval = DB::table('evaluations')->where('id', $evalId)->first();
            if (!$eval) continue;

            $validated = $eval->status === 'validated';
            $validatedBy = $validated ? $eval->teacher_id : null;
            $validatedAt = $validated ? Carbon::parse($eval->eval_date)->addDays(5)->toDateTimeString() : null;

            foreach ($entries as $entry) {
                if (!DB::table('grades')->where('evaluation_id', $evalId)->where('student_id', $entry['student_id'])->exists()) {
                    DB::table('grades')->insert([
                        'id'            => (string) Str::uuid(),
                        'school_id'     => $eval->school_id,
                        'student_id'    => $entry['student_id'],
                        'evaluation_id' => $evalId,
                        'score'         => $entry['score'],
                        'teacher_id'    => $eval->teacher_id,
                        'validated'     => $validated,
                        'validated_by'  => $validatedBy,
                        'validated_at'  => $validatedAt,
                        'observation'   => $entry['observation'],
                        'is_delete'     => 0,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);
                    $count++;
                }
            }
        }
        $this->command->info("    ✅ $count notes insérées.");
    }

    private function buildGradeMap(): array
    {
        // Récupérer les apprenants de chaque classe
        $lmcC1Students = DB::table('users')->where('class_id', MultiSchoolSeeder::LMC_CLASS1_ID)->where('user_type', 3)->pluck('id')->toArray();
        $lmcC2Students = DB::table('users')->where('class_id', MultiSchoolSeeder::LMC_CLASS2_ID)->where('user_type', 3)->pluck('id')->toArray();
        $csmC1Students = DB::table('users')->where('class_id', MultiSchoolSeeder::CSM_CLASS1_ID)->where('user_type', 3)->pluck('id')->toArray();
        $csmC2Students = DB::table('users')->where('class_id', MultiSchoolSeeder::CSM_CLASS2_ID)->where('user_type', 3)->pluck('id')->toArray();
        $epeC1Students = DB::table('users')->where('class_id', MultiSchoolSeeder::EPE_CLASS1_ID)->where('user_type', 3)->pluck('id')->toArray();
        $epeC2Students = DB::table('users')->where('class_id', MultiSchoolSeeder::EPE_CLASS2_ID)->where('user_type', 3)->pluck('id')->toArray();

        $scores = [
            // LMC C1 — Maths T1 int
            self::E_LMC_C1_T1_MATHS_INT => $this->makeEntries($lmcC1Students, [14.5, 17.0, 09.5, 11.0, 16.0, 13.5]),
            self::E_LMC_C1_T1_MATHS_DS  => $this->makeEntries($lmcC1Students, [12.0, 15.5, 08.0, 10.5, 14.0, 11.0]),
            self::E_LMC_C1_T1_FRENCH_INT=> $this->makeEntries($lmcC1Students, [16.0, 18.0, 12.0, 14.5, 17.5, 15.0]),
            self::E_LMC_C1_T1_FRENCH_DS => $this->makeEntries($lmcC1Students, [13.5, 16.0, 10.0, 13.0, 15.5, 12.5]),
            self::E_LMC_C1_T2_MATHS_INT => $this->makeEntries($lmcC1Students, [15.0, 18.0, 10.5, 12.0, 17.0, 14.0]),
            self::E_LMC_C1_T2_FRENCH_INT=> $this->makeEntries($lmcC1Students, [14.0, 17.5, 11.0, 13.5, 16.5, 13.0]),
            // LMC C2
            self::E_LMC_C2_T1_MATHS_INT => $this->makeEntries($lmcC2Students, [12.5, 09.0, 15.0, 11.5, 13.0, 16.5]),
            self::E_LMC_C2_T1_PHYS_DS   => $this->makeEntries($lmcC2Students, [11.0, 08.5, 14.0, 10.0, 12.5, 15.5]),
            // CSM C1
            self::E_CSM_C1_T1_MATHS_INT => $this->makeEntries($csmC1Students, [13.0, 15.5, 10.5, 12.0, 14.5]),
            self::E_CSM_C1_T1_FRENCH_DS => $this->makeEntries($csmC1Students, [15.5, 17.0, 12.0, 14.0, 16.5]),
            self::E_CSM_C1_T2_MATHS_INT => $this->makeEntries($csmC1Students, [14.0, 16.0, 11.0, 13.5, 15.0]),
            // CSM C2
            self::E_CSM_C2_T1_ENG_INT   => $this->makeEntries($csmC2Students, [16.0, 14.5, 11.0, 13.0, 15.5]),
            // EPE C1
            self::E_EPE_C1_T1_CALC_INT  => $this->makeEntries($epeC1Students, [18.0, 16.5]),
            self::E_EPE_C1_T1_LECT_DS   => $this->makeEntries($epeC1Students, [17.5, 15.0]),
            // EPE C2
            self::E_EPE_C2_T1_CALC_INT  => $this->makeEntries($epeC2Students, [15.5, 14.0, 12.5]),
            self::E_EPE_C2_T1_FRENCH_DS => $this->makeEntries($epeC2Students, [14.5, 13.0, 11.5]),
        ];

        return $scores;
    }

    private function makeEntries(array $studentIds, array $scores): array
    {
        $entries = [];
        foreach ($studentIds as $i => $sid) {
            $score = $scores[$i] ?? 10.0;
            $absent = ($score < 0);
            $entries[] = [
                'student_id'  => $sid,
                'score'       => $absent ? null : $score,
                'observation' => $absent ? 'Absent' : ($score >= 16 ? 'Excellent' : ($score >= 12 ? 'Bien' : ($score >= 10 ? 'Passable' : 'Insuffisant'))),
            ];
        }
        return $entries;
    }

    // ── Barème de notation (marks_grade) ──────────────────────────────────────

    private function seedMarksGrade(): void
    {
        $grades = [
            ['name' => 'Excellent',    'percent_from' => 80, 'percent_to' => 100],
            ['name' => 'Très bien',    'percent_from' => 70, 'percent_to' => 79],
            ['name' => 'Bien',         'percent_from' => 60, 'percent_to' => 69],
            ['name' => 'Assez bien',   'percent_from' => 50, 'percent_to' => 59],
            ['name' => 'Passable',     'percent_from' => 40, 'percent_to' => 49],
            ['name' => 'Insuffisant',  'percent_from' => 0,  'percent_to' => 39],
        ];

        foreach ($grades as $g) {
            if (!DB::table('marks_grade')->where('name', $g['name'])->exists()) {
                DB::table('marks_grade')->insert([
                    'id'            => (string) Str::uuid(),
                    'name'          => $g['name'],
                    'percent_from'  => $g['percent_from'],
                    'percent_to'    => $g['percent_to'],
                    'is_delete'     => 0,
                    'created_by'    => SuperAdminSeeder::SUPER_ADMIN_ID,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        }
        $this->command->info("    ✅ 6 niveaux de barème insérés.");
    }

    // ── Bulletins — insérés directement (valeurs précalculées, 0 requête) ─────

    private function seedBulletins(): void
    {
        // Données précalculées à partir des notes du seedGrades()
        // Format : [student_email, school_id, period_id, admin_id, average, rank, total, success_rate, appreciation, comment, subjects]
        $bulletins = [
            // ── LMC 3ème A — T1 ──────────────────────────────────────────────
            ['eleve1@lmc.bj', SchoolSeeder::LMC_ID, PeriodsSeeder::LMC_T1, MultiSchoolSeeder::LMC_ADMIN_ID,
             15.25, 1, 6, 83.33, 'Très bien', 'Très bon trimestre. Encouragements mérités.',
             [[MultiSchoolSeeder::SUBJ_MATHS_ID, 4, 13.25], [MultiSchoolSeeder::SUBJ_FRENCH_ID, 4, 14.75]]],
            ['eleve2@lmc.bj', SchoolSeeder::LMC_ID, PeriodsSeeder::LMC_T1, MultiSchoolSeeder::LMC_ADMIN_ID,
             16.50, 2, 6, 83.33, 'Excellent', 'Résultats remarquables. Continuez sur cette excellente lancée.',
             [[MultiSchoolSeeder::SUBJ_MATHS_ID, 4, 16.25], [MultiSchoolSeeder::SUBJ_FRENCH_ID, 4, 17.00]]],
            ['eleve3@lmc.bj', SchoolSeeder::LMC_ID, PeriodsSeeder::LMC_T1, MultiSchoolSeeder::LMC_ADMIN_ID,
             8.75, 3, 6, 83.33, 'Passable', 'Trimestre difficile. Des efforts supplémentaires sont nécessaires.',
             [[MultiSchoolSeeder::SUBJ_MATHS_ID, 4, 8.75], [MultiSchoolSeeder::SUBJ_FRENCH_ID, 4, 11.00]]],
            // ── LMC 2nde B — T1 ──────────────────────────────────────────────
            ['eleve4@lmc.bj', SchoolSeeder::LMC_ID, PeriodsSeeder::LMC_T1, MultiSchoolSeeder::LMC_ADMIN_ID,
             11.83, 1, 3, 66.67, 'Bien', 'Bon travail ce trimestre. Quelques efforts pour progresser encore.',
             [[MultiSchoolSeeder::SUBJ_MATHS_ID, 4, 12.50], [MultiSchoolSeeder::SUBJ_PHYS_ID, 3, 10.00]]],
            ['eleve5@lmc.bj', SchoolSeeder::LMC_ID, PeriodsSeeder::LMC_T1, MultiSchoolSeeder::LMC_ADMIN_ID,
             9.0, 2, 3, 66.67, 'Passable', 'Résultats insuffisants. Un travail sérieux de rattrapage s\'impose.',
             [[MultiSchoolSeeder::SUBJ_MATHS_ID, 4, 9.00], [MultiSchoolSeeder::SUBJ_PHYS_ID, 3, 8.50]]],
            // ── CSM 5ème C — T1 ──────────────────────────────────────────────
            ['eleve1@csm.bj', SchoolSeeder::CSM_ID, PeriodsSeeder::CSM_T1, MultiSchoolSeeder::CSM_ADMIN_ID,
             14.25, 1, 3, 100.0, 'Très bien', 'Très bon trimestre. Encouragements mérités.',
             [[MultiSchoolSeeder::SUBJ_MATHS_ID, 3, 13.00], [MultiSchoolSeeder::SUBJ_FRENCH_ID, 3, 15.50]]],
            ['eleve2@csm.bj', SchoolSeeder::CSM_ID, PeriodsSeeder::CSM_T1, MultiSchoolSeeder::CSM_ADMIN_ID,
             16.25, 2, 3, 100.0, 'Excellent', 'Résultats remarquables. Continuez sur cette excellente lancée.',
             [[MultiSchoolSeeder::SUBJ_MATHS_ID, 3, 15.50], [MultiSchoolSeeder::SUBJ_FRENCH_ID, 3, 17.00]]],
            // ── EPE CE2 — T1 ─────────────────────────────────────────────────
            ['eleve1@epe.bj', SchoolSeeder::EPE_ID, PeriodsSeeder::EPE_T1, MultiSchoolSeeder::EPE_ADMIN_ID,
             17.75, 1, 2, 100.0, 'Excellent', 'Résultats remarquables. Continuez sur cette excellente lancée.',
             [[MultiSchoolSeeder::SUBJ_CALCUL_ID, 3, 18.00], [MultiSchoolSeeder::SUBJ_LECTURE_ID, 3, 17.50]]],
            ['eleve2@epe.bj', SchoolSeeder::EPE_ID, PeriodsSeeder::EPE_T1, MultiSchoolSeeder::EPE_ADMIN_ID,
             15.75, 2, 2, 100.0, 'Très bien', 'Très bon trimestre. Encouragements mérités.',
             [[MultiSchoolSeeder::SUBJ_CALCUL_ID, 3, 16.50], [MultiSchoolSeeder::SUBJ_LECTURE_ID, 3, 15.00]]],
            // ── EPE CM1 — T1 ─────────────────────────────────────────────────
            ['eleve3@epe.bj', SchoolSeeder::EPE_ID, PeriodsSeeder::EPE_T1, MultiSchoolSeeder::EPE_ADMIN_ID,
             15.50, 1, 3, 100.0, 'Très bien', 'Très bon trimestre. Encouragements mérités.',
             [[MultiSchoolSeeder::SUBJ_CALCUL_ID, 3, 15.50], [MultiSchoolSeeder::SUBJ_FRENCH_ID, 2, 14.50]]],
            ['eleve4@epe.bj', SchoolSeeder::EPE_ID, PeriodsSeeder::EPE_T1, MultiSchoolSeeder::EPE_ADMIN_ID,
             13.50, 2, 3, 100.0, 'Bien', 'Bon travail ce trimestre.',
             [[MultiSchoolSeeder::SUBJ_CALCUL_ID, 3, 14.00], [MultiSchoolSeeder::SUBJ_FRENCH_ID, 2, 13.00]]],
        ];

        $bulletinRows = [];
        $subjectRows  = [];

        foreach ($bulletins as $b) {
            [$email, $schoolId, $periodId, $adminId, $avg, $rank, $total, $rate, $appre, $comment, $subjects] = $b;

            $student = DB::table('users')->where('email', $email)->first();
            if (!$student) continue;
            if (DB::table('bulletins')->where('student_id', $student->id)->where('period_id', $periodId)->exists()) continue;

            $bulletinId = (string) Str::uuid();
            $bulletinRows[] = [
                'id'                => $bulletinId,
                'school_id'         => $schoolId,
                'student_id'        => $student->id,
                'period_id'         => $periodId,
                'average'           => $avg,
                'rank'              => $rank,
                'total_students'    => $total,
                'class_success_rate'=> $rate,
                'appreciation'      => $appre,
                'teacher_comment'   => $comment,
                'status'            => 'published',
                'generated_by'      => $adminId,
                'generated_at'      => now()->toDateTimeString(),
                'is_delete'         => 0,
                'created_at'        => now()->toDateTimeString(),
                'updated_at'        => now()->toDateTimeString(),
            ];

            foreach ($subjects as [$subjId, $coeff, $subjAvg]) {
                $subjectRows[] = [
                    'id'              => (string) Str::uuid(),
                    'bulletin_id'     => $bulletinId,
                    'subject_id'      => $subjId,
                    'coefficient'     => $coeff,
                    'average'         => $subjAvg,
                    'weighted_points' => round($subjAvg * $coeff, 2),
                    'appreciation'    => $subjAvg >= 14 ? 'Bien' : ($subjAvg >= 10 ? 'Assez bien' : 'À améliorer'),
                    'rank'            => $rank,
                    'created_at'      => now()->toDateTimeString(),
                    'updated_at'      => now()->toDateTimeString(),
                ];
            }
        }

        if (!empty($bulletinRows)) {
            DB::table('bulletins')->insert($bulletinRows);
        }
        if (!empty($subjectRows)) {
            DB::table('bulletin_subjects')->insert($subjectRows);
        }

        $this->command->info("    ✅ " . count($bulletinRows) . " bulletins + " . count($subjectRows) . " lignes matières insérés.");
    }
}
