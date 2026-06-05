<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Crée les tables avec les bons types de FK (unsignedBigInteger) compatibles
 * avec les tables Laravel qui utilisent $table->id() = BIGINT UNSIGNED.
 *
 * Remplace les migrations 100002 à 100006 qui avaient unsignedInteger (INT).
 */
return new class extends Migration {
    public function up(): void
    {
        // ── evaluations ─────────────────────────────────────────────────────
        if (!Schema::hasTable('evaluations')) {
            Schema::create('evaluations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('school_id')->nullable()->comment('Multi-tenant prep');
                $table->unsignedBigInteger('exam_id')->nullable();
                $table->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade');
                $table->unsignedBigInteger('class_id')->nullable();
                $table->foreign('class_id')->references('id')->on('class')->onDelete('cascade');
                $table->unsignedBigInteger('subject_id')->nullable();
                $table->foreign('subject_id')->references('id')->on('subject')->onDelete('cascade');
                $table->unsignedBigInteger('teacher_id')->nullable();
                $table->foreign('teacher_id')->references('id')->on('users')->onDelete('set null');
                $table->enum('type', ['interrogation', 'devoir_surveille', 'travail_maison', 'examen_blanc'])->default('interrogation');
                $table->unsignedTinyInteger('coefficient')->default(1);
                $table->decimal('max_score', 5, 2)->default(20.00);
                $table->date('eval_date')->nullable();
                $table->string('title')->nullable();
                $table->enum('status', ['draft', 'open', 'closed', 'validated'])->default('draft');
                $table->unsignedBigInteger('period_id')->nullable();
                $table->foreign('period_id')->references('id')->on('periods')->onDelete('set null');
                $table->unsignedBigInteger('created_by')->nullable();
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
                $table->tinyInteger('is_delete')->default(0);
                $table->timestamps();
                $table->index(['school_id', 'class_id', 'period_id']);
            });
        }

        // ── grades ───────────────────────────────────────────────────────────
        if (!Schema::hasTable('grades')) {
            Schema::create('grades', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('school_id')->nullable();
                $table->unsignedBigInteger('student_id');
                $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
                $table->unsignedBigInteger('evaluation_id');
                $table->foreign('evaluation_id')->references('id')->on('evaluations')->onDelete('cascade');
                $table->decimal('score', 5, 2)->nullable();
                $table->unsignedBigInteger('teacher_id')->nullable();
                $table->foreign('teacher_id')->references('id')->on('users')->onDelete('set null');
                $table->boolean('validated')->default(false);
                $table->unsignedBigInteger('validated_by')->nullable();
                $table->foreign('validated_by')->references('id')->on('users')->onDelete('set null');
                $table->timestamp('validated_at')->nullable();
                $table->string('observation')->nullable();
                $table->tinyInteger('is_delete')->default(0);
                $table->timestamps();
                $table->unique(['student_id', 'evaluation_id']);
            });
        }

        // ── bulletins ────────────────────────────────────────────────────────
        if (!Schema::hasTable('bulletins')) {
            Schema::create('bulletins', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('school_id')->nullable();
                $table->unsignedBigInteger('student_id');
                $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
                $table->unsignedBigInteger('period_id');
                $table->foreign('period_id')->references('id')->on('periods')->onDelete('cascade');
                $table->decimal('average', 5, 2)->nullable();
                $table->unsignedSmallInteger('rank')->nullable();
                $table->unsignedSmallInteger('total_students')->nullable();
                $table->decimal('class_success_rate', 5, 2)->nullable();
                $table->string('appreciation')->nullable();
                $table->text('teacher_comment')->nullable();
                $table->enum('status', ['draft', 'published'])->default('draft');
                $table->unsignedBigInteger('generated_by')->nullable();
                $table->foreign('generated_by')->references('id')->on('users')->onDelete('set null');
                $table->timestamp('generated_at')->nullable();
                $table->tinyInteger('is_delete')->default(0);
                $table->timestamps();
                $table->unique(['student_id', 'period_id']);
            });
        }

        // ── bulletin_subjects ────────────────────────────────────────────────
        if (!Schema::hasTable('bulletin_subjects')) {
            Schema::create('bulletin_subjects', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('bulletin_id');
                $table->foreign('bulletin_id')->references('id')->on('bulletins')->onDelete('cascade');
                $table->unsignedBigInteger('subject_id');
                $table->foreign('subject_id')->references('id')->on('subject')->onDelete('cascade');
                $table->unsignedTinyInteger('coefficient')->default(1);
                $table->decimal('average', 5, 2)->nullable();
                $table->decimal('weighted_points', 6, 2)->nullable();
                $table->string('appreciation')->nullable();
                $table->unsignedSmallInteger('rank')->nullable();
                $table->timestamps();
            });
        }

        // ── staff ────────────────────────────────────────────────────────────
        if (!Schema::hasTable('staff')) {
            Schema::create('staff', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->string('role')->default('teacher');
                $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
                $table->date('hire_date')->nullable();
                $table->date('end_date')->nullable();
                $table->string('employee_number')->nullable()->unique();
                $table->string('department')->nullable();
                $table->text('bio')->nullable();
                $table->unsignedBigInteger('school_id')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
                $table->tinyInteger('is_delete')->default(0);
                $table->timestamps();
                $table->index(['school_id', 'role']);
            });
        }

        // ── leave_types ──────────────────────────────────────────────────────
        if (!Schema::hasTable('leave_types')) {
            Schema::create('leave_types', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('school_id')->nullable();
                $table->string('name');
                $table->string('description')->nullable();
                $table->string('color', 7)->default('#6366f1');
                $table->tinyInteger('is_delete')->default(0);
                $table->timestamps();
            });
        }

        // ── staff_leaves ─────────────────────────────────────────────────────
        if (!Schema::hasTable('staff_leaves')) {
            Schema::create('staff_leaves', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('staff_id');
                $table->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');
                $table->unsignedBigInteger('leave_type_id');
                $table->foreign('leave_type_id')->references('id')->on('leave_types')->onDelete('cascade');
                $table->date('start_date');
                $table->date('end_date')->nullable();
                $table->text('reason')->nullable();
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
                $table->timestamp('approved_at')->nullable();
                $table->text('admin_note')->nullable();
                $table->tinyInteger('is_delete')->default(0);
                $table->timestamps();
            });
        }

        // ── staff_events ─────────────────────────────────────────────────────
        if (!Schema::hasTable('staff_events')) {
            Schema::create('staff_events', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('school_id')->nullable();
                $table->string('title');
                $table->text('description')->nullable();
                $table->date('event_date');
                $table->time('start_time')->nullable();
                $table->time('end_time')->nullable();
                $table->enum('event_type', ['academic', 'cultural', 'administrative', 'exam', 'ceremony', 'trip'])->default('academic');
                $table->string('location')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
                $table->tinyInteger('is_delete')->default(0);
                $table->timestamps();
            });
        }

        // ── deletion_logs ────────────────────────────────────────────────────
        if (!Schema::hasTable('deletion_logs')) {
            Schema::create('deletion_logs', function (Blueprint $table) {
                $table->id();
                $table->string('table_name');
                $table->unsignedBigInteger('record_id');
                $table->json('record_data')->nullable();
                $table->unsignedBigInteger('deleted_by')->nullable();
                $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
                $table->string('reason')->nullable();
                $table->timestamp('deleted_at');
                $table->index(['table_name', 'record_id']);
                $table->index('deleted_by');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('deletion_logs');
        Schema::dropIfExists('staff_events');
        Schema::dropIfExists('staff_leaves');
        Schema::dropIfExists('leave_types');
        Schema::dropIfExists('staff');
        Schema::dropIfExists('bulletin_subjects');
        Schema::dropIfExists('bulletins');
        Schema::dropIfExists('grades');
        Schema::dropIfExists('evaluations');
    }
};
