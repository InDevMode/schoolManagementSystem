<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ClassSubjectModel extends Model
{
    use HasFactory;

    protected $table = 'class_subject';

    protected $fillable = [
        'class_id',
        'subject_id',
        'created_by',
        'status'
    ];

    protected $hidden = [
        'is_delete',
    ];

    public static function getSingle(int $id)
    {
        return ClassSubjectModel::find($id);
    }

    public static function getAllClassSubject(int $perPage): LengthAwarePaginator
    {
        $results = ClassSubjectModel::select(
            'class_subject.*',
            'class.name as class_name',
            'subject.name as subject_name',
            'users.name as created_by_name'
        )
            ->join('subject', 'subject.id', '=', 'class_subject.subject_id')
            ->join('class', 'class.id', '=', 'class_subject.class_id')
            ->join('users', 'users.id', '=', 'class_subject.created_by')
            ->where('class_subject.is_delete', 0);

        $filters = [
            'class.name' => strtolower(Request::get('class_name')),
            'subject.name' => strtolower(Request::get('subject_name')),
            'class_subject.created_at' => strtolower(Request::get('created_at')),
            'class_subject.updated_at' => strtolower(Request::get('updated_at')),
        ];

        foreach ($filters as $column => $value) {
            if (!empty($value)) {
                $results->where($column, 'like', '%' . $value . '%');
            }
        }

        $status = Request::get('status');
        if (in_array($status, ['0', '1'], true)) {
            $results->where('class_subject.status', $status);
        }

        return $results->orderBy('class_subject.id', 'desc')
            ->paginate($perPage);
    }

    public static function getAlreadyExist($class_id, $subject_id)
    {
        return ClassSubjectModel::where('class_id', '=', $class_id)->where('subject_id', '=', $subject_id)->first();
    }

    public static function getAssignSubject($class_id)
    {
        return ClassSubjectModel::where('class_id', '=', $class_id)->where('is_delete', 0)->get();
    }

    public static function deleteSubjectAssign($class_id)
    {
        return ClassSubjectModel::where('class_id', '=', $class_id)->delete();
    }

    public static function studentStubject(int $perPage, int $class_id)
    {
        $results = ClassSubjectModel::select(
            'class_subject.*',
            'class.name as class_name',
            'subject.name as subject_name',
            'subject.type as subject_type',
            'subject.status as subject_status',
            'teacher.name as teacher_name',
            'teacher.last_name as teacher_last_name'
        )
            ->join('subject', 'subject.id', '=', 'class_subject.subject_id')
            ->join('class', 'class.id', '=', 'class_subject.class_id')
            ->join('class_teacher', 'class_teacher.class_id', '=', 'class.id')
            ->join('users as teacher', 'teacher.id', '=', 'class_teacher.teacher_id')
            ->where('class_subject.class_id', '=', $class_id)
            ->where('class_subject.is_delete', '=', 0)
            ->where('class_subject.status', '=', 1);

        $filters = [
            'class.name' => strtolower(Request::get('class_name')),
            'subject.name' => strtolower(Request::get('subject_name')),
            'class_subject.created_at' => strtolower(Request::get('created_at')),
            'class_subject.updated_at' => strtolower(Request::get('updated_at')),
        ];

        foreach ($filters as $column => $value) {
            if (!empty($value)) {
                $results->where($column, 'like', '%' . $value . '%');
            }
        }

        $status = Request::get('subject_status');
        if (in_array($status, ['0', '1'], true)) {
            $results->where('subject.status', $status);
        }

        $type = Request::get('subject_type');
        if (in_array($type, ['theoretical', 'practical'], true)) {
            $results->where('subject.type', $type);
        }


        return $results->orderBy('class_subject.id', 'desc')
            ->groupBy('class_subject.id')
            ->paginate($perPage);
    }

    public static function getSubject(int $class_id)
    {
        return ClassSubjectModel::select(
            'class_subject.*',
            'class.name as class_name',
            'subject.id as subject_id',
            'subject.name as subject_name',
            'subject.type as subject_type',
            'subject.status as subject_status',
        )
            ->join('subject', 'subject.id', '=', 'class_subject.subject_id')
            ->join('class', 'class.id', '=', 'class_subject.class_id')
            ->where('class_subject.class_id', '=', $class_id)
            ->where('class_subject.is_delete', '=', 0)
            ->where('class_subject.status', '=', 1)
            ->orderBy('class_subject.id', 'desc')
            ->get();
    }

    public static function getTotalClassAndSubject()
    {
        return ClassSubjectModel::select('class_subject.*')
            ->where('class_subject.is_delete', 0)
            ->count();
    }

    public static function getTotalStudentSubject()
    {
        return ClassSubjectModel::select('class_subject.subject_id')
            ->join('subject', 'subject.id', '=', 'class_subject.subject_id')
            ->join('class', 'class.id', '=', 'class_subject.class_id')
            ->where('class_subject.class_id', '=', Auth::user()->class_id)
            ->where('class_subject.is_delete', '=', 0)
            ->where('class.is_delete', '=', 0)
            ->distinct('class_subject.subject_id')
            ->count('class_subject.subject_id');
    }

}
