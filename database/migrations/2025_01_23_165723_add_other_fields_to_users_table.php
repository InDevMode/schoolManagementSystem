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
        Schema::table('users', function (Blueprint $table) {
            $table->text('marital_status')->nullable();
            $table->text('qualification')->nullable();
            $table->string('permanent_address')->nullable();
            $table->string('note')->nullable();
            $table->string('work_experience')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'marital_status',
                'qualification',
                'permanent_address',
                'note',
                'work_experience',
            ]);
        });
    }
};
