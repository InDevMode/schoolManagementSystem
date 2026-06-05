<?php

namespace Database\Seeders;

use App\Models\ClassModel;
use App\Models\EvaluationModel;
use App\Models\GradeModel;
use App\Models\PeriodModel;
use App\Models\SubjectModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * EvaluationSeeder — Génère des données réalistes pour le module Évaluation.
 *
 * Ce seeder crée :
 *  - 3 Périodes (Trimestre 1, 2, 3)
 *  - Des évaluations pour chaque classe × matière × période
 *  - Des notes pour chaque élève (avec différents statuts : à valider, validées)
 *
 * Usage : php artisan db:seed --class=EvaluationSeeder
 */
class EvaluationSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Seeder Évaluations — démarrage...');

        // ── 1. Périodes ───────────────────────────────────────────────────────
        $periods = $this->seedPeriods();
        $this->command->info('  ✅ ' . count($periods) . ' périodes créées/existantes');

        // ── 2. Récupérer les données existantes ───────────────────────────────
        $classes  = ClassModel::where('is_delete', 0)->get();
        $subjects = SubjectModel::where('is_delete', 0)->get();
        $teachers = User::where('user_type', 2)->where('is_delete', 0)->where('status', 1)->get();
        $admin    = User::where('user_type', 1)->where('is_delete', 0)->first();

        if ($classes->isEmpty()) {
            $this->command->warn('  ⚠️  Aucune classe trouvée. Créez des classes d\'abord.');
            return;
        }
        if ($subjects->isEmpty()) {
            $this->command->warn('  ⚠️  Aucune matière trouvée. Créez des matières d\'abord.');
            return;
        }

        $createdBy = $admin?->id ?? 1;
        $evalCount  = 0;
        $gradeCount = 0;

        // ── 3. Pour chaque classe ─────────────────────────────────────────────
        foreach ($classes->take(3) as $class) {
            // Élèves de la classe
            $students = User::where('class_id', $class->id)
                ->where('user_type', 3)
                ->where('is_delete', 0)
                ->where('status', 1)
                ->get();

            if ($students->isEmpty()) continue;

            // Matières de la classe (max 5)
            $classSubjects = $subjects->take(5);

            // Prof assigné à cette classe (si dispo)
            $teacher = $teachers->first();

            foreach ($periods as $period) {
                foreach ($classSubjects as $subject) {
                    // Générer différents types d'évaluations par matière/période
                    $evals = $this->createEvaluationsForSubject(
                        $class->id,
                        $subject->id,
                        $period->id,
                        $teacher?->id,
                        $createdBy,
                        $period
                    );

                    $evalCount += count($evals);

                    // Générer des notes pour chaque évaluation
                    foreach ($evals as $eval) {
                        $grades = $this->createGradesForEvaluation($eval, $students, $teacher?->id ?? $createdBy);
                        $gradeCount += count($grades);
                    }
                }
            }
        }

        $this->command->info("  ✅ $evalCount évaluations créées");
        $this->command->info("  ✅ $gradeCount notes créées");
        $this->command->info('🎉 Seeder Évaluations terminé !');
        $this->command->newLine();
        $this->command->line('📊 Résumé :');
        $this->command->line('  • Notes À VALIDER : ' . GradeModel::where('validated', false)->where('is_delete', 0)->count());
        $this->command->line('  • Notes VALIDÉES  : ' . GradeModel::where('validated', true)->where('is_delete', 0)->count());
        $this->command->line('  • Évals OUVERTES  : ' . EvaluationModel::where('status', 'open')->where('is_delete', 0)->count());
        $this->command->line('  • Évals VALIDÉES  : ' . EvaluationModel::where('status', 'validated')->where('is_delete', 0)->count());
    }

    // ── Créer les 3 périodes de l'année ──────────────────────────────────────

    private function seedPeriods(): array
    {
        $settings = DB::table('settings')->first();
        $settingsId = $settings?->id ?? 1;

        $periodsData = [
            [
                'name'       => '1er Trimestre',
                'start_date' => '2025-09-01',
                'end_date'   => '2025-12-20',
                'status'     => 0, // Terminé
            ],
            [
                'name'       => '2ème Trimestre',
                'start_date' => '2026-01-06',
                'end_date'   => '2026-03-28',
                'status'     => 1, // En cours
            ],
            [
                'name'       => '3ème Trimestre',
                'start_date' => '2026-04-07',
                'end_date'   => '2026-06-30',
                'status'     => 0, // Pas encore commencé
            ],
        ];

        $periods = [];
        foreach ($periodsData as $data) {
            $existing = PeriodModel::where('name', $data['name'])->where('is_delete', 0)->first();
            if ($existing) {
                $periods[] = $existing;
                continue;
            }

            $p = new PeriodModel;
            $p->settings_id = $settingsId;
            $p->name        = $data['name'];
            $p->start_date  = $data['start_date'];
            $p->end_date    = $data['end_date'];
            $p->status      = $data['status'];
            $p->is_current  = 0;
            $p->created_by  = 1;
            $p->save();

            $periods[] = $p;
        }

        return $periods;
    }

    // ── Créer des évaluations pour une matière/classe/période ────────────────

    private function createEvaluationsForSubject(
        int $classId,
        int $subjectId,
        int $periodId,
        ?int $teacherId,
        int $createdBy,
        $period
    ): array {
        $evals = [];
        $baseDate = Carbon::parse($period->start_date);

        // Types d'évaluations à créer selon la période
        $evalTypes = [
            // Type                  | offset jours | statut       | coeff
            ['interrogation',         14,            'validated',    1],
            ['travail_maison',         21,            'validated',    1],
            ['devoir_surveille',       35,            'validated',    2],
            ['interrogation',          50,            'open',         1],
            ['examen_blanc',           60,            'open',         3],
        ];

        // Période 3 (future) : brouillons seulement
        if ($period->status == 0 && Carbon::parse($period->start_date)->isFuture()) {
            $evalTypes = [
                ['interrogation',   7,  'draft', 1],
                ['devoir_surveille',14, 'draft', 2],
            ];
        }

        foreach ($evalTypes as [$type, $offsetDays, $status, $coeff]) {
            $evalDate = $baseDate->copy()->addDays($offsetDays)->format('Y-m-d');

            // Éviter les doublons
            $existing = EvaluationModel::where('class_id', $classId)
                ->where('subject_id', $subjectId)
                ->where('period_id', $periodId)
                ->where('type', $type)
                ->where('eval_date', $evalDate)
                ->where('is_delete', 0)
                ->first();

            if ($existing) {
                $evals[] = $existing;
                continue;
            }

            $eval             = new EvaluationModel;
            $eval->class_id   = $classId;
            $eval->subject_id = $subjectId;
            $eval->period_id  = $periodId;
            $eval->teacher_id = $teacherId;
            $eval->type       = $type;
            $eval->coefficient= $coeff;
            $eval->max_score  = 20;
            $eval->eval_date  = $evalDate;
            $eval->title      = $this->generateTitle($type, $evalDate);
            $eval->status     = $status;
            $eval->created_by = $createdBy;
            $eval->save();

            $evals[] = $eval;
        }

        return $evals;
    }

    // ── Créer des notes pour une évaluation ──────────────────────────────────

    private function createGradesForEvaluation($eval, $students, int $teacherId): array
    {
        // Ne pas créer de notes pour les évals en brouillon
        if ($eval->status === 'draft') return [];

        $grades = [];

        foreach ($students as $student) {
            $existing = GradeModel::where('evaluation_id', $eval->id)
                ->where('student_id', $student->id)
                ->where('is_delete', 0)
                ->first();

            if ($existing) {
                $grades[] = $existing;
                continue;
            }

            // Générer une note réaliste (distribution gaussienne simplifiée)
            $score = $this->generateRealisticScore($eval->max_score);

            // 5% d'absents (score null)
            $isAbsent = rand(1, 20) === 1;

            $validated   = $eval->status === 'validated';
            $validatedAt = $validated ? Carbon::parse($eval->eval_date)->addDays(rand(3, 10)) : null;

            $grade               = new GradeModel;
            $grade->student_id   = $student->id;
            $grade->evaluation_id= $eval->id;
            $grade->score        = $isAbsent ? null : $score;
            $grade->teacher_id   = $teacherId;
            $grade->validated    = $validated;
            $grade->validated_by = $validated ? $teacherId : null;
            $grade->validated_at = $validatedAt;
            $grade->observation  = $isAbsent ? 'Absent' : null;
            $grade->save();

            $grades[] = $grade;
        }

        return $grades;
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function generateRealisticScore(float $max): float
    {
        // Distribution : 20% faibles (< 8), 50% moyens (8-14), 30% bons (14-20)
        $rand = rand(1, 100);
        if ($rand <= 20) {
            // Note faible
            $score = round(rand(2, 8) + (rand(0, 1) * 0.5), 1);
        } elseif ($rand <= 70) {
            // Note moyenne
            $score = round(rand(8, 14) + (rand(0, 1) * 0.5), 1);
        } else {
            // Bonne note
            $score = round(rand(14, 20) + (rand(0, 1) * 0.5), 1);
        }

        return min($max, $score);
    }

    private function generateTitle(string $type, string $date): string
    {
        $month = Carbon::parse($date)->locale('fr')->isoFormat('MMMM');
        return match($type) {
            'interrogation'    => "Interrogation de $month",
            'devoir_surveille' => "Devoir surveillé de $month",
            'travail_maison'   => "Travail de maison $month",
            'examen_blanc'     => "Examen blanc $month",
            default            => "Évaluation $month",
        };
    }
}
