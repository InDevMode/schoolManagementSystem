<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ── 1. Rôle super_admin ────────────────────────────────────────────
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'super_admin', 'guard_name' => 'web'],
            ['user_type' => 0, 'description' => 'Super administrateur — accès total au système']
        );
        // Mise à jour si déjà existant sans ces infos
        Role::where('name', 'super_admin')
            ->whereNull('user_type')
            ->update(['user_type' => 0, 'description' => 'Super administrateur — accès total au système']);

        // Permissions RBAC exclusives au super_admin
        $rbacPermissions = [
            'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
            'permissions.view', 'permissions.create', 'permissions.edit', 'permissions.delete',
            'permissions.assign',
        ];

        foreach ($rbacPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Le super_admin doit toujours avoir TOUTES les permissions en base.
        // On recharge après avoir créé les permissions RBAC pour être certain
        // d'inclure celles créées par RolesAndPermissionsSeeder.
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $allPermissions = Permission::where('guard_name', 'web')->get();
        $superAdminRole->syncPermissions($allPermissions);

        $this->command->info("✅ Rôle super_admin synchronisé avec {$allPermissions->count()} permission(s).");

        // ── 2. Créer l'utilisateur super_admin ─────────────────────────────
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@sms.local'],
            [
                'name'      => 'Super',
                'last_name' => 'Admin',
                'email'     => 'superadmin@sms.local',
                'password'  => Hash::make('SuperAdmin@2025'),
                'user_type' => 0,
                'status'    => 1,
                'is_delete' => 0,
            ]
        );

        // Assign rôle
        if (! $superAdmin->hasRole('super_admin')) {
            $superAdmin->assignRole('super_admin');
        }

        $this->command->info('✅ Super Admin créé : superadmin@sms.local / SuperAdmin@2025');
        $this->command->info('⚠️  Changez ce mot de passe après la première connexion !');
    }
}
