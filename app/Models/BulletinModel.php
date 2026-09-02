<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class BulletinModel extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'bulletins';

    protected $fillable = [
        'school_id', 'student_id', 'period_id', 'average', 'rank', 'total_students',
        'class_success_rate', 'appreciation', 'teacher_comment',
        'status', 'generated_by', 'generated_at',
    ];

    protected $hidden = ['is_delete'];

    protected $casts = [
        'generated_at' => 'datetime',
        'average'      => 'float',
    ];

    /**
     * Appréciations selon la moyenne — référentiel béninois
     */
    public static function getAppreciation(float $average): string
    {
        return match(true) {
            $average >= 18 => 'Excellent',
            $average >= 16 => 'Très Bien',
            $average >= 14 => 'Bien',
            $average >= 12 => 'Assez Bien',
            $average >= 10 => 'Passable',
            $average >= 8  => 'Insuffisant',
            default        => 'Très Insuffisant',
        };
    }

    public static function getSingle(string $id): ?self
    {
        return self::find($id);
    }

    public static function getByStudentAndPeriod(string $student_id, string $period_id): ?self
    {
        return self::where('student_id', $student_id)
            ->where('period_id', $period_id)
            ->where('is_delete', 0)
            ->first();
    }

    /**
     * Bulletins d'un apprenant (toutes périodes, publiés)
     */
    public static function getByStudent(string $student_id)
    {
        return self::select('bulletins.*', 'periods.name as period_name', 'periods.type as period_type', 'periods.order_number')
            ->join('periods', 'periods.id', '=', 'bulletins.period_id')
            ->where('bulletins.student_id', $student_id)
            ->where('bulletins.is_delete', 0)
            ->where('bulletins.status', 'published')
            ->orderBy('periods.order_number', 'desc')
            ->get();
    }

    /**
     * Liste paginée des bulletins pour l'admin — scopée par école.
     */
    public static function getAll(int $perPage)
    {
        $user         = \Illuminate\Support\Facades\Auth::user();
        $isSuperAdmin = $user && (int) $user->user_type === 0;

        $q = self::select(
            'bulletins.*',
            'users.name as student_name',
            'users.last_name as student_last_name',
            'users.admission_number',
            'periods.name as period_name',
            'class.name as class_name',
        )
            ->join('users', 'users.id', '=', 'bulletins.student_id')
            ->join('periods', 'periods.id', '=', 'bulletins.period_id')
            ->join('class', 'class.id', '=', 'users.class_id')
            ->where('bulletins.is_delete', 0);

        // Scoping multi-tenant
        if (! $isSuperAdmin && $user) {
            $q->where('bulletins.school_id', $user->school_id);
        }

        if ($v = Request::get('period_id')) $q->where('bulletins.period_id', $v);
        if ($v = Request::get('class_id'))  $q->where('class.id', $v);
        if ($v = Request::get('status'))    $q->where('bulletins.status', $v);

        return $q->orderBy('class.name')->orderBy('bulletins.rank')->paginate($perPage);
    }

    /**
     * Génère ou recalcule le bulletin d'un apprenant pour une période.
     *
     * ⚠️  Pour un calcul de rang correct, préférer generateForClass() qui calcule
     *     les rangs en une seule passe sur toute la classe.
     *     Appeler generate() sur un seul apprenant donne un rang approximatif basé
     *     sur les moyennes recalculées à la volée pour toute la classe.
     */
    public static function generate(string $student_id, string $period_id, string $generated_by): self
    {
        $student = User::find($student_id);
        if (!$student) throw new \Exception("apprenant introuvable.");

        $class_id = $student->class_id;

        // Calcul des moyennes de toute la classe (nécessaire pour le rang)
        $classAverages = self::computeClassAverages($class_id, $period_id);

        return self::generateWithClassAverages($student_id, $period_id, $generated_by, $classAverages);
    }

    /**
     * Génère le bulletin d'un apprenant en réutilisant les moyennes de classe déjà calculées.
     * Évite de recalculer les moyennes de toute la classe pour chaque apprenant.
     *
     * @param array $classAverages  [student_id => general_average] — calculé une seule fois pour la classe
     */
    public static function generateWithClassAverages(
        int   $student_id,
        int   $period_id,
        int   $generated_by,
        array $classAverages
    ): self {
        $student = User::find($student_id);
        if (!$student) throw new \Exception("apprenant introuvable.");

        $class_id = $student->class_id;

        // Matières de la classe avec leurs coefficients
        $classSubjects = ClassSubjectModel::getAssignSubject($class_id);

        $totalWeightedPoints = 0;
        $totalSubjectCoeff   = 0;
        $subjectDetails      = [];

        foreach ($classSubjects as $cs) {
            $subjectAvg = EvaluationModel::calculateSubjectAverage($student_id, $cs->subject_id, $period_id);

            if ($subjectAvg !== null) {
                $subjectCoeff        = (int) ($cs->coefficient ?? 1);
                $weightedPoints      = $subjectAvg * $subjectCoeff;
                $totalWeightedPoints += $weightedPoints;
                $totalSubjectCoeff   += $subjectCoeff;

                $subjectDetails[] = [
                    'subject_id'      => $cs->subject_id,
                    'coefficient'     => $subjectCoeff,
                    'average'         => $subjectAvg,
                    'weighted_points' => round($weightedPoints, 2),
                    'appreciation'    => self::getAppreciation($subjectAvg),
                ];
            }
        }

        $generalAverage = $totalSubjectCoeff > 0
            ? round($totalWeightedPoints / $totalSubjectCoeff, 2)
            : null;

        // ── Rang ─────────────────────────────────────────────────────────────
        // On utilise les moyennes passées en paramètre (calculées une fois pour
        // toute la classe) pour garantir que tous les apprenants ont le même référentiel.
        $rank          = self::computeRank($student_id, $classAverages);
        $totalStudents = count($classAverages);
        $passCount     = count(array_filter($classAverages, fn($a) => $a >= 10));
        $successRate   = $totalStudents > 0 ? round(($passCount / $totalStudents) * 100, 2) : 0;

        // Upsert du bulletin
        $bulletin = self::firstOrNew([
            'student_id' => $student_id,
            'period_id'  => $period_id,
        ]);
        $bulletin->school_id          = $student->school_id; // scoping multi-tenant
        $bulletin->average            = $generalAverage;
        $bulletin->rank               = $rank;
        $bulletin->total_students     = $totalStudents;
        $bulletin->class_success_rate = $successRate;
        $bulletin->appreciation       = $generalAverage ? self::getAppreciation($generalAverage) : null;
        $bulletin->status             = 'draft';
        $bulletin->generated_by       = $generated_by;
        $bulletin->generated_at       = now();
        $bulletin->is_delete          = 0;
        $bulletin->save();

        // Upsert des détails par matière
        foreach ($subjectDetails as $detail) {
            DB::table('bulletin_subjects')->updateOrInsert(
                ['bulletin_id' => $bulletin->id, 'subject_id' => $detail['subject_id']],
                [
                    'coefficient'     => $detail['coefficient'],
                    'average'         => $detail['average'],
                    'weighted_points' => $detail['weighted_points'],
                    'appreciation'    => $detail['appreciation'],
                    'updated_at'      => now(),
                    'created_at'      => now(),
                ]
            );
        }

        return $bulletin;
    }

    /**
     * Génère tous les bulletins d'une classe pour une période.
     *
     * Les moyennes de classe sont calculées UNE SEULE FOIS puis partagées
     * entre tous les apprenants — garantit des rangs cohérents et identiques
     * quel que soit l'ordre de génération.
     *
     * Gestion des ex-aequo : deux apprenants avec la même moyenne ont le même rang.
     * L'apprenant suivant prend le rang +2 (méthode standard, ex : 1er, 2e, 2e, 4e).
     */
    public static function generateForClass(string $class_id, string $period_id, string $generated_by): array
    {
        $students = User::where('class_id', $class_id)
            ->where('user_type', 3)
            ->where('is_delete', 0)
            ->where('status', 1)
            ->get();

        // ── Étape 1 : calculer les moyennes de toute la classe en une passe ──
        $classAverages = self::computeClassAverages($class_id, $period_id);

        // ── Étape 2 : générer chaque bulletin en réutilisant ces moyennes ────
        $results = ['success' => 0, 'errors' => []];

        foreach ($students as $student) {
            try {
                self::generateWithClassAverages($student->id, $period_id, $generated_by, $classAverages);
                $results['success']++;
            } catch (\Exception $e) {
                $results['errors'][] = "apprenant #{$student->id} ({$student->name} {$student->last_name}) : {$e->getMessage()}";
            }
        }

        return $results;
    }

    /**
     * Calcule le rang d'un apprenant dans sa classe à partir du tableau des moyennes.
     *
     * Règle des ex-aequo (méthode standard) :
     *   - Deux apprenants avec 14.50 → tous les deux 2e rang
     *   - L'apprenant suivant (14.25) → 4e rang (pas 3e)
     *
     * @param  array $classAverages  [student_id => general_average]
     */
    public static function computeRank(string $student_id, array $classAverages): ?int
    {
        if (!isset($classAverages[$student_id])) return null;

        $studentAvg = $classAverages[$student_id];

        // Nombre d'apprenants ayant une moyenne STRICTEMENT supérieure
        $countAbove = count(array_filter($classAverages, fn($avg) => $avg > $studentAvg));

        return $countAbove + 1;
    }

    /**
     * Calcule les moyennes générales de tous les apprenants d'une classe.
     * Retourne [student_id => average]
     *
     * Les matières assignées à la classe sont chargées une seule fois
     * pour éviter N requêtes redondantes (une par apprenant).
     */
    public static function computeClassAverages(string $class_id, string $period_id): array
    {
        $students = User::where('class_id', $class_id)
            ->where('user_type', 3)
            ->where('is_delete', 0)
            ->where('status', 1)
            ->get();

        // Charger les matières une seule fois pour toute la classe
        $classSubjects = ClassSubjectModel::getAssignSubject($class_id);

        $averages = [];
        foreach ($students as $student) {
            $totalWeighted = 0;
            $totalCoeff    = 0;

            foreach ($classSubjects as $cs) {
                $avg = EvaluationModel::calculateSubjectAverage($student->id, $cs->subject_id, $period_id);
                if ($avg !== null) {
                    $coeff          = (int) ($cs->coefficient ?? 1);
                    $totalWeighted += $avg * $coeff;
                    $totalCoeff    += $coeff;
                }
            }

            $averages[$student->id] = $totalCoeff > 0 ? round($totalWeighted / $totalCoeff, 2) : 0.0;
        }

        return $averages;
    }

    /**
     * Détail complet d'un bulletin (matières + détail des évaluations par type)
     * Utilisé pour la génération du bulletin PDF style béninois.
     *
     * Pour chaque matière du bulletin, on charge aussi le détail des évaluations
     * validées (interrogations, devoirs, etc.) afin que le PDF puisse afficher
     * chaque note individuellement.
     */
    public static function getFullDetail(string $bulletin_id): array
    {
        $bulletin = self::select('bulletins.*', 'users.name as student_name', 'users.last_name as student_last_name',
                'users.admission_number', 'users.profile_picture', 'users.date_of_birth', 'users.gender',
                'class.name as class_name', 'periods.name as period_name', 'periods.type as period_type',
                'periods.school_year', 'periods.order_number')
            ->join('users', 'users.id', '=', 'bulletins.student_id')
            ->join('periods', 'periods.id', '=', 'bulletins.period_id')
            ->join('class', 'class.id', '=', 'users.class_id')
            ->where('bulletins.id', $bulletin_id)
            ->first();

        if (!$bulletin) return [];

        // Matières avec leurs moyennes calculées (table bulletin_subjects)
        $subjects = DB::table('bulletin_subjects')
            ->select('bulletin_subjects.*', 'subject.name as subject_name')
            ->join('subject', 'subject.id', '=', 'bulletin_subjects.subject_id')
            ->where('bulletin_subjects.bulletin_id', $bulletin_id)
            ->orderBy('subject.name')
            ->get();

        // Pour chaque matière, charger le détail des évaluations validées
        // (excluant les cancelled et is_delete=1)
        $subjectsWithDetail = $subjects->map(function ($subject) use ($bulletin) {
            $detail = EvaluationModel::calculateSubjectAverageDetail(
                $bulletin->student_id,
                $subject->subject_id,
                $bulletin->period_id
            );
            // Ajouter le détail comme propriété de l'objet (pas array_merge qui casse la syntaxe ->)
            $subject->detail = $detail;
            return $subject;
        });

        return [
            'bulletin' => $bulletin,
            'subjects' => $subjectsWithDetail,
        ];
    }
}
