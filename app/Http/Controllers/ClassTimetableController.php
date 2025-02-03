<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\ClassSubjectModel;
use App\Models\ClassTimetableModel;
use App\Models\WeekModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClassTimetableController extends Controller
{
    public function list(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['header_title'] = "Liste des horaires de cours";
        $data['getClass'] = ClassModel::getClass();

        if (!empty($request->class_id)) {
            $data['getSubject'] = ClassTimetableModel::getSubject($request->class_id);
        }
        $data['getWeek'] = WeekModel::getAllWeek();

        $week = [];
        foreach ($data['getWeek'] as $weekValue) {
            $weekEntry = [
                'week_id' => $weekValue->id,
                'week_name' => $weekValue->name,
                'start_time' => '',
                'end_time' => '',
                'room_number' => ''
            ];

            if (!empty($request->class_id) && !empty($request->subject_id)) {
                $classSubjectTimetable = ClassTimetableModel::getClassTimetable($request->class_id, $request->subject_id, $weekValue->id);
                if ($classSubjectTimetable) {
                    $weekEntry['start_time'] = $classSubjectTimetable->start_time;
                    $weekEntry['end_time'] = $classSubjectTimetable->end_time;
                    $weekEntry['room_number'] = $classSubjectTimetable->room_number;
                }
            }

            $week[] = $weekEntry;
        }
        $data['week'] = $week;
        return view('admin.class_timetable.list', $data);
    }

    public function getSubject(Request $request): \Illuminate\Http\JsonResponse
    {
        $getSubject = ClassTimetableModel::getSubject($request->class_id);

        if ($getSubject->isEmpty()) {
            return response()->json(['subjects' => []], 200);
        }
        $subjects = [];

        foreach ($getSubject as $subject) {
            $subjects[] = [
                'id' => $subject->subject_id,
                'name' => $subject->subject_name,
            ];
        }
        return response()->json(['subjects' => $subjects], 200);
    }

    public function add(Request $request): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            ClassTimetableModel::where('class_id', '=', $request->class_id)->where('subject_id', '=', $request->subject_id)->delete();
            foreach ($request->timetable as $timetable) {
                if (!empty($timetable['week_id']) && !empty($timetable['start_time']) && !empty($timetable['end_time']) && !empty($timetable['room_number'])) {
                    $classTimetable = new ClassTimetableModel;
                    $classTimetable->class_id = $request->class_id;
                    $classTimetable->subject_id = $request->subject_id;
                    $classTimetable->week_id = $timetable['week_id'];
                    $classTimetable->start_time = $timetable['start_time'];
                    $classTimetable->end_time = $timetable['end_time'];
                    $classTimetable->room_number = $timetable['room_number'];
                    $classTimetable->save();
                }
            }

            return redirect('admin/class_timetable/list')->with('success', 'Ces horaires de cours ont été bien crée à cette classe avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur lors de la création de ces horaires de cours. " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }

    public function studentTimetable(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['header_title'] = "Horaire de cours de l'élève";
        return view('student.timetable', $data);
    }

    public function create()
    {
    }

    public function edit()
    {
    }

    public function update()
    {
    }

    public function delete()
    {
    }
}
