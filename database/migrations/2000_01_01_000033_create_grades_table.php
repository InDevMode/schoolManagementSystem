<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table grades — notes individuelles des apprenants pour une évaluation.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id')->nullable()->comment('Multi-tenant');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('evaluation_id');
            $table->decimal('score', 5, 2)->nullable();
            $table->unsignedBigInteger('teacher_id')->nullable();
            $table->boolean('validated')->default(false);
            $table->unsignedBigInteger('validated_by')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->string('observation')->nullable()->comment('Ex: Absent, Dispensé...');
            $table->tinyInteger('is_delete')->default(0)->comment('0: actif, 1: supprimé');
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('evaluation_id')->references('id')->on('evaluations')->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('validated_by')->references('id')->on('users')->onDelete('set null');

            $table->unique(['student_id', 'evaluation_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
