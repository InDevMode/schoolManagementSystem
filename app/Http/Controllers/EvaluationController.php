<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\ClassSubjectModel;
use App\Models\ClassTeacherModel;
use App\Models\DeletionLogModel;
use App\Models\EvaluationModel;
use App\Models\GradeModel;
use App\Models\PeriodModel;
use App\Models\SubjectModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class EvaluationController extends Controller
{
    // ──────────────────────────────────────────────────────────────────────────
    // ADMIN — Liste des évaluations
    // ──────────────────────────────────────────────────────────────────────────

    public function list()
    {
        return Inertia::render('Admin/Evaluations/Index', [
            'evaluations'   => EvaluationModel::getAll(15),
            'classes'       => ClassModel::getClass(),
            'periods'       => PeriodModel::getAllPeriods(),
            'currentPeriod' => PeriodModel::getCurrentPeriod()->first(),
            'typeLabels'    => EvaluationModel::$typeLabels,
            'typeCoeffs'    => EvaluationModel::$typeCoefficients,
        ]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'class_id'   => 'required|integer',
            'subject_id' => 'required|integer',
            'period_id'  => 'required|integer',
            'type'       => 'required|in:interrogation,devoir_surveille,travail_maison,examen_blanc',
            'eval_date'  => 'required|date',
        ]);

        try {
            // Le coefficient vient de l'assignation classe-matière, pas du type
            $classSubject = ClassSubjectModel::getClassSubject((int) $request->class_id, (int) $request->subject_id);
            $coefficient  = $classSubject?->coefficient ?? 1;

            $eval              = new EvaluationModel;
            $eval->class_id    = $request->class_id;
            $eval->subject_id  = $request->subject_id;
            $eval->period_id   = $request->period_id;
            $eval->teacher_id  = $request->teacher_id ?? null;
            $eval->exam_id     = $request->exam_id ?? null;
            $eval->type        = $request->type;
            $eval->coefficient = $coefficient;
            $eval->max_score   = $request->max_score ?? 20;
            $eval->eval_date   = $request->eval_date;
            $eval->title       = trim($request->title ?? '');
            $eval->status      = 'open';
            $eval->created_by  = Auth::id();
            $eval->save();

            return redirect()->back()->with('success', 'Évaluation créée avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur création évaluation : " . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    public function edit(int $id)
    {
        $eval = EvaluationModel::getSingle($id);
        if (!$eval) abort(404);

        return response()->json([
            'evaluation' => $eval,
            'classes'    => ClassModel::getClass(),
            'subjects'   => SubjectModel::getSubject(),
            'periods'    => PeriodModel::getAllPeriods(),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'type'      => 'required|in:interrogation,devoir_surveille,travail_maison,examen_blanc',
            'eval_date' => 'required|date',
        ]);

        try {
            $eval = EvaluationModel::getSingle($id);
            if (!$eval) abort(404);

            $eval->class_id    = $request->class_id ?? $eval->class_id;
            $eval->subject_id  = $request->subject_id ?? $eval->subject_id;
            $eval->period_id   = $request->period_id ?? $eval->period_id;
            $eval->teacher_id  = $request->teacher_id ?? $eval->teacher_id;
            $eval->type        = $request->type;
            $eval->coefficient = $request->coefficient ?? EvaluationModel::$typeCoefficients[$request->type] ?? 1;
            $eval->max_score   = $request->max_score ?? $eval->max_score;
            $eval->eval_date   = $request->eval_date;
            $eval->title       = trim($request->title ?? '');
            $eval->save();

            return redirect()->back()->with('success', 'Évaluation mise à jour avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur mise à jour évaluation : " . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    public function delete(int $id)
    {
        $eval = EvaluationModel::getSingle($id);
        if (!$eval) abort(404);

        // Journalisation avant suppression
        DeletionLogModel::log('evaluations', $eval->id, $eval->toArray());

        $eval->is_delete = 1;
        $eval->save();

        return redirect()->back()->with('success', 'Évaluation supprimée avec succès.');
    }

    /**
     * Changer le statut d'une évaluation (open, closed, validated)
     */
    public function changeStatus(Request $request, int $id)
    {
        $request->validate(['status' => 'required|in:draft,open,closed,validated']);

        try {
            $eval = EvaluationModel::getSingle($id);
            if (!$eval) abort(404);

            $eval->status = $request->status;
            $eval->save();

            return response()->json(['success' => true, 'message' => 'Statut mis à jour.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur lors de la mise à jour.'], 500);
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // ADMIN — Saisie des notes
    // ──────────────────────────────────────────────────────────────────────────

    public function gradeEntry(Request $request)
    {
        $data = [
            'classes'       => ClassModel::getClass(),
            'periods'       => PeriodModel::getCurrentPeriod(), // saisie : période courante uniquement
            'currentPeriod' => PeriodModel::getCurrentPeriod()->first(),
            'evaluations'   => [],
            'grades'        => [],
        ];

        if ($request->evaluation_id) {
            $eval = EvaluationModel::getSingle((int) $request->evaluation_id);
            if ($eval) {
                $data['evaluation'] = $eval;
                $data['grades']     = GradeModel::getGradesForEvaluation($eval->id, $eval->class_id);
                $data['stats']      = GradeModel::getEvaluationStats($eval->id, (float) $eval->max_score);
            }
        }

        if ($request->class_id && $request->period_id) {
            $data['evaluations'] = EvaluationModel::getByClassAndPeriod(
                (int) $request->class_id,
                (int) $request->period_id
            );
        }

        return Inertia::render('Admin/Evaluations/GradeEntry', $data);
    }

    public function saveGrades(Request $request)
    {
        $request->validate([
            'evaluation_id'  => 'required|integer',
            'grades'         => 'required|array',
            'grades.*.student_id' => 'required|integer',
            'grades.*.score'      => 'nullable|numeric|min:0',
        ]);

        try {
            $eval = EvaluationModel::getSingle($request->evaluation_id);
            if (!$eval) {
                return response()->json(['success' => false, 'message' => 'Évaluation introuvable.'], 404);
            }

            foreach ($request->grades as $gradeData) {
                $score = isset($gradeData['score']) && $gradeData['score'] !== '' ? (float) $gradeData['score'] : null;

                // Valider que la note ne dépasse pas la note max
                if ($score !== null && $score > (float) $eval->max_score) {
                    return response()->json([
                        'success' => false,
                        'message' => "La note {$score} dépasse la note maximale ({$eval->max_score}) pour l'élève #{$gradeData['student_id']}.",
                    ]);
                }

                GradeModel::updateOrCreate(
                    [
                        'student_id'    => $gradeData['student_id'],
                        'evaluation_id' => $eval->id,
                    ],
                    [
                        'score'       => $score,
                        'teacher_id'  => Auth::id(),
                        'observation' => $gradeData['observation'] ?? null,
                        'validated'   => false,
                        'is_delete'   => 0,
                    ]
                );
            }

            return response()->json(['success' => true, 'message' => 'Notes enregistrées avec succès.']);
        } catch (\Exception $e) {
            Log::error("Erreur sauvegarde notes : " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Une erreur est survenue.'], 500);
        }
    }

    /**
     * Validation en masse des notes d'une évaluation
     */
    public function validateGrades(Request $request)
    {
        $request->validate([
            'evaluation_id' => 'required|integer',
            'grade_ids'     => 'nullable|array',
        ]);

        try {
            $query = GradeModel::where('evaluation_id', $request->evaluation_id)
                ->where('is_delete', 0);

            if ($request->grade_ids) {
                $query->whereIn('id', $request->grade_ids);
            }

            $query->update([
                'validated'    => true,
                'validated_by' => Auth::id(),
                'validated_at' => now(),
            ]);

            // Marquer l'évaluation comme validée
            $eval = EvaluationModel::getSingle($request->evaluation_id);
            if ($eval) {
                $eval->status = 'validated';
                $eval->save();
            }

            return response()->json(['success' => true, 'message' => 'Notes validées avec succès.']);
        } catch (\Exception $e) {
            Log::error("Erreur validation notes : " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Une erreur est survenue.'], 500);
        }
    }

    /**
     * Notes en attente de validation
     */
    public function pendingValidation()
    {
        return Inertia::render('Admin/Evaluations/PendingValidation', [
            'grades' => GradeModel::getPendingValidation(20),
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // PROF — Ses évaluations
    // ──────────────────────────────────────────────────────────────────────────

    public function teacherList()
    {
        $teacher_id = Auth::id();
        $classes    = ClassTeacherModel::getMyClassSubjectGroup($teacher_id);

        return Inertia::render('Teacher/Evaluations/Index', [
            'evaluations'   => EvaluationModel::getByTeacherPaginated($teacher_id, 15),
            'classes'       => $classes,
            'currentPeriod' => PeriodModel::getCurrentPeriod()->first(), // le prof ne voit que la période courante
            'typeLabels'    => EvaluationModel::$typeLabels,
            'typeCoeffs'    => EvaluationModel::$typeCoefficients,
        ]);
    }

    public function teacherCreate(Request $request)
    {
        $request->validate([
            'class_id'   => 'required|integer',
            'subject_id' => 'required|integer',
            'period_id'  => 'required|integer',
            'type'       => 'required|in:interrogation,devoir_surveille,travail_maison,examen_blanc',
            'eval_date'  => 'required|date',
        ]);

        try {
            // Le coefficient vient de l'assignation classe-matière
            $classSubject = ClassSubjectModel::getClassSubject((int) $request->class_id, (int) $request->subject_id);
            $coefficient  = $classSubject?->coefficient ?? 1;

            $eval              = new EvaluationModel;
            $eval->class_id    = $request->class_id;
            $eval->subject_id  = $request->subject_id;
            $eval->period_id   = $request->period_id;
            $eval->teacher_id  = Auth::id();
            $eval->type        = $request->type;
            $eval->coefficient = $coefficient;
            $eval->max_score   = $request->max_score ?? 20;
            $eval->eval_date   = $request->eval_date;
            $eval->title       = trim($request->title ?? '');
            $eval->status      = 'open';
            $eval->created_by  = Auth::id();
            $eval->save();

            return redirect()->back()->with('success', 'Évaluation créée avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur création évaluation prof : " . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue.');
        }
    }

    public function teacherGradeEntry(Request $request)
    {
        $teacher_id = Auth::id();
        $classes    = ClassTeacherModel::getMyClassSubjectGroup($teacher_id);
        $data       = [
            'classes'       => $classes,
            'periods'       => PeriodModel::getCurrentPeriod(), // période courante uniquement
            'currentPeriod' => PeriodModel::getCurrentPeriod()->first(),
            'evaluations'   => [],
            'grades'        => [],
        ];

        if ($request->evaluation_id) {
            $eval = EvaluationModel::getSingle((int) $request->evaluation_id);
            // Vérifier que le prof est bien le responsable
            if ($eval && $eval->teacher_id === $teacher_id) {
                $data['evaluation'] = $eval;
                $data['grades']     = GradeModel::getGradesForEvaluation($eval->id, $eval->class_id);
                $data['stats']      = GradeModel::getEvaluationStats($eval->id, (float) $eval->max_score);
            }
        }

        if ($request->class_id && $request->period_id) {
            $data['evaluations'] = EvaluationModel::getByTeacher($teacher_id, (int) $request->class_id);
        }

        return Inertia::render('Teacher/Evaluations/GradeEntry', $data);
    }

    public function teacherSaveGrades(Request $request)
    {
        // Déléguer à la méthode admin (même logique)
        return $this->saveGrades($request);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // ÉLÈVE — Ses notes
    // ──────────────────────────────────────────────────────────────────────────

    public function studentGrades(Request $request)
    {
        $student_id = Auth::id();
        $periods    = PeriodModel::getAllPeriods();
        $period_id  = $request->period_id ?? ($periods->first()?->id);

        $grades = $period_id
            ? GradeModel::getStudentGradesForPeriod($student_id, (int) $period_id)
            : collect();

        return Inertia::render('Student/Evaluations/MyGrades', [
            'grades'   => $grades,
            'periods'  => $periods,
            'selected_period_id' => $period_id,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // PARENT — Notes de son enfant
    // ──────────────────────────────────────────────────────────────────────────

    public function parentStudentGrades(Request $request, int $student_id)
    {
        // Vérifier que l'élève appartient bien au parent
        $student = User::find($student_id);
        if (!$student || $student->parent_id !== Auth::id()) abort(403);

        $periods   = PeriodModel::getAllPeriods();
        $period_id = $request->period_id ?? ($periods->first()?->id);

        $grades = $period_id
            ? GradeModel::getStudentGradesForPeriod($student_id, (int) $period_id)
            : collect();

        return Inertia::render('Parent/Evaluations/StudentGrades', [
            'student'            => $student,
            'grades'             => $grades,
            'periods'            => $periods,
            'selected_period_id' => $period_id,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // API JSON — pour les selects dynamiques
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Retourne les matières actives assignées à une classe (pour les selects dépendants)
     * Retourne : subject_id, subject_name, coefficient
     */
    public function getSubjectsByClass(int $class_id)
    {
        $subjects = ClassSubjectModel::getSubject($class_id)
            ->map(fn($s) => [
                'subject_id'   => $s->subject_id,
                'subject_name' => $s->subject_name,
                'coefficient'  => $s->coefficient,
            ]);

        return response()->json($subjects);
    }

    /**
     * Évaluations d'une classe/période (pour le select de saisie)
     */
    public function getEvaluationsByClassPeriod(Request $request)
    {
        if (!$request->class_id || !$request->period_id) {
            return response()->json([]);
        }

        $evals = EvaluationModel::getByClassAndPeriod(
            (int) $request->class_id,
            (int) $request->period_id
        );

        return response()->json($evals);
    }
}
