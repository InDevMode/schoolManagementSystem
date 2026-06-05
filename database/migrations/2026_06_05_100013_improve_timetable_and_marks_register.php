<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Améliorations :
 * 1. class_timetable — ajout teacher_id, color, session_type, notes, school_id
 * 2. marks_register  — ajout eval_type, workflow validation, period_id, max_score
 *
 * Tous les FK utilisent unsignedBigInteger pour matcher les PKs Laravel (bigIncrements).
 */
return new class extends Migration {
    public function up(): void
    {
        // ── Amélioration class_timetable ─────────────────────────────────────
        Schema::table('class_timetable', function (Blueprint $table) {
            if (!Schema::hasColumn('class_timetable', 'school_id')) {
                $table->unsignedBigInteger('school_id')->nullable()->after('id')->comment('Multi-tenant prep');
            }
            if (!Schema::hasColumn('class_timetable', 'teacher_id')) {
                $table->unsignedBigInteger('teacher_id')->nullable()->after('subject_id');
                $table->foreign('teacher_id')->references('id')->on('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('class_timetable', 'color')) {
                $table->string('color', 7)->nullable()->after('room_number')
                    ->comment('Couleur hex du créneau dans l\'emploi du temps');
            }
            if (!Schema::hasColumn('class_timetable', 'session_type')) {
                $table->enum('session_type', ['cours', 'tp', 'td', 'examen', 'autre'])
                    ->default('cours')->after('color');
            }
            if (!Schema::hasColumn('class_timetable', 'notes')) {
                $table->string('notes')->nullable()->after('session_type');
            }
        });

        // ── Amélioration marks_register ──────────────────────────────────────
        Schema::table('marks_register', function (Blueprint $table) {
            // Type béninois : détermine quelle colonne de note est pertinente
            if (!Schema::hasColumn('marks_register', 'eval_type')) {
                $table->enum('eval_type', ['interrogation', 'devoir_surveille', 'travail_maison', 'examen_blanc'])
                    ->nullable()->after('exam_id')
                    ->comment('interrogation=quiz, devoir_surveille=test/exam, travail_maison=home/class/assignment, examen_blanc=exam coeff3');
            }
            // Lien période pour le bulletin
            if (!Schema::hasColumn('marks_register', 'period_id')) {
                $table->unsignedBigInteger('period_id')->nullable()->after('eval_type');
                $table->foreign('period_id')->references('id')->on('periods')->onDelete('set null');
            }
            // Note max (20 par défaut)
            if (!Schema::hasColumn('marks_register', 'max_score')) {
                $table->decimal('max_score', 5, 2)->default(20)->after('full_marks');
            }
            // Moyenne matière calculée (formule béninoise)
            if (!Schema::hasColumn('marks_register', 'subject_average')) {
                $table->decimal('subject_average', 5, 2)->nullable()->after('max_score')
                    ->comment('Σ(note×coeff) / Σ(coeffs) sur 20');
            }
            // Observation libre (absent, dispensé…)
            if (!Schema::hasColumn('marks_register', 'observation')) {
                $table->string('observation')->nullable()->after('subject_average');
            }
            // Workflow validation prof → admin
            if (!Schema::hasColumn('marks_register', 'validated')) {
                $table->boolean('validated')->default(false)->after('observation');
            }
            if (!Schema::hasColumn('marks_register', 'validated_by')) {
                $table->unsignedBigInteger('validated_by')->nullable()->after('validated');
                $table->foreign('validated_by')->references('id')->on('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('marks_register', 'validated_at')) {
                $table->timestamp('validated_at')->nullable()->after('validated_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('class_timetable', function (Blueprint $table) {
            foreach (['teacher_id', 'color', 'session_type', 'notes', 'school_id'] as $col) {
                if (Schema::hasColumn('class_timetable', $col)) {
                    if ($col === 'teacher_id') {
                        $table->dropForeign(['teacher_id']);
                    }
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('marks_register', function (Blueprint $table) {
            foreach (['eval_type', 'period_id', 'max_score', 'subject_average', 'observation', 'validated', 'validated_by', 'validated_at'] as $col) {
                if (Schema::hasColumn('marks_register', $col)) {
                    if (in_array($col, ['period_id', 'validated_by'])) {
                        try { $table->dropForeign([$col]); } catch (\Exception $e) {}
                    }
                    $table->dropColumn($col);
                }
            }
        });
    }
};
