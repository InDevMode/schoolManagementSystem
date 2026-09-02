<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * EventTypeCustomModel — Types d'événements personnalisés par école.
 *
 * Chaque administrateur d'école peut créer ses propres types d'événements
 * (ex: "Journée portes ouvertes", "Réunion parents-profs", etc.).
 * Ces types sont isolés par school_id et ne sont visibles que dans leur école.
 */
class EventTypeCustomModel extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'event_type_customs';

    protected $fillable = [
        'school_id',
        'name',
        'color',
        'description',
        'created_by',
    ];

    protected $hidden = ['is_delete'];

    // ── Helpers ───────────────────────────────────────────────────────────────

    public static function getSingle(string $id): ?self
    {
        return self::find($id);
    }

    /**
     * Types disponibles pour l'école de l'utilisateur connecté.
     * Le super admin voit tout.
     */
    public static function getForCurrentSchool()
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
     * Types d'une école spécifique (usage contrôleur).
     */
    public static function getBySchool(int $schoolId)
    {
        return self::where('school_id', $schoolId)
            ->where('is_delete', 0)
            ->orderBy('name')
            ->get();
    }

    /**
     * Liste paginée pour la page de gestion des types.
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
     * Vérification d'unicité du nom dans l'école (pour validation).
     */
    public static function getByNameAndSchool(string $name, int $schoolId, ?int $excludeId = null): ?self
    {
        $q = self::where('name', $name)
            ->where('school_id', $schoolId)
            ->where('is_delete', 0);

        if ($excludeId) {
            $q->where('id', '!=', $excludeId);
        }

        return $q->first();
    }
}
