<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('school_id')->nullable()->comment('Multi-tenant');
            $table->uuid('exam_id')->nullable();
            $table->uuid('class_id')->nullable();
            $table->uuid('subject_id')->nullable();
            $table->uuid('teacher_id')->nullable();
            $table->uuid('period_id')->nullable();
            $table->string('type')->default('interrogation')
                  ->comment('interrogation, devoir_surveille, travail_maison, examen_blanc');
            $table->smallInteger('coefficient')->default(1);
            $table->decimal('max_score', 5, 2)->default(20.00);
            $table->date('eval_date')->nullable();
            $table->string('title')->nullable();
            $table->string('status')->default('open')
                  ->comment('draft, open, closed, validated, cancelled');
            $table->smallInteger('is_delete')->default(0)->comment('0: actif, 1: supprimé');
            $table->uuid('created_by')->nullable();
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
