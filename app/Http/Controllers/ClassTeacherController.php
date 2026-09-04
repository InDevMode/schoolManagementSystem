<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\ClassTeacherModel;
use App\Models\User;
use App\Services\RefDataCache;
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
            'classes'       => RefDataCache::classes(),
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

    /**
     * Retourne les classes assignées à un professeur (JSON — pour le modal admin).
     */
    public function teacherClasses(int $teacher_id): \Illuminate\Http\JsonResponse
    {
        $classes = ClassTeacherModel::select(
                'class_teacher.id',
                'class_teacher.status',
                'class.id as class_id',
                'class.name as class_name',
            )
            ->join('class', 'class.id', '=', 'class_teacher.class_id')
            ->where('class_teacher.teacher_id', $teacher_id)
            ->where('class_teacher.is_delete', 0)
            ->where('class.is_delete', 0)
            ->orderBy('class.name')
            ->get()
            ->map(fn($row) => [
                'id'         => $row->class_id,
                'name'       => $row->class_name,
                'status'     => (int) $row->status,
                // Nombre d'apprenants
                'students'   => \App\Models\User::where('class_id', $row->class_id)
                    ->where('user_type', 3)
                    ->where('is_delete', 0)
                    ->where('status', 1)
                    ->count(),
            ]);

        return response()->json(['classes' => $classes]);
    }

    public function myClassSubject()
    {        $teacherId = Auth::user()->id;

        $classSubjects = ClassTeacherModel::getMyClassSubjectGroup($teacherId);

        // Pour chaque classe, on enrichit avec les matières et le nombre d'apprenants
        $classSubjects = $classSubjects->map(function ($item) {
            // Matières assignées à cette classe
            $item->subjects = \App\Models\ClassSubjectModel::select(
                    'class_subject.id',
                    'class_subject.coefficient',
                    'subject.id as subject_id',
                    'subject.name as subject_name',
                    'subject.type as subject_type',
                )
                ->join('subject', 'subject.id', '=', 'class_subject.subject_id')
                ->where('class_subject.class_id', $item->class_id)
                ->where('class_subject.is_delete', 0)
                ->where('class_subject.status', 1)
                ->where('subject.status', 1)
                ->orderBy('subject.name')
                ->get();

            // Nombre d'apprenants dans cette classe
            $item->student_count = \App\Models\User::where('class_id', $item->class_id)
                ->where('user_type', 3)
                ->where('is_delete', 0)
                ->where('status', 1)
                ->count();

            return $item;
        });

        return Inertia::render('Teacher/ClassSubject/Index', [
            'classSubjects' => $classSubjects,
        ]);
    }

}
