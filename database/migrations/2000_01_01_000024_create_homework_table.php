<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table homework — soumissions des apprenants pour un work.
 * Fusionne : create_homework + add_status
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('homework', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_id')->nullable();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->string('document_file')->nullable();
            $table->longText('description')->nullable();
            $table->enum('status', ['hold', 'submitted', 'done', 'processed', 'resolved'])
                  ->default('hold');
            $table->tinyInteger('is_delete')->default(0)->comment('0: actif, 1: supprimé');
            $table->timestamps();

            $table->foreign('work_id')->references('id')->on('works')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('homework');
    }
};
