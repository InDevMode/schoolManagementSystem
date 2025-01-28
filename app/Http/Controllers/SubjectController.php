<?php

namespace App\Http\Controllers;

use App\Models\ClassSubjectModel;
use App\Models\SubjectModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SubjectController extends Controller
{
    public function list(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['header_title'] = "Liste des matières";
        $data['getSubject'] = SubjectModel::getAllSubject(10);
        return view('admin.subject.list', $data);
    }

    public function add(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['header_title'] = "Créer une matière";
        return view('admin.subject.add', $data);
    }

    public function create(Request $request): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {

            $existingSubject = SubjectModel::getNameSingle($request->name);

            if ($existingSubject) {
                return redirect()->back()->with('error', 'Une matière avec ce nom existe déjà.');
            }

            $subject = new SubjectModel();
            $subject->name = trim($request->name);
            $subject->type = trim($request->type);
            $subject->status = trim($request->status);
            $subject->created_by = auth()->user()->id;
            $subject->save();

            return redirect('admin/subject/list')->with('success', 'Cette matière a été créé avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur lors de la création d'une matière : " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }

    public function edit($id)
    {
        $data['getSubject'] = SubjectModel::getSingle($id);
        if (!empty($data['getSubject'])) {
            $data['header_title'] = "Modifier une matière";
            return view('admin.subject.edit', $data);
        } else {
            abort(404);
        }
    }

    public function update(Request $request, $id): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $subject = SubjectModel::getSingle($id);
            $existingSubject = SubjectModel::checkNameSingle($request->name, $id);

            if ($existingSubject) {
                return redirect()->back()->with('error', 'Une matière avec ce nom existe déjà.');
            }

            if (!$subject) {
                return redirect()->back()->with('error', 'Cette matière est introuvable.');
            }

            $subject->name = trim($request->name);
            $subject->type = trim($request->type);
            $subject->status = intval($request->status);
            $subject->save();
            return redirect('admin/subject/list')->with('success', 'Cette matière a été modifiée avec succès.');

        } catch (\Exception $e) {
            Log::error("Erreur lors de la modification de cette matière : " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }

    public function delete($id)
    {
        $class = SubjectModel::getSingle($id);
        if ($class) {
            $class->is_delete = 1;
            $class->save();
            return redirect('admin/subject/list')->with('success', 'Cette matière a été supprimé avec succès.');
        } else {
            abort(404);
        }
    }

    public function studentSubject(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['getStudentSubject'] = ClassSubjectModel::studentStubject(10, Auth::user()->class_id);
        $data['student_id'] = Auth::user()->id;
        $data['header_title'] = "Mes Cours";
        return view('student.subject', $data);

    }
}
