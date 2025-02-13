<?php

namespace App\Http\Controllers;

use App\Models\ClassSubjectModel;
use App\Models\ClassTimetableModel;
use App\Models\ExaminationModel;
use App\Models\WeekModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{

    public function myCalendar(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {

        $data['header_title'] = "Mon Calendrier";

        $result = array();
        $getCalendar = ClassSubjectModel::getSubject(Auth::user()->class_id);
        foreach ($getCalendar as $calendar) {
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
        $data['getMyCalendar'] = $result;
        return view('student.calendar', $data);
    }

}
