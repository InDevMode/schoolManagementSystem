<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->foreignId('period_id')->unsigned()->nullable()->foreign('period_id')->references('id')->on('periods')->onDelete('cascade')->after('name');
            $table->date('start_date')->nullable()->after('period_id');
            $table->date('end_date')->nullable()->after('start_date');
            $table->enum('status', ['planned', 'in_progress', 'completed', 'graded'])->default('planned')->after('end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn(['period_id', 'start_date', 'end_date', 'status']);
        });
    }
};
