<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SyncSuperAdminPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $role = Role::where('name', 'super_admin')->firstOrFail();
        $all  = Permission::where('guard_name', 'web')->get();

        $role->syncPermissions($all);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info("✅ {$all->count()} permission(s) assignées au rôle super_admin.");
    }
}
