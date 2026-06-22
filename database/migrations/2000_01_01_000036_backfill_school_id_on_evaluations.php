<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Backfill school_id sur les évaluations existantes.
 * Copie le school_id de la classe associée vers chaque évaluation
 * qui n'a pas encore de school_id renseigné.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('
            UPDATE evaluations
            INNER JOIN class ON class.id = evaluations.class_id
            SET evaluations.school_id = class.school_id
            WHERE evaluations.school_id IS NULL
              AND class.school_id IS NOT NULL
        ');
    }

    public function down(): void
    {
        // Pas de rollback pertinent — on ne peut pas distinguer les valeurs
        // qui étaient null avant ce backfill de celles mises à jour.
    }
};
