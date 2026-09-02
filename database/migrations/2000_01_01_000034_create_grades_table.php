<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('school_id')->nullable()->comment('Multi-tenant');
            $table->uuid('student_id');
            $table->uuid('evaluation_id');
            $table->decimal('score', 5, 2)->nullable();
            $table->uuid('teacher_id')->nullable();
            $table->boolean('validated')->default(false);
            $table->uuid('validated_by')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->string('observation')->nullable()->comment('Ex: Absent, Dispensé...');
            $table->smallInteger('is_delete')->default(0)->comment('0: actif, 1: supprimé');
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
