<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\ClassSubjectModel;
use App\Models\ExaminationModel;
use Illuminate\Http\Request;
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

    public function create(Request $request)
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

    public function examSchedule(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['header_title'] = "Programmation des examens";
        $data['getClass'] = ClassModel::getClass();
        $data['getExams'] = ExaminationModel::getExams();
        if (!empty($request->get('exam_id')) && !empty($request->get('class_id'))) {
            $getSubject = ClassSubjectModel::studentStubject(10, $request->get('class_id'));
            dd($getSubject);
        }
        return view('admin.examinations.schedule.list', $data);
    }

}
