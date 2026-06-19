<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table marks_grade — barème de notation (A, B, C... ou Excellent, Bien...).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marks_grade', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('percent_from');
            $table->integer('percent_to');
            $table->tinyInteger('is_delete')->default(0)->comment('0: actif, 1: supprimé');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marks_grade');
    }
};
