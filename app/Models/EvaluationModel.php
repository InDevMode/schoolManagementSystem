<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class EvaluationModel extends Model
{
    use HasFactory;

    protected $table = 'evaluations';

    protected $fillable = [
        'exam_id', 'class_id', 'subject_id', 'teacher_id', 'period_id',
        'type', 'coefficient', 'max_score', 'eval_date', 'title',
        'status', 'created_by',
    ];

    protected $hidden = ['is_delete'];

    /**
     * Coefficients par défaut selon le type d'évaluation béninois
     */
    public static array $typeCoefficients = [
        'interrogation'    => 1,
        'devoir_surveille' => 2,
        'travail_maison'   => 1,
        'examen_blanc'     => 3,
    ];

    /**
     * Labels lisibles des types
     */
    public static array $typeLabels = [
        'interrogation'    => 'Interrogation',
        'devoir_surveille' => 'Devoir surveillé',
        'travail_maison'   => 'Travail de maison',
        'examen_blanc'     => 'Examen blanc',
    ];

    public static function getSingle(int $id): ?self
    {
        return self::find($id);
    }

    /**
     * Liste paginée avec filtres — pour l'admin
     */
    public static function getAll(int $perPage)
    {
        $q = self::select(
            'evaluations.*',
            'class.name as class_name',
            'subject.name as subject_name',
            'periods.name as period_name',
            'teacher.name as teacher_name',
            'teacher.last_name as teacher_last_name',
        )
            ->join('class', 'class.id', '=', 'evaluations.class_id')
            ->join('subject', 'subject.id', '=', 'evaluations.subject_id')
            ->leftJoin('periods', 'periods.id', '=', 'evaluations.period_id')
            ->leftJoin('users as teacher', 'teacher.id', '=', 'evaluations.teacher_id')
            ->where('evaluations.is_delete', 0);

        if ($v = Request::get('class_id'))   $q->where('evaluations.class_id', $v);
        if ($v = Request::get('subject_id')) $q->where('evaluations.subject_id', $v);
        if ($v = Request::get('period_id'))  $q->where('evaluations.period_id', $v);
        if ($v = Request::get('type'))       $q->where('evaluations.type', $v);
        if ($v = Request::get('status'))     $q->where('evaluations.status', $v);

        return $q->orderBy('evaluations.eval_date', 'desc')
            ->orderBy('evaluations.id', 'desc')
            ->paginate($perPage);
    }

    /**
     * Évaluations d'une classe pour une période donnée
     */
    public static function getByClassAndPeriod(int $class_id, int $period_id)
    {
        return self::select('evaluations.*', 'subject.name as subject_name')
            ->join('subject', 'subject.id', '=', 'evaluations.subject_id')
            ->where('evaluations.class_id', $class_id)
            ->where('evaluations.period_id', $period_id)
            ->where('evaluations.is_delete', 0)
            ->where('evaluations.status', '!=', 'draft')
            ->orderBy('evaluations.subject_id')
            ->orderBy('evaluations.eval_date')
            ->get();
    }

    /**
     * Évaluations d'un prof
     */
    public static function getByTeacher(int $teacher_id, ?int $class_id = null, ?int $subject_id = null)
    {
        $q = self::select(
            'evaluations.*',
            'class.name as class_name',
            'subject.name as subject_name',
            'periods.name as period_name'
        )
            ->join('class', 'class.id', '=', 'evaluations.class_id')
            ->join('subject', 'subject.id', '=', 'evaluations.subject_id')
            ->leftJoin('periods', 'periods.id', '=', 'evaluations.period_id')
            ->where('evaluations.teacher_id', $teacher_id)
            ->where('evaluations.is_delete', 0);

        if ($class_id)   $q->where('evaluations.class_id', $class_id);
        if ($subject_id) $q->where('evaluations.subject_id', $subject_id);

        return $q->orderBy('evaluations.eval_date', 'desc')->get();
    }

    /**
     * Évaluations d'un prof paginées avec filtres
     */
    public static function getByTeacherPaginated(int $teacher_id, int $perPage)
    {
        $q = self::select(
            'evaluations.*',
            'class.name as class_name',
            'subject.name as subject_name',
            'periods.name as period_name'
        )
            ->join('class', 'class.id', '=', 'evaluations.class_id')
            ->join('subject', 'subject.id', '=', 'evaluations.subject_id')
            ->leftJoin('periods', 'periods.id', '=', 'evaluations.period_id')
            ->where('evaluations.teacher_id', $teacher_id)
            ->where('evaluations.is_delete', 0);

        if ($v = Request::get('class_id'))   $q->where('evaluations.class_id', $v);
        if ($v = Request::get('subject_id')) $q->where('evaluations.subject_id', $v);
        if ($v = Request::get('period_id'))  $q->where('evaluations.period_id', $v);
        if ($v = Request::get('type'))       $q->where('evaluations.type', $v);
        if ($v = Request::get('status'))     $q->where('evaluations.status', $v);

        return $q->orderBy('evaluations.eval_date', 'desc')->paginate($perPage);
    }

    /**
     * Calcule la moyenne d'un élève pour une matière sur une période
     * Formule béninoise : Σ(note_sur_20 × coeff) / Σ(coefficients)
     */
    public static function calculateSubjectAverage(int $student_id, int $subject_id, int $period_id): ?float
    {
        $evaluations = self::where('subject_id', $subject_id)
            ->where('period_id', $period_id)
            ->where('is_delete', 0)
            ->where('status', 'validated')
            ->get();

        if ($evaluations->isEmpty()) return null;

        $totalWeighted = 0;
        $totalCoeff    = 0;

        foreach ($evaluations as $eval) {
            $grade = GradeModel::where('evaluation_id', $eval->id)
                ->where('student_id', $student_id)
                ->where('is_delete', 0)
                ->whereNotNull('score')
                ->first();

            if ($grade) {
                // Normaliser sur 20 si max_score différent de 20
                $maxScore        = (float) ($eval->max_score ?: 20);
                $normalizedScore = $maxScore > 0 ? ($grade->score / $maxScore) * 20 : 0;

                $totalWeighted += $normalizedScore * $eval->coefficient;
                $totalCoeff    += $eval->coefficient;
            }
        }

        if ($totalCoeff === 0) return null;

        return round($totalWeighted / $totalCoeff, 2);
    }
}
