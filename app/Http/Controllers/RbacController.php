<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RbacController extends Controller
{
    // ══════════════════════════════════════════════════════════════════════════
    // ROLES
    // ══════════════════════════════════════════════════════════════════════════

    public function roleList()
    {
        $roles = Role::withCount('permissions')
            ->orderBy('name')
            ->get()
            ->map(fn($r) => [
                'id'                => $r->id,
                'name'              => $r->name,
                'guard_name'        => $r->guard_name,
                'user_type'         => $r->user_type,
                'description'       => $r->description,
                'permissions_count' => $r->permissions_count,
                'created_at'        => $r->created_at?->format('d/m/Y'),
            ]);

        // user_types déjà utilisés pour le formulaire
        $usedUserTypes = $roles->pluck('user_type')->filter()->values();

        return Inertia::render('SuperAdmin/Config/Roles', [
            'roles'         => $roles,
            'usedUserTypes' => $usedUserTypes,
        ]);
    }

    public function roleCreate(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:80|unique:roles,name',
            'user_type'   => [
                'required', 'integer', 'min:5',
                // Un user_type ne peut appartenir qu'à un seul rôle
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

        // Protéger les rôles système (user_type 0-4)
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

    public function roleDelete(int $id)
    {
        $role = Role::findOrFail($id);

        if ($role->user_type !== null && $role->user_type <= 4) {
            return back()->with('error', 'Les rôles système (user_type 0–4) ne peuvent pas être supprimés.');
        }

        try {
            $role->delete();
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
            return back()->with('success', 'Rôle supprimé avec succès.');
        } catch (\Exception $e) {
            Log::error('RBAC roleDelete: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la suppression du rôle.');
        }
    }

    // ══════════════════════════════════════════════════════════════════════════
    // PERMISSIONS
    // ══════════════════════════════════════════════════════════════════════════

    public function permissionList()
    {
        $permissions = Permission::orderBy('name')
            ->get()
            ->map(fn($p) => [
                'id'         => $p->id,
                'name'       => $p->name,
                'guard_name' => $p->guard_name,
                'module'     => explode('.', $p->name)[0],
                'created_at' => $p->created_at?->format('d/m/Y'),
            ]);

        // Grouper par module pour l'affichage
        $grouped = $permissions->groupBy('module')->map(fn($g) => $g->values())->toArray();

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

    public function permissionDelete(int $id)
    {
        $permission = Permission::findOrFail($id);

        try {
            $permission->delete();
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
            return back()->with('success', 'Permission supprimée avec succès.');
        } catch (\Exception $e) {
            Log::error('RBAC permissionDelete: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la suppression.');
        }
    }

    // ══════════════════════════════════════════════════════════════════════════
    // ATTRIBUTION DES PERMISSIONS AUX RÔLES
    // ══════════════════════════════════════════════════════════════════════════

    public function assignList()
    {
        $roles = Role::with('permissions')->orderBy('name')->get()->map(fn($r) => [
            'id'          => $r->id,
            'name'        => $r->name,
            'permissions' => $r->permissions->pluck('name')->toArray(),
        ]);

        $permissions = Permission::orderBy('name')->get()->map(fn($p) => [
            'id'     => $p->id,
            'name'   => $p->name,
            'module' => explode('.', $p->name)[0],
        ]);

        return Inertia::render('SuperAdmin/Config/AssignPermissions', [
            'roles'       => $roles,
            'permissions' => $permissions,
        ]);
    }

    public function assignSync(Request $request, int $roleId)
    {
        $request->validate([
            'permissions' => 'present|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $role = Role::findOrFail($roleId);

        // super_admin toujours tout permissions — on ne laisse pas modifier
        if ($role->name === 'super_admin') {
            return back()->with('error', 'Les permissions du super_admin ne peuvent pas être modifiées.');
        }

        try {
            $role->syncPermissions($request->permissions);
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
            return back()->with('success', "Permissions du rôle « {$role->name} » mises à jour.");
        } catch (\Exception $e) {
            Log::error('RBAC assignSync: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la mise à jour des permissions.');
        }
    }
}
