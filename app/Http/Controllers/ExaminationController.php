<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\ClassSubjectModel;
use App\Models\ExaminationModel;
use App\Models\ScheduleModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ExaminationController extends Controller
{
    public function list(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['getExams'] = ExaminationModel::getExaminations(10);
        $data['header_title'] = "Liste des évaluations";
        return view('admin.examinations.exam.list', $data);
    }

    public function add(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['header_title'] = "Créer une évaluation";
        return view('admin.examinations.exam.add', $data);
    }

    public function create(Request $request): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $existingClass = ExaminationModel::getNameSingle($request->name);

            if ($existingClass) {
                return redirect()->back()->with('error', 'Une évaluation avec ce nom existe déjà.');
            }

            $exam = new ExaminationModel;
            $exam->name = trim($request->name);
            $exam->note = trim($request->note);
            $exam->created_by = auth()->user()->id;
            $exam->save();

            return redirect('admin/examinations/exam/list')->with('success', 'Cette évaluation a été créé avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur lors de la création d'une évaluation : " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }

    public function edit($id)
    {
        $data['getExams'] = ExaminationModel::getSingle($id);
        if (!empty($data['getExams'])) {
            $data['header_title'] = "Modifier une évaluation";
            return view('admin.examinations.exam.edit', $data);
        } else {
            abort(404);
        }
    }

    public function update(Request $request, $id): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $exam = ExaminationModel::getSingle($id);
            $existingExam = ExaminationModel::checkNameSingle($request->name, $id);

            if ($existingExam) {
                return redirect()->back()->with('error', 'Une évaluation avec ce nom existe déjà.');
            }

            if (!$exam) {
                return redirect()->back()->with('error', 'Cette évaluation est introuvable.');
            }

            $exam->name = trim($request->name);
            $exam->note = trim($request->note);
            $exam->save();
            return redirect('admin/examinations/exam/list')->with('success', 'Cette évaluation a été modifiée avec succès.');

        } catch (\Exception $e) {
            Log::error("Erreur lors de la modification de cette évaluation : " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }

    public function delete($id)
    {
        $exam = ExaminationModel::getSingle($id);
        if ($exam) {
            $exam->is_delete = 1;
            $exam->save();
            return redirect('admin/examinations/exam/list')->with('success', 'Cette évaluation a été supprimé avec succès.');
        } else {
            abort(404);
        }
    }

    public function scheduleList(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['header_title'] = "Programmation des examens";
        $data['getClass'] = ClassModel::getClass();
        $data['getExams'] = ExaminationModel::getExams();
        $result = array();

        if (!empty($request->get('exam_id')) && !empty($request->get('class_id'))) {
            $getSubject = ClassSubjectModel::getSubject($request->get('class_id'));

            foreach ($getSubject as $subject) {
                $dataSchedule = array();
                $dataSchedule['subject_id'] = $subject->subject_id;
                $dataSchedule['class_id'] = $subject->class_id;
                $dataSchedule['subject_name'] = $subject->subject_name;
                $dataSchedule['subject_type'] = $subject->subject_type;

                $examSchedule = ScheduleModel::getExamSchedule($request->get('exam_id'), $request->get('class_id'), $subject->subject_id);
                if ($examSchedule) {
                    $dataSchedule['exam_date'] = $examSchedule->exam_date;
                    $dataSchedule['start_time'] = $examSchedule->start_time;
                    $dataSchedule['end_time'] = $examSchedule->end_time;
                    $dataSchedule['room_number'] = $examSchedule->room_number;
                    $dataSchedule['full_marks'] = $examSchedule->full_marks;
                    $dataSchedule['passing_marks'] = $examSchedule->passing_marks;
                } else {
                    $dataSchedule['exam_date'] = '';
                    $dataSchedule['start_time'] = '';
                    $dataSchedule['end_time'] = '';
                    $dataSchedule['room_number'] = '';
                    $dataSchedule['full_marks'] = '';
                    $dataSchedule['passing_marks'] = '';

                }

                $result[] = $dataSchedule;
            }
        }
        $data['getExamSchedule'] = $result;
        return view('admin.examinations.schedule.list', $data);
    }

    public function scheduleCreate(Request $request): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $existingSchedules = ScheduleModel::checkExamSchedule($request->exam_id, $request->class_id);
            ScheduleModel::deleteExamSchedule($request->exam_id, $request->class_id);
            $isUpdated = $existingSchedules;
            if (!empty($request->schedule)) {
                foreach ($request->schedule as $schedule) {
                    if (!empty($schedule['subject_id']) &&
                        !empty($schedule['exam_date']) &&
                        !empty($schedule['start_time']) &&
                        !empty($schedule['end_time']) &&
                        !empty($schedule['room_number']) &&
                        !empty($schedule['full_marks']) &&
                        !empty($schedule['passing_marks'])
                    ) {
                        $examSchedule = new ScheduleModel;
                        $examSchedule->exam_id = $request->exam_id;
                        $examSchedule->class_id = $request->class_id;
                        $examSchedule->subject_id = $schedule['subject_id'];
                        $examSchedule->exam_date = $schedule['exam_date'];
                        $examSchedule->start_time = $schedule['start_time'];
                        $examSchedule->end_time = $schedule['end_time'];
                        $examSchedule->room_number = $schedule['room_number'];
                        $examSchedule->full_marks = $schedule['full_marks'];
                        $examSchedule->passing_marks = $schedule['passing_marks'];
                        $examSchedule->created_by = auth()->user()->id;
                        $examSchedule->save();
                    }
                }
            }
            $message = $isUpdated
                ? 'Ce programme d\'évaluation a été modifié avec succès.'
                : 'Ce programme d\'évaluation a été créé avec succès.';

            return redirect('admin/examinations/schedule/list')->with('success', $message);
        } catch (\Exception $e) {
            Log::error("Erreur lors de la création de ce programme d'évaluation : " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }

    public function myExamTimetable(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['header_title'] = "Mon calendrier d'examens";
        $class_id = Auth::user()->class_id;
        $getExamSchedule = ScheduleModel::getExam($class_id);
        $results = array();
        foreach ($getExamSchedule as $examSchedule) {
            $dataExam = array();
            $dataExam['name'] = $examSchedule->exam_name;
            $getExamTimetable = ScheduleModel::getExamTimetable($examSchedule->exam_id, $class_id);
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
            $dataExam['getExams'] = $results;
            $result[] = $dataExam;
        }
        $data['getExamTimetable'] = $result;
        return view('student.exam_timetable', $data);
    }

}
