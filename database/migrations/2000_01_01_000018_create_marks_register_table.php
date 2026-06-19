<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table marks_register — registre des notes (ancien système).
 * Fusionne : create_marks_register + improve_timetable_and_marks_register (colonnes marks_register).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marks_register', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->unsignedBigInteger('exam_id')->nullable();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->unsignedBigInteger('period_id')->nullable()
                  ->comment('Lien période pour le bulletin');
            $table->enum('eval_type', ['interrogation', 'devoir_surveille', 'travail_maison', 'examen_blanc'])
                  ->nullable()->comment('Type d\'évaluation béninois');

            // Notes brutes
            $table->decimal('class_work', 5, 2)->nullable();
            $table->decimal('home_work', 5, 2)->nullable();
            $table->decimal('exam_work', 5, 2)->nullable();
            $table->decimal('test_work', 5, 2)->nullable();
            $table->decimal('quiz_1', 5, 2)->nullable();
            $table->decimal('quiz_2', 5, 2)->nullable();
            $table->decimal('quiz_3', 5, 2)->nullable();
            $table->decimal('quiz_4', 5, 2)->nullable();
            $table->decimal('quiz_5', 5, 2)->nullable();
            $table->decimal('assignment_1', 5, 2)->nullable();
            $table->decimal('assignment_2', 5, 2)->nullable();
            $table->decimal('assignment_3', 5, 2)->nullable();

            // Agrégats
            $table->decimal('passing_marks', 6, 2)->nullable();
            $table->decimal('full_marks', 6, 2)->nullable();
            $table->decimal('max_score', 5, 2)->default(20)->comment('Note maximale (20 par défaut)');
            $table->decimal('total_marks', 6, 2)->nullable();
            $table->decimal('quiz_average', 5, 2)->nullable();
            $table->decimal('assignment_average', 5, 2)->nullable();
            $table->decimal('subject_average', 5, 2)->nullable()
                  ->comment('Σ(note×coeff) / Σ(coeffs) sur 20');
            $table->decimal('coefficient', 3, 1)->default(1);

            // Observation & workflow
            $table->string('observation')->nullable()->comment('Ex: Absent, Dispensé...');
            $table->boolean('validated')->default(false);
            $table->unsignedBigInteger('validated_by')->nullable();
            $table->timestamp('validated_at')->nullable();

            $table->tinyInteger('is_delete')->default(0)->comment('0: actif, 1: supprimé');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('class')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subject')->onDelete('cascade');
            $table->foreign('period_id')->references('id')->on('periods')->onDelete('set null');
            $table->foreign('validated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marks_register');
    }
};
