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
            $table->string('last_name')->nullable()->after('email');
            $table->text('address')->nullable();
            $table->string('admission_number')->nullable();
            $table->date('admission_date')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('caste')->nullable();
            $table->Integer('class_id')->unsigned()->nullable()->foreign('class_id')->references('id')->on('class')->onDelete('cascade');
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable();
            $table->string('height')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('occupation')->nullable();
            $table->Integer('parent_id')->unsigned()->nullable()->foreign('parent_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('profile_picture')->nullable();
            $table->string('religion')->nullable();
            $table->string('roll_number')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0: Inactive, 1: Active');
            $table->string('weight')->nullable();
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
                'address',
                'admission_date',
                'admission_number',
                'blood_group',
                'caste',
                'class_id',
                'date_of_birth',
                'gender',
                'height',
                'mobile_number',
                'occupation',
                'parent_id',
                'profile_picture',
                'religion',
                'roll_number',
                'status',
                'weight',
            ]);
        });
    }
};
