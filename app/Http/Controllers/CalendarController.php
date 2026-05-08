<?php

namespace App\Http\Controllers;

use App\Models\ClassSubjectModel;
use App\Models\ClassTeacherModel;
use App\Models\ClassTimetableModel;
use App\Models\ExaminationModel;
use App\Models\ScheduleModel;
use App\Models\User;
use App\Models\WeekModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CalendarController extends Controller
{

    public function myCalendar()
    {
        return Inertia::render('Student/Calendar/Index', [
            'timetable' => $this->getTimetable(Auth::user()->class_id),
            'examTimetable' => $this->getExamTimetable(Auth::user()->class_id),
        ]);
    }

    public function getTimetable($class_id): array
    {
        $result = [];
        $getCalendar = ClassSubjectModel::getSubject($class_id);

        foreach ($getCalendar as $calendar) {
            $dataCalendar = [];
            $dataCalendar['name'] = $calendar->subject_name;

            $getWeek = WeekModel::getAllWeek();
            $week = [];
            foreach ($getWeek as $weekValue) {
                $dataWeek = [];
                $dataWeek['week_name'] = $weekValue->name;
                $dataWeek['day'] = $weekValue->day;

                $classSubject = ClassTimetableModel::getClassTimetable($calendar->class_id, $calendar->subject_id, $weekValue->id);
                if (!empty($classSubject)) {
                    $dataWeek['start_time'] = $classSubject->start_time;
                    $dataWeek['end_time'] = $classSubject->end_time;
                    $dataWeek['room_number'] = $classSubject->room_number;
                } else {
                    $dataWeek['start_time'] = '';
                    $dataWeek['end_time'] = '';
                    $dataWeek['room_number'] = '';
                }
                $week[] = $dataWeek;
            }
            $dataCalendar['weeks'] = $week;
            $result[] = $dataCalendar;
        }
        return $result;
    }

    public function getExamTimetable($class_id): array
    {
        $getExamSchedule = ScheduleModel::getExam($class_id);
        $result = [];
        foreach ($getExamSchedule as $examSchedule) {
            $dataExam = [];
            $dataExam['name'] = $examSchedule->exam_name;
            $getExamTimetable = ScheduleModel::getExamTimetable($examSchedule->exam_id, $class_id);
            $results = [];
            foreach ($getExamTimetable as $examTimetable) {
                $dataSchedule = [];
                $dataSchedule['subject_name'] = $examTimetable->subject_name;
                $dataSchedule['exam_date'] = $examTimetable->exam_date;
                $dataSchedule['start_time'] = $examTimetable->start_time;
                $dataSchedule['end_time'] = $examTimetable->end_time;
                $dataSchedule['room_number'] = $examTimetable->room_number;
                $dataSchedule['full_marks'] = $examTimetable->full_marks;
                $dataSchedule['passing_marks'] = $examTimetable->passing_marks;
                $results[] = $dataSchedule;
            }
            $dataExam['exams'] = $results;
            $result[] = $dataExam;
        }
        return $result;
    }

    public function parentStudentExamCalendar($student_id)
    {
        $getStudent = User::getSingle($student_id);
        return Inertia::render('Parent/Calendar/Index', [
            'timetable' => $this->getTimetable($getStudent->class_id),
            'examTimetable' => $this->getExamTimetable($getStudent->class_id),
            'student' => $getStudent,
        ]);
    }

    public function myTeacherCalendar()
    {
        $teacher_id = Auth::user()->id;
        return Inertia::render('Teacher/Calendar/Index', [
            'classTimetable' => ClassTeacherModel::getTeacherCalendar($teacher_id),
            'examTimetable' => ScheduleModel::getExamTimetableTeacher($teacher_id),
        ]);
    }

}
