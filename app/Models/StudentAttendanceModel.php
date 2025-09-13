<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
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
        'is_delete'
    ];

    public static function checkAlreadyAttendance(int $studentId, int $classId, string $date)
    {
        return StudentAttendanceModel::where('student_id', $studentId)->where('class_id', $classId)->where('attendance_date', $date)->first();
    }

    public static function getStudentAttendance(int $perpage)
    {
        $results = StudentAttendanceModel::select('attendances.*', 'class.name as class_name', 'student.name as student_name', 'student.last_name as student_last_name', 'created_by.name as created_by_name')
            ->join('class', 'class.id', '=', 'attendances.class_id')
            ->join('users as student', 'student.id', '=', 'attendances.student_id')
            ->join('users as created_by', 'created_by.id', '=', 'attendances.created_by')
            ->where('student.is_delete', '=', 0)
            ->where('created_by.is_delete', '=', 0)
            ->where('attendances.is_delete', '=', 0)
            ->where('student.status', '=', 1)
            ->where('created_by.status', '=', 1)
            ->where('class.is_delete', '=', 0)
            ->where('class.status', '=', 1);

        $filters = [
            'student.name' => strtolower(Request::get('student_name')),
            'student.last_name' => strtolower(Request::get('student_last_name')),
            'attendances.class_id' => strtolower(Request::get('class_id')),
            'attendances.attendance_date' => strtolower(Request::get('attendance_date')),
            'attendances.created_at' => strtolower(Request::get('created_at')),
        ];

        foreach ($filters as $column => $value) {
            if (!empty($value)) {
                $results->where($column, 'like', '%' . $value . '%');
            }
        }

        $attendanceType = Request::get('attendance_type');
        if (in_array($attendanceType, ['1', '2', '3', '4'], true)) {
            $results->where('attendances.attendance_type', $attendanceType);
        }

        return $results->orderBy('attendances.id', 'desc')
            ->paginate($perpage);
    }

    public static function getStudentAttendanceTeacher(int $perpage, $class_ids)
    {
        if (!empty($class_ids)) {

            $results = StudentAttendanceModel::select('attendances.*', 'class.name as class_name', 'student.name as student_name', 'student.last_name as student_last_name', 'created_by.name as created_by_name')
                ->join('class', 'class.id', '=', 'attendances.class_id')
                ->join('users as student', 'student.id', '=', 'attendances.student_id')
                ->join('users as created_by', 'created_by.id', '=', 'attendances.created_by')
                ->where('student.is_delete', '=', 0)
                ->where('created_by.is_delete', '=', 0)
                ->where('attendances.is_delete', '=', 0)
                ->where('student.status', '=', 1)
                ->where('created_by.status', '=', 1)
                ->where('class.is_delete', '=', 0)
                ->where('class.status', '=', 1)
                ->whereIn('attendances.class_id', $class_ids);

            $filters = [
                'student.name' => strtolower(Request::get('student_name')),
                'student.last_name' => strtolower(Request::get('student_last_name')),
                'attendances.class_id' => strtolower(Request::get('class_id')),
                'attendances.created_at' => strtolower(Request::get('created_at')),
            ];

            foreach ($filters as $column => $value) {
                if (!empty($value)) {
                    $results->where($column, 'like', '%' . $value . '%');
                }
            }

            if (!empty(Request::get('start_attendance_date')) && !empty(Request::get('end_attendance_date'))) {
                $results->whereBetween('attendances.attendance_date', [Request::get('start_attendance_date'), Request::get('end_attendance_date')]);
            }

            $attendanceType = Request::get('attendance_type');
            if (in_array($attendanceType, ['1', '2', '3', '4'], true)) {
                $results->where('attendances.attendance_type', $attendanceType);
            }

            return $results->orderBy('attendances.id', 'desc')
                ->paginate($perpage);
        } else {
            return "";
        }
    }

    public static function getMyAttendance(int $student_id, int $perpage)
    {
        $results = StudentAttendanceModel::select('attendances.*', 'class.name as class_name')
            ->join('class', 'class.id', '=', 'attendances.class_id')
            ->where('class.is_delete', '=', 0)
            ->where('class.status', '=', 1)
            ->where('attendances.is_delete', '=', 0)
            ->where('attendances.student_id', '=', $student_id);

        $filters = [
            'attendances.class_id' => strtolower(Request::get('class_id')),
            'attendances.created_at' => strtolower(Request::get('created_at')),
        ];

        foreach ($filters as $column => $value) {
            if (!empty($value)) {
                $results->where($column, 'like', '%' . $value . '%');
            }
        }

        if (!empty(Request::get('start_attendance_date')) && !empty(Request::get('end_attendance_date'))) {
            $results->whereBetween('attendances.attendance_date', [Request::get('start_attendance_date'), Request::get('end_attendance_date')]);
        }

        $attendanceType = Request::get('attendance_type');
        if (in_array($attendanceType, ['1', '2', '3', '4'], true)) {
            $results->where('attendances.attendance_type', $attendanceType);
        }

        $results = $results->orderBy('attendances.id', 'desc')
            ->paginate($perpage);

        return $results;
    }

    public static function getClassStudent(int $student_id)
    {
        return StudentAttendanceModel::select('attendances.*', 'class.name as class_name')
            ->join('class', 'class.id', '=', 'attendances.class_id')
            ->where('attendances.student_id', '=', $student_id)
            ->where('class.is_delete', '=', 0)
            ->where('class.status', '=', 1)
            ->where('attendances.is_delete', '=', 0)
            ->groupBy('attendances.class_id')
            ->get();
    }

    public static function getTotalAttendance()
    {
        return StudentAttendanceModel::where('attendances.is_delete', '=', 0)->count();
    }

    public static function getTotalAttendanceStudent()
    {
        return StudentAttendanceModel::join('class', 'class.id', '=', 'attendances.class_id')
            ->where('attendances.student_id', '=', Auth::user()->id)
            ->where('attendances.is_delete', '=', 0)
            ->count();
    }

    public static function getTotalAttendanceTypeStudent(int $attendanceType)
    {
        return StudentAttendanceModel::where('attendances.attendance_type', '=', $attendanceType)
            ->where('attendances.is_delete', '=', 0)
            ->count();
    }

    public static function getTotalByAttendanceTypeStudent(int $attendanceType, $student_ids)
    {
        return StudentAttendanceModel::where('attendances.attendance_type', '=', $attendanceType)
            ->whereIn('attendances.student_id', $student_ids)
            ->where('attendances.is_delete', '=', 0)
            ->count();
    }

    public static function getTotalAttendanceTypeByStudent(int $attendanceType, int $student_id)
    {
        return StudentAttendanceModel::where('attendances.attendance_type', '=', $attendanceType)
            ->where('attendances.student_id', '=', $student_id)
            ->where('attendances.is_delete', '=', 0)
            ->count();
    }

    public static function getAllAttendance()
    {
        $results = StudentAttendanceModel::select(
            'attendances.*',
            'class.name as class_name',
            'student.admission_number as student_number',
            'student.name as student_name',
            'student.last_name as student_last_name',
            'created_by.name as created_by_name'
        )
            ->join('class', 'class.id', '=', 'attendances.class_id')
            ->join('users as student', 'student.id', '=', 'attendances.student_id')
            ->join('users as created_by', 'created_by.id', '=', 'attendances.created_by')
            ->where('attendances.is_delete', '=', 0);

        $filters = [
            'student.name' => strtolower(Request::get('student_name')),
            'student.last_name' => strtolower(Request::get('student_last_name')),
            'attendances.class_id' => strtolower(Request::get('class_id')),
            'attendances.attendance_date' => strtolower(Request::get('attendance_date')),
            'attendances.created_at' => strtolower(Request::get('created_at')),
        ];

        foreach ($filters as $column => $value) {
            if (!empty($value)) {
                $results->where($column, 'like', '%' . $value . '%');
            }
        }

        $attendanceType = Request::get('attendance_type');
        if (in_array($attendanceType, ['1', '2', '3', '4'], true)) {
            $results->where('attendances.attendance_type', $attendanceType);
        }

        return $results->orderBy('attendances.id', 'asc')
            ->get();
    }


}
