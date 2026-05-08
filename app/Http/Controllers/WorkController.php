<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\ClassSubjectModel;
use App\Models\ClassTeacherModel;
use App\Models\HomeworkModel;
use App\Models\User;
use App\Models\WorkModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;

class WorkController extends Controller
{
    public function practicalWorksList()
    {
        return Inertia::render('Admin/Homework/Index', [
            'works' => WorkModel::getWorks(15),
            'classes' => ClassModel::getClass(),
        ]);
    }

    public function practicalWorksDetails($id)
    {
        return Inertia::render('Admin/Homework/Details', [
            'work' => WorkModel::getWorkIdWithHomeworks($id),
        ]);
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

    public function homeworkSubmission($id)
    {
        $homework = WorkModel::getSingle($id);
        if (!empty($homework)) {
            return Inertia::render('Admin/Homework/Submission', [
                'homeworks' => HomeworkModel::getHomeworks($id, 15),
                'workId' => $id,
            ]);
        } else {
            abort(404);
        }
    }

    public function homeworkReportList()
    {
        return Inertia::render('Admin/Homework/Reports', [
            'homeworks' => HomeworkModel::getAllHomeworks(15),
        ]);
    }

    public function teacherPracticalWorksList()
    {
        $class_ids = [];
        $getClass = ClassTeacherModel::getMyClassSubjectGroup(Auth::user()->id);
        foreach ($getClass as $class) {
            $class_ids[] = $class->class_id;
        }

        return Inertia::render('Teacher/Homework/Index', [
            'works' => WorkModel::getWorksTeacher(15, $class_ids),
            'classes' => ClassTeacherModel::getMyClassSubjectGroup(Auth::user()->id),
        ]);
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

    public function teacherHomeworkSubmission($id)
    {
        $homework = WorkModel::getSingle($id);
        if (!empty($homework)) {
            return Inertia::render('Teacher/Homework/Submission', [
                'homeworks' => HomeworkModel::getHomeworks($id, 15),
                'workId' => $id,
            ]);
        } else {
            abort(404);
        }
    }

    public function myHomework()
    {
        return Inertia::render('Student/Homework/Index', [
            'works' => WorkModel::getWorksWithStudentStatus(Auth::user()->class_id, Auth::user()->id, 15),
        ]);
    }

    public function myHomeworkSubmission($work_id)
    {
        return Inertia::render('Student/Homework/Submission', [
            'work' => WorkModel::getSingle($work_id),
        ]);
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

    public function parentHomeworkSubmission($student_id)
    {
        $getStudent = User::getSingle($student_id);
        return Inertia::render('Parent/Homework/Submission', [
            'works' => WorkModel::getWorksWithStudentStatus($getStudent->class_id, $getStudent->id, 15),
            'student' => $getStudent,
        ]);
    }
}
