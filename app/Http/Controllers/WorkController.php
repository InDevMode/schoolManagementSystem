<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\ClassSubjectModel;
use App\Models\ClassTeacherModel;
use App\Models\HomeworkModel;
use App\Models\WorkModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WorkController extends Controller
{
    public function practicalWorksList()
    {
        $data['header_title'] = 'Liste des travaux';
        $data['getWorks'] = WorkModel::getWorks(5);
        return view('admin.practicalworks.list', $data);
    }

    public function practicalWorksAdd()
    {
        $data['header_title'] = 'Ajouter un travail';
        $data['getClass'] = ClassModel::getClass();
        return view('admin.practicalworks.add', $data);
    }

    public function getSubjectByClassId($classId)
    {
        $data['getSubject'] = ClassSubjectModel::getSubject($classId);
        return response()->json($data);
    }

    public function practicalWorksCreate(Request $request)
    {
        try {

            $work = new WorkModel();
            $work->class_id = intval($request->class_id);
            $work->subject_id = intval($request->subject_id);
            $work->work_date = trim($request->work_date);
            $work->submission_date = trim($request->submission_date);
            $work->description = trim($request->description);
            $work->created_by = Auth::user()->id;

            if (!empty($request->file('document_file'))) {
                $ext = $request->file('document_file')->getClientOriginalExtension();
                $file = $request->file('document_file');
                $randomStr = 'homework_admin' . date('dmYhis') . Str::random(20);
                $fileName = strtolower($randomStr) . '.' . $ext;
                $file->move('upload/practicalworks/', $fileName);
                $work->document_file = $fileName;
            }

            $work->save();

            return redirect('admin/practicalworks/homework/list')->with('success', 'Ce travail de maison a été créé avec succès.');

        } catch (\Exception $e) {
            Log::error("Erreur lors de la création d'un  travail de maison : " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }

    public function practicalWorksEdit($id)
    {
        $data['header_title'] = 'Modifier un travail';
        $data['getClass'] = ClassModel::getClass();
        $data['getWorks'] = WorkModel::getSingle($id);
        $data['getSubject'] = ClassSubjectModel::getSubject($data['getWorks']->class_id);
        return view('admin.practicalworks.edit', $data);
    }

    public function practicalWorksUpdate(Request $request, $id)
    {

        try {
            $work = WorkModel::getSingle($id);
            $work->class_id = intval($request->class_id);
            $work->subject_id = intval($request->subject_id);
            $work->work_date = trim($request->work_date);
            $work->submission_date = trim($request->submission_date);
            $work->description = trim($request->description);

            if (!empty($request->file('document_file'))) {
                $ext = $request->file('document_file')->getClientOriginalExtension();
                $file = $request->file('document_file');
                $randomStr = 'homework_admin' . date('dmYhis') . Str::random(20);
                $fileName = strtolower($randomStr) . '.' . $ext;
                $file->move('upload/practicalworks/', $fileName);
                $work->document_file = $fileName;
            }

            $work->save();

            return redirect('admin/practicalworks/homework/list')->with('success', 'Ce travail de maison a été modifié avec succès.');

        } catch (\Exception $e) {
            Log::error("Erreur lors de la modification d'un  travail de maison : " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }

    public function practicalWorksDelete($id)
    {
        try {
            $work = WorkModel::getSingle($id);
            $work->is_delete = 1;
            $work->save();

            return redirect('admin/practicalworks/homework/list')->with('success', 'Ce travail de maison a été supprimé avec succès.');

        } catch (\Exception $e) {
            Log::error("Erreur lors de la suppression d'un  travail de maison : " . $e->getMessage());
            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }

    public function teacherPracticalWorksList()
    {
        $data['header_title'] = 'Liste des travaux';
        $class_ids = [];
        $getClass = ClassTeacherModel::getMyClassSubjectGroup(Auth::user()->id);
        foreach ($getClass as $class) {
            $class_ids[] = $class->class_id;
        }
        $data['getWorks'] = WorkModel::getWorksTeacher(5, $class_ids);
        return view('teacher.practicalworks.list', $data);
    }

    public function teacherPracticalWorksAdd()
    {
        $data['header_title'] = 'Ajouter un travail';
        $data['getClass'] = ClassTeacherModel::getMyClassSubjectGroup(Auth::user()->id);
        return view('teacher.practicalworks.add', $data);
    }

    public function teacherPracticalWorksCreate(Request $request)
    {
        try {
            $work = new WorkModel();
            $work->class_id = intval($request->class_id);
            $work->subject_id = intval($request->subject_id);
            $work->work_date = trim($request->work_date);
            $work->submission_date = trim($request->submission_date);
            $work->description = trim($request->description);
            $work->created_by = Auth::user()->id;

            if (!empty($request->file('document_file'))) {
                $ext = $request->file('document_file')->getClientOriginalExtension();
                $file = $request->file('document_file');
                $randomStr = 'homework_teacher' . date('dmYhis') . Str::random(20);
                $fileName = strtolower($randomStr) . '.' . $ext;
                $file->move('upload/practicalworks/', $fileName);
                $work->document_file = $fileName;
            }

            $work->save();

            return redirect('teacher/practicalworks/homework/list')->with('success', 'Ce travail de maison a été créé avec succès.');

        } catch (\Exception $e) {
            Log::error("Erreur lors de la modification d'un  travail de maison : " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }

    }

    public function teacherPracticalWorksEdit($id)
    {
        $data['header_title'] = 'Modifier un travail';
        $data['getClass'] = ClassTeacherModel::getMyClassSubjectGroup(Auth::user()->id);
        $data['getWorks'] = WorkModel::getSingle($id);
        return view('teacher.practicalworks.edit', $data);
    }

    public function teacherPracticalWorksUpdate(Request $request, $id)
    {
        try {
            $work = WorkModel::getSingle($id);
            $work->class_id = intval($request->class_id);
            $work->subject_id = intval($request->subject_id);
            $work->work_date = trim($request->work_date);
            $work->submission_date = trim($request->submission_date);
            $work->description = trim($request->description);

            if (!empty($request->file('document_file'))) {
                $ext = $request->file('document_file')->getClientOriginalExtension();
                $file = $request->file('document_file');
                $randomStr = 'homework_teacher' . date('dmYhis') . Str::random(20);
                $fileName = strtolower($randomStr) . '.' . $ext;
                $file->move('upload/practicalworks/', $fileName);
                $work->document_file = $fileName;
            }

            $work->save();
            return redirect('teacher/practicalworks/homework/list')->with('success', 'Ce travail de maison a été modifié avec succès.');

        } catch (\Exception $e) {
            Log::error("Erreur lors de la modification d'un  travail de maison : " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }

    }

    public function teacherPracticalWorksDelete($id)
    {
        try {
            $work = WorkModel::getSingle($id);
            $work->is_delete = 1;
            $work->save();

            return redirect('teacher/practicalworks/homework/list')->with('success', 'Ce travail de maison a été supprimé avec succès.');

        } catch (\Exception $e) {
            Log::error("Erreur lors de la suppression d'un  travail de maison : " . $e->getMessage());
            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }

    public function myHomework()
    {
        $data['header_title'] = 'Mes travaux';
        $data['getWorks'] = WorkModel::getWorksWithStudentStatus(Auth::user()->class_id, Auth::user()->id, 5);
        return view('student.practicalworks.list', $data);
    }


    //  TODO LATER
    public function myHomeworkDetails($id)
    {
        $data['header_title'] = 'Détails d\'un travail';
        $data['getWorks'] = WorkModel::getSingle($id);
        return view('student.practicalworks.list', $data);
    }

    public function myHomeworkSubmission($work_id)
    {
        $data['getWorks'] = WorkModel::getSingle($work_id);
        $data['header_title'] = 'Soumettre un travail de maison';
        return view('student.practicalworks.submission', $data);
    }

    public function myHomeworkSubmissionCreate(Request $request, $work_id)
    {

        try {
            $homework = new HomeworkModel();
            $homework->work_id = intval($work_id);
            $homework->student_id = Auth::user()->id;
            $homework->description = trim($request->description);
            $homework->status = 'submitted';

            if (!empty($request->file('document_file'))) {
                $ext = $request->file('document_file')->getClientOriginalExtension();
                $file = $request->file('document_file');
                $randomStr = 'homework_student' . date('dmYhis') . Str::random(20);
                $fileName = strtolower($randomStr) . '.' . $ext;
                $file->move('upload/homeworks/', $fileName);
                $homework->document_file = $fileName;
            }

            $homework->save();

            return redirect('student/my_homework')->with('success', 'Ce travail de maison a été soumis avec succès.');

        } catch (\Exception $e) {
            Log::error("Erreur lors de la soumission d'un travail de maison : " . $e->getMessage());

            return redirect()->back()->with('error', 'Vos informations ne sont pas correctes. Veuillez réessayer.');
        }
    }

    public function myHomeworkSubmissionDetails($id)
    {
        $data['header_title'] = 'Détails d\'un travail de maison soumis';
        $data['getHomeworks'] = HomeworkModel::getSingle($id);
        return view('student.practicalworks.list', $data);
    }

}
