<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\ClassSubjectModel;
use App\Models\ClassTimetableModel;
use App\Models\WeekModel;
use Illuminate\Http\Request;

class ClassTimetableController extends Controller
{
    public function list(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['header_title'] = "Liste des horaires de cours";
        $data['getClass'] = ClassModel::getClass();

        if (!empty($request->class_id)) {
            $data['getSubject'] = ClassTimetableModel::getSubject($request->class_id);
        }
        $data['getClassTimetable'] = ClassTimetableModel::getClassTimetable(10);
        $data['getWeek'] = WeekModel::getAllWeek();

        // Correction de la boucle
        $week = [];
        foreach ($data['getWeek'] as $weekValue) {
            $week[] = [
                'week_id' => $weekValue->id,
                'week_name' => $weekValue->name
            ];
        }
        $data['week'] = $week;
        return view('admin.class_timetable.list', $data);
    }

    public function add(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['header_title'] = "Ajouter des horaires de cours";
        $data['getClass'] = ClassModel::getClass();
        return view('admin.class_timetable.add', $data);

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
