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
        Schema::create('class_teacher', function (Blueprint $table) {
            $table->id();
            $table->Integer('class_id')->unsigned()->nullable()->foreign('class_id')->references('id')->on('class')->onDelete('cascade');
            $table->Integer('teacher_id')->unsigned()->nullable()->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade');
            $table->Integer('created_by')->unsigned()->nullable()->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->tinyInteger('status')->default(0)->comment('0: Inactive, 1: Active');;
            $table->tinyInteger('is_delete')->default(0)->comment('0: isntDeleted, 1: Deleted');;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_teacher');
    }
};
