<?php

namespace App\Http\Controllers;

use App\Models\ClassSubjectModel;
use App\Models\ClassTeacherModel;
use App\Models\ClassTimetableModel;
use App\Models\StaffEventModel;
use App\Models\User;
use App\Models\WeekModel;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CalendarController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────
    // ADMIN / SUPER ADMIN
    // ─────────────────────────────────────────────────────────────────────

    public function adminCalendar()
    {
        return Inertia::render('Admin/Calendar/Index', [
            'events' => StaffEventModel::getCalendarEvents(),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // ÉTUDIANT
    // ─────────────────────────────────────────────────────────────────────

    public function myCalendar()
    {
        return Inertia::render('Student/Calendar/Index', [
            'timetable' => $this->buildTimetableMatrix(Auth::user()->class_id),
            'events'    => StaffEventModel::getCalendarEvents(),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // PARENT
    // ─────────────────────────────────────────────────────────────────────

    public function parentStudentExamCalendar($student_id)
    {
        $getStudent = User::getSingle($student_id);

        return Inertia::render('Parent/Calendar/Index', [
            'timetable' => $this->buildTimetableMatrix($getStudent->class_id),
            'events'    => StaffEventModel::getCalendarEvents(),
            'student'   => $getStudent,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // PROFESSEUR
    // ─────────────────────────────────────────────────────────────────────

    public function myTeacherCalendar()
    {
        return Inertia::render('Teacher/Calendar/Index', [
            'classTimetable' => ClassTeacherModel::getTeacherCalendar(Auth::user()->id),
            'events'         => StaffEventModel::getCalendarEvents(),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // HELPER — matrice matières × semaine
    // ─────────────────────────────────────────────────────────────────────

    public function buildTimetableMatrix(int $class_id): array
    {
        $result   = [];
        $subjects = ClassSubjectModel::getSubject($class_id);
        $weeks    = WeekModel::getAllWeek();

        foreach ($subjects as $subject) {
            $weekRows = [];

            foreach ($weeks as $week) {
                $slot = ClassTimetableModel::getClassTimetable(
                    $subject->class_id,
                    $subject->subject_id,
                    $week->id
                );

                $weekRows[] = [
                    'week_id'     => $week->id,
                    'week_name'   => $week->name,
                    'day'         => $week->day,   // 1=Lun … 7=Dim (ISO)
                    'start_time'  => $slot?->start_time  ?? '',
                    'end_time'    => $slot?->end_time    ?? '',
                    'room_number' => $slot?->room_number ?? '',
                ];
            }

            $result[] = [
                'name'  => $subject->subject_name,
                'weeks' => $weekRows,
            ];
        }

        return $result;
    }
}
