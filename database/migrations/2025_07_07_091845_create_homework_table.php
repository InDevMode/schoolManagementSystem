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
        Schema::create('homework', function (Blueprint $table) {
            $table->id();
            $table->Integer('work_id')->unsigned()->nullable()->foreign('work_id')->references('id')->on('works')->onDelete('cascade');
            $table->Integer('student_id')->unsigned()->nullable()->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('document_file')->nullable();
            $table->longText('description')->nullable();
            $table->tinyInteger('is_delete')->default(0)->comment('0: isntDeleted, 1: Deleted');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homework');
    }
};
