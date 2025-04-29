<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarksGradeModel extends Model
{
    use HasFactory;

    protected $table = 'marks_grade';

    protected $fillable = [
        'name',
        'percent_from',
        'percent_to',
        'created_by',
    ];

    protected $hidden = [
        'is_delete',
    ];

    static public function getSingle(int $id): ?MarksGradeModel
    {
        return MarksGradeModel::find($id);
    }

    static public function getNameSingle(string $name)
    {
        return MarksGradeModel::where('name', $name)->first();
    }

    static public function checkNameSingle(string $name, int $id)
    {
        return MarksGradeModel::where('name', $name)
            ->where('id', '!=', $id)
            ->first();
    }

    static public function getMarksGrade(int $perPage)
    {
        return MarksGradeModel::select('marks_grade.*', 'users.name as created_name')
            ->join('users', 'users.id', '=', 'marks_grade.created_by')
            ->orderBy('marks_grade.id', 'desc')
            ->groupBy('marks_grade.id')
            ->paginate($perPage);
    }

    static public function getGrade(int $percent)
    {
        $result = MarksGradeModel::select('marks_grade.*')
        ->where('percent_from', '<=', $percent)
        ->where('percent_to', '>=', $percent)
        ->first();

        return !empty($result) ? $result->name : '';
    }

}
