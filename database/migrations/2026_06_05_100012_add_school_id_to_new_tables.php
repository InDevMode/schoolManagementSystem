<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Prépare le multi-tenant en ajoutant school_id nullable sur toutes les nouvelles tables.
 * En mono-tenant, school_id = null (on utilise settings.id = 1).
 * En multi-tenant, school_id discrimine les données par école.
 */
return new class extends Migration {
    public function up(): void
    {
        // evaluations
        if (Schema::hasTable('evaluations') && !Schema::hasColumn('evaluations', 'school_id')) {
            Schema::table('evaluations', function (Blueprint $table) {
                $table->unsignedInteger('school_id')->nullable()->after('id')->comment('Multi-tenant prep');
                $table->index('school_id');
            });
        }

        // grades
        if (Schema::hasTable('grades') && !Schema::hasColumn('grades', 'school_id')) {
            Schema::table('grades', function (Blueprint $table) {
                $table->unsignedInteger('school_id')->nullable()->after('id')->comment('Multi-tenant prep');
            });
        }

        // bulletins
        if (Schema::hasTable('bulletins') && !Schema::hasColumn('bulletins', 'school_id')) {
            Schema::table('bulletins', function (Blueprint $table) {
                $table->unsignedInteger('school_id')->nullable()->after('id')->comment('Multi-tenant prep');
            });
        }

        // staff_events
        if (Schema::hasTable('staff_events') && !Schema::hasColumn('staff_events', 'school_id')) {
            Schema::table('staff_events', function (Blueprint $table) {
                $table->unsignedInteger('school_id')->nullable()->after('id')->comment('Multi-tenant prep');
            });
        }

        // leave_types
        if (Schema::hasTable('leave_types') && !Schema::hasColumn('leave_types', 'school_id')) {
            Schema::table('leave_types', function (Blueprint $table) {
                $table->unsignedInteger('school_id')->nullable()->after('id')->comment('Multi-tenant prep');
            });
        }
    }

    public function down(): void
    {
        foreach (['evaluations', 'grades', 'bulletins', 'staff_events', 'leave_types'] as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'school_id')) {
                Schema::table($table, fn(Blueprint $t) => $t->dropColumn('school_id'));
            }
        }
    }
};
