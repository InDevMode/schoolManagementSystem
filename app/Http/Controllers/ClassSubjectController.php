<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\ClassSubjectModel;
use App\Models\SubjectModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ClassSubjectController extends Controller
{
    public function list(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['getClassSubject'] = ClassSubjectModel::getAllClassSubject(10);
        $data['header_title'] = "Listes des matières assignées";
        return view('admin.assign_subject.list', $data);
    }

    public function add(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $data['getClass'] = ClassModel::getClass();
        $data['getSubject'] = SubjectModel::getSubject();
        $data['header_title'] = "Assignez une matière";
        return view('admin.assign_subject.add', $data);
    }

    public function create(Request $request): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {

            if (!empty($request->subject_id)) {
                foreach ($request->subject_id as $subject_id) {
                    $classSubjectAlreadyExist = ClassSubjectModel::getAlreadyExist($request->class_id, $subject_id);
                    if (!empty($classSubjectAlreadyExist)) {
                        $classSubjectAlreadyExist->status = $request->status;
                        $classSubjectAlreadyExist->save();
                    }
                    $classSubject = new ClassSubjectModel;
                    $classSubject->class_id = $request->class_id;
                    $classSubject->subject_id = $subject_id;
                    $classSubject->status = $request->status;
                    $classSubject->created_by = Auth::user()->id;
                    $classSubject->save();

                }
            } else {
                return redirect()->back()->with('error', 'Veuillez bien remplir tous les champs s\'il vous plaît....');
            }

            return redirect('admin/assign_subject/list')->with('success', 'Ces matières ont été bien assignées à cette classe avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur lors de la création de l'assignation de ces matières. " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }

    public function edit($id): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $editExisting = ClassSubjectModel::getSingle($id);
        if (!empty($editExisting)) {
            $data['getClassSubject'] = $editExisting;
            $data['getClass'] = ClassModel::getClass();
            $data['getSubject'] = SubjectModel::getSubject();
            $data['getAssignSubject'] = ClassSubjectModel::getAssignSubject($editExisting->class_id);
            $data['header_title'] = "Modifier une assignation";
            return view('admin.assign_subject.edit', $data);
        } else {
            abort(404);
        }
    }

    public function update(Request $request): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            ClassSubjectModel::deleteSubjectAssign($request->class_id);

            if (!empty($request->subject_id)) {
                foreach ($request->subject_id as $subject_id) {
                    $classSubjectAlreadyExist = ClassSubjectModel::getAlreadyExist($request->class_id, $subject_id);
                    if (!empty($classSubjectAlreadyExist)) {
                        $classSubjectAlreadyExist->status = $request->status;
                        $classSubjectAlreadyExist->save();
                    }
                    $classSubject = new ClassSubjectModel;
                    $classSubject->class_id = $request->class_id;
                    $classSubject->subject_id = $subject_id;
                    $classSubject->status = $request->status;
                    $classSubject->created_by = Auth::user()->id;
                    $classSubject->save();
                }
            } else {
                return redirect()->back()->with('error', 'Veuillez bien remplir tous les champs s\'il vous plaît....');
            }

            return redirect('admin/assign_subject/list')->with('success', 'La modification de ces assignations ont été effectuée avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur lors de la modification de l'assignation de ces matières. " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }

    public function editSingle($id)
    {
        $editExisting = ClassSubjectModel::getSingle($id);
        if (!empty($editExisting)) {
            $data['getClassSubject'] = $editExisting;
            $data['getClass'] = ClassModel::getClass();
            $data['getSubject'] = SubjectModel::getSubject();
            $data['getAssignSubject'] = ClassSubjectModel::getAssignSubject($editExisting->class_id);
            $data['header_title'] = "Modifier une assignation";
            return view('admin.assign_subject.edit_single', $data);
        } else {
            abort(404);
        }
    }

    public function updateSingle(Request $request, $id): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {

            $classSubjectAlreadyExist = ClassSubjectModel::getAlreadyExist($request->class_id, $request->subject_id);
            if (!empty($classSubjectAlreadyExist)) {
                $classSubjectAlreadyExist->status = $request->status;
                $classSubjectAlreadyExist->save();

                return redirect('admin/assign_subject/list')->with('success', 'Le status de cette assignation a été modifié avec succès.');
            }else{
                $classSubject = ClassSubjectModel::getSingle($id);;
                $classSubject->class_id = $request->class_id;
                $classSubject->subject_id = $request->subject_id;
                $classSubject->status = $request->status;
                $classSubject->save();
            }

            return redirect('admin/assign_subject/list')->with('success', 'Cette assignation a été modifiée avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur lors de la modification de l'assignation de cette matière. " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }

    }

    public function delete($id)
    {
        $classSubject = ClassSubjectModel::getSingle($id);
        if ($classSubject) {
            $classSubject->is_delete = 1;
            $classSubject->save();
            return redirect('admin/assign_subject/list')->with('success', 'Cette assignation a été supprimée avec succès.');
        } else {
            abort(404);
        }
    }

}
