<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Ajoute les permissions manquantes pour la gestion globale des utilisateurs
     * et s'assure qu'elles sont assignées au rôle super_admin et au rôle admin.
     */
    public function up(): void
    {
        $now = now();

        $newPerms = [
            'view.users.all',      // Accéder à la page /superadmin/users
            'action.users.edit',   // Modifier un utilisateur depuis cette page
            'action.users.delete', // Supprimer un utilisateur depuis cette page
        ];

        foreach ($newPerms as $name) {
            // Insérer seulement si elle n'existe pas déjà
            $exists = DB::table('permissions')
                ->where('name', $name)
                ->where('guard_name', 'web')
                ->exists();

            if (! $exists) {
                DB::table('permissions')->insert([
                    'name'       => $name,
                    'guard_name' => 'web',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // Vider le cache Spatie
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Récupérer les IDs des nouvelles permissions
        $permIds = DB::table('permissions')
            ->whereIn('name', $newPerms)
            ->where('guard_name', 'web')
            ->pluck('id');

        // Assigner au rôle super_admin (s'il existe)
        $superAdminRole = DB::table('roles')
            ->where('name', 'super_admin')
            ->where('guard_name', 'web')
            ->first();

        if ($superAdminRole) {
            foreach ($permIds as $permId) {
                $exists = DB::table('role_has_permissions')
                    ->where('permission_id', $permId)
                    ->where('role_id', $superAdminRole->id)
                    ->exists();
                if (! $exists) {
                    DB::table('role_has_permissions')->insert([
                        'permission_id' => $permId,
                        'role_id'       => $superAdminRole->id,
                    ]);
                }
            }
        }

        // Assigner au rôle admin également (pour qu'il puisse recevoir ces permissions)
        $adminRole = DB::table('roles')
            ->where('name', 'admin')
            ->where('guard_name', 'web')
            ->first();

        if ($adminRole) {
            // On n'assigne PAS automatiquement à admin — le super_admin devra
            // les attribuer manuellement via l'interface RBAC pour chaque utilisateur.
            // Mais on s'assure que le rôle admin peut les avoir (elles existent en base).
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        $perms = ['view.users.all', 'action.users.edit', 'action.users.delete'];

        $permIds = DB::table('permissions')
            ->whereIn('name', $perms)
            ->where('guard_name', 'web')
            ->pluck('id');

        // Supprimer les liaisons rôle ↔ permission
        DB::table('role_has_permissions')
            ->whereIn('permission_id', $permIds)
            ->delete();

        // Supprimer les liaisons user ↔ permission directes
        DB::table('model_has_permissions')
            ->whereIn('permission_id', $permIds)
            ->delete();

        // Supprimer les permissions elles-mêmes
        DB::table('permissions')
            ->whereIn('name', $perms)
            ->where('guard_name', 'web')
            ->delete();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
