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
        if (Schema::hasTable('marks_register')) {
            return; // Table déjà présente en base, rien à faire
        }

        Schema::create('marks_register', function (Blueprint $table) {
            $table->id();
            $table->Integer('student_id')->unsigned()->nullable()->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->Integer('exam_id')->unsigned()->nullable()->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade');
            $table->Integer('class_id')->unsigned()->nullable()->foreign('class_id')->references('id')->on('class')->onDelete('cascade');
            $table->Integer('subject_id')->unsigned()->nullable()->foreign('subject_id')->references('id')->on('subject')->onDelete('cascade');
            $table->decimal('class_work', 5, 2)->nullable();
            $table->decimal('home_work', 5, 2)->nullable();
            $table->decimal('exam_work', 5, 2)->nullable();
            $table->decimal('test_work', 5, 2)->nullable();
            $table->decimal('quiz_1', 5, 2)->nullable();
            $table->decimal('quiz_2', 5, 2)->nullable();
            $table->decimal('quiz_3', 5, 2)->nullable();
            $table->decimal('quiz_4', 5, 2)->nullable();
            $table->decimal('quiz_5', 5, 2)->nullable();
            $table->decimal('assignment_1', 5, 2)->nullable();
            $table->decimal('assignment_2', 5, 2)->nullable();
            $table->decimal('assignment_3', 5, 2)->nullable();
            $table->decimal('passing_marks', 6, 2)->nullable();
            $table->decimal('full_marks', 6, 2)->nullable();
            $table->decimal('total_marks', 6, 2)->nullable();
            $table->decimal('quiz_average', 5, 2)->nullable();
            $table->decimal('assignment_average', 5, 2)->nullable();
            $table->decimal('coefficient', 3, 1)->default(1);
            $table->Integer('created_by')->unsigned()->nullable()->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->tinyInteger('is_delete')->default(0)->comment('0: isntDeleted, 1: Deleted');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marks_register');
    }
};
