<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tables Spatie Laravel-Permission — version UUID.
 * model_morph_key est uuid (string) pour pointer vers users.id (uuid).
 * permissions et roles gardent bigIncrements (Spatie les gère en interne).
 */
return new class extends Migration
{
    public function up(): void
    {
        $tableNames  = config('permission.table_names');
        $columnNames = config('permission.column_names');
        $pivotRole   = $columnNames['role_pivot_key'] ?? 'role_id';
        $pivotPerm   = $columnNames['permission_pivot_key'] ?? 'permission_id';

        throw_if(
            empty($tableNames),
            Exception::class,
            'Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.'
        );

        // ── permissions ──────────────────────────────────────────────────────
        Schema::create($tableNames['permissions'], function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name');
            $table->smallInteger('is_delete')->default(0)->comment('0: actif, 1: supprimé');
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });

        // ── roles ────────────────────────────────────────────────────────────
        Schema::create($tableNames['roles'], function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name');
            $table->smallInteger('user_type')->nullable()
                  ->comment('0:super_admin 1:admin 2:teacher 3:student 4:parent 5+:custom');
            $table->string('description')->nullable();
            $table->smallInteger('is_delete')->default(0)->comment('0: actif, 1: supprimé');
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });

        // ── model_has_permissions ────────────────────────────────────────────
        Schema::create($tableNames['model_has_permissions'], function (Blueprint $table) use (
            $tableNames, $columnNames, $pivotPerm
        ) {
            $table->unsignedBigInteger($pivotPerm);
            $table->string('model_type');
            // UUID pour pointer vers users.id
            $table->uuid($columnNames['model_morph_key']);
            $table->index(
                [$columnNames['model_morph_key'], 'model_type'],
                'model_has_permissions_model_id_model_type_index'
            );

            $table->foreign($pivotPerm)
                  ->references('id')->on($tableNames['permissions'])->onDelete('cascade');

            $table->primary(
                [$pivotPerm, $columnNames['model_morph_key'], 'model_type'],
                'model_has_permissions_permission_model_type_primary'
            );
        });

        // ── model_has_roles ──────────────────────────────────────────────────
        Schema::create($tableNames['model_has_roles'], function (Blueprint $table) use (
            $tableNames, $columnNames, $pivotRole
        ) {
            $table->unsignedBigInteger($pivotRole);
            $table->string('model_type');
            // UUID pour pointer vers users.id
            $table->uuid($columnNames['model_morph_key']);
            $table->index(
                [$columnNames['model_morph_key'], 'model_type'],
                'model_has_roles_model_id_model_type_index'
            );

            $table->foreign($pivotRole)
                  ->references('id')->on($tableNames['roles'])->onDelete('cascade');

            $table->primary(
                [$pivotRole, $columnNames['model_morph_key'], 'model_type'],
                'model_has_roles_role_model_type_primary'
            );
        });

        // ── role_has_permissions ─────────────────────────────────────────────
        Schema::create($tableNames['role_has_permissions'], function (Blueprint $table) use (
            $tableNames, $pivotRole, $pivotPerm
        ) {
            $table->unsignedBigInteger($pivotPerm);
            $table->unsignedBigInteger($pivotRole);

            $table->foreign($pivotPerm)
                  ->references('id')->on($tableNames['permissions'])->onDelete('cascade');
            $table->foreign($pivotRole)
                  ->references('id')->on($tableNames['roles'])->onDelete('cascade');

            $table->primary(
                [$pivotPerm, $pivotRole],
                'role_has_permissions_permission_id_role_id_primary'
            );
        });

        app('cache')
            ->store(config('permission.cache.store') !== 'default'
                ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));
    }

    public function down(): void
    {
        $tableNames = config('permission.table_names');

        throw_if(
            empty($tableNames),
            Exception::class,
            'Error: config/permission.php not found. Drop the tables manually.'
        );

        Schema::drop($tableNames['role_has_permissions']);
        Schema::drop($tableNames['model_has_roles']);
        Schema::drop($tableNames['model_has_permissions']);
        Schema::drop($tableNames['roles']);
        Schema::drop($tableNames['permissions']);
    }
};
