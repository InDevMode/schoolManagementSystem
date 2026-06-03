<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── Rôles ───────────────────────────────────────────────────────────
        Schema::table('roles', function (Blueprint $table) {
            $table->tinyInteger('is_delete')->default(0)
                  ->comment('0: actif, 1: supprimé (soft delete)')
                  ->after('description');
            $table->unsignedInteger('deleted_by')->nullable()->after('is_delete');
            $table->timestamp('deleted_at')->nullable()->after('deleted_by');
        });

        // ── Permissions ─────────────────────────────────────────────────────
        Schema::table('permissions', function (Blueprint $table) {
            $table->tinyInteger('is_delete')->default(0)
                  ->comment('0: actif, 1: supprimé (soft delete)')
                  ->after('guard_name');
            $table->unsignedInteger('deleted_by')->nullable()->after('is_delete');
            $table->timestamp('deleted_at')->nullable()->after('deleted_by');
        });
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn(['is_delete', 'deleted_by', 'deleted_at']);
        });
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn(['is_delete', 'deleted_by', 'deleted_at']);
        });
    }
};
