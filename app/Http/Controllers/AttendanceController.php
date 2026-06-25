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
        $existingAttendance = [];

        if ($request->filled('class_id') && $request->filled('attendance_date')) {
            $students = User::getStudent($request->class_id);

            $records = StudentAttendanceModel::where('class_id', $request->class_id)
                ->where('attendance_date', $request->attendance_date)
                ->where('is_delete', 0)
                ->get();

            foreach ($records as $record) {
                $existingAttendance[$record->student_id] = $record->attendance_type;
            }
        }

        return Inertia::render('Admin/Attendance/Index', [
            'classes'            => ClassModel::getClass(),
            'students'           => $students,
            'selectedClass'      => $request->class_id,
            'selectedDate'       => $request->attendance_date,
            'existingAttendance' => $existingAttendance,
        ]);
    }

    /**
     * Enregistre les Présences (batch ou single).
     * Si un enregistrement existait et avait été soft-supprimé, on le restaure.
     */
    public function attendanceStudentSave(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $entries = $request->has('entries')
                ? $request->input('entries')
                : [[
                    'student_id'      => $request->input('student_id'),
                    'class_id'        => $request->input('class_id'),
                    'attendance_date' => $request->input('attendance_date'),
                    'attendance_type' => $request->input('attendance_type'),
                ]];

            foreach ($entries as $entry) {
                // Cherche toutes les entrées (y compris supprimées) pour éviter les doublons
                $record = StudentAttendanceModel::where('student_id', $entry['student_id'])
                    ->where('class_id', $entry['class_id'])
                    ->where('attendance_date', $entry['attendance_date'])
                    ->first();

                if ($record) {
                    // Met à jour l'existant et le restaure si supprimé
                    $record->is_delete = 0;
                } else {
                    $record = new StudentAttendanceModel();
                    $record->student_id      = $entry['student_id'];
                    $record->class_id        = $entry['class_id'];
                    $record->attendance_date = $entry['attendance_date'];
                    $record->created_by      = Auth::user()->id;
                    $record->is_delete       = 0;
                }

                $record->attendance_type = $entry['attendance_type'];
                $record->save();
            }

            return response()->json(['success' => true, 'message' => 'Presences enregistrees.'], 200);

        } catch (\Exception $e) {
            Log::error('Erreur enregistrement presence : ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur serveur.'], 500);
        }
    }

    public function attendanceDelete(int $id): \Illuminate\Http\JsonResponse
    {
        try {
            $attendance = StudentAttendanceModel::find($id);

            if (!$attendance) {
                return response()->json(['success' => false, 'message' => 'Presence introuvable.'], 404);
            }

            $attendance->is_delete = 1;
            $attendance->save();

            return response()->json(['success' => true, 'message' => 'Presence supprimee.']);
        } catch (\Exception $e) {
            Log::error('Erreur suppression presence : ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur serveur.'], 500);
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
        $existingAttendance = [];

        if ($request->filled('class_id') && $request->filled('attendance_date')) {
            $students = User::getStudent($request->class_id);

            $records = StudentAttendanceModel::where('class_id', $request->class_id)
                ->where('attendance_date', $request->attendance_date)
                ->where('is_delete', 0)
                ->get();

            foreach ($records as $record) {
                $existingAttendance[$record->student_id] = $record->attendance_type;
            }
        }

        return Inertia::render('Teacher/Attendance/Index', [
            'classes'            => ClassTeacherModel::getMyClassSubjectGroup(Auth::user()->id),
            'students'           => $students,
            'selectedClass'      => $request->class_id,
            'selectedDate'       => $request->attendance_date,
            'existingAttendance' => $existingAttendance,
        ]);
    }

    public function attendanceStudentTeacherSave(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $entries = $request->has('entries')
                ? $request->input('entries')
                : [[
                    'student_id'      => $request->input('student_id'),
                    'class_id'        => $request->input('class_id'),
                    'attendance_date' => $request->input('attendance_date'),
                    'attendance_type' => $request->input('attendance_type'),
                ]];

            foreach ($entries as $entry) {
                $record = StudentAttendanceModel::where('student_id', $entry['student_id'])
                    ->where('class_id', $entry['class_id'])
                    ->where('attendance_date', $entry['attendance_date'])
                    ->first();

                if ($record) {
                    $record->is_delete = 0;
                } else {
                    $record = new StudentAttendanceModel();
                    $record->student_id      = $entry['student_id'];
                    $record->class_id        = $entry['class_id'];
                    $record->attendance_date = $entry['attendance_date'];
                    $record->created_by      = Auth::user()->id;
                    $record->is_delete       = 0;
                }

                $record->attendance_type = $entry['attendance_type'];
                $record->save();
            }

            return response()->json(['success' => true, 'message' => 'Presences enregistrees.'], 200);

        } catch (\Exception $e) {
            Log::error('Erreur enregistrement presence teacher : ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur serveur.'], 500);
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
        $studentId = Auth::user()->id;

        $counts = StudentAttendanceModel::where('student_id', $studentId)
            ->where('is_delete', 0)
            ->selectRaw("
                SUM(attendance_type = 'present')  as present,
                SUM(attendance_type = 'late')     as late,
                SUM(attendance_type = 'absent')   as absent,
                SUM(attendance_type = 'half_day') as half_day
            ")
            ->first();

        $classStudent = [
            'present'  => (int) ($counts->present  ?? 0),
            'late'     => (int) ($counts->late      ?? 0),
            'absent'   => (int) ($counts->absent    ?? 0),
            'half_day' => (int) ($counts->half_day  ?? 0),
        ];

        return Inertia::render('Student/Attendance/Index', [
            'attendance'   => StudentAttendanceModel::getMyAttendance($studentId, 10),
            'classStudent' => $classStudent,
        ]);
    }

    public function parentStudentAttendance(int $student_id)
    {
        $student = User::getSingle($student_id);

        $counts = StudentAttendanceModel::where('student_id', $student_id)
            ->where('is_delete', 0)
            ->selectRaw("
                SUM(attendance_type = 'present')  as present,
                SUM(attendance_type = 'late')     as late,
                SUM(attendance_type = 'absent')   as absent,
                SUM(attendance_type = 'half_day') as half_day
            ")
            ->first();

        $classStudent = [
            'present'  => (int) ($counts->present  ?? 0),
            'late'     => (int) ($counts->late      ?? 0),
            'absent'   => (int) ($counts->absent    ?? 0),
            'half_day' => (int) ($counts->half_day  ?? 0),
        ];

        return Inertia::render('Parent/Attendance/Index', [
            'student'      => $student,
            'attendance'   => StudentAttendanceModel::getMyAttendance($student_id, 10),
            'classStudent' => $classStudent,
        ]);
    }

    public function attendanceReportExport()
    {
        return Excel::download(new ExportAttendance, 'attendance_' . date('d_m_Y') . '.xlsx');
    }
}
