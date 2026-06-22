<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use App\Notifications\PermissionsChangedNotification;
use App\Notifications\RoleChangedNotification;

class RbacController extends Controller
{
    // ══════════════════════════════════════════════════════════════════════
    // RÔLES
    // ══════════════════════════════════════════════════════════════════════

    public function roleList()
    {
        $allRoles = Role::withCount('permissions')
            ->orderBy('user_type')
            ->orderBy('name')
            ->get()
            ->map(fn($r) => [
                'id'                => $r->id,
                'name'              => $r->name,
                'guard_name'        => $r->guard_name,
                'user_type'         => $r->user_type,
                'description'       => $r->description,
                'is_delete'         => (int) ($r->is_delete ?? 0),
                'deleted_at'        => $r->deleted_at
                    ? \Carbon\Carbon::parse($r->deleted_at)->format('d-m-Y H:i')
                    : null,
                'permissions_count' => $r->permissions_count,
                'created_at'        => $r->created_at?->format('d-m-Y'),
            ]);

        $usedUserTypes = Role::whereNotNull('user_type')->pluck('user_type')->values();

        return Inertia::render('SuperAdmin/Config/Roles', [
            'roles'         => $allRoles,
            'usedUserTypes' => $usedUserTypes,
        ]);
    }

    public function roleCreate(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:80|unique:roles,name',
            'user_type'   => [
                'required', 'integer', 'min:5',
                function ($attr, $value, $fail) {
                    if (Role::where('user_type', $value)->exists()) {
                        $fail("Le user_type {$value} est déjà utilisé par un autre rôle.");
                    }
                },
            ],
            'description' => 'nullable|string|max:255',
        ]);

        try {
            Role::create([
                'name'        => trim($request->name),
                'guard_name'  => 'web',
                'user_type'   => (int) $request->user_type,
                'description' => $request->description ? trim($request->description) : null,
            ]);
            return back()->with('success', "Rôle « {$request->name} » (user_type={$request->user_type}) créé avec succès.");
        } catch (\Exception $e) {
            Log::error('RBAC roleCreate: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la création du rôle.');
        }
    }

    public function roleUpdate(Request $request, int $id)
    {
        $role = Role::findOrFail($id);

        if ($role->user_type !== null && $role->user_type <= 4) {
            return back()->with('error', 'Les rôles système (user_type 0–4) ne peuvent pas être modifiés.');
        }

        $request->validate([
            'name'        => "required|string|max:80|unique:roles,name,{$id}",
            'user_type'   => [
                'required', 'integer', 'min:5',
                function ($attr, $value, $fail) use ($id) {
                    if (Role::where('user_type', $value)->where('id', '!=', $id)->exists()) {
                        $fail("Le user_type {$value} est déjà utilisé par un autre rôle.");
                    }
                },
            ],
            'description' => 'nullable|string|max:255',
        ]);

        try {
            $role->update([
                'name'        => trim($request->name),
                'user_type'   => (int) $request->user_type,
                'description' => $request->description ? trim($request->description) : null,
            ]);
            return back()->with('success', 'Rôle modifié avec succès.');
        } catch (\Exception $e) {
            Log::error('RBAC roleUpdate: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la modification du rôle.');
        }
    }

    /** Soft-delete d'un rôle */
    public function roleDelete(int $id)
    {
        $role = Role::findOrFail($id);

        if ($role->user_type !== null && $role->user_type <= 4) {
            return back()->with('error', 'Les rôles système (user_type 0–4) ne peuvent pas être supprimés.');
        }

        try {
            DB::table('roles')->where('id', $id)->update([
                'is_delete'  => 1,
                'deleted_by' => Auth::id(),
                'deleted_at' => now(),
            ]);
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
            return back()->with('success', "Rôle « {$role->name} » supprimé (soft delete).");
        } catch (\Exception $e) {
            Log::error('RBAC roleDelete: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la suppression du rôle.');
        }
    }

    /** Restaurer un rôle supprimé */
    public function roleRestore(int $id)
    {
        DB::table('roles')->where('id', $id)->update([
            'is_delete'  => 0,
            'deleted_by' => null,
            'deleted_at' => null,
        ]);
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        return back()->with('success', 'Rôle restauré avec succès.');
    }

    // ══════════════════════════════════════════════════════════════════════
    // PERMISSIONS
    // ══════════════════════════════════════════════════════════════════════

    public function permissionList()
    {
        $permissions = Permission::orderBy('name')
            ->get()
            ->map(fn($p) => [
                'id'         => $p->id,
                'name'       => $p->name,
                'guard_name' => $p->guard_name,
                'module'     => explode('.', $p->name)[0],
                'is_delete'  => (int) ($p->is_delete ?? 0),
                'deleted_at' => $p->deleted_at
                    ? \Carbon\Carbon::parse($p->deleted_at)->format('d-m-Y H:i')
                    : null,
                'created_at' => $p->created_at?->format('d-m-Y'),
            ]);

        $grouped = $permissions
            ->groupBy('module')
            ->map(fn($g) => $g->values())
            ->toArray();

        return Inertia::render('SuperAdmin/Config/Permissions', [
            'permissions' => $permissions,
            'grouped'     => $grouped,
        ]);
    }

    public function permissionCreate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:permissions,name',
        ]);

        try {
            Permission::create(['name' => trim($request->name), 'guard_name' => 'web']);
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
            return back()->with('success', "Permission « {$request->name} » créée avec succès.");
        } catch (\Exception $e) {
            Log::error('RBAC permissionCreate: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la création de la permission.');
        }
    }

    public function permissionUpdate(Request $request, int $id)
    {
        $permission = Permission::findOrFail($id);

        $request->validate([
            'name' => "required|string|max:100|unique:permissions,name,{$id}",
        ]);

        try {
            $permission->update(['name' => trim($request->name)]);
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
            return back()->with('success', 'Permission modifiée avec succès.');
        } catch (\Exception $e) {
            Log::error('RBAC permissionUpdate: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la modification.');
        }
    }

    /** Soft-delete d'une permission */
    public function permissionDelete(int $id)
    {
        $permission = Permission::findOrFail($id);

        try {
            DB::table('permissions')->where('id', $id)->update([
                'is_delete'  => 1,
                'deleted_by' => Auth::id(),
                'deleted_at' => now(),
            ]);
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
            return back()->with('success', "Permission « {$permission->name} » supprimée (soft delete).");
        } catch (\Exception $e) {
            Log::error('RBAC permissionDelete: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la suppression.');
        }
    }

    /** Restaurer une permission supprimée */
    public function permissionRestore(int $id)
    {
        DB::table('permissions')->where('id', $id)->update([
            'is_delete'  => 0,
            'deleted_by' => null,
            'deleted_at' => null,
        ]);
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        return back()->with('success', 'Permission restaurée avec succès.');
    }

    // ══════════════════════════════════════════════════════════════════════
    // ATTRIBUTION — par rôle OU par utilisateur
    // ══════════════════════════════════════════════════════════════════════

    public function assignList()
    {
        // Tous les rôles (actifs)
        $roles = Role::where('is_delete', 0)
            ->with('permissions')
            ->orderBy('name')
            ->get()
            ->map(fn($r) => [
                'id'          => $r->id,
                'name'        => $r->name,
                'user_type'   => $r->user_type,
                'description' => $r->description,
                'permissions' => $r->permissions->where('is_delete', 0)->pluck('name')->toArray(),
            ]);

        // Tous les utilisateurs actifs (sauf super_admin user_type=0)
        $users = User::where('is_delete', 0)
            ->where('status', 1)
            ->where('user_type', '!=', 0)
            ->orderBy('name')
            ->get()
            ->map(function ($u) {
                // Permissions directes (hors rôle)
                $directPerms = $u->getDirectPermissions()
                    ->where('is_delete', 0)
                    ->pluck('name')
                    ->toArray();
                // Permissions héritées du rôle
                $rolePerms = $u->getPermissionsViaRoles()
                    ->where('is_delete', 0)
                    ->pluck('name')
                    ->toArray();

                return [
                    'id'               => $u->id,
                    'name'             => $u->name,
                    'last_name'        => $u->last_name,
                    'email'            => $u->email,
                    'user_type'        => $u->user_type,
                    'roles'            => $u->getRoleNames()->toArray(),
                    'direct_perms'     => $directPerms,
                    'role_perms'       => $rolePerms,
                    'all_perms'        => array_unique(array_merge($directPerms, $rolePerms)),
                    'profile_picture'  => $u->profile_picture,
                ];
            });

        // Toutes les permissions actives, groupées par module
        $permissions = Permission::where('is_delete', 0)
            ->orderBy('name')
            ->get()
            ->map(fn($p) => [
                'id'     => $p->id,
                'name'   => $p->name,
                'module' => explode('.', $p->name)[0],
            ]);

        return Inertia::render('SuperAdmin/Config/AssignPermissions', [
            'roles'       => $roles,
            'users'       => $users,
            'permissions' => $permissions,
        ]);
    }

    /** Sync permissions d'un rôle */
    public function assignSync(Request $request, int $roleId)
    {
        $request->validate([
            'permissions'   => 'present|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $role = Role::findOrFail($roleId);

        if ($role->name === 'super_admin') {
            return back()->with('error', 'Les permissions du super_admin ne peuvent pas être modifiées.');
        }

        try {
            $role->syncPermissions($request->permissions);
            app()[PermissionRegistrar::class]->forgetCachedPermissions();

            // Invalider le cache et notifier tous les utilisateurs du rôle (sauf le super_admin connecté)
            try {
                $currentUserId = Auth::id();
                $usersWithRole  = User::role($role->name)->get();

                foreach ($usersWithRole as $u) {
                    // Forcer le rechargement des permissions au prochain hit
                    Cache::put("perm_refreshed_{$u->id}", true, now()->addHours(2));

                    // Ne pas notifier le super_admin qui effectue le changement
                    if ($u->id === $currentUserId) {
                        continue;
                    }

                    // Anti-doublon : une seule notification non lue de ce type par fenêtre de 5 min
                    $alreadyNotified = $u->unreadNotifications()
                        ->where('type', PermissionsChangedNotification::class)
                        ->where('created_at', '>=', now()->subMinutes(5))
                        ->exists();

                    if (!$alreadyNotified) {
                        $u->notify(new PermissionsChangedNotification());
                    }
                }
            } catch (\Exception $notifEx) {
                Log::warning('RBAC assignSync notification: ' . $notifEx->getMessage());
            }

            return back()->with('success', "Permissions du rôle « {$role->name} » mises à jour.");
        } catch (\Exception $e) {
            Log::error('RBAC assignSync: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la mise à jour des permissions.');
        }
    }

    /** Sync permissions DIRECTES d'un utilisateur (en plus des permissions du rôle) */
    public function assignUserSync(Request $request, int $userId)
    {
        $request->validate([
            'permissions'   => 'present|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $user = User::findOrFail($userId);

        if ($user->user_type === 0) {
            return back()->with('error', 'Les permissions du super_admin ne peuvent pas être modifiées ici.');
        }

        try {
            // syncPermissions sur l'utilisateur = remplace les permissions DIRECTES
            // (ne touche pas aux permissions héritées via les rôles)
            $user->syncPermissions($request->permissions);
            app()[PermissionRegistrar::class]->forgetCachedPermissions();

            // Forcer le rechargement des permissions au prochain hit
            Cache::put("perm_refreshed_{$user->id}", true, now()->addHours(2));

            // Notifier l'utilisateur ciblé (anti-doublon : une seule notif non lue par fenêtre de 5 min)
            try {
                $alreadyNotified = $user->unreadNotifications()
                    ->where('type', PermissionsChangedNotification::class)
                    ->where('created_at', '>=', now()->subMinutes(5))
                    ->exists();

                if (!$alreadyNotified) {
                    $user->notify(new PermissionsChangedNotification());
                }
            } catch (\Exception $notifEx) {
                Log::warning('RBAC assignUserSync notification: ' . $notifEx->getMessage());
            }

            return back()->with('success', "Permissions directes de {$user->name} {$user->last_name} mises à jour.");
        } catch (\Exception $e) {
            Log::error('RBAC assignUserSync: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la mise à jour des permissions utilisateur.');
        }
    }
}
