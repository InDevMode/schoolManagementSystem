<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table marks_register — registre des notes (ancien système, conservé).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marks_register', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('student_id')->nullable();
            $table->uuid('exam_id')->nullable();
            $table->uuid('class_id')->nullable();
            $table->uuid('subject_id')->nullable();
            $table->uuid('period_id')->nullable()
                  ->comment('Lien période pour le bulletin');
            $table->string('eval_type')->nullable()
                  ->comment('interrogation, devoir_surveille, travail_maison, examen_blanc');

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
            $table->decimal('max_score', 5, 2)->default(20);
            $table->decimal('total_marks', 6, 2)->nullable();
            $table->decimal('quiz_average', 5, 2)->nullable();
            $table->decimal('assignment_average', 5, 2)->nullable();
            $table->decimal('subject_average', 5, 2)->nullable();
            $table->decimal('coefficient', 3, 1)->default(1);

            // Observation & workflow
            $table->string('observation')->nullable();
            $table->boolean('validated')->default(false);
            $table->uuid('validated_by')->nullable();
            $table->timestamp('validated_at')->nullable();

            $table->smallInteger('is_delete')->default(0)->comment('0: actif, 1: supprimé');
            $table->uuid('created_by')->nullable();
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
