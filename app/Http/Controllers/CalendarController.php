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

class CalendarController extends Controller
{

    public function myCalendar(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['header_title'] = "Mon Calendrier";
        $data['getMyTimetable'] = $this->getTimetable(Auth::user()->class_id);
        $data['getExamTimetable'] = $this->getExamTimetable(Auth::user()->class_id);
        return view('student.calendar', $data);
    }

    public function getTimetable($class_id): array
    {
        $result = array();
        $getCalendar = ClassSubjectModel::getSubject($class_id);

        foreach ($getCalendar as $calendar) {
            $dataCalendar = array();
            $dataCalendar['name'] = $calendar->subject_name;

            $getWeek = WeekModel::getAllWeek();
            $week = array();
            foreach ($getWeek as $weekValue) {
                $dataWeek = array();
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
        $result = array();
        foreach ($getExamSchedule as $examSchedule) {
            $dataExam = array();
            $dataExam['name'] = $examSchedule->exam_name;
            $getExamTimetable = ScheduleModel::getExamTimetable($examSchedule->exam_id, $class_id);
            $results = array();
            foreach ($getExamTimetable as $examTimetable) {
                $dataSchedule = array();
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

    public function parentStudentExamCalendar($student_id): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['header_title'] = "Son Calendrier";
        $getStudent = User::getSingle($student_id);
        $data['getStudent'] = $getStudent;
        $data['getMyTimetable'] = $this->getTimetable($getStudent->class_id);
        $data['getExamTimetable'] = $this->getExamTimetable($getStudent->class_id);
        return view('parent.calendar', $data);
    }

    public function myTeacherCalendar(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['header_title'] = "Mon calendrier";
        $teacher_id = Auth::user()->id;
        $data['getClassTeacherTimetable'] = ClassTeacherModel::getTeacherCalendar($teacher_id);
        return view('teacher.calendar', $data);
    }

}
