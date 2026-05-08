<?php

namespace App\Http\Controllers;

use App\Models\ClassSubjectModel;
use App\Models\SubjectModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class SubjectController extends Controller
{
    public function list()
    {
        return Inertia::render('Admin/Subjects/Index', [
            'subjects' => SubjectModel::getAllSubject(15),
        ]);
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

    public function studentSubject()
    {
        return Inertia::render('Student/Subjects/Index', [
            'subjects' => ClassSubjectModel::studentStubject(15, Auth::user()->class_id),
        ]);
    }

    public function parentStudentSubject($student_id)
    {
        $student = User::getSingle($student_id);
        return Inertia::render('Parent/Subjects/Index', [
            'student' => $student,
            'subjects' => ClassSubjectModel::studentStubject(15, $student->class_id),
        ]);
    }
}
