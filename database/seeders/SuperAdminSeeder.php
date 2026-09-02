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
    public const SUPER_ADMIN_ID = 'c0000000-cc00-4000-8000-000000000001';

    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $role = Role::firstOrCreate(
            ['name' => 'super_admin', 'guard_name' => 'web'],
            ['user_type' => 0, 'description' => 'Super administrateur — accès total']
        );
        $role->syncPermissions(Permission::where('guard_name', 'web')->get());

        $superAdmin = User::firstOrCreate(
            ['email' => 'schoolmanagementsystem00@gmail.com'],
            [
                'id'            => self::SUPER_ADMIN_ID,
                'name'          => 'Super',
                'last_name'     => 'Admin',
                'password'      => Hash::make('SuperAdmin@2025'),
                'user_type'     => 0,
                'status'        => 1,
                'is_delete'     => 0,
                'school_id'     => null,
                'gender'        => 'Male',
                'mobile_number' => '+229 97 00 00 00',
                'address'       => 'Cotonou, Bénin',
                'occupation'    => 'Administrateur Système',
            ]
        );

        if (!$superAdmin->hasRole('super_admin')) {
            $superAdmin->assignRole('super_admin');
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->command->info('  ✅ Super Admin : schoolmanagementsystem00@gmail.com / SuperAdmin@2025');
    }
}
