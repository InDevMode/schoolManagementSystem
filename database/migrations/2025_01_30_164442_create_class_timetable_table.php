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
        Schema::create('class_timetable', function (Blueprint $table) {
            $table->id();
            $table->Integer('class_id')->unsigned()->nullable()->foreign('class_id')->references('id')->on('class')->onDelete('cascade');
            $table->Integer('subject_id')->unsigned()->nullable()->foreign('subject_id')->references('id')->on('subject')->onDelete('cascade');
            $table->Integer('week_id')->unsigned()->nullable()->foreign('week_id')->references('id')->on('week')->onDelete('cascade');
            $table->string('start_time')->nullable();
            $table->string('end_time')->nullable();
            $table->string('room_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_timetable');
    }
};
