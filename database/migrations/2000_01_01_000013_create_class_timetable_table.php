<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table class_timetable — version consolidée.
 * Fusionne : create + add_is_delete + add_teacher_id/color/session_type/notes/school_id
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_timetable', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id')->nullable()->comment('Multi-tenant prep');
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->unsignedBigInteger('week_id')->nullable();
            $table->unsignedBigInteger('teacher_id')->nullable()
                  ->comment('Professeur assigné à ce créneau');
            $table->string('start_time')->nullable();
            $table->string('end_time')->nullable();
            $table->string('room_number')->nullable();
            $table->string('color', 7)->nullable()->comment('Couleur hex du créneau');
            $table->enum('session_type', ['cours', 'tp', 'td', 'examen', 'autre'])
                  ->default('cours');
            $table->string('notes')->nullable();
            $table->tinyInteger('is_delete')->default(0)->comment('0: actif, 1: supprimé');
            $table->timestamps();

            $table->foreign('class_id')->references('id')->on('class')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subject')->onDelete('cascade');
            $table->foreign('week_id')->references('id')->on('week')->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_timetable');
    }
};
