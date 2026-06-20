<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * SuperAdminSeeder — Crée le compte super administrateur global.
 *
 * Connexion : superadmin@sms.local / SuperAdmin@2025
 * ⚠️  Changez ce mot de passe après la première connexion !
 *
 * Dépendances : RolesAndPermissionsSeeder (rôle super_admin doit exister)
 */
class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ── 1. S'assurer que le rôle super_admin existe ───────────────────────
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'super_admin', 'guard_name' => 'web'],
            ['user_type' => 0, 'description' => 'Super administrateur — accès total au système']
        );

        // Lui donner TOUTES les permissions existantes
        $allPermissions = Permission::where('guard_name', 'web')->get();
        $superAdminRole->syncPermissions($allPermissions);

        // ── 2. Créer l'utilisateur super_admin ────────────────────────────────
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@sms.local'],
            [
                'name'      => 'Super',
                'last_name' => 'Admin',
                'password'  => Hash::make('SuperAdmin@2025'),
                'user_type' => 0,
                'status'    => 1,
                'is_delete' => 0,
                'school_id' => null, // Super admin = global, pas d'école
            ]
        );

        if (! $superAdmin->hasRole('super_admin')) {
            $superAdmin->assignRole('super_admin');
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('  ✅ Super Admin : superadmin@sms.local / SuperAdmin@2025');
        $this->command->warn('  ⚠️  Changez ce mot de passe après la première connexion !');
    }
}
