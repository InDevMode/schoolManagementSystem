<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * StaffModel — Extension RH des utilisateurs.
 *
 * Le staff est un User (user_id obligatoire).
 * Directeur, comptable, surveillant, etc. = users qui se connectent
 * avec un rôle Spatie approprié + une fiche RH dans cette table.
 */
class StaffModel extends Model
{
    use HasFactory;

    protected $table = 'staff';

    protected $fillable = [
        'user_id', 'role', 'status', 'hire_date', 'end_date',
        'employee_number', 'department', 'bio', 'school_id', 'created_by',
    ];

    protected $hidden = ['is_delete'];

    /** Rôles RH disponibles */
    public static array $roles = [
        'teacher'    => 'Professeur',
        'director'   => 'Directeur',
        'accountant' => 'Comptable',
        'supervisor' => 'Surveillant',
        'secretary'  => 'Secrétaire',
        'librarian'  => 'Bibliothécaire',
        'other'      => 'Autre',
    ];

    public static function getSingle(int $id): ?self
    {
        return self::find($id);
    }

    /**
     * Vérifie si un user a déjà une fiche staff
     */
    public static function getByUserId(int $user_id): ?self
    {
        return self::where('user_id', $user_id)->where('is_delete', 0)->first();
    }

    /**
     * Liste paginée avec les infos du user associé
     */
    public static function getAll(int $perPage)
    {
        $currentUser  = Auth::user();
        $isSuperAdmin = $currentUser && (int) $currentUser->user_type === 0;

        $q = self::select(
            'staff.*',
            'users.name',
            'users.last_name',
            'users.email',
            'users.profile_picture',
            'users.mobile_number',
            'users.gender',
            'users.status as user_status',
        )
            ->join('users', 'users.id', '=', 'staff.user_id')
            ->where('staff.is_delete', 0);

        // Scoping multi-tenant : un admin ne voit que le personnel de son école
        if (! $isSuperAdmin && $currentUser) {
            $q->where('staff.school_id', $currentUser->school_id);
        }

        if ($v = Request::get('role'))   $q->where('staff.role', $v);
        if ($v = Request::get('status')) $q->where('staff.status', $v);
        if ($v = Request::get('search')) {
            $q->where(function ($sub) use ($v) {
                $sub->where('users.name', 'like', "%$v%")
                    ->orWhere('users.last_name', 'like', "%$v%")
                    ->orWhere('users.email', 'like', "%$v%")
                    ->orWhere('staff.employee_number', 'like', "%$v%");
            });
        }

        return $q->orderBy('users.last_name')->paginate($perPage);
    }

    /**
     * Liste pour les selects (congés, événements, etc.) — scopée par école.
     */
    public static function getAllActive()
    {
        $user         = Auth::user();
        $isSuperAdmin = $user && (int) $user->user_type === 0;

        $q = self::select('staff.*', 'users.name', 'users.last_name', 'users.email', 'users.profile_picture')
            ->join('users', 'users.id', '=', 'staff.user_id')
            ->where('staff.is_delete', 0)
            ->where('staff.status', 'active')
            ->orderBy('users.last_name');

        if (! $isSuperAdmin && $user) {
            $q->where('staff.school_id', $user->school_id);
        }

        return $q->get();
    }

    /**
     * Congés en cours pour un membre du staff (pour le dashboard)
     */
    public static function getCurrentLeaves()
    {
        return StaffLeaveModel::select(
            'staff_leaves.*',
            'users.name',
            'users.last_name',
            'staff.role',
            'leave_types.name as leave_type_name',
            'leave_types.color',
        )
            ->join('staff', 'staff.id', '=', 'staff_leaves.staff_id')
            ->join('users', 'users.id', '=', 'staff.user_id')
            ->join('leave_types', 'leave_types.id', '=', 'staff_leaves.leave_type_id')
            ->where('staff_leaves.status', 'approved')
            ->where('staff_leaves.is_delete', 0)
            ->where('staff_leaves.start_date', '<=', today())
            ->where(function ($q) {
                $q->whereNull('staff_leaves.end_date')
                  ->orWhere('staff_leaves.end_date', '>=', today());
            })
            ->get();
    }

    public static function getTotalActive(): int
    {
        return self::where('is_delete', 0)->where('status', 'active')->count();
    }

    public function getFullName(): string
    {
        return trim(($this->last_name ?? '') . ' ' . ($this->name ?? ''));
    }
}
