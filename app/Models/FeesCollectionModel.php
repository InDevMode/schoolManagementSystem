<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class FeesCollectionModel extends Model
{
    use HasFactory;

    protected $table = 'feescollections';

    protected $fillable = [
        'class_id',
        'student_id',
        'total_amount',
        'paid_amount',
        'remaning_amount',
        'payment_type',
        'remark',
        'created_by',
    ];

    protected $hidden = [
        'is_delete'
    ];

    public static function getSingle(int $id)
    {
        return FeesCollectionModel::find($id);
    }

    public static function getFeesCollections(int $perpage)
    {
        $results = FeesCollectionModel::select(
            'feescollections.*',
            'class.name as class_name',
            'class.amount as class_amount',
            'feescollections.paid_amount as paid_amount',
            'feescollections.remaning_amount as remaning_amount',
            'feescollections.payment_type as payment_type',
            'feescollections.created_at as created_at',
            'created_by.name as created_by_name',
            'users.name as student_name',
            'users.last_name as student_last_name',
            'users.admission_number as student_admission_number'
        )
            ->join('class', 'class.id', '=', 'feescollections.class_id')
            ->join('users as created_by', 'created_by.id', '=', 'feescollections.created_by')
            ->join('users', 'users.id', '=', 'feescollections.student_id')
            ->where('feescollections.is_delete', 0);

        $filters = [
            'class_name' => Request::get('class_name'),
            'student_name' => Request::get('student_name'),
            'student_last_name' => Request::get('student_last_name'),
            'admission_number' => Request::get('admission_number'),
            'payment_type' => Request::get('payment_type'),
            'created_at' => Request::get('created_at'),
            'updated_at' => Request::get('updated_at'),
        ];

        $map = [
            'class_name' => ['class.name', '=', 'like'],
            'student_name' => ['users.name', 'like'],
            'student_last_name' => ['users.last_name', 'like'],
            'admission_number' => ['users.admission_number', 'like'],
            'payment_type' => ['feescollections.payment_type', 'like'],
            'created_at' => ['feescollections.created_at', 'date'],
            'updated_at' => ['feescollections.updated_at', 'date'],
        ];

        foreach ($map as $key => [$column, $operator]) {
            $value = $filters[$key] ?? null;

            if ($value === null || $value === '')
                continue;

            match ($operator) {
                'like' => $results->where($column, 'like', '%' . $value . '%'),
                'date' => $results->whereDate($column, $value),
                default => $results->where($column, $value),
            };
        }

        return $results->orderBy('id', 'desc')
            ->paginate($perpage);
    }

    public static function getFees(int $student_id, int $perpage)
    {
        return FeesCollectionModel::select(
            'feescollections.*',
            'class.name as class_name',
            'feescollections.paid_amount as paid_amount',
            'feescollections.remaning_amount as remaning_amount',
            'feescollections.payment_type as payment_type',
            'feescollections.created_at as created_at',
            'created_by.name as created_by_name',
            'users.name as student_name',
            'users.last_name as student_last_name',
            'users.admission_number as student_admission_number'
        )
            ->join('class', 'class.id', '=', 'feescollections.class_id')
            ->join('users as created_by', 'created_by.id', '=', 'feescollections.created_by')
            ->join('users', 'users.id', '=', 'feescollections.student_id')
            ->where('feescollections.student_id', $student_id)
            ->where('feescollections.is_payment', 1)
            ->orderBy('id', 'desc')
            ->paginate($perpage);
    }

    public static function getPaidAmount(int $student_id, int $class_id)
    {
        return FeesCollectionModel::where('feescollections.student_id', $student_id)
            ->where('feescollections.class_id', $class_id)
            ->where('feescollections.is_payment', 1)
            ->sum('feescollections.paid_amount');
    }

    public static function getFeesByStudentIdAndClassId(int $student_id, int $class_id)
    {
        return FeesCollectionModel::where('feescollections.student_id', $student_id)
            ->where('feescollections.class_id', $class_id)
            ->first();
    }

}
