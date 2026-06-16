<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

/**
 * Ajoute la permission granulaire action.users.reset_password
 * pour le bouton "Réinitialiser MDP" dans la gestion des utilisateurs.
 *
 * Permissions utilisateurs complètes :
 *   - view.users.all              → voir la page gestion utilisateurs
 *   - action.users.edit           → modifier un utilisateur
 *   - action.users.reset_password → réinitialiser le mot de passe
 *   - action.users.delete         → supprimer un utilisateur
 */
return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $exists = DB::table('permissions')
            ->where('name', 'action.users.reset_password')
            ->where('guard_name', 'web')
            ->exists();

        if (! $exists) {
            DB::table('permissions')->insert([
                'name'       => 'action.users.reset_password',
                'guard_name' => 'web',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Assigner au rôle super_admin
        $permId = DB::table('permissions')
            ->where('name', 'action.users.reset_password')
            ->where('guard_name', 'web')
            ->value('id');

        $superAdminRole = DB::table('roles')
            ->where('name', 'super_admin')
            ->where('guard_name', 'web')
            ->first();

        if ($superAdminRole && $permId) {
            $alreadyLinked = DB::table('role_has_permissions')
                ->where('permission_id', $permId)
                ->where('role_id', $superAdminRole->id)
                ->exists();

            if (! $alreadyLinked) {
                DB::table('role_has_permissions')->insert([
                    'permission_id' => $permId,
                    'role_id'       => $superAdminRole->id,
                ]);
            }
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        $permId = DB::table('permissions')
            ->where('name', 'action.users.reset_password')
            ->where('guard_name', 'web')
            ->value('id');

        if ($permId) {
            DB::table('role_has_permissions')->where('permission_id', $permId)->delete();
            DB::table('model_has_permissions')->where('permission_id', $permId)->delete();
            DB::table('permissions')->where('id', $permId)->delete();
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
