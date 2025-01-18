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
            $table->string('last_name')->nullable();
            $table->string('admission_number')->nullable();
            $table->string('roll_number')->nullable();
            $table->Integer('class_id')->unsigned()->foreign('class_id')->references('id')->on('class')->onDelete('cascade');
            $table->string('gender')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('caste')->nullable();
            $table->string('religion')->nullable();
            $table->string('mobile_number')->nullable();
            $table->date('admission_date')->nullable();
            $table->string('profile_picture')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('height')->nullable();
            $table->string('weight')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0: Inactive, 1: Active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'last_name',
                'admission_number',
                'roll_number',
                'class_id',
                'gender',
                'date_of_birth',
                'caste',
                'religion',
                'mobile_number',
                'admission_date',
                'profile_picture',
                'blood_group',
                'height',
                'weight',
                'status',
            ]);
        });
    }
};
