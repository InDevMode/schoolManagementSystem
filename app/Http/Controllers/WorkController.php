<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\ClassSubjectModel;
use App\Models\ClassTeacherModel;
use App\Models\HomeworkModel;
use App\Models\User;
use App\Models\WorkAttachmentModel;
use App\Models\WorkModel;
use App\Notifications\NewHomeworkNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;

class WorkController extends Controller
{
    // ══════════════════════════════════════════════════════════════════════════
    // UTILITAIRE — Gestion des pièces jointes multiples
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Sauvegarde les fichiers uploadés et crée les enregistrements d'attachement.
     *
     * @param  \Illuminate\Http\UploadedFile[]  $files
     * @param  int  $workId
     * @param  string  $prefix
     */
    private function saveAttachments(array $files, int $workId, string $prefix = 'homework'): void
    {
        foreach ($files as $file) {
            if (!$file->isValid()) continue;

            // Capturer toutes les métadonnées AVANT le move() car le fichier
            // temporaire est supprimé dès que move() est appelé.
            $originalName = $file->getClientOriginalName();
            $ext          = $file->getClientOriginalExtension();
            $fileSize     = $file->getSize();
            $original     = pathinfo($originalName, PATHINFO_FILENAME);
            $slug         = Str::slug(mb_substr($original, 0, 40));
            $fileName     = $prefix . '_' . date('dmYHis') . '_' . Str::random(8) . '_' . $slug . '.' . $ext;

            $file->move(public_path('upload/practicalworks'), $fileName);

            WorkAttachmentModel::create([
                'work_id'   => $workId,
                'file_name' => $originalName,
                'file_path' => $fileName,
                'file_ext'  => strtolower($ext),
                'file_size' => $fileSize,
            ]);
        }
    }

