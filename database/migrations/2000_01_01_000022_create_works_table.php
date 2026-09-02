<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('works', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('class_id')->nullable();
            $table->uuid('subject_id')->nullable();
            $table->date('work_date')->nullable();
            $table->date('submission_date')->nullable();
            $table->string('document_file')->nullable();
            $table->longText('description')->nullable();
            $table->smallInteger('is_delete')->default(0)->comment('0: actif, 1: supprimé');
            $table->timestamp('deleted_at')->nullable();
            $table->uuid('deleted_by')->nullable();
            $table->uuid('created_by')->nullable();
            $table->timestamps();

            $table->foreign('class_id')->references('id')->on('class')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subject')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('works');
    }
};
