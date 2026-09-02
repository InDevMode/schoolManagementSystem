<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('school_id')->nullable()->comment('Multi-tenant');
            $table->string('role')->default('teacher')
                  ->comment('director, teacher, accountant, secretary...');
            $table->string('status')->default('active')
                  ->comment('active, inactive, suspended');
            $table->date('hire_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('employee_number')->nullable()->unique();
            $table->string('department')->nullable();
            $table->text('bio')->nullable();
            $table->smallInteger('is_delete')->default(0)->comment('0: actif, 1: supprimé');
            $table->uuid('created_by')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');

            $table->index(['school_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
