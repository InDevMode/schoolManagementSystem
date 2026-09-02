<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bulletins', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('school_id')->nullable()->comment('Multi-tenant');
            $table->uuid('student_id');
            $table->uuid('period_id');
            $table->decimal('average', 5, 2)->nullable()->comment('Moyenne générale /20');
            $table->smallInteger('rank')->nullable()->comment('Rang dans la classe');
            $table->smallInteger('total_students')->nullable();
            $table->decimal('class_success_rate', 5, 2)->nullable()->comment('% de réussite de la classe');
            $table->string('appreciation')->nullable();
            $table->text('teacher_comment')->nullable();
            $table->string('status')->default('draft')->comment('draft, published');
            $table->uuid('generated_by')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->smallInteger('is_delete')->default(0)->comment('0: actif, 1: supprimé');
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('period_id')->references('id')->on('periods')->onDelete('cascade');
            $table->foreign('generated_by')->references('id')->on('users')->onDelete('set null');

            // Un seul bulletin par apprenant par période
            $table->unique(['student_id', 'period_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bulletins');
    }
};