    /**
     * Supprime les pièces jointes dont les IDs sont fournis (soft-delete).
     */
    private function removeAttachments(array $attachmentIds, int $workId): void
    {
        if (empty($attachmentIds)) return;

        WorkAttachmentModel::where('work_id', $workId)
            ->whereIn('id', $attachmentIds)
            ->update(['is_delete' => 1]);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // ADMIN — Liste et CRUD des travaux
    // ══════════════════════════════════════════════════════════════════════════

    public function practicalWorksList()
    {
        $user = Auth::user();
        return Inertia::render('Admin/Homework/Index', [
            'works'           => WorkModel::getWorks(15),
            'classes'         => ClassModel::getClass(),
            'currentUserId'   => $user->id,
            'currentUserType' => $user->user_type,
            'canCreate'       => $user->user_type === 0 || $user->can('action.homework.create'),
            'canView'         => $user->user_type === 0 || $user->can('action.homework.view'),
            'canEdit'         => $user->user_type === 0 || $user->can('action.homework.edit'),
            'canDelete'       => $user->user_type === 0 || $user->can('action.homework.delete'),
        ]);
    }

    public function practicalWorksDetails($id)
    {
        return Inertia::render('Admin/Homework/Details', [
            'work' => WorkModel::getWorkIdWithHomeworks($id),
        ]);
    }

    public function practicalWorksDetailsJson($id)
    {
        $work = WorkModel::getWorkIdWithHomeworks($id);
        abort_unless($work, 404);

        // Forcer l'inclusion des accessors calculés sur chaque pièce jointe
        if ($work->attachments) {
            $work->attachments->each(function ($att) {
                $att->append(['url', 'readable_size']);
            });
        }

        return response()->json(['work' => $work]);
    }

    public function getSubjectByClassId($classId)
    {
        $data['getSubject'] = ClassSubjectModel::getSubject($classId);
        return response()->json($data);
    }

    /** JSON — récupère un work pour le formulaire d'édition */
    public function practicalWorksEditJson($id)
    {
        $user = Auth::user();

        $work = WorkModel::select(
                'works.*',
                'class.name as class_name',
                'subject.name as subject_name'
            )
            ->join('class',   'class.id',   '=', 'works.class_id')
            ->join('subject', 'subject.id', '=', 'works.subject_id')
            ->where('works.id', $id)
            ->where('works.is_delete', 0)
            ->first();

        abort_unless($work, 404);

        // Un admin ne peut modifier que ses propres travaux (sauf super_admin)
        if ($user->user_type !== 0 && (int) $work->created_by !== $user->id) {
            return response()->json(['error' => 'Vous ne pouvez modifier que vos propres travaux.'], 403);
        }

        $work->load('attachments');
        $work->attachments->each(function ($att) {
            $att->append(['url', 'readable_size']);
        });

        return response()->json(['work' => $work]);
    }

    public function practicalWorksCreate(Request $request)
    {
        $request->validate([
            'class_id'         => 'required|integer',
            'subject_id'       => 'required|integer',
            'work_date'        => 'required|date',
            'submission_date'  => 'required|date',
            'description'      => 'nullable|string',
            'attachments'      => 'nullable|array',
            'attachments.*'    => 'file|max:20480', // 20 Mo max par fichier
        ]);

        try {
            $work                  = new WorkModel();
            $work->class_id        = intval($request->class_id);
            $work->subject_id      = intval($request->subject_id);
            $work->work_date       = trim($request->work_date);
            $work->submission_date = trim($request->submission_date);
            $work->description     = trim($request->description ?? '');
            $work->created_by      = Auth::user()->id;

            // Compatibilité ascendante — fichier unique legacy
            if ($request->hasFile('document_file')) {
                $f        = $request->file('document_file');
                $ext      = $f->getClientOriginalExtension();
                $fileName = strtolower('homework_admin' . date('dmYhis') . Str::random(20)) . '.' . $ext;
                $f->move('upload/practicalworks/', $fileName);
                $work->document_file = $fileName;
            }

            $work->save();

            // Pièces jointes multiples
            if ($request->hasFile('attachments')) {
                $this->saveAttachments($request->file('attachments'), $work->id, 'hw_admin');
            }

            // Notifier les apprenants de la classe et leurs parents
            try {
                $class   = ClassModel::getSingle($work->class_id);
                $subject = ClassSubjectModel::select('subject.name')
                    ->join('subject', 'subject.id', '=', 'class_subject.subject_id')
                    ->where('class_subject.subject_id', $work->subject_id)
                    ->first();

                $className      = $class?->name ?? 'votre classe';
                $subjectName    = $subject?->name ?? 'une matière';
                $submissionDate = \Carbon\Carbon::parse($work->submission_date)->format('d-m-Y');

                $students = User::where('class_id', $work->class_id)
                    ->where('user_type', 3)
                    ->where('is_delete', 0)
                    ->where('status', 1)
                    ->get();

                $notifiedParents = [];
                foreach ($students as $student) {
                    $student->notify(new NewHomeworkNotification($subjectName, $className, $submissionDate));
                    if ($student->parent_id && !in_array($student->parent_id, $notifiedParents)) {
                        $parent = User::find($student->parent_id);
                        $parent?->notify(new NewHomeworkNotification($subjectName, $className, $submissionDate));
                        $notifiedParents[] = $student->parent_id;
                    }
                }
            } catch (\Exception $notifEx) {
                Log::warning("Homework create notification failed: " . $notifEx->getMessage());
            }

            return redirect('admin/practicalworks/homework/list')
                ->with('success', 'Le travail de maison a été créé avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur création travail de maison : " . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    public function practicalWorksUpdate(Request $request, $id)
    {
        $request->validate([
            'class_id'           => 'required|integer',
            'subject_id'         => 'required|integer',
            'work_date'          => 'required|date',
            'submission_date'    => 'required|date',
            'description'        => 'nullable|string',
            'attachments'        => 'nullable|array',
            'attachments.*'      => 'file|max:20480',
            'remove_attachments' => 'nullable|array',
            'remove_attachments.*' => 'integer',
        ]);

        try {
            $user = Auth::user();
            $work = WorkModel::getSingle($id);
            abort_unless($work && $work->is_delete == 0, 404);

            // Seul le créateur ou le super_admin peut modifier
            if ($user->user_type !== 0 && (int) $work->created_by !== $user->id) {
                return redirect()->back()->with('error', 'Vous ne pouvez modifier que vos propres travaux.');
            }

            $work->class_id        = intval($request->class_id);
            $work->subject_id      = intval($request->subject_id);
            $work->work_date       = trim($request->work_date);
            $work->submission_date = trim($request->submission_date);
            $work->description     = trim($request->description ?? '');

            // Fichier unique legacy
            if ($request->hasFile('document_file')) {
                $f        = $request->file('document_file');
                $ext      = $f->getClientOriginalExtension();
                $fileName = strtolower('homework_admin' . date('dmYhis') . Str::random(20)) . '.' . $ext;
                $f->move('upload/practicalworks/', $fileName);
                $work->document_file = $fileName;
            }

            $work->save();

            // Supprimer les pièces jointes sélectionnées
            if ($request->has('remove_attachments')) {
                $this->removeAttachments($request->remove_attachments, $work->id);
            }

            // Ajouter les nouvelles pièces jointes
            if ($request->hasFile('attachments')) {
                $this->saveAttachments($request->file('attachments'), $work->id, 'hw_admin');
            }

            return redirect('admin/practicalworks/homework/list')
                ->with('success', 'Le travail de maison a été modifié avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur modification travail de maison : " . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    public function practicalWorksDelete($id)
    {
        try {
            $user = Auth::user();
            $work = WorkModel::getSingle($id);
            abort_unless($work, 404);

            // Seul le créateur ou le super_admin peut supprimer
            if ($user->user_type !== 0 && (int) $work->created_by !== $user->id) {
                return redirect()->back()->with('error', 'Vous ne pouvez supprimer que vos propres travaux.');
            }

            $work->is_delete  = 1;
            $work->deleted_at = now();
            $work->deleted_by = $user->id;
            $work->save();

            return redirect('admin/practicalworks/homework/list')
                ->with('success', 'Le travail a été mis à la corbeille. Vous pouvez le restaurer.');
        } catch (\Exception $e) {
            Log::error("Erreur suppression travail : " . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue.');
        }
    }

    public function practicalWorksRestore($id)
    {
        try {
            $work = WorkModel::find($id);
            abort_unless($work && $work->is_delete == 1, 404);

            $work->is_delete  = 0;
            $work->deleted_at = null;
            $work->deleted_by = null;
            $work->save();

            return redirect()->back()->with('success', 'Le travail a été restauré avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur restauration travail : " . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la restauration.');
        }
    }

    /** Corbeille admin */
    public function practicalWorksTrashed()
    {
        return Inertia::render('Admin/Homework/Trash', [
            'works' => WorkModel::getWorksAdminTrashed(15),
        ]);
    }

    public function homeworkSubmission($id)
    {
        $homework = WorkModel::getSingle($id);
        if (!empty($homework)) {
            return Inertia::render('Admin/Homework/Submission', [
                'homeworks' => HomeworkModel::getHomeworks($id, 15),
                'workId'    => $id,
            ]);
        } else {
            abort(404);
        }
    }

    public function homeworkReportList()
    {
        $user = Auth::user();

        // Super admin voit tout
        // Admin voit tout mais ne peut modifier que les siens (géré côté vue)
        // Le filtre de visibilité des rapports = toujours tout (is_delete=0)
        return Inertia::render('Admin/Homework/Reports', [
            'homeworks'  => HomeworkModel::getAllHomeworks(15),
            'creatorId'  => $user->id,
            'isSuperAdmin' => $user->user_type === 0,
        ]);
    }

    public function homeworkReportDetails($id)
    {
        $work = WorkModel::getWorkIdWithHomeworks($id);
        abort_unless($work, 404);
        return Inertia::render('Admin/Homework/Submission', [
            'homeworks' => HomeworkModel::getHomeworks($id, 15),
            'workId'    => $id,
        ]);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // TEACHER — Liste et CRUD des travaux (ses classes seulement)
    // ══════════════════════════════════════════════════════════════════════════

    public function teacherPracticalWorksList()
    {
        $teacherId = Auth::user()->id;
        $class_ids = [];
        $getClass  = ClassTeacherModel::getMyClassSubjectGroup($teacherId);
        foreach ($getClass as $class) {
            $class_ids[] = $class->class_id;
        }

        return Inertia::render('Teacher/Homework/Index', [
            'works'   => WorkModel::getWorksTeacher(15, $class_ids),
            'classes' => ClassTeacherModel::getMyClassSubjectGroup($teacherId),
        ]);
    }

    public function teacherPracticalWorksAdd()
    {
        return redirect('teacher/practicalworks/homework/list');
    }

    public function teacherPracticalWorksEdit($id)
    {
        return redirect('teacher/practicalworks/homework/list');
    }

    /** JSON — données d'un work pour le formulaire d'édition teacher */
    public function teacherPracticalWorksEditJson($id)
    {
        $teacherId = Auth::user()->id;

        $work = WorkModel::select(
                'works.*',
                'class.name as class_name',
                'subject.name as subject_name'
            )
            ->join('class',   'class.id',   '=', 'works.class_id')
            ->join('subject', 'subject.id', '=', 'works.subject_id')
            ->where('works.id', $id)
            ->where('works.is_delete', 0)
            ->where('works.created_by', $teacherId)
            ->first();

        abort_unless($work, 404);

        $work->load('attachments');
        $work->attachments->each(function ($att) {
            $att->append(['url', 'readable_size']);
        });

        return response()->json(['work' => $work]);
    }

    public function teacherPracticalWorksCreate(Request $request)
    {
        $request->validate([
            'class_id'        => 'required|integer',
            'subject_id'      => 'required|integer',
            'work_date'       => 'required|date',
            'submission_date' => 'required|date',
            'description'     => 'nullable|string',
            'attachments'     => 'nullable|array',
            'attachments.*'   => 'file|max:20480',
        ]);

        try {
            $work                  = new WorkModel();
            $work->class_id        = intval($request->class_id);
            $work->subject_id      = intval($request->subject_id);
            $work->work_date       = trim($request->work_date);
            $work->submission_date = trim($request->submission_date);
            $work->description     = trim($request->description ?? '');
            $work->created_by      = Auth::user()->id;

            if ($request->hasFile('document_file')) {
                $f        = $request->file('document_file');
                $ext      = $f->getClientOriginalExtension();
                $fileName = strtolower('homework_teacher' . date('dmYhis') . Str::random(20)) . '.' . $ext;
                $f->move('upload/practicalworks/', $fileName);
                $work->document_file = $fileName;
            }

            $work->save();

            if ($request->hasFile('attachments')) {
                $this->saveAttachments($request->file('attachments'), $work->id, 'hw_teacher');
            }

            // Notifier les apprenants de la classe et leurs parents
            try {
                $class   = ClassModel::getSingle($work->class_id);
                $subject = ClassSubjectModel::select('subject.name')
                    ->join('subject', 'subject.id', '=', 'class_subject.subject_id')
                    ->where('class_subject.subject_id', $work->subject_id)
                    ->first();

                $className      = $class?->name ?? 'votre classe';
                $subjectName    = $subject?->name ?? 'une matière';
                $submissionDate = \Carbon\Carbon::parse($work->submission_date)->format('d-m-Y');

                $students = User::where('class_id', $work->class_id)
                    ->where('user_type', 3)
                    ->where('is_delete', 0)
                    ->where('status', 1)
                    ->get();

                $notifiedParents = [];
                foreach ($students as $student) {
                    $student->notify(new NewHomeworkNotification($subjectName, $className, $submissionDate));
                    if ($student->parent_id && !in_array($student->parent_id, $notifiedParents)) {
                        $parent = User::find($student->parent_id);
                        $parent?->notify(new NewHomeworkNotification($subjectName, $className, $submissionDate));
                        $notifiedParents[] = $student->parent_id;
                    }
                }
            } catch (\Exception $notifEx) {
                Log::warning("Homework teacher notification failed: " . $notifEx->getMessage());
            }

            return redirect('teacher/practicalworks/homework/list')
                ->with('success', 'Le travail de maison a été créé avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur création travail de maison (prof) : " . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    public function teacherPracticalWorksUpdate(Request $request, $id)
    {
        $request->validate([
            'class_id'             => 'required|integer',
            'subject_id'           => 'required|integer',
            'work_date'            => 'required|date',
            'submission_date'      => 'required|date',
            'description'          => 'nullable|string',
            'attachments'          => 'nullable|array',
            'attachments.*'        => 'file|max:20480',
            'remove_attachments'   => 'nullable|array',
            'remove_attachments.*' => 'integer',
        ]);

        try {
            $teacherId = Auth::user()->id;
            $work      = WorkModel::where('id', $id)
                ->where('is_delete', 0)
                ->where('created_by', $teacherId) // sécurité — seulement ses propres travaux
                ->first();

            if (!$work) {
                return redirect()->back()->with('error', 'Travail introuvable ou vous n\'êtes pas autorisé à le modifier.');
            }

            $work->class_id        = intval($request->class_id);
            $work->subject_id      = intval($request->subject_id);
            $work->work_date       = trim($request->work_date);
            $work->submission_date = trim($request->submission_date);
            $work->description     = trim($request->description ?? '');

            if ($request->hasFile('document_file')) {
                $f        = $request->file('document_file');
                $ext      = $f->getClientOriginalExtension();
                $fileName = strtolower('homework_teacher' . date('dmYhis') . Str::random(20)) . '.' . $ext;
                $f->move('upload/practicalworks/', $fileName);
                $work->document_file = $fileName;
            }

            $work->save();

            if ($request->has('remove_attachments')) {
                $this->removeAttachments($request->remove_attachments, $work->id);
            }

            if ($request->hasFile('attachments')) {
                $this->saveAttachments($request->file('attachments'), $work->id, 'hw_teacher');
            }

            return redirect('teacher/practicalworks/homework/list')
                ->with('success', 'Le travail de maison a été modifié avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur modification travail de maison (prof) : " . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    public function teacherPracticalWorksDelete($id)
    {
        try {
            $teacherId = Auth::user()->id;
            $work      = WorkModel::where('id', $id)
                ->where('is_delete', 0)
                ->where('created_by', $teacherId)
                ->first();

            if (!$work) {
                return redirect()->back()->with('error', 'Travail introuvable ou vous n\'êtes pas autorisé à le supprimer.');
            }

            $work->is_delete  = 1;
            $work->deleted_at = now();
            $work->deleted_by = $teacherId;
            $work->save();

            return redirect('teacher/practicalworks/homework/list')
                ->with('success', 'Le travail a été mis à la corbeille. Vous pouvez le restaurer depuis la corbeille.');
        } catch (\Exception $e) {
            Log::error("Erreur suppression travail (prof) : " . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue.');
        }
    }

    public function teacherPracticalWorksRestore($id)
    {
        try {
            $teacherId = Auth::user()->id;
            $work      = WorkModel::find($id);
            abort_unless($work && $work->is_delete == 1 && (int) $work->created_by === $teacherId, 404);

            $work->is_delete  = 0;
            $work->deleted_at = null;
            $work->deleted_by = null;
            $work->save();

            return redirect()->back()->with('success', 'Le travail a été restauré avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur restauration travail (prof) : " . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la restauration.');
        }
    }

    /** Corbeille teacher — ses propres travaux supprimés */
    public function teacherPracticalWorksTrashed()
    {
        $teacherId = Auth::user()->id;
        $class_ids = [];
        foreach (ClassTeacherModel::getMyClassSubjectGroup($teacherId) as $c) {
            $class_ids[] = $c->class_id;
        }

        $trashed = WorkModel::select(
                'works.*',
                'class.name as class_name',
                'subject.name as subject_name',
                'users.name as created_by_name'
            )
            ->join('class',   'class.id',   '=', 'works.class_id')
            ->join('subject', 'subject.id', '=', 'works.subject_id')
            ->join('users',   'users.id',   '=', 'works.created_by')
            ->where('works.is_delete', 1)
            ->where('works.created_by', $teacherId)
            ->orderBy('works.id', 'desc')
            ->paginate(min((int) request('per_page', 15), 100));

        return Inertia::render('Teacher/Homework/Trash', [
            'works' => $trashed,
        ]);
    }

    public function teacherHomeworkSubmission($id)
    {
        $homework = WorkModel::getSingle($id);
        if (!empty($homework)) {
            return Inertia::render('Teacher/Homework/Submission', [
                'homeworks' => HomeworkModel::getHomeworks($id, 15),
                'workId'    => $id,
            ]);
        } else {
            abort(404);
        }
    }

    // ══════════════════════════════════════════════════════════════════════════
    // STUDENT
    // ══════════════════════════════════════════════════════════════════════════

    public function myHomework()
    {
        return Inertia::render('Student/Homework/Index', [
            'works' => WorkModel::getWorksWithStudentStatus(
                Auth::user()->class_id,
                Auth::user()->id,
                15
            ),
        ]);
    }

    public function myHomeworkSubmission($work_id)
    {
        $work = WorkModel::where('id', $work_id)->where('is_delete', 0)->first();
        if ($work) {
            $work->load('attachments');
            $work->attachments->each(function ($att) {
                $att->append(['url', 'readable_size']);
            });
        }
        return Inertia::render('Student/Homework/Submission', [
            'work' => $work,
        ]);
    }

    public function myHomeworkSubmissionCreate(Request $request, $work_id)
    {
        $request->validate([
            'description'   => 'nullable|string',
            'document_file' => 'nullable|file|max:20480',
        ]);

        try {
            $homework             = new HomeworkModel();
            $homework->work_id    = intval($work_id);
            $homework->student_id = Auth::user()->id;
            $homework->description = trim($request->description ?? '');
            $homework->status     = 'submitted';

            if ($request->hasFile('document_file')) {
                $f        = $request->file('document_file');
                $ext      = $f->getClientOriginalExtension();
                $fileName = strtolower('homework_student' . date('dmYhis') . Str::random(20)) . '.' . $ext;
                $f->move('upload/homeworks/', $fileName);
                $homework->document_file = $fileName;
            }

            $homework->save();

            return redirect('student/my_homework')
                ->with('success', 'Votre travail a été soumis avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur soumission travail (apprenant) : " . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    // ══════════════════════════════════════════════════════════════════════════
    // PARENT
    // ══════════════════════════════════════════════════════════════════════════

    public function parentHomeworkSubmission($student_id)
    {
        $getStudent = User::getSingle($student_id);
        return Inertia::render('Parent/Homework/Submission', [
            'works'   => WorkModel::getWorksWithStudentStatus($getStudent->class_id, $getStudent->id, 15),
            'student' => $getStudent,
        ]);
    }
}
