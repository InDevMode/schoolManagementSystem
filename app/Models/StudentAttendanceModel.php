<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAttendanceModel extends Model
{
    use HasFactory;

    protected $table = 'attendances';

    protected $fillable = [
        'class_id',
        'attendance_date',
        'attendance_type',
        'student_id',
        'created_by',
    ];

    protected $hidden = [
        ''
    ];

    static public function checkAlreadyAttendance(int $studentId,int $classId, string $date){
        return StudentAttendanceModel::where('student_id', $studentId)->where('class_id', $classId)->where('attendance_date', $date)->first();
    }

}
