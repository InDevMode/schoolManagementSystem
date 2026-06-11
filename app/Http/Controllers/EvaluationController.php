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
            'evaluations'   => EvaluationModel::getAll(6),
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

            // ── Règle anti-fraude : une évaluation validée est IMMUABLE ────────────
            // Même le super administrateur ne peut pas modifier une évaluation validée.
            // Pour corriger une erreur, il faut d'abord annuler la validation des notes
            // via la page "À valider" puis revenir ici.
            if ($eval->status === 'validated') {
                return redirect()->back()->with('error',
                    'Cette évaluation est validée et ne peut plus être modifiée. ' .
                    'Pour la corriger, annulez d\'abord la validation de ses notes dans la page « À valider ».'
                );
            }

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

        $user     = Auth::user();
        $userType = (int) $user->user_type;

        // Règle 1 : une évaluation validée ne peut JAMAIS être supprimée
        if ($eval->status === 'validated') {
            return redirect()->back()->with('error', 'Une évaluation validée ne peut pas être supprimée.');
        }

        // Règle 2 : open ou closed → super admin uniquement
        if (in_array($eval->status, ['open', 'closed']) && $userType !== 0) {
            return redirect()->back()->with('error', 'Seul le Super Administrateur peut supprimer une évaluation ouverte ou fermée.');
        }

        // Règle 3 : draft → admin (user_type=1) et super admin (user_type=0)
        if ($eval->status === 'draft' && !in_array($userType, [0, 1])) {
            return redirect()->back()->with('error', 'Vous n\'avez pas la permission de supprimer cette évaluation.');
        }

        // Journalisation avant suppression
        DeletionLogModel::log('evaluations', $eval->id, $eval->toArray());

        $eval->is_delete = 1;
        $eval->save();

        return redirect()->back()->with('success', 'Évaluation supprimée avec succès.');
    }

    /**
     * Changer le statut d'une évaluation.
     *
     * Le passage à "validated" est INTERDIT ici — il ne peut se faire
     * qu'automatiquement via validateGrades() une fois que TOUTES les notes
     * sont saisies ET validées une à une depuis la page « À valider ».
     */
    public function changeStatus(Request $request, int $id)
    {
        $request->validate(['status' => 'required|in:draft,open,closed']);

        try {
            $eval = EvaluationModel::getSingle($id);
            if (!$eval) abort(404);

            // Bloquer toute tentative de passage manuel à "validated"
            if ($request->status === 'validated') {
                return response()->json([
                    'success' => false,
                    'message' => 'La validation doit passer par la page « À valider » après saisie complète de toutes les notes.',
                ], 422);
            }

            // Bloquer le retour en arrière depuis validated
            if ($eval->status === 'validated') {
                return response()->json([
                    'success' => false,
                    'message' => 'Une évaluation validée ne peut pas changer de statut. Annulez d\'abord les notes depuis « À valider ».',
                ], 422);
            }

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
            'classes'           => ClassModel::getClass(),
            'periods'           => PeriodModel::getCurrentPeriod(),
            'currentPeriod'     => PeriodModel::getCurrentPeriod()->first(),
            'evaluations'       => [],
            'grades'            => [],
            'selectedClassId'   => $request->class_id   ? (int) $request->class_id   : null,
            'selectedPeriodId'  => $request->period_id  ? (int) $request->period_id  : null,
        ];

        if ($request->evaluation_id) {
            $eval = EvaluationModel::getSingle((int) $request->evaluation_id);
            if ($eval) {
                $data['evaluation']      = $eval;
                $data['grades']          = GradeModel::getGradesForEvaluation($eval->id, $eval->class_id);
                $data['stats']           = GradeModel::getEvaluationStats($eval->id, (float) $eval->max_score);
                // S'assurer que les sélecteurs restent synchronisés avec l'évaluation choisie
                $data['selectedClassId']  = (int) $eval->class_id;
                $data['selectedPeriodId'] = (int) $eval->period_id;
            }
        }

        if ($request->class_id && $request->period_id) {
            $data['evaluations'] = EvaluationModel::getByClassAndPeriod(
                (int) $request->class_id,
                (int) $request->period_id
            );
        } elseif (isset($data['evaluation'])) {
            // Charger aussi la liste pour le select
            $data['evaluations'] = EvaluationModel::getByClassAndPeriod(
                (int) $data['evaluation']->class_id,
                (int) $data['evaluation']->period_id
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
            'grades.*.score'      => 'nullable|numeric|min:0|max:20',
        ]);

        try {
            $eval = EvaluationModel::getSingle($request->evaluation_id);
            if (!$eval) {
                return response()->json(['success' => false, 'message' => 'Évaluation introuvable.'], 404);
            }

            // ── Règle anti-fraude : évaluation validée = notes immuables ──────────
            if ($eval->status === 'validated') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette évaluation est validée. La saisie de notes est impossible. ' .
                                 'Annulez la validation depuis la page « À valider » pour corriger une erreur.',
                ], 403);
            }

            foreach ($request->grades as $gradeData) {
                $score = isset($gradeData['score']) && $gradeData['score'] !== '' ? (float) $gradeData['score'] : null;

                if ($score !== null && $score > (float) $eval->max_score) {
                    return response()->json([
                        'success' => false,
                        'message' => "La note {$score} dépasse la note maximale ({$eval->max_score}) pour l'élève #{$gradeData['student_id']}.",
                    ]);
                }

                // Chercher d'abord une note active non validée
                $existing = GradeModel::where('student_id', $gradeData['student_id'])
                    ->where('evaluation_id', $eval->id)
                    ->where('is_delete', 0)
                    ->first();

                // Refuser la modification d'une note déjà validée
                if ($existing && $existing->validated) {
                    continue;
                }

                if ($existing) {
                    // Mettre à jour la note active existante
                    $existing->score       = $score;
                    $existing->teacher_id  = Auth::id();
                    $existing->observation = $gradeData['observation'] ?? null;
                    $existing->save();
                } else {
                    // Chercher une note rejetée (is_delete=1) et la ressusciter
                    $rejected = GradeModel::where('student_id', $gradeData['student_id'])
                        ->where('evaluation_id', $eval->id)
                        ->where('is_delete', 1)
                        ->latest()
                        ->first();

                    if ($rejected) {
                        $rejected->score       = $score;
                        $rejected->teacher_id  = Auth::id();
                        $rejected->observation = $gradeData['observation'] ?? null;
                        $rejected->validated   = false;
                        $rejected->is_delete   = 0;
                        $rejected->save();
                    } else {
                        // Créer une nouvelle note
                        GradeModel::create([
                            'student_id'    => $gradeData['student_id'],
                            'evaluation_id' => $eval->id,
                            'score'         => $score,
                            'teacher_id'    => Auth::id(),
                            'observation'   => $gradeData['observation'] ?? null,
                            'validated'     => false,
                            'is_delete'     => 0,
                        ]);
                    }
                }
            }

            return response()->json(['success' => true, 'message' => 'Notes enregistrées avec succès.']);
        } catch (\Exception $e) {
            Log::error("Erreur sauvegarde notes : " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Une erreur est survenue.'], 500);
        }
    }

    /**
     * Validation des notes d'une évaluation depuis la page « À valider ».
     *
     * RÈGLE STRICTE :
     * - Avec grade_ids  → validation individuelle de ces notes uniquement.
     *                     L'éval ne passe en "validated" que si toutes les
     *                     conditions sont réunies (voir ci-dessous).
     * - Sans grade_ids  → validation globale, BLOQUÉE si des notes manquent.
     *
     * L'évaluation ne passe en statut "validated" QUE SI :
     *   1. Tous les élèves actifs de la classe ont une note saisie (score non null)
     *   2. Toutes ces notes sont validées (validated = true)
     */
    public function validateGrades(Request $request)
    {
        $request->validate([
            'evaluation_id' => 'required|integer',
            'grade_ids'     => 'nullable|array',
        ]);

        try {
            $eval = EvaluationModel::getSingle($request->evaluation_id);
            if (!$eval) {
                return response()->json(['success' => false, 'message' => 'Évaluation introuvable.'], 404);
            }

            // Nombre total d'élèves actifs dans la classe
            $totalStudents = User::where('class_id', $eval->class_id)
                ->where('user_type', 3)
                ->where('is_delete', 0)
                ->where('status', 1)
                ->count();

            // ── Validation individuelle (grade_ids fournis) ───────────────────
            if ($request->grade_ids && count($request->grade_ids) > 0) {

                // Vérifier que chaque note ciblée a bien un score saisi
                $gradesWithoutScore = GradeModel::whereIn('id', $request->grade_ids)
                    ->where('evaluation_id', $eval->id)
                    ->where('is_delete', 0)
                    ->whereNull('score')
                    ->count();

                if ($gradesWithoutScore > 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Impossible de valider une note vide. Saisissez d\'abord la note.',
                    ], 422);
                }

                GradeModel::whereIn('id', $request->grade_ids)
                    ->where('evaluation_id', $eval->id)
                    ->where('is_delete', 0)
                    ->update([
                        'validated'    => true,
                        'validated_by' => Auth::id(),
                        'validated_at' => now(),
                    ]);

                // Vérifier si TOUTES les conditions sont maintenant réunies pour valider l'éval :
                // - Chaque élève actif a une note saisie
                // - Toutes les notes sont validées
                $savedGrades    = GradeModel::where('evaluation_id', $eval->id)
                    ->where('is_delete', 0)->whereNotNull('score')->count();
                $validatedGrades = GradeModel::where('evaluation_id', $eval->id)
                    ->where('is_delete', 0)->where('validated', true)->count();

                if ($totalStudents > 0
                    && $savedGrades    >= $totalStudents
                    && $validatedGrades >= $totalStudents
                ) {
                    $eval->status = 'validated';
                    $eval->save();
                    return response()->json(['success' => true, 'message' => 'Note validée. Toutes les notes sont complètes — évaluation validée automatiquement.']);
                }

                return response()->json(['success' => true, 'message' => 'Note(s) validée(s) avec succès.']);
            }

            // ── Validation globale ────────────────────────────────────────────
            // Condition 1 : toutes les notes sont saisies
            $savedGrades = GradeModel::where('evaluation_id', $eval->id)
                ->where('is_delete', 0)
                ->whereNotNull('score')
                ->count();

            if ($savedGrades < $totalStudents) {
                $missing = $totalStudents - $savedGrades;
                return response()->json([
                    'success' => false,
                    'message' => "{$missing} élève(s) n'ont pas encore de note saisie. "
                               . "Saisissez toutes les notes avant de valider.",
                    'missing' => $missing,
                    'total'   => $totalStudents,
                    'saved'   => $savedGrades,
                ], 422);
            }

            // Toutes les notes présentes → les valider toutes en masse
            GradeModel::where('evaluation_id', $eval->id)
                ->where('is_delete', 0)
                ->update([
                    'validated'    => true,
                    'validated_by' => Auth::id(),
                    'validated_at' => now(),
                ]);

            $eval->status = 'validated';
            $eval->save();

            return response()->json(['success' => true, 'message' => 'Toutes les notes ont été validées. Évaluation clôturée.']);

        } catch (\Exception $e) {
            Log::error("Erreur validation notes : " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Une erreur est survenue.'], 500);
        }
    }

    /**
     * Rejeter une ou plusieurs notes (suppression logique — is_delete = 1)
     * La note rejetée disparaît de la liste ; il faudra la re-saisir.
     */
    public function rejectGrades(Request $request)
    {
        $request->validate([
            'grade_ids' => 'required|array',
            'grade_ids.*' => 'integer',
        ]);

        try {
            GradeModel::whereIn('id', $request->grade_ids)
                ->where('validated', false) // on ne rejette que des notes non validées
                ->update(['is_delete' => 1]);

            return response()->json(['success' => true, 'message' => 'Note(s) rejetée(s) avec succès.']);
        } catch (\Exception $e) {
            Log::error("Erreur rejet notes : " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Une erreur est survenue.'], 500);
        }
    }

    /**
     * Annuler la validation d'une ou plusieurs notes (repasse validated = false)
     * Uniquement accessible si la note EST validée — remet en attente de validation.
     */
    public function cancelValidation(Request $request)
    {
        $request->validate([
            'grade_ids' => 'required|array',
            'grade_ids.*' => 'integer',
        ]);

        try {
            GradeModel::whereIn('id', $request->grade_ids)
                ->where('validated', true)
                ->update([
                    'validated'    => false,
                    'validated_by' => null,
                    'validated_at' => null,
                ]);

            return response()->json(['success' => true, 'message' => 'Validation annulée. La note repasse en attente.']);
        } catch (\Exception $e) {
            Log::error("Erreur annulation validation : " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Une erreur est survenue.'], 500);
        }
    }

    /**
     * Annuler une évaluation entière — elle sera exclue du calcul des moyennes.
     *
     * Cette action est distincte de la suppression :
     * - L'évaluation reste en base avec status = 'cancelled'
     * - Toutes ses notes passent à is_delete = 0, validated = false
     * - Elle n'est plus prise en compte lors du calcul des bulletins
     * - Elle peut être réactivée si nécessaire (status → open)
     */
    public function cancelEvaluation(Request $request)
    {
        $request->validate(['evaluation_id' => 'required|integer']);

        try {
            $eval = EvaluationModel::getSingle($request->evaluation_id);
            if (!$eval) {
                return response()->json(['success' => false, 'message' => 'Évaluation introuvable.'], 404);
            }

            // Marquer l'évaluation comme annulée
            $eval->status = 'cancelled';
            $eval->save();

            // Remettre toutes les notes à l'état "non validé" mais les conserver
            GradeModel::where('evaluation_id', $eval->id)
                ->update([
                    'validated'    => false,
                    'validated_by' => null,
                    'validated_at' => null,
                ]);

            $evalLabel = $eval->title ?: $eval->type;
            return response()->json([
                'success' => true,
                'message' => "L'évaluation « {$evalLabel} » a été annulée et ne sera pas prise en compte dans le calcul des moyennes.",
            ]);
        } catch (\Exception $e) {
            Log::error("Erreur annulation évaluation : " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Une erreur est survenue.'], 500);
        }
    }

    /**
     * Notes en attente de validation
     * Passe aussi totalStudents par évaluation pour que le frontend
     * puisse désactiver "Valider tout" si des notes sont manquantes.
     */
    public function pendingValidation()
    {
        $grades = GradeModel::getPendingValidation(100); // on charge tout pour le groupement

        // Calculer le nombre total d'élèves actifs pour chaque évaluation concernée
        $evalIds    = $grades->pluck('evaluation_id')->unique();
        $evalCounts = \DB::table('evaluations')
            ->join('users', function ($j) {
                $j->on('users.class_id', '=', 'evaluations.class_id')
                  ->where('users.user_type', 3)
                  ->where('users.is_delete', 0)
                  ->where('users.status', 1);
            })
            ->whereIn('evaluations.id', $evalIds)
            ->groupBy('evaluations.id')
            ->select('evaluations.id as evaluation_id', \DB::raw('COUNT(users.id) as total_students'))
            ->pluck('total_students', 'evaluation_id');

        return Inertia::render('Admin/Evaluations/PendingValidation', [
            'grades'      => $grades,
            'evalCounts'  => $evalCounts, // Map<evaluation_id, total_students>
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
            'classes'          => $classes,
            'periods'          => PeriodModel::getCurrentPeriod(),
            'currentPeriod'    => PeriodModel::getCurrentPeriod()->first(),
            'evaluations'      => [],
            'grades'           => [],
            'selectedClassId'  => $request->class_id  ? (int) $request->class_id  : null,
            'selectedPeriodId' => $request->period_id ? (int) $request->period_id : null,
        ];

        if ($request->evaluation_id) {
            $eval = EvaluationModel::getSingle((int) $request->evaluation_id);
            if ($eval && $eval->teacher_id === $teacher_id) {
                $data['evaluation']      = $eval;
                $data['grades']          = GradeModel::getGradesForEvaluation($eval->id, $eval->class_id);
                $data['stats']           = GradeModel::getEvaluationStats($eval->id, (float) $eval->max_score);
                $data['selectedClassId']  = (int) $eval->class_id;
                $data['selectedPeriodId'] = (int) $eval->period_id;
            }
        }

        if ($request->class_id && $request->period_id) {
            $data['evaluations'] = EvaluationModel::getByClassAndPeriod(
                (int) $request->class_id,
                (int) $request->period_id
            )->filter(fn($e) => $e->teacher_id === $teacher_id)->values();
        } elseif (isset($data['evaluation'])) {
            $data['evaluations'] = EvaluationModel::getByClassAndPeriod(
                (int) $data['evaluation']->class_id,
                (int) $data['evaluation']->period_id
            )->filter(fn($e) => $e->teacher_id === $teacher_id)->values();
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
    public function getSubjectsByClass(int|string $class_id)
    {
        $subjects = ClassSubjectModel::getSubject((int) $class_id)
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
