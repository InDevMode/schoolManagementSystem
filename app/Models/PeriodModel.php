<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class PeriodModel extends Model
{
    use HasFactory;

    protected $table = 'periods';

    protected $fillable = [
        'settings_id',
        'name',
        'start_date',
        'end_date',
        'is_current',
        'status',
        'created_by'
    ];

    protected $hidden = [
        'is_delete'
    ];

    public static function getSingle(int $id)
    {
        return PeriodModel::find($id);
    }

    public static function getPeriods(int $perpage)
    {
        $results = PeriodModel::select('periods.*')
            ->join('settings', 'periods.settings_id', '=', 'settings.id')
            ->join('users', 'periods.created_by', '=', 'users.id')
            ->where('periods.is_delete', '=', 0)
            ->where('periods.is_current', '=', 0)
            ->where('periods.status', '=', 1);

        $filters = [
            'periods.name' => strtolower(Request::get('name')),
            'users.name' => strtolower(Request::get('created_by')),
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

        return $results->orderBy('periods.id', 'desc')
            ->paginate($perpage);
    }

}
