<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table evaluations — évaluations du nouveau système (remplace marks_register).
 * Fusionne : fix_fk_types_new_tables (evaluations) + add_cancelled_to_status
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id')->nullable()->comment('Multi-tenant');
            $table->unsignedBigInteger('exam_id')->nullable();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->unsignedBigInteger('teacher_id')->nullable();
            $table->unsignedBigInteger('period_id')->nullable();
            $table->enum('type', ['interrogation', 'devoir_surveille', 'travail_maison', 'examen_blanc'])
                  ->default('interrogation');
            $table->unsignedTinyInteger('coefficient')->default(1);
            $table->decimal('max_score', 5, 2)->default(20.00);
            $table->date('eval_date')->nullable();
            $table->string('title')->nullable();
            $table->enum('status', ['draft', 'open', 'closed', 'validated', 'cancelled'])
                  ->default('open');
            $table->tinyInteger('is_delete')->default(0)->comment('0: actif, 1: supprimé');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('class')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subject')->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('period_id')->references('id')->on('periods')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');

            $table->index(['school_id', 'class_id', 'period_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
