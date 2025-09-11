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
        Schema::table('settings', function (Blueprint $table) {
            $table->string('school_name')->after('id')->nullable();
            $table->string('address')->after('school_name')->nullable();
            $table->string('phone')->after('address')->nullable();
            $table->string('email')->after('phone')->nullable();
            $table->string('uai_number')->after('email')->nullable();
            $table->string('school_type')->after('uai_number')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0: Inactive, 1: Active')->after('logo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'school_name',
                'address',
                'phone',
                'email',
                'uai_number',
                'school_type',
                'status',
            ]);
        });
    }
};
