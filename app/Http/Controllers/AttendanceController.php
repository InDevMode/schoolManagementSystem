<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\ClassTeacherModel;
use App\Models\StudentAttendanceModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    public function attendanceStudent(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        $data['header_title'] = 'Définir la présence des apprenants';
        $data['getClass'] = ClassModel::getClass();

        if (!empty($request->get('class_id')) && !empty($request->get('attendance_date'))) {
            $data['getStudent'] = User::getStudent($request->get('class_id'));
        }

        return view('admin.attendance.student.list', $data);
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

    public function attendanceReport(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['header_title'] = 'Rapport de présences';
        $data['getClass'] = ClassModel::getClass();
        $data['getStudentAttendance'] = StudentAttendanceModel::getStudentAttendance(5);
        return view('admin.attendance.report', $data);
    }

    public function attendanceStudentTeacher(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['header_title'] = 'Liste de présence des apprenants';
        $data['getClass'] = ClassTeacherModel::getMyClassSubjectGroup(Auth::user()->id);
        if (!empty($request->get('class_id')) && !empty($request->get('attendance_date'))) {
            $data['getStudent'] = User::getStudent($request->get('class_id'));
        }
        return view('teacher.attendance.student.list', $data);
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

    public function attendanceReportTeacher(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['header_title'] = 'Rapport de présences';
        $getClass = ClassTeacherModel::getMyClassSubjectGroup(Auth::user()->id);
        $classArray = array();
        foreach ($getClass as $class) {
            $classArray[] = $class->class_id;
        }
        $data['getClass'] = $getClass;
        $data['getStudentAttendance'] = StudentAttendanceModel::getStudentAttendanceTeacher(10, $classArray);
        return view('teacher.attendance.report', $data);
    }

    public function myAttendance()
    {
        $data['header_title'] = 'Ma Présence';
        $data['getMyAttendance'] = StudentAttendanceModel::getMyAttendance(Auth::user()->id, 10);
        $data['getClassStudent'] = StudentAttendanceModel::getClassStudent(Auth::user()->id);
        return view('student.attendance', $data);
    }

    public function parentStudentAttendance($student_id)
    {
        $data['header_title'] = "Présence des apprenants";
        $data['getStudent'] = User::getSingle($student_id);
        $data['getParentStudentAttendance'] = StudentAttendanceModel::getMyAttendance($student_id, 10);
        $data['getClassStudent'] = StudentAttendanceModel::getClassStudent($student_id);
        return view('parent.student_attendance', $data);
    }

}
