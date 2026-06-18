<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetIncompleteEvaluations extends Command
{
    protected $signature   = 'evaluations:reset-incomplete {--dry-run : Afficher sans modifier}';
    protected $description = 'Remet à "open" toutes les évaluations "validated" dont les notes sont incomplètes ou non toutes validées.';

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');

        $this->info($isDryRun
            ? '🔍  Mode aperçu — aucune modification ne sera effectuée.'
            : '🔧  Correction des évaluations mal validées...'
        );
        $this->newLine();

        // Récupérer toutes les évaluations en statut "validated"
        $evaluations = DB::table('evaluations')
            ->where('status', 'validated')
            ->where('is_delete', 0)
            ->select('id', 'class_id', 'title', 'type', 'eval_date')
            ->get();

        if ($evaluations->isEmpty()) {
            $this->info('✅  Aucune évaluation validée trouvée.');
            return 0;
        }

        $toReset  = [];
        $reasons  = [];

        foreach ($evaluations as $eval) {
            // Nombre d'élèves actifs dans la classe
            $totalStudents = DB::table('users')
                ->where('class_id', $eval->class_id)
                ->where('user_type', 3)
                ->where('is_delete', 0)
                ->where('status', 1)
                ->count();

            if ($totalStudents === 0) continue;

            // Notes saisies (score non null, non supprimées)
            $savedGrades = DB::table('grades')
                ->where('evaluation_id', $eval->id)
                ->where('is_delete', 0)
                ->whereNotNull('score')
                ->count();

            // Notes validées
            $validatedGrades = DB::table('grades')
                ->where('evaluation_id', $eval->id)
                ->where('is_delete', 0)
                ->where('validated', true)
                ->count();

            $reason = null;

            if ($savedGrades < $totalStudents) {
                $missing = $totalStudents - $savedGrades;
                $reason  = "{$missing} note(s) non saisie(s) sur {$totalStudents} élève(s)";
            } elseif ($validatedGrades < $totalStudents) {
                $notValidated = $totalStudents - $validatedGrades;
                $reason       = "{$notValidated} note(s) saisie(s) mais non validée(s)";
            }

            if ($reason) {
                $toReset[]           = $eval->id;
                $reasons[$eval->id]  = $reason;
            }
        }

        if (empty($toReset)) {
            $this->info('✅  Toutes les évaluations validées sont cohérentes — rien à corriger.');
            return 0;
        }

        // Afficher le tableau des évaluations concernées
        $rows = [];
        foreach ($evaluations as $eval) {
            if (in_array($eval->id, $toReset)) {
                $rows[] = [
                    $eval->id,
                    $eval->type,
                    $eval->eval_date,
                    $reasons[$eval->id],
                ];
            }
        }

        $this->table(['ID', 'Type', 'Date', 'Raison'], $rows);
        $this->newLine();
        $this->warn(count($toReset) . ' évaluation(s) à remettre en "open".');

        if ($isDryRun) {
            $this->newLine();
            $this->line('Relancez sans --dry-run pour appliquer la correction.');
            return 0;
        }

        if (!$this->confirm('Confirmer la remise à "open" de ces ' . count($toReset) . ' évaluation(s) ?', true)) {
            $this->line('Annulé.');
            return 0;
        }

        // Remettre à "open" ET annuler la validation des notes concernées
        DB::transaction(function () use ($toReset) {
            // 1. Remettre les évaluations à "open"
            DB::table('evaluations')
                ->whereIn('id', $toReset)
                ->update(['status' => 'open']);

            // 2. Annuler la validation de toutes les notes de ces évaluations
            //    (elles repasseront en attente de validation)
            DB::table('grades')
                ->whereIn('evaluation_id', $toReset)
                ->where('is_delete', 0)
                ->update([
                    'validated'    => false,
                    'validated_by' => null,
                    'validated_at' => null,
                ]);
        });

        $this->newLine();
        $this->info('✅  ' . count($toReset) . ' évaluation(s) remise(s) à "open". Les notes associées repassent en attente de validation.');

        return 0;
    }
}
