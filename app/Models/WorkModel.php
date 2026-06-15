<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class WorkModel extends Model
{
    use HasFactory;

    protected $table = 'works';

    protected $fillable = [
        'class_id',
        'subject_id',
        'work_date',
        'submission_date',
        'document_file',
        'description',
        'created_by',
    ];

    protected $hidden = [
        'is_delete',
    ];

    public static function getSingle($id)
    {
        return WorkModel::find($id);
    }

    public static function getWorkIdWithHomeworks(int $workId)
    {
        $work = WorkModel::select(
            'works.*',
            'class.name as class_name',
            'subject.name as subject_name',
            'users.name as created_by_name'
        )
            ->join('class', 'class.id', '=', 'works.class_id')
            ->join('subject', 'subject.id', '=', 'works.subject_id')
            ->join('users', 'users.id', '=', 'works.created_by')
            ->where('works.id', $workId)
            ->where('works.is_delete', 0)
            ->with([
                'homeworks' => function ($query) {
                    $query->where('is_delete', 0)
                          ->with(['student:id,name,last_name']);
                },
                'attachments',
            ])
            ->first();

        if ($work) {
            // Total des élèves de la classe pour afficher X/total soumis
            $work->total_students = \App\Models\User::where('class_id', $work->class_id)
                ->where('user_type', 3)
                ->where('is_delete', 0)
                ->count();
        }

        return $work;
    }


    public static function getWorks(int $perpage)
    {
        $results = WorkModel::select('works.*', 'class.name as class_name', 'subject.name as subject_name', 'users.name as created_by_name')
            ->join('class', 'class.id', '=', 'works.class_id')
            ->join('subject', 'subject.id', '=', 'works.subject_id')
            ->join('users', 'users.id', '=', 'works.created_by')
            ->where('works.is_delete', '=', 0);

        $filters = [
            'class.name' => strtolower(Request::get('class_name')),
            'subject.name' => strtolower(Request::get('subject_name')),
            'works.work_date' => strtolower(Request::get('work_date')),
            'works.submission_date' => strtolower(Request::get('submission_date')),
            'works.created_at' => strtolower(Request::get('created_at')),
            'works.updated_at' => strtolower(Request::get('updated_at')),
        ];

        foreach ($filters as $column => $value) {
            if (!empty($value)) {
                $results->where($column, 'like', '%' . $value . '%');
            }
        }

        return $results->orderBy('works.id', 'desc')
            ->paginate($perpage);
    }

    public static function getWorksTeacher(int $perpage, $class_ids)
    {
        $results = WorkModel::select('works.*', 'class.name as class_name', 'subject.name as subject_name', 'users.name as created_by_name')
            ->join('class', 'class.id', '=', 'works.class_id')
            ->join('subject', 'subject.id', '=', 'works.subject_id')
            ->join('users', 'users.id', '=', 'works.created_by')
            ->whereIn('works.class_id', $class_ids)
            ->where('works.is_delete', '=', 0);

        $filters = [
            'class.name' => strtolower(Request::get('class_name')),
            'subject.name' => strtolower(Request::get('subject_name')),
            'works.work_date' => strtolower(Request::get('work_date')),
            'works.submission_date' => strtolower(Request::get('submission_date')),
            'works.created_at' => strtolower(Request::get('created_at')),
            'works.updated_at' => strtolower(Request::get('updated_at')),
        ];

        foreach ($filters as $column => $value) {
            if (!empty($value)) {
                $results->where($column, 'like', '%' . $value . '%');
            }
        }

        return $results->orderBy('works.id', 'desc')
            ->paginate($perpage);
    }

    public static function getWorksWithStudentStatus(int $class_id, int $student_id, int $perpage)
    {
        $results = WorkModel::select(
            'works.*',
            'class.name as class_name',
            'subject.name as subject_name',
            'users.name as created_by_name',
            'homework.status as homework_status',
            'homework.description as homework_description',
            'homework.document_file as homework_document_file'
        )
            ->join('class', 'class.id', '=', 'works.class_id')
            ->join('subject', 'subject.id', '=', 'works.subject_id')
            ->join('users', 'users.id', '=', 'works.created_by')
            ->leftJoin('homework', function ($join) use ($student_id) {
                $join->on('homework.work_id', '=', 'works.id')
                    ->where('homework.student_id', '=', $student_id)
                    ->where('homework.is_delete', '=', 0);
            })
            ->where('works.class_id', '=', $class_id)
            ->where('works.is_delete', '=', 0);

        $filters = [
            'class.name' => strtolower(Request::get('class_name')),
            'subject.name' => strtolower(Request::get('subject_name')),
            'works.work_date' => strtolower(Request::get('work_date')),
            'works.submission_date' => strtolower(Request::get('submission_date')),
            'works.created_at' => strtolower(Request::get('created_at')),
            'works.updated_at' => strtolower(Request::get('updated_at')),
        ];

        foreach ($filters as $column => $value) {
            if (!empty($value)) {
                $results->where($column, 'like', '%' . $value . '%');
            }
        }

        return $results->orderBy('works.id', 'desc')
            ->paginate($perpage);
    }

    // WorkModel.php
    public function homeworks()
    {
        return $this->hasMany(HomeworkModel::class, 'work_id', 'id');
    }

    /** Pièces jointes du travail */
    public function attachments()
    {
        return $this->hasMany(WorkAttachmentModel::class, 'work_id', 'id')
            ->where('is_delete', 0)
            ->orderBy('id');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Listing avec filtres communs — base réutilisable
    // ─────────────────────────────────────────────────────────────────────────
    private static function baseQuery(bool $onlyDeleted = false)
    {
        $q = WorkModel::select(
                'works.*',
                'class.name as class_name',
                'subject.name as subject_name',
                'users.name as created_by_name',
                'users.last_name as created_by_last_name'
            )
            ->join('class',   'class.id',   '=', 'works.class_id')
            ->join('subject', 'subject.id', '=', 'works.subject_id')
            ->join('users',   'users.id',   '=', 'works.created_by');

        if ($onlyDeleted) {
            $q->where('works.is_delete', 1);
        } else {
            $q->where('works.is_delete', 0);
        }

        return $q;
    }

    private static function applyFilters($query)
    {
        $filters = [
            'class.name'            => strtolower(Request::get('class_name')),
            'subject.name'          => strtolower(Request::get('subject_name')),
            'works.work_date'       => strtolower(Request::get('work_date')),
            'works.submission_date' => strtolower(Request::get('submission_date')),
            'works.created_at'      => strtolower(Request::get('created_at')),
            'works.updated_at'      => strtolower(Request::get('updated_at')),
        ];

        foreach ($filters as $column => $value) {
            if (!empty($value)) {
                $query->where($column, 'like', '%' . $value . '%');
            }
        }

        return $query;
    }

    /**
     * Admin — voir tous les travaux (actifs)
     */
    public static function getWorksAdmin(int $perpage)
    {
        $q = static::baseQuery(false);
        static::applyFilters($q);
        return $q->orderBy('works.id', 'desc')->paginate($perpage);
    }

    /**
     * Admin — corbeille (soft-deleted)
     */
    public static function getWorksAdminTrashed(int $perpage)
    {
        $q = static::baseQuery(true);
        static::applyFilters($q);
        return $q->orderBy('works.id', 'desc')->paginate($perpage);
    }

    /**
     * Admin — ses propres travaux uniquement (pour l'édition sécurisée)
     */
    public static function getWorksByCreator(int $creatorId, int $perpage)
    {
        $q = static::baseQuery(false)->where('works.created_by', $creatorId);
        static::applyFilters($q);
        return $q->orderBy('works.id', 'desc')->paginate($perpage);
    }

    /**
     * Teacher — travaux de ses classes uniquement
     */
    public static function getWorksTeacherOwn(int $teacherId, array $class_ids, int $perpage)
    {
        $q = static::baseQuery(false)
            ->whereIn('works.class_id', $class_ids)
            ->where('works.created_by', $teacherId);
        static::applyFilters($q);
        return $q->orderBy('works.id', 'desc')->paginate($perpage);
    }

    public static function getTotalWork()
    {
        return WorkModel::where('works.is_delete', '=', 0)->count();
        ;
    }

    public static function getTotalWorkStudent()
    {
        $student_id = Auth::user()->id;
        $class_id = Auth::user()->class_id;

        return WorkModel::join('class', 'class.id', '=', 'works.class_id')
            ->join('subject', 'subject.id', '=', 'works.subject_id')
            ->join('users', 'users.id', '=', 'works.created_by')
            ->leftJoin('homework', function ($join) use ($student_id) {
                $join->on('homework.work_id', '=', 'works.id')
                    ->where('homework.student_id', '=', $student_id)
                    ->where('homework.is_delete', '=', 0);
            })
            ->where('works.class_id', '=', $class_id)
            ->where('works.is_delete', '=', 0)
            ->distinct('works.id')
            ->count('works.id');
    }

        public static function getTotalWorkParentStudent($class_ids, $student_ids)
    {

        return WorkModel::join('class', 'class.id', '=', 'works.class_id')
            ->join('subject', 'subject.id', '=', 'works.subject_id')
            ->join('users', 'users.id', '=', 'works.created_by')
            ->leftJoin('homework', function ($join) use ($student_ids) {
                $join->on('homework.work_id', '=', 'works.id')
                    ->whereIn('homework.student_id', $student_ids)
                    ->where('homework.is_delete', '=', 0);
            })
            ->whereIn('works.class_id', $class_ids)
            ->where('works.is_delete', '=', 0)
            ->distinct('works.id')
            ->count('works.id');
    }


}
