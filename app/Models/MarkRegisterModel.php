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

}
