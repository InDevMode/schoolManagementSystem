<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * AddMissingPermissionsSeeder
 *
 * Ajoute les permissions manquantes pour les actions granulaires :
 *  - action.assign_classes.{create,edit,delete}
 *  - action.assign_subjects.{create,edit,delete}
 *  - action.timetable.manage
 *  - action.users.export
 *
 * Puis les assigne au rôle admin (qui doit avoir toutes les permissions hors RBAC).
 *
 * Usage : php artisan db:seed --class=AddMissingPermissionsSeeder
 */
class AddMissingPermissionsSeeder extends Seeder
{
    private const NEW_PERMISSIONS = [
        'action.assign_classes.create',
        'action.assign_classes.edit',
        'action.assign_classes.delete',
        'action.assign_subjects.create',
        'action.assign_subjects.edit',
        'action.assign_subjects.delete',
        'action.timetable.manage',
        'action.users.export',
    ];

    private const RBAC_PERMISSIONS = [
        'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
        'permissions.view', 'permissions.create', 'permissions.edit',
        'permissions.delete', 'permissions.assign',
    ];

    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Créer les permissions manquantes
        $created = 0;
        foreach (self::NEW_PERMISSIONS as $name) {
            [$perm, $wasCreated] = [
                Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']),
                false,
            ];
            if ($perm->wasRecentlyCreated) {
                $created++;
                $this->command->line("  + Créée : <info>{$name}</info>");
            } else {
                $this->command->line("  ~ Déjà existante : <comment>{$name}</comment>");
            }
        }
        $this->command->info("  ✅ {$created} nouvelle(s) permission(s) créée(s).");

        // 2. Assigner au rôle admin toutes les permissions (hors RBAC)
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $allPerms = Permission::where('guard_name', 'web')
                ->where('is_delete', 0)
                ->whereNotIn('name', self::RBAC_PERMISSIONS)
                ->pluck('name')
                ->toArray();

            $adminRole->syncPermissions($allPerms);
            $this->command->info("  ✅ Rôle admin re-synchronisé avec " . count($allPerms) . " permission(s).");
        } else {
            $this->command->warn("  ⚠️  Rôle 'admin' introuvable — synchronisation ignorée.");
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->command->info("  ✅ Cache des permissions vidé.");
    }
}
