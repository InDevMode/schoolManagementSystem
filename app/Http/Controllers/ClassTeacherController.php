<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\ClassTeacherModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ClassTeacherController extends Controller
{
    public function list()
    {
        return Inertia::render('Admin/AssignClass/Index', [
            'classTeachers' => ClassTeacherModel::getAllClassTeacher(15),
            'classes'       => ClassModel::getClass(),
            'teachers'      => User::getTeacher(),
        ]);
    }

    public function create(Request $request): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            if (!empty($request->teacher_id)) {
                foreach ($request->teacher_id as $teacher_id) {
                    $classTeacherAlreadyExist = ClassTeacherModel::getAlreadyExist($request->class_id, $teacher_id);
                    if (!empty($classTeacherAlreadyExist)) {
                        $classTeacherAlreadyExist->status = $request->status;
                        $classTeacherAlreadyExist->save();
                    }
                    $classTeacher = new ClassTeacherModel;
                    $classTeacher->class_id = $request->class_id;
                    $classTeacher->teacher_id = $teacher_id;
                    $classTeacher->status = $request->status;
                    $classTeacher->created_by = Auth::user()->id;
                    $classTeacher->save();
                }
            } else {
                return redirect()->back()->with('error', 'Veuillez bien remplir tous les champs s\'il vous plaît....');
            }

            return redirect('admin/assign_class/list')->with('success', 'Ces professeurs ont été bien assignées à cette classe avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur lors de la création de l'assignation de cette classe. " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }

    public function update(Request $request): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            ClassTeacherModel::deleteClassAssign($request->class_id);
            if (!empty($request->teacher_id)) {
                foreach ($request->teacher_id as $teacher_id) {
                    $classTeacherAlreadyExist = ClassTeacherModel::getAlreadyExist($request->class_id, $teacher_id);
                    if (!empty($classTeacherAlreadyExist)) {
                        $classTeacherAlreadyExist->status = $request->status;
                        $classTeacherAlreadyExist->save();
                    }
                    $classTeacher = new ClassTeacherModel;
                    $classTeacher->class_id = $request->class_id;
                    $classTeacher->teacher_id = $teacher_id;
                    $classTeacher->status = $request->status;
                    $classTeacher->created_by = Auth::user()->id;
                    $classTeacher->save();
                }
            } else {
                return redirect()->back()->with('error', 'Veuillez bien remplir tous les champs s\'il vous plaît....');
            }

            return redirect('admin/assign_class/list')->with('success', 'La modification de ces assignations ont été effectuée avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur lors de la modification de l'assignation de cette classe. " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }

    public function updateSingle(Request $request, $id): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $classClassAlreadyExist = ClassTeacherModel::getAlreadyExist($request->class_id, $request->teacher_id);
            if (!empty($classClassAlreadyExist)) {
                $classClassAlreadyExist->status = $request->status;
                $classClassAlreadyExist->save();

                return redirect('admin/assign_class/list')->with('success', 'Le status de cette assignation a été modifié avec succès.');
            } else {
                $classClass = ClassTeacherModel::getSingle($id);
                $classClass->class_id = $request->class_id;
                $classClass->teacher_id = $request->teacher_id;
                $classClass->status = $request->status;
                $classClass->save();
            }

            return redirect('admin/assign_class/list')->with('success', 'Cette assignation a été modifiée avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur lors de la modification de l'assignation de cette classe. " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }

    }

    public function delete($id)
    {
        $classTeacher = ClassTeacherModel::getSingle($id);
        if ($classTeacher) {
            $classTeacher->is_delete = 1;
            $classTeacher->save();
            return redirect('admin/assign_class/list')->with('success', 'Cette assignation a été supprimée avec succès.');
        } else {
            abort(404);
        }
    }

    public function myClassSubject()
    {
        return Inertia::render('Teacher/ClassSubject/Index', [
            'classSubjects' => ClassTeacherModel::getMyClassSubjectGroup(Auth::user()->id),
        ]);
    }

}
