<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

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

    static public function checkAlreadyAttendance(int $studentId, int $classId, string $date)
    {
        return StudentAttendanceModel::where('student_id', $studentId)->where('class_id', $classId)->where('attendance_date', $date)->first();
    }

    static public function getStudentAttendance(int $perpage)
    {
        $results = StudentAttendanceModel::select('attendances.*', 'class.name as class_name', 'student.name as student_name', 'student.last_name as student_last_name', 'created_by.name as created_by_name')
            ->join('class', 'class.id', '=', 'attendances.class_id')
            ->join('users as student', 'student.id', '=', 'attendances.student_id')
            ->join('users as created_by', 'created_by.id', '=', 'attendances.created_by')
            ->where('student.is_delete', '=', 0)
            ->where('created_by.is_delete', '=', 0)
            ->where('student.status', '=', 1)
            ->where('created_by.status', '=', 1)
            ->where('class.is_delete', '=', 0)
            ->where('class.status', '=', 1);

        $filters = [
            'class.name' => strtolower(Request::get('class_id')),
            'attendances.attendance_date' => strtolower(Request::get('attendance_date')),
            'attendances.created_at' => strtolower(Request::get('created_at')),
        ];

        if(!empty(Request::get('student_name'))){
            $student_name = Request::get('student_name');
           $results->where(function ($query) use ($student_name) {
                $query->where('student.name', 'like', '%' . $student_name . '%')
                    ->orWhere('student.last_name', 'like', '%' . $student_name . '%');
            });
        }

        foreach ($filters as $column => $value) {
            if (!empty($value)) {
                $results->where($column, 'like', '%' . $value . '%');
            }
        }

        $attendanceType = Request::get('atendance_type');
        if (in_array($attendanceType, ['1', '2', '3', '4'], true)) {
            $results->where('attendances.attendance_type', $attendanceType);
        }

       return $results->orderBy('attendances.id', 'desc')
            ->paginate($perpage);
    }

}
