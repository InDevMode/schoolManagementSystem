<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table class_subject — version consolidée.
 * Fusionne : create_class_subject + add_coefficient + add_unique
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_subject', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('subject_id');
            $table->integer('coefficient')->default(1)->comment('Coefficient de la matière dans la classe');
            $table->tinyInteger('status')->default(0)->comment('0: Inactive, 1: Active');
            $table->tinyInteger('is_delete')->default(0)->comment('0: actif, 1: supprimé');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('class_id')->references('id')->on('class')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subject')->onDelete('cascade');

            // Contrainte unique : une matière ne peut être assignée qu'une fois à une classe
            $table->unique(['class_id', 'subject_id'], 'class_subject_class_subject_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_subject');
    }
};
