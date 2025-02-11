<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class ExaminationModel extends Model
{
    use HasFactory;

    protected $table = 'exams';

    protected $fillable = [
        'name',
        'note',
        'created_by',
    ];

    protected $hidden = [
        'is_delete',
    ];


    static function getExaminations(int $perPage)
    {
        $results = ExaminationModel::select('exams.*', 'users.name as created_name')
            ->join('users', 'users.id', '=', 'exams.created_by');

        $filters = [
            'exams.name' => strtolower(Request::get('exam_name')),
            'exams.note' => strtolower(Request::get('exam_note')),
            'users.name' => strtolower(Request::get('created_name')),
            'exams.created_at' => strtolower(Request::get('created_at')),
            'exams.updated_at' => strtolower(Request::get('updated_at')),
        ];

        foreach ($filters as $column => $value) {
            if (!empty($value)) {
                $results->where($column, 'like', '%' . $value . '%');
            }
        }

        return $results->where('exams.is_delete', 0)
            ->orderBy('exams.id', 'desc')
            ->paginate($perPage);
    }

    static public function getSingle(int $id): ?ExaminationModel
    {
        return ExaminationModel::find($id);
    }

    static public function getNameSingle(string $name): ?ExaminationModel
    {
        return ExaminationModel::where('name', $name)->first();
    }

    static public function checkNameSingle(string $name, int $id): ?ExaminationModel
    {
        return ExaminationModel::where('name', $name)
            ->where('id', '!=', $id)
            ->first();
    }

    static public function getExams()
    {
        return ExaminationModel::select('exams.*')
            ->join('users', 'users.id', '=', 'exams.created_by')
            ->where('exams.is_delete', '=', 0)
            ->orderBy('exams.id', 'desc')
            ->get();
    }

}
