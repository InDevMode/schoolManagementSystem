<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ScheduleModel extends Model
{
    use HasFactory;

    protected $table = 'schedules';

    protected $fillable = [
        'exam_date',
        'start_time',
        'end_time',
        'room_number',
        'full_marks',
        'passing_marks',
        'exam_id',
        'class_id',
        'subject_id',
        'created_by',
    ];

    protected $hidden = [
        'is_delete'
    ];

    public static function getSingle(int $id)
    {
        return ScheduleModel::find($id);
    }

    public static function getExamSchedule(int $exam_id, int $class_id, int $subject_id)
    {
        return ScheduleModel::where('exam_id', '=', $exam_id)
            ->where('class_id', '=', $class_id)
            ->where('subject_id', '=', $subject_id)
            ->first();
    }

    public static function deleteExamSchedule(int $exam_id, int $class_id)
    {
        return ScheduleModel::where('exam_id', '=', $exam_id)
            ->where('class_id', '=', $class_id)
            ->update(['is_delete' => 1]);
    }

    public static function checkExamSchedule(int $exam_id, int $class_id)
    {
        return ScheduleModel::where('exam_id', $exam_id)
            ->where('class_id', $class_id)
            ->exists();
    }

    public static function getExam(int $class_id)
    {
        return ScheduleModel::select('schedules.*', 'exams.name as exam_name')
            ->join('exams', 'exams.id', '=', 'schedules.exam_id')
            ->where('schedules.class_id', '=', $class_id)
            ->where('exams.is_delete', '=', 0)
            ->where('schedules.is_delete', '=', 0)
            ->groupBy('schedules.exam_id')
            ->orderBy('schedules.id', 'desc')
            ->get();
    }

    public static function getExamTeacher(int $teacher_id)
    {
        return ScheduleModel::select('schedules.*', 'exams.name as exam_name')
            ->join('exams', 'exams.id', '=', 'schedules.exam_id')
            ->join('class_teacher', 'class_teacher.class_id', '=', 'schedules.class_id')
            ->where('class_teacher.teacher_id', '=', $teacher_id)
            ->where('class_teacher.status', '=', 1)
            ->where('class_teacher.is_delete', '=', 0)
            ->where('exams.is_delete', '=', 0)
            ->where('schedules.is_delete', '=', 0)
            ->groupBy('schedules.exam_id')
            ->orderBy('schedules.id', 'desc')
            ->get();
    }

    public static function getExamTimetable(int $exam_id, int $class_id)
    {
        return ScheduleModel::select('schedules.*', 'subject.name as subject_name', 'subject.type as subject_type')
            ->join('subject', 'subject.id', '=', 'schedules.subject_id')
            ->where('schedules.exam_id', '=', $exam_id)
            ->where('schedules.class_id', '=', $class_id)
            ->where('schedules.is_delete', '=', 0)
            ->where('subject.is_delete', '=', 0)
            ->where('subject.status', '=', 1)
            ->groupBy('exam_id')
            ->get();
    }

    public static function getSubject(int $exam_id, int $class_id)
    {
        return ScheduleModel::select('schedules.*', 'subject.name as subject_name', 'subject.type as subject_type')
            ->join('subject', 'subject.id', '=', 'schedules.subject_id')
            ->where('schedules.exam_id', '=', $exam_id)
            ->where('schedules.class_id', '=', $class_id)
            ->where('schedules.is_delete', '=', 0)
            ->where('subject.status', '=', 1)
            ->get();
    }

    public static function getExamTimetableTeacher(int $teacher_id)
    {
        return ScheduleModel::select('schedules.*', 'class.name as class_name', 'subject.name as subject_name', 'exams.name as exam_name')
            ->join('class_teacher', 'class_teacher.class_id', '=', 'schedules.class_id')
            ->join('class', 'class.id', '=', 'schedules.class_id')
            ->join('subject', 'subject.id', '=', 'schedules.subject_id')
            ->join('exams', 'subject.id', '=', 'schedules.exam_id')
            ->where('class_teacher.teacher_id', '=', $teacher_id)
            ->where('schedules.is_delete', 0)
            ->where('class_teacher.is_delete', 0)
            ->where('class_teacher.status', 1)
            ->where('class.is_delete', 0)
            ->where('class.status', 1)
            ->where('subject.is_delete', 0)
            ->where('subject.status', 1)
            ->where('exams.is_delete', 0)
            ->get();
    }

    public static function getMarks(int $student_id, int $exam_id, int $class_id, int $subject_id)
    {
        return MarkRegisterModel::checkAlreadyMarks($student_id, $exam_id, $class_id, $subject_id);
    }

    public static function getTotalExamClassTimebableTeacher()
    {
        return ScheduleModel::select('schedules.id')
            ->join('class_teacher', 'class_teacher.class_id', '=', 'schedules.class_id')
            ->join('exams', 'schedules.exam_id', '=', 'exams.id')
            ->where('class_teacher.teacher_id', '=', Auth::user()->id)
            ->where('schedules.is_delete', 0)
            ->where('class_teacher.is_delete', 0)
            ->where('exams.is_delete', 0)
            ->count('schedules.id');
    }

    public static function getTotalExamClassTimebableTeacherToday()
    {
        return ScheduleModel::select('schedules.id')
            ->join('class_teacher', 'schedules.class_id', '=', 'class_teacher.class_id')
            ->join('exams', 'schedules.exam_id', '=', 'exams.id')
            ->where('class_teacher.teacher_id', Auth::user()->id)
            ->where('class_teacher.is_delete', 0)
            ->where('schedules.is_delete', 0)
            ->where('exams.is_delete', 0)
            ->whereDate('schedules.exam_date', today())
            ->count('schedules.id');
    }

    public static function getTotalExamStudent()
    {
        return ScheduleModel::select('schedules.id')
            ->join('exams', 'schedules.exam_id', '=', 'exams.id')
            ->join('subject', 'schedules.subject_id', '=', 'subject.id')
            ->join('users as student', 'student.class_id', '=', 'schedules.class_id')
            ->where('student.id', '=', Auth::user()->id)
            ->where('schedules.class_id', '=', Auth::user()->class_id)
            ->count();
    }

}
