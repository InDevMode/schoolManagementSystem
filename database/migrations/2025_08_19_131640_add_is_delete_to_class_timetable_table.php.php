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
        Schema::table('class_timetable', function (Blueprint $table) {
            $table->tinyInteger('is_delete')
                ->default(0)
                ->comment('0: isntDeleted, 1: Deleted')
                ->after('room_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('class_timetable', function (Blueprint $table) {
            $table->dropColumn('is_delete');
        });
    }
};
