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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->Integer('exam_id')->unsigned()->nullable()->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade');
            $table->Integer('class_id')->unsigned()->nullable()->foreign('class_id')->references('id')->on('class')->onDelete('cascade');
            $table->Integer('subject_id')->unsigned()->nullable()->foreign('subject_id')->references('id')->on('subject')->onDelete('cascade');
            $table->date('exam_date')->nullable();
            $table->string('start_time')->nullable();
            $table->string('end_time')->nullable();
            $table->string('room_number')->nullable();
            $table->string('full_marks')->nullable();
            $table->string('passing_marks')->nullable();
            $table->Integer('created_by')->unsigned()->nullable()->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->tinyInteger('is_delete')->default(0)->comment('0: isntDeleted, 1: Deleted');;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
