<?php

namespace App\Http\Controllers;

use App\Exports\ExportAttendance;
use App\Models\ClassModel;
use App\Models\ClassTeacherModel;
use App\Models\StudentAttendanceModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceController extends Controller
{
    public function attendanceStudent(Request $request)
    {
        $students = [];
        if ($request->filled('class_id') && $request->filled('attendance_date')) {
            $students = User::getStudent($request->class_id);
        }
        return Inertia::render('Admin/Attendance/Index', [
            'classes'       => ClassModel::getClass(),
            'students'      => $students,
            'selectedClass' => $request->class_id,
            'selectedDate'  => $request->attendance_date,
        ]);
    }

    public function attendanceStudentSave(Request $request): \Illuminate\Http\JsonResponse
    {
        try {

            $checkAttendance = StudentAttendanceModel::checkAlreadyAttendance($request->student_id, $request->class_id, $request->attendance_date);

            if (!empty($checkAttendance)) {
                $attendance = $checkAttendance;
            } else {
                // Création d'une nouvelle entrée dans la base de données
                $attendance = new StudentAttendanceModel();
                $attendance->student_id = $request->student_id;
                $attendance->class_id = $request->class_id;
                $attendance->attendance_date = $request->attendance_date;
                $attendance->created_by = Auth::user()->id;
            }

            $attendance->attendance_type = $request->attendance_type;
            $attendance->save();

            return response()->json([
                'success' => true,
                'message' => 'Présence enregistrée avec succès.'
            ], 200);

        } catch (\Exception $e) {
            Log::error("Une erreur est survenue lors de l’enregistrement de la présence : " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l’enregistrement de la présence.',
            ], 500);
        }
    }

    public function attendanceReport()
    {
        return Inertia::render('Admin/Attendance/Report', [
            'classes'    => ClassModel::getClass(),
            'attendance' => StudentAttendanceModel::getStudentAttendance(15),
        ]);
    }

    public function attendanceStudentTeacher(Request $request)
    {
        $students = [];
        if ($request->filled('class_id') && $request->filled('attendance_date')) {
            $students = User::getStudent($request->class_id);
        }
        return Inertia::render('Teacher/Attendance/Index', [
            'classes'       => ClassTeacherModel::getMyClassSubjectGroup(Auth::user()->id),
            'students'      => $students,
            'selectedClass' => $request->class_id,
            'selectedDate'  => $request->attendance_date,
        ]);
    }

    public function attendanceStudentTeacherSave(Request $request): \Illuminate\Http\JsonResponse
    {
        try {

            $checkAttendance = StudentAttendanceModel::checkAlreadyAttendance($request->student_id, $request->class_id, $request->attendance_date);

            if (!empty($checkAttendance)) {
                $attendance = $checkAttendance;
            } else {
                // Création d'une nouvelle entrée dans la base de données
                $attendance = new StudentAttendanceModel();
                $attendance->student_id = $request->student_id;
                $attendance->class_id = $request->class_id;
                $attendance->attendance_date = $request->attendance_date;
                $attendance->created_by = Auth::user()->id;
            }

            $attendance->attendance_type = $request->attendance_type;
            $attendance->save();

            return response()->json([
                'success' => true,
                'message' => 'Présence enregistrée avec succès.'
            ], 200);

        } catch (\Exception $e) {
            Log::error("Une erreur est survenue lors de l’enregistrement de la présence : " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l’enregistrement de la présence.',
            ], 500);
        }
    }

    public function attendanceReportTeacher()
    {
        return Inertia::render('Teacher/Attendance/Report', [
            'classes'    => ClassTeacherModel::getMyClassSubjectGroup(Auth::user()->id),
            'attendance' => StudentAttendanceModel::getStudentAttendance(10),
        ]);
    }

    public function myAttendance()
    {
        return Inertia::render('Student/Attendance/Index', [
            'attendance'   => StudentAttendanceModel::getMyAttendance(Auth::user()->id, 10),
            'classStudent' => StudentAttendanceModel::getClassStudent(Auth::user()->id),
        ]);
    }

    public function parentStudentAttendance(int $student_id)
    {
        $student = User::getSingle($student_id);
        return Inertia::render('Parent/Attendance/Index', [
            'student'      => $student,
            'attendance'   => StudentAttendanceModel::getMyAttendance($student_id, 10),
            'classStudent' => StudentAttendanceModel::getClassStudent($student_id),
        ]);
    }

    public function attendanceReportExport()
    {
        return Excel::download(new ExportAttendance, 'attendance_' . date('d_m_Y') . '.xlsx');
    }

}
