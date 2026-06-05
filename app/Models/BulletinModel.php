<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class BulletinModel extends Model
{
    use HasFactory;

    protected $table = 'bulletins';

    protected $fillable = [
        'student_id', 'period_id', 'average', 'rank', 'total_students',
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

    public static function getSingle(int $id): ?self
    {
        return self::find($id);
    }

    public static function getByStudentAndPeriod(int $student_id, int $period_id): ?self
    {
        return self::where('student_id', $student_id)
            ->where('period_id', $period_id)
            ->where('is_delete', 0)
            ->first();
    }

    /**
     * Bulletins d'un élève (toutes périodes, publiés)
     */
    public static function getByStudent(int $student_id)
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
     * Liste paginée des bulletins pour l'admin
     */
    public static function getAll(int $perPage)
    {
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

        if ($v = Request::get('period_id')) $q->where('bulletins.period_id', $v);
        if ($v = Request::get('class_id'))  $q->where('class.id', $v);
        if ($v = Request::get('status'))    $q->where('bulletins.status', $v);

        return $q->orderBy('class.name')->orderBy('bulletins.rank')->paginate($perPage);
    }

    /**
     * Génère ou recalcule le bulletin d'un élève pour une période
     * Applique la logique béninoise : Σ(moy_matière × coeff_matière) / Σ(coefficients)
     */
    public static function generate(int $student_id, int $period_id, int $generated_by): self
    {
        $student = User::find($student_id);
        if (!$student) throw new \Exception("Élève introuvable.");

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

        // Rang dans la classe
        $classAverages = self::computeClassAverages($class_id, $period_id);
        arsort($classAverages);
        $rankedStudentIds = array_keys($classAverages);
        $rankPosition     = array_search($student_id, $rankedStudentIds);
        $rank             = $rankPosition !== false ? $rankPosition + 1 : null;

        $totalStudents = count($classAverages);
        $passCount     = count(array_filter($classAverages, fn($a) => $a >= 10));
        $successRate   = $totalStudents > 0 ? round(($passCount / $totalStudents) * 100, 2) : 0;

        // Upsert du bulletin
        $bulletin = self::firstOrNew([
            'student_id' => $student_id,
            'period_id'  => $period_id,
        ]);
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
     * Génère tous les bulletins d'une classe pour une période
     */
    public static function generateForClass(int $class_id, int $period_id, int $generated_by): array
    {
        $students = User::where('class_id', $class_id)
            ->where('user_type', 3)
            ->where('is_delete', 0)
            ->where('status', 1)
            ->get();

        $results = ['success' => 0, 'errors' => []];

        foreach ($students as $student) {
            try {
                self::generate($student->id, $period_id, $generated_by);
                $results['success']++;
            } catch (\Exception $e) {
                $results['errors'][] = "Élève #{$student->id} ({$student->name} {$student->last_name}) : {$e->getMessage()}";
            }
        }

        return $results;
    }

    /**
     * Calcule les moyennes générales de tous les élèves d'une classe
     * Retourne [student_id => average]
     */
    public static function computeClassAverages(int $class_id, int $period_id): array
    {
        $students = User::where('class_id', $class_id)
            ->where('user_type', 3)
            ->where('is_delete', 0)
            ->where('status', 1)
            ->get();

        $averages = [];
        foreach ($students as $student) {
            $classSubjects = ClassSubjectModel::getAssignSubject($class_id);
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
     * Détail complet d'un bulletin (matières + notes par évaluation)
     * Utilisé pour la génération du bulletin PDF style béninois
     */
    public static function getFullDetail(int $bulletin_id): array
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

        $subjects = DB::table('bulletin_subjects')
            ->select('bulletin_subjects.*', 'subject.name as subject_name')
            ->join('subject', 'subject.id', '=', 'bulletin_subjects.subject_id')
            ->where('bulletin_subjects.bulletin_id', $bulletin_id)
            ->orderBy('subject.name')
            ->get();

        return [
            'bulletin' => $bulletin,
            'subjects' => $subjects,
        ];
    }
}
