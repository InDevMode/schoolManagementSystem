<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('marks_register', function (Blueprint $table) {
            $table->integer('passing_marks')->nullable();
            $table->integer('full_marks')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marks_register', function (Blueprint $table) {
            $table->dropColumn(['passing_marks', 'full_marks']);
        });
    }
};
