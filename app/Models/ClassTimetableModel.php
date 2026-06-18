<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ClassTimetableModel extends Model
{
    use HasFactory;

    protected $table = 'class_timetable';

    protected $fillable = [
        'start_time',
        'end_time',
        'room_number',
        'class_id',
        'subject_id',
        'week_id',
    ];

    protected $hidden = [
    ];

    public static function getClassTimetable(int $class_id, int $subject_id, int $week_id)
    {
        return ClassTimetableModel::where('class_id', '=', $class_id)
            ->where('subject_id', '=', $subject_id)
            ->where('week_id', '=', $week_id)
            ->where('class_timetable.is_delete', '=', 0)
            ->first();
    }

    public static function getSubject(int $class_id)
    {
        return ClassSubjectModel::select(
            'class_subject.id',
            'class_subject.class_id',
            'class_subject.subject_id',
            'class_subject.coefficient',
            'class_subject.status',
            'subject.name as subject_name',
            'subject.type as subject_type'
        )
            ->join('subject', 'subject.id', '=', 'class_subject.subject_id')
            ->join('class', 'class.id', '=', 'class_subject.class_id')
            ->where('class_subject.class_id', '=', $class_id)
            ->where('class_subject.is_delete', '=', 0)
            ->where('class_subject.status', '=', 1)
            ->orderBy('class_subject.id', 'desc')
            ->get();
    }

    public static function getTotalClassTimetable()
    {
        return ClassTimetableModel::where('class_timetable.is_delete', '=', 0)->count();
    }

    public static function getTotalClassTimetableTeacher()
    {
        return ClassTimetableModel::select('class_timetable.*')
            ->join('class_teacher', 'class_timetable.class_id', '=', 'class_teacher.class_id')
            ->where('class_teacher.teacher_id', Auth::user()->id)
            ->where('class_teacher.is_delete', '=', 0)
            ->where('class_timetable.is_delete', '=', 0)
            ->count('class_timetable.id');
    }

    public static function getTotalClassTimetableStudent()
    {
        return ClassTimetableModel::join('class_subject', 'class_subject.class_id', '=', 'class_timetable.class_id')
            ->join('subject', 'subject.id', '=', 'class_timetable.subject_id')
            ->join('class', 'class.id', '=', 'class_subject.class_id')
            ->where('class_timetable.class_id', Auth::user()->class_id)
            ->where('class_timetable.is_delete', 0)
            ->count();
    }


}
