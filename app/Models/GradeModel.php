<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradeModel extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'grades';

    protected $fillable = [
        'student_id', 'evaluation_id', 'score', 'teacher_id',
        'validated', 'validated_by', 'validated_at', 'observation',
    ];

    protected $hidden = ['is_delete'];

    protected $casts = [
        'validated'    => 'boolean',
        'validated_at' => 'datetime',
        'score'        => 'float',
    ];

    // ── Relations ────────────────────────────────────────────────────────────

    public function evaluation(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(EvaluationModel::class, 'evaluation_id');
    }

    public function student(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public static function getSingle(string $id): ?self
    {
        return self::find($id);
    }

    public static function findByStudentAndEvaluation(string $student_id, string $evaluation_id): ?self
    {
        return self::where('student_id', $student_id)
            ->where('evaluation_id', $evaluation_id)
            ->where('is_delete', 0)
            ->first();
    }

    /**
     * Toutes les notes d'une évaluation (saisie en masse)
     * Retourne les apprenants de la classe avec leur note si elle existe
     */
    public static function getGradesForEvaluation(string $evaluation_id, string $class_id)
    {
        $students = User::select('users.id as student_id', 'users.name', 'users.last_name', 'users.admission_number')
            ->where('users.class_id', $class_id)
            ->where('users.user_type', 3)
            ->where('users.is_delete', 0)
            ->where('users.status', 1)
            ->orderBy('users.last_name')
            ->get();

        return $students->map(function ($student) use ($evaluation_id) {
            // Note active (non supprimée)
            $grade = self::where('evaluation_id', $evaluation_id)
                ->where('student_id', $student->student_id)
                ->where('is_delete', 0)
                ->first();

            // Note rejetée (is_delete = 1) — pour informer l'interface
            $rejected = !$grade && self::where('evaluation_id', $evaluation_id)
                ->where('student_id', $student->student_id)
                ->where('is_delete', 1)
                ->exists();

            return [
                'student_id'       => $student->student_id,
                'name'             => $student->name,
                'last_name'        => $student->last_name,
                'admission_number' => $student->admission_number,
                'grade_id'         => $grade?->id,
                'score'            => $grade?->score,
                'validated'        => $grade?->validated ?? false,
                'observation'      => $grade?->observation,
                'rejected'         => $rejected, // true si la note a été rejetée et doit être re-saisie
            ];
        });
    }

    /**
     * Notes en attente de validation — scopées par école de l'admin connecté.
     * Ne retourne QUE les notes avec un score saisi (score NOT NULL)
     * et dont l'évaluation n'est pas annulée (status != 'cancelled').
     */
    public static function getPendingValidation(int $perPage)
    {
        $user         = \Illuminate\Support\Facades\Auth::user();
        $isSuperAdmin = $user && (int) $user->user_type === 0;

        $q = self::select(
            'grades.*',
            'users.name as student_name',
            'users.last_name as student_last_name',
            'evaluations.type as eval_type',
            'evaluations.eval_date',
            'evaluations.title as eval_title',
            'evaluations.max_score',
            'subject.name as subject_name',
            'class.name as class_name',
        )
            ->join('users', 'users.id', '=', 'grades.student_id')
            ->join('evaluations', 'evaluations.id', '=', 'grades.evaluation_id')
            ->join('subject', 'subject.id', '=', 'evaluations.subject_id')
            ->join('class', 'class.id', '=', 'evaluations.class_id')
            ->where('grades.validated', false)
            ->whereNotNull('grades.score')
            ->where('grades.is_delete', 0)
            ->where('evaluations.is_delete', 0)
            ->where('evaluations.status', '!=', 'cancelled');

        // Scoping multi-tenant
        if (! $isSuperAdmin && $user) {
            $q->where('evaluations.school_id', $user->school_id);
        }

        return $q->orderBy('grades.created_at', 'desc')->paginate($perPage);
    }

    /**
     * Notes d'un apprenant pour une période
     */
    public static function getStudentGradesForPeriod(string $student_id, string $period_id)
    {
        return self::select(
            'grades.*',
            'evaluations.type as eval_type',
            'evaluations.coefficient as eval_coeff',
            'evaluations.max_score',
            'evaluations.eval_date',
            'evaluations.title as eval_title',
            'subject.name as subject_name',
            'subject.id as subject_id',
        )
            ->join('evaluations', 'evaluations.id', '=', 'grades.evaluation_id')
            ->join('subject', 'subject.id', '=', 'evaluations.subject_id')
            ->where('grades.student_id', $student_id)
            ->where('evaluations.period_id', $period_id)
            ->where('evaluations.is_delete', 0)
            ->where('grades.is_delete', 0)
            ->orderBy('evaluations.subject_id')
            ->orderBy('evaluations.eval_date')
            ->get();
    }

    /**
     * Stats d'une évaluation (min, max, moyenne de la classe)
     */
    public static function getEvaluationStats(string $evaluation_id, float $max_score): array
    {
        $grades = self::where('evaluation_id', $evaluation_id)
            ->where('is_delete', 0)
            ->whereNotNull('score')
            ->pluck('score')
            ->map(fn($s) => $max_score > 0 ? ($s / $max_score) * 20 : 0);

        if ($grades->isEmpty()) {
            return ['min' => null, 'max' => null, 'average' => null, 'count' => 0];
        }

        return [
            'min'     => round($grades->min(), 2),
            'max'     => round($grades->max(), 2),
            'average' => round($grades->average(), 2),
            'count'   => $grades->count(),
        ];
    }
}
