<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('homework', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('work_id')->nullable();
            $table->uuid('student_id')->nullable();
            $table->string('document_file')->nullable();
            $table->longText('description')->nullable();
            $table->string('status')->default('hold')
                  ->comment('hold, submitted, done, processed, resolved');
            $table->smallInteger('is_delete')->default(0)->comment('0: actif, 1: supprimé');
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
