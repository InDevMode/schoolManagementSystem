<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bulletin_subjects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('bulletin_id');
            $table->uuid('subject_id');
            $table->smallInteger('coefficient')->default(1);
            $table->decimal('average', 5, 2)->nullable()->comment('Moyenne de la matière /20');
            $table->decimal('weighted_points', 6, 2)->nullable()->comment('average × coefficient');
            $table->string('appreciation')->nullable();
            $table->smallInteger('rank')->nullable()->comment('Rang dans la matière');
            $table->timestamps();

            $table->foreign('bulletin_id')->references('id')->on('bulletins')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subject')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bulletin_subjects');
    }
};
