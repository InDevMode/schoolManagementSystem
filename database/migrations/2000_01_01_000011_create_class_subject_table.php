<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_subject', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('class_id');
            $table->uuid('subject_id');
            $table->integer('coefficient')->default(1)->comment('Coefficient de la matière dans la classe');
            $table->smallInteger('status')->default(0)->comment('0: Inactive, 1: Active');
            $table->smallInteger('is_delete')->default(0)->comment('0: actif, 1: supprimé');
            $table->uuid('created_by')->nullable();
            $table->timestamps();

            $table->foreign('class_id')->references('id')->on('class')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subject')->onDelete('cascade');

            $table->unique(['class_id', 'subject_id'], 'class_subject_class_subject_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_subject');
    }
};
