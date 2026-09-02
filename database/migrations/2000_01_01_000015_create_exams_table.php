<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->nullable();
            $table->uuid('period_id')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status')->default('planned')
                  ->comment('planned, in_progress, completed, graded');
            $table->smallInteger('is_delete')->default(0)->comment('0: actif, 1: supprimé');
            $table->uuid('created_by')->nullable();
            $table->timestamps();

            $table->foreign('period_id')->references('id')->on('periods')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
