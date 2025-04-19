<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarkRegisterModel extends Model
{
    use HasFactory;

    protected $table = 'marks_register';

    protected $fillable = [
        'test_work',
        'exam_work',
        'home_work',
        'class_work',
    ];

    protected $hidden = [
        'student_id',
        'exam_id',
        'class_id',
        'subject_id',
        'created_by',
        'is_delete'
    ];

    static public function checkAlreadyMarks(int $student_id, int $exam_id, int $class_id, int $subject_id)
    {
        return MarkRegisterModel::where('student_id', '=', $student_id)
            ->where('exam_id', '=', $exam_id)
            ->where('class_id', '=', $class_id)
            ->where('subject_id', '=', $subject_id)
            ->first();
    }

    static public function getExam(int $student_id)
    {
        return MarkRegisterModel::select('marks_register.*', 'exams.name as exam_name')
            ->join('exams', 'exams.id', '=', 'marks_register.exam_id')
            ->where('marks_register.student_id', '=', $student_id)
            ->where('marks_register.is_delete', '=', 0)
            ->where('exams.is_delete', '=', 0)
            ->groupBy('marks_register.exam_id')
            ->get();

    }

    static public function getExamSubject(int $exam_id, int $student_id)
    {
        return MarkRegisterModel::select('marks_register.*', 'exams.name as exam_name', 'subject.name as subject_name')
            ->join('exams', 'exams.id', '=', 'marks_register.exam_id')
            ->join('subject', 'subject.id', '=', 'marks_register.subject_id')
            ->where('marks_register.exam_id', '=', $exam_id)
            ->where('marks_register.student_id', '=', $student_id)
            ->where('marks_register.is_delete', '=', 0)
            ->where('exams.is_delete', '=', 0)
            ->where('subject.is_delete', '=', 0)
            ->where('subject.status', '=', 1)
            ->get();
    }

}
