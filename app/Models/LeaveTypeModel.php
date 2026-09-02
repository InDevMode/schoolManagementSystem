<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class LeaveTypeModel extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'leave_types';

    protected $fillable = ['name', 'description', 'color', 'school_id'];

    protected $hidden = ['is_delete'];

    // ── Helpers ───────────────────────────────────────────────────────────────

    public static function getSingle(string $id): ?self
    {
        return self::find($id);
    }

    /**
     * Retourne tous les types de congés visibles pour l'utilisateur connecté.
     * - Super admin : voit tout.
     * - Admin école : voit uniquement les types de son école.
     */
    public static function getAll()
    {
        $user         = Auth::user();
        $isSuperAdmin = $user && (int) $user->user_type === 0;

        $q = self::where('is_delete', 0)->orderBy('name');

        if (! $isSuperAdmin && $user) {
            $q->where('school_id', $user->school_id);
        }

        return $q->get();
    }

    /**
     * Liste paginée avec scoping école.
     */
    public static function getAllPaginated(int $perPage)
    {
        $user         = Auth::user();
        $isSuperAdmin = $user && (int) $user->user_type === 0;

        $q = self::where('is_delete', 0)->orderBy('name');

        if (! $isSuperAdmin && $user) {
            $q->where('school_id', $user->school_id);
        }

        return $q->paginate($perPage);
    }

    /**
     * Vérifie l'unicité du nom dans l'école lors de la création.
     */
    public static function getNameSingle(string $name, ?int $schoolId = null): ?self
    {
        $q = self::where('name', $name)->where('is_delete', 0);

        if ($schoolId !== null) {
            $q->where('school_id', $schoolId);
        }

        return $q->first();
    }

    /**
     * Vérifie l'unicité du nom dans l'école lors de la modification.
     */
    public static function checkNameSingle(string $name, string $id, ?int $schoolId = null): ?self
    {
        $q = self::where('name', $name)->where('id', '!=', $id)->where('is_delete', 0);

        if ($schoolId !== null) {
            $q->where('school_id', $schoolId);
        }

        return $q->first();
    }
}
