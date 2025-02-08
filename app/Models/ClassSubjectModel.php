<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
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

    static public function getSingle(int $id)
    {
        return ClassSubjectModel::find($id);
    }

    static public function getAllClassSubject(int $perPage): LengthAwarePaginator
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

    static public function getAlreadyExist($class_id, $subject_id)
    {
        return ClassSubjectModel::where('class_id', '=', $class_id)->where('subject_id', '=', $subject_id)->first();
    }

    static public function getAssignSubject($class_id)
    {
        return ClassSubjectModel::where('class_id', '=', $class_id)->where('is_delete', 0)->get();
    }

    static public function deleteSubjectAssign($class_id)
    {
        return ClassSubjectModel::where('class_id', '=', $class_id)->delete();
    }

    static public function studentStubject(int $perPage, int $class_id)
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
            ->paginate($perPage);
    }


}
