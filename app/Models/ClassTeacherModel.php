<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class ClassTeacherModel extends Model
{
    use HasFactory;

    protected $table = 'class_teacher';

    protected $fillable = [
        'class_id',
        'teacher_id',
        'created_by',
        'status'
    ];

    protected $hidden = [
        'is_delete',
    ];

    public static function getSingle(int $id)
    {
        return ClassTeacherModel::find($id);
    }

    public static function getAllClassTeacher(int $perPage)
    {
        $results = ClassTeacherModel::select(
            'class_teacher.*',
            'class.name as class_name',
            'teacher.name as teacher_name',
            'teacher.last_name as teacher_last_name',
            'users.name as created_by_name'

        )
            ->join('class', 'class.id', '=', 'class_teacher.class_id')
            ->join('users as teacher', 'teacher.id', '=', 'class_teacher.teacher_id')
            ->join('users', 'users.id', '=', 'class_teacher.created_by')
            ->where('class_teacher.is_delete', 0)
            ->where('class.is_delete', 0)
            ->where('class.status', 1)
            ->where('users.is_delete', 0);

        $filters = [
            'class.name' => strtolower(Request::get('class_name')),
            'teacher.name' => strtolower(Request::get('teacher_name')),
            'class_teacher.created_at' => strtolower(Request::get('created_at')),
            'class_teacher.updated_at' => strtolower(Request::get('updated_at')),
        ];

        foreach ($filters as $column => $value) {
            if (!empty($value)) {
                $results->where($column, 'like', '%' . $value . '%');
            }
        }

        $status = Request::get('status');
        if (in_array($status, ['0', '1'], true)) {
            $results->where('class_teacher.status', $status);
        }

        return $results->orderBy('class_teacher.id', 'desc')
            ->paginate($perPage);
    }

    public static function getAlreadyExist($class_id, $teacher_id)
    {
        return ClassTeacherModel::where('class_id', '=', $class_id)->where('teacher_id', '=', $teacher_id)->first();
    }

    public static function getAssignTeacher($class_id)
    {
        return ClassTeacherModel::where('class_id', '=', $class_id)->where('is_delete', 0)->get();
    }

    public static function deleteClassAssign($class_id)
    {
        return ClassTeacherModel::where('class_id', '=', $class_id)->delete();
    }

    public static function getMyClassSubject(int $perPage, int $teacher_id)
    {
        $results = ClassTeacherModel::select(
            'class_teacher.*',
            'class.name as class_name',
            'subject.id as subject_id',
            'subject.name as subject_name',
            'subject.type as subject_type',
        )
            ->join('class', 'class.id', '=', 'class_teacher.class_id')
            ->join('class_subject', 'class_subject.class_id', '=', 'class.id')
            ->join('subject', 'subject.id', '=', 'class_subject.subject_id')
            ->where('class_teacher.is_delete', 0)
            ->where('class_teacher.status', 1)
            ->where('subject.is_delete', 0)
            ->where('subject.status', 1)
            ->where('class_subject.is_delete', 0)
            ->where('class_subject.status', 1)
            ->where('class.is_delete', 0)
            ->where('class.status', 1)
            ->where('class_teacher.teacher_id', '=', $teacher_id);

        $filters = [
            'class.name' => strtolower(Request::get('class_name')),
            'subject.name' => strtolower(Request::get('subject_name')),
            'class_teacher.created_at' => strtolower(Request::get('created_at')),
            'class_teacher.updated_at' => strtolower(Request::get('updated_at')),
        ];

        foreach ($filters as $column => $value) {
            if (!empty($value)) {
                $results->where($column, 'like', '%' . $value . '%');
            }
        }

        $type = Request::get('subject_type');
        if (in_array($type, ['theoretical', 'practical'], true)) {
            $results->where('subject.type', $type);
        }

        return $results->orderBy('class_teacher.id', 'desc')
            ->paginate($perPage);
    }

    public static function getMyClassTimetable(int $class_id, int $subject_id)
    {
        Carbon::setLocale('fr');
        $dayName = Carbon::now()->translatedFormat('l');
        $getWeek = WeekModel::getWeekUsingName($dayName);
        return ClassTimetableModel::getClassTimetable($class_id, $subject_id, $getWeek->id);
    }

    public static function getTeacherCalendar($teacher_id)
    {
        return ClassTeacherModel::select(
            'class_timetable.*',
            'class.name as class_name',
            'subject.name as subject_name',
            'week.name as week_name',
            'week.day as week_day'
        )
            ->join('class', 'class.id', '=', 'class_teacher.class_id')
            ->join('class_subject', 'class_subject.class_id', '=', 'class.id')
            ->join('class_timetable', 'class_timetable.subject_id', '=', 'class_subject.subject_id')
            ->join('subject', 'subject.id', '=', 'class_timetable.subject_id')
            ->join('week', 'week.id', '=', 'class_timetable.week_id')
            ->where('class_teacher.teacher_id', '=', $teacher_id)
            ->where('class_teacher.is_delete', 0)
            ->where('class_teacher.status', 1)
            ->where('class.is_delete', 0)
            ->where('class.status', 1)
            ->where('subject.is_delete', 0)
            ->where('subject.status', 1)
            ->where('class_subject.is_delete', 0)
            ->where('class_subject.status', 1)
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

}
