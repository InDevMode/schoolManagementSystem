<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public static function getFees(int $student_id)
    {
        return FeesCollectionModel::select('feescollections.*')
            ->join('class', 'class.id', '=', 'feescollections.class_id')
            ->where('feescollections.student_id', $student_id)
            ->get();
    }

}
