<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SyncSuperAdminPermissions extends Command
{
    protected $signature = 'superadmin:sync-permissions';

    protected $description = 'Synchronise le rôle super_admin avec TOUTES les permissions en base (à relancer après chaque ajout de permission).';

    public function handle(): int
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $role = Role::where('name', 'super_admin')->where('guard_name', 'web')->first();

        if (! $role) {
            $this->error('Rôle super_admin introuvable. Lancez d\'abord : php artisan db:seed --class=SuperAdminSeeder');
            return self::FAILURE;
        }

        $total = Permission::where('guard_name', 'web')->count();

        $this->info("Synchronisation du rôle super_admin avec {$total} permission(s)…");

        $role->syncPermissions(Permission::where('guard_name', 'web')->get());

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->info("✅ Rôle super_admin synchronisé avec {$total} permission(s).");
        $this->line('   Le Super Admin a désormais accès à toutes les permissions du système.');

        return self::SUCCESS;
    }
}
