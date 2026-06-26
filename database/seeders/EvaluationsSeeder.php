<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * EvaluationsSeeder — Génère des évaluations et notes réalistes.
 *
 * Crée pour chaque classe × matière × période :
 *   - Des évaluations variées (interrogation, devoir surveillé, etc.)
 *   - Des notes pour chaque apprenant
 *
 * Dépendances :
 *   - PeriodsSeeder (periods doit être peuplé)
 *   - MultiSchoolSeeder (classes, matières, apprenants doivent exister)
 */
class EvaluationsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('  🌱 Évaluations & notes...');

        // ── Données de base ───────────────────────────────────────────────────
        $periods  = DB::table('periods')->where('is_delete', 0)->orderBy('order_number')->get();
        $classes  = DB::table('class')->where('is_delete', 0)->take(3)->get();
        $subjects = DB::table('subject')->where('is_delete', 0)->take(4)->get();
        $admin    = User::where('user_type', 1)->where('is_delete', 0)->orderBy('id')->first();
        $teacher  = User::where('user_type', 2)->where('is_delete', 0)->orderBy('id')->first();

        if ($periods->isEmpty()) {
            $this->command->warn('  ⚠  Aucune période trouvée — lancez PeriodsSeeder d\'abord.');
            return;
        }
        if ($classes->isEmpty() || $subjects->isEmpty()) {
            $this->command->warn('  ⚠  Aucune classe ou matière trouvée — lancez MultiSchoolSeeder d\'abord.');
            return;
        }

        $createdBy = $admin?->id ?? 1;
        $teacherId = $teacher?->id ?? $createdBy;
        $evalCount = 0;
        $gradeCount = 0;

        foreach ($classes as $class) {
            // apprenants de cette classe
            $students = User::where('class_id', $class->id)
                ->where('user_type', 3)
                ->where('is_delete', 0)
                ->get();

            if ($students->isEmpty()) continue;

            foreach ($periods as $period) {
                foreach ($subjects->take(3) as $subject) {
                    $evals = $this->createEvaluations($class->id, $subject->id, $period, $teacherId, $createdBy);
                    $evalCount += count($evals);

                    foreach ($evals as $evalId) {
                        $gc = $this->createGrades($evalId, $students, $teacherId);
                        $gradeCount += $gc;
                    }
                }
            }
        }

        $this->command->info("  ✅ $evalCount évaluations, $gradeCount notes créées.");
    }

    // ── Créer les évaluations d'une matière/classe/période ───────────────────

    private function createEvaluations(
        int $classId,
        int $subjectId,
        object $period,
        int $teacherId,
        int $createdBy
    ): array {
        $base = Carbon::parse($period->start_date);

        // Évaluations à créer selon le statut de la période
        $isFuture = Carbon::parse($period->start_date)->isFuture();
        $types = $isFuture
            ? [
                ['interrogation',   7,  'draft', 1],
                ['devoir_surveille',14, 'draft', 2],
            ]
            : [
                ['interrogation',         14, 'validated', 1],
                ['travail_maison',         21, 'validated', 1],
                ['devoir_surveille',       35, 'validated', 2],
                ['interrogation',          50, 'open',      1],
                ['examen_blanc',           60, 'open',      3],
            ];

        $ids = [];
        foreach ($types as [$type, $offset, $status, $coeff]) {
            $evalDate = $base->copy()->addDays($offset)->format('Y-m-d');

            $existing = DB::table('evaluations')
                ->where('class_id', $classId)
                ->where('subject_id', $subjectId)
                ->where('period_id', $period->id)
                ->where('type', $type)
                ->where('eval_date', $evalDate)
                ->where('is_delete', 0)
                ->value('id');

            if ($existing) {
                $ids[] = $existing;
                continue;
            }

            $month = Carbon::parse($evalDate)->locale('fr')->isoFormat('MMMM');
            $title = match($type) {
                'interrogation'    => "Interrogation de $month",
                'devoir_surveille' => "Devoir surveillé de $month",
                'travail_maison'   => "Travail de maison $month",
                'examen_blanc'     => "Examen blanc $month",
                default            => "Évaluation $month",
            };

            $ids[] = DB::table('evaluations')->insertGetId([
                'class_id'    => $classId,
                'subject_id'  => $subjectId,
                'period_id'   => $period->id,
                'teacher_id'  => $teacherId,
                'type'        => $type,
                'coefficient' => $coeff,
                'max_score'   => 20,
                'eval_date'   => $evalDate,
                'title'       => $title,
                'status'      => $status,
                'is_delete'   => 0,
                'created_by'  => $createdBy,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        return $ids;
    }

    // ── Créer des notes pour une évaluation ──────────────────────────────────

    private function createGrades(int $evalId, $students, int $teacherId): int
    {
        $eval = DB::table('evaluations')->find($evalId);
        if (! $eval || $eval->status === 'draft') return 0;

        $count     = 0;
        $validated = $eval->status === 'validated';

        foreach ($students as $student) {
            $exists = DB::table('grades')
                ->where('evaluation_id', $evalId)
                ->where('student_id', $student->id)
                ->exists();
            if ($exists) continue;

            $isAbsent = rand(1, 20) === 1; // 5% absent
            $score    = $isAbsent ? null : $this->randomScore(20);

            DB::table('grades')->insert([
                'student_id'    => $student->id,
                'evaluation_id' => $evalId,
                'score'         => $score,
                'teacher_id'    => $teacherId,
                'validated'     => $validated,
                'validated_by'  => $validated ? $teacherId : null,
                'validated_at'  => $validated
                    ? Carbon::parse($eval->eval_date)->addDays(rand(3, 10))->toDateTimeString()
                    : null,
                'observation'   => $isAbsent ? 'Absent' : null,
                'is_delete'     => 0,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
            $count++;
        }

        return $count;
    }

    // ── Note aléatoire réaliste (distribution : 20% faible, 50% moyen, 30% bon) ──

    private function randomScore(float $max): float
    {
        $r = rand(1, 100);
        $score = match(true) {
            $r <= 20 => rand(2, 8) + (rand(0, 1) * 0.5),
            $r <= 70 => rand(8, 14) + (rand(0, 1) * 0.5),
            default  => rand(14, 20) + (rand(0, 1) * 0.5),
        };
        return min($max, round($score, 1));
    }
}
