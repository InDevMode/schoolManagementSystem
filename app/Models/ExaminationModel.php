<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ExaminationModel extends Model
{
    use HasFactory;

    protected $table = 'exams';

    protected $fillable = [
        'name',
        'period_id',
        'start_date',
        'end_date',
        'status',
        'created_by',
    ];

    protected $hidden = [
        'is_delete',
    ];


    public static function getExaminations(int $perPage)
    {
        $results = ExaminationModel::select('exams.*', 'users.name as created_by_name', 'periods.name as periods_name')
            ->leftJoin('users', 'users.id', '=', 'exams.created_by')
            ->leftJoin('periods', 'periods.id', '=', 'exams.period_id');

        $filters = [
            'exams.name' => Request::get('exam_name'),
            'users.name' => Request::get('created_name'),
            'exams.created_at' => Request::get('created_at'),
            'exams.updated_at' => Request::get('updated_at'),
        ];

        foreach ($filters as $column => $value) {
            if (!empty($value)) {
                $results->where($column, 'like', '%' . strtolower($value) . '%');
            }
        }

        return $results
            ->where('exams.is_delete', '=', 0)
            ->orderBy('exams.id', 'desc')
            ->groupBy('exams.id')
            ->paginate($perPage);
    }


    public static function getSingle(int $id): ?ExaminationModel
    {
        return ExaminationModel::find($id);
    }

    public static function getNameSingle(string $name): ?ExaminationModel
    {
        return ExaminationModel::where('name', $name)->first();
    }

    public static function checkNameSingle(string $name, int $id): ?ExaminationModel
    {
        return ExaminationModel::where('name', $name)
            ->where('id', '!=', $id)
            ->first();
    }

    public static function getExams()
    {
        return ExaminationModel::select('exams.*')
            ->join('users', 'users.id', '=', 'exams.created_by')
            ->where('exams.is_delete', '=', 0)
            ->orderBy('exams.id', 'desc')
            ->get();
    }
    public static function getMyClassSubjectGroup(int $teacher_id)
    {
        return ClassTeacherModel::select(
            'class_teacher.*',
            'class.id as class_id',
            'class.name as class_name',
        )
            ->join('class', 'class.id', '=', 'class_teacher.class_id')
            ->where('class_teacher.is_delete', 0)
            ->where('class_teacher.status', 1)
            ->where('class_teacher.teacher_id', '=', $teacher_id)
            ->groupBy('class_teacher.id')
            ->get();
    }

    public static function getTotalExam()
    {
        return ExaminationModel::where('is_delete', 0)->count();
    }

    public static function getTotalExamTeacherToday()
    {
        return ExaminationModel::join('marks_register', 'exams.id', '=', 'marks_register.exam_id')
            ->join('class_teacher', 'marks_register.class_id', '=', 'class_teacher.class_id')
            ->where('class_teacher.teacher_id', Auth::user()->id)
            ->where('class_teacher.is_delete', '=', 0)
            ->where('marks_register.is_delete', '=', 0)
            ->where('exams.is_delete', '=', 0)
            ->whereDate('exams.created_at', today())
            ->distinct('exams.id')
            ->count('exams.id');
    }

    public static function getTotalExamStudent()
    {
        return ExaminationModel::select('exams.id')
            ->join('schedules', 'schedules.exam_id', '=', 'exams.id')
            ->join('marks_register', 'marks_register.exam_id', '=', 'exams.id')
            ->where('schedules.class_id', '=', Auth::user()->class_id)
            ->where('schedules.class_id', '=', Auth::user()->class_id)
            ->where('schedules.is_delete', '=', 0)
            ->where('exams.is_delete', '=', 0)
            ->count();

    }

}
