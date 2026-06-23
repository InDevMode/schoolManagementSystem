<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class PeriodModel extends Model
{
    use HasFactory;

    protected $table = 'periods';

    protected $fillable = [
        'school_id',
        'settings_id',
        'name',
        'type',
        'order_number',
        'school_year',
        'start_date',
        'end_date',
        'is_current',
        'status',
        'created_by',
    ];

    protected $hidden = [
        'is_delete',
    ];

    protected $casts = [
        'is_current' => 'boolean',
    ];

    public static function getSingle(int $id)
    {
        return PeriodModel::find($id);
    }

    public static function getPeriods(int $perpage)
    {
        $user         = Auth::user();
        $isSuperAdmin = $user && (int) $user->user_type === 0;

        $results = PeriodModel::select(
                'periods.*',
                'created_by.name as created_by_name',
            )
            ->leftJoin('users as created_by', 'periods.created_by', '=', 'created_by.id')
            ->where('periods.is_delete', '=', 0);

        // Scoping multi-tenant : un admin ne voit que les périodes de son école
        if (! $isSuperAdmin && $user) {
            $results->where('periods.school_id', $user->school_id);
        }

        $filters = [
            'periods.name' => strtolower(Request::get('name')),
            'periods.created_at' => strtolower(Request::get('created_at')),
            'periods.updated_at' => strtolower(Request::get('updated_at')),
        ];

        if (!empty(Request::get('start_date_from'))) {
            $results->whereDate('start_date', '>=', Request::get('start_date_from'));
        }
        if (!empty(Request::get('start_date_to'))) {
            $results->whereDate('start_date', '<=', Request::get('start_date_to'));
        }
        if (!empty(Request::get('end_date_from'))) {
            $results->whereDate('end_date', '>=', Request::get('end_date_from'));
        }
        if (!empty(Request::get('end_date_to'))) {
            $results->whereDate('end_date', '<=', Request::get('end_date_to'));
        }

        foreach ($filters as $column => $value) {
            if (!empty($value)) {
                $results->where($column, 'like', '%' . $value . '%');
            }
        }

        $status = Request::get('status');
        if (in_array($status, ['0', '1'], true)) {
            $results->where('periods.status', $status);
        }

        return $results->orderBy('periods.id', 'desc')->paginate($perpage);
    }

    /**
     * Toutes les périodes actives scopées par école de l'utilisateur connecté.
     */
    public static function getAllPeriods()
    {
        $user         = Auth::user();
        $isSuperAdmin = $user && (int) $user->user_type === 0;

        $q = PeriodModel::select('periods.*')
            ->where('periods.is_delete', '=', 0)
            ->where('periods.status', '=', 1);

        if (! $isSuperAdmin && $user) {
            $q->where('periods.school_id', $user->school_id);
        }

        return $q->orderBy('periods.school_year', 'desc')
            ->orderBy('periods.order_number', 'asc')
            ->get();
    }

    /**
     * Période courante — scopée par école de l'utilisateur connecté.
     */
    public static function getCurrentPeriod()
    {
        $user         = Auth::user();
        $isSuperAdmin = $user && (int) $user->user_type === 0;

        $q = PeriodModel::select('periods.*')
            ->where('periods.is_delete', '=', 0)
            ->where('periods.status', '=', 1)
            ->where('periods.is_current', '=', true);

        if (! $isSuperAdmin && $user) {
            $q->where('periods.school_id', $user->school_id);
        }

        return $q->get();
    }

    /**
     * Marquer une période comme courante UNIQUEMENT dans son école.
     * Désactive les autres périodes courantes de la même école.
     */
    public static function setCurrentForSchool(int $periodId, int $schoolId): bool
    {
        // Désactiver les périodes courantes de cette école uniquement
        PeriodModel::where('school_id', $schoolId)
            ->where('is_current', true)
            ->update(['is_current' => false]);

        $period = PeriodModel::find($periodId);
        if (! $period) return false;

        $period->is_current = true;
        $period->save();

        return true;
    }
}
