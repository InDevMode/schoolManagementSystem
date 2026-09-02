<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class EvaluationModel extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'evaluations';

    protected $fillable = [
        'school_id', 'exam_id', 'class_id', 'subject_id', 'teacher_id', 'period_id',
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

    public static function getSingle(string $id): ?self
    {
        return self::find($id);
    }

    /**
     * Liste paginée avec filtres — pour l'admin
     */
    public static function getAll(int $perPage)
    {
        $user         = \Illuminate\Support\Facades\Auth::user();
        $isSuperAdmin = $user && (int) $user->user_type === 0;

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

        // Multi-tenant : un admin ne voit que les évaluations de son école
        if (! $isSuperAdmin && $user && $user->school_id) {
            $q->where('evaluations.school_id', $user->school_id);
        }

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
    public static function getByClassAndPeriod(string $class_id, string $period_id)
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
     * Retourne le détail du calcul de moyenne par groupe de type pour une matière.
     * Utile pour l'affichage sur le bulletin (montrer la moyenne des interros, des devoirs, etc.)
     *
     * Retourne un tableau de la forme :
     * [
     *   'groups' => [
     *     'interrogation'    => ['scores' => [12, 14], 'average' => 13.0, 'count' => 2],
     *     'devoir_surveille' => ['scores' => [13, 15], 'average' => 14.0, 'count' => 2],
     *     'travail_maison'   => ['scores' => [16],     'average' => 16.0, 'count' => 1],
     *     'examen_blanc'     => ['scores' => [12],     'average' => 12.0, 'count' => 1],
     *   ],
     *   'subject_average'  => 13.75,   // (13+14+16+12)/4 — moyenne des groupes présents
     *   'coefficient'      => 2,        // coefficient de la matière
     *   'weighted_average' => 27.5,     // subject_average × coefficient
     *   'groups_count'     => 4,
     * ]
     */
    public static function calculateSubjectAverageDetail(string $student_id, string $subject_id, string $period_id): array
    {
        $evaluations = self::where('subject_id', $subject_id)
            ->where('period_id', $period_id)
            ->where('is_delete', 0)
            ->where('status', 'validated')   // seules les évals entièrement validées, cancelled exclu
            ->get();

        $groups = [
            'interrogation'    => ['scores' => [], 'average' => null, 'count' => 0],
            'devoir_surveille' => ['scores' => [], 'average' => null, 'count' => 0],
            'travail_maison'   => ['scores' => [], 'average' => null, 'count' => 0],
            'examen_blanc'     => ['scores' => [], 'average' => null, 'count' => 0],
        ];

        $coefficient = 1; // sera écrasé dès la première évaluation trouvée

        foreach ($evaluations as $eval) {
            $coefficient = (float) ($eval->coefficient ?: 1);

            $grade = GradeModel::where('evaluation_id', $eval->id)
                ->where('student_id', $student_id)
                ->where('is_delete', 0)
                ->whereNotNull('score')
                ->first();

            if ($grade && array_key_exists($eval->type, $groups)) {
                $maxScore        = (float) ($eval->max_score ?: 20);
                $normalizedScore = $maxScore > 0 ? round(($grade->score / $maxScore) * 20, 4) : 0;
                $groups[$eval->type]['scores'][] = $normalizedScore;
            }
        }

        $groupAverages = [];
        foreach ($groups as $type => &$data) {
            $data['count'] = count($data['scores']);
            if ($data['count'] > 0) {
                $data['average']   = round(array_sum($data['scores']) / $data['count'], 2);
                $groupAverages[]   = $data['average'];
            }
        }
        unset($data);

        $subjectAverage  = count($groupAverages) > 0 ? round(array_sum($groupAverages) / count($groupAverages), 2) : null;
        $weightedAverage = $subjectAverage !== null ? round($subjectAverage * $coefficient, 2) : null;

        return [
            'groups'          => $groups,
            'groups_count'    => count($groupAverages),
            'subject_average' => $subjectAverage,
            'coefficient'     => $coefficient,
            'weighted_average'=> $weightedAverage,
        ];
    }
    public static function getByTeacher(string $teacher_id, ?string $class_id = null, ?string $subject_id = null)
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
    public static function getByTeacherPaginated(string $teacher_id, int $perPage)
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
     * Calcule la moyenne d'un apprenant pour une matière sur une période.
     *
     * Formule béninoise en deux étapes :
     *
     * Étape 1 — Moyenne par groupe de type :
     *   Moy_interros = Σ(notes interros) / nb_interros
     *   Moy_devoirs  = Σ(notes devoirs)  / nb_devoirs
     *   Moy_TM       = Σ(notes TM)       / nb_TM
     *   Moy_EB       = Σ(notes EB)       / nb_EB
     *   (seuls les groupes ayant au moins une note sont comptés)
     *
     * Étape 2 — Moyenne des groupes × coefficient de la matière :
     *   Moy_matière   = (Moy_interros + Moy_devoirs + Moy_TM + Moy_EB) / nb_groupes_présents
     *   Note_coefficée = Moy_matière × coefficient_matière
     *   (le coefficient est celui défini lors de l'assignation classe-matière,
     *    récupéré sur l'évaluation elle-même — il est identique pour tous les types)
     *
     * Retourne la moyenne simple de la matière (sur 20), sans le coefficient.
     * Le coefficient est appliqué ensuite dans BulletinModel::generate().
     */
    public static function calculateSubjectAverage(string $student_id, string $subject_id, string $period_id): ?float
    {
        $evaluations = self::where('subject_id', $subject_id)
            ->where('period_id', $period_id)
            ->where('is_delete', 0)
            ->where('status', 'validated')   // seules les évals entièrement validées
            // 'cancelled' est déjà exclu par la condition ci-dessus
            ->get();

        if ($evaluations->isEmpty()) return null;

        // Regrouper les notes par type d'évaluation
        $groups = [
            'interrogation'    => [],
            'devoir_surveille' => [],
            'travail_maison'   => [],
            'examen_blanc'     => [],
        ];

        foreach ($evaluations as $eval) {
            $grade = GradeModel::where('evaluation_id', $eval->id)
                ->where('student_id', $student_id)
                ->where('is_delete', 0)
                ->whereNotNull('score')
                ->first();

            if ($grade) {
                // Normaliser sur 20 si max_score différent de 20
                $maxScore        = (float) ($eval->max_score ?: 20);
                $normalizedScore = $maxScore > 0 ? round(($grade->score / $maxScore) * 20, 4) : 0;

                if (array_key_exists($eval->type, $groups)) {
                    $groups[$eval->type][] = $normalizedScore;
                }
            }
        }

        // Étape 1 : moyenne par groupe (uniquement les groupes non vides)
        $groupAverages = [];
        foreach ($groups as $type => $scores) {
            if (count($scores) > 0) {
                $groupAverages[$type] = array_sum($scores) / count($scores);
            }
        }

        if (empty($groupAverages)) return null;

        // Étape 2 : moyenne des groupes (chaque groupe compte pour 1, peu importe son nombre d'évals)
        $subjectAverage = array_sum($groupAverages) / count($groupAverages);

        return round($subjectAverage, 2);
    }
}
