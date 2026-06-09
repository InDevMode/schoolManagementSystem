<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Ajoute une contrainte unique (class_id, subject_id) sur class_subject.
 *
 * Avant d'appliquer, les doublons existants sont dépilés :
 * on garde le premier enregistrement (id le plus petit) et on supprime les autres.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Supprimer les doublons existants ──────────────────────────────
        // Pour chaque paire (class_id, subject_id), on garde l'id le plus petit.
        DB::statement('
            DELETE cs1
            FROM class_subject cs1
            INNER JOIN class_subject cs2
                ON  cs1.class_id   = cs2.class_id
                AND cs1.subject_id = cs2.subject_id
                AND cs1.id         > cs2.id
        ');

        // ── 2. Ajouter la contrainte unique ──────────────────────────────────
        Schema::table('class_subject', function (Blueprint $table) {
            $table->unique(['class_id', 'subject_id'], 'class_subject_class_subject_unique');
        });
    }

    public function down(): void
    {
        Schema::table('class_subject', function (Blueprint $table) {
            $table->dropUnique('class_subject_class_subject_unique');
        });
    }
};
