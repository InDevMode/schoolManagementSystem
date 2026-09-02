<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class HomeworkModel extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'homework';

    protected $fillable = [
        'work_id',
        'student_id',
        'description',
        'document_file',
    ];

    protected $hidden = [
        'is_delete'
    ];

    public static function getSingle(string $id)
    {
        return HomeworkModel::find($id);
    }

    public static function getHomeworkStudent($student_id, $perpage)
    {
        $results = HomeworkModel::select('homework.*', 'class.name as class_name', 'subject.name as subject_name', 'homework.description as homework_description', 'homework.document_file as homework_document_file')
            ->join('works', 'works.id', '=', 'homework.work_id')
            ->join('class', 'class.id', '=', 'works.class_id')
            ->join('subject', 'subject.id', '=', 'works.subject_id')
            ->where('homework.student_id', '=', $student_id)
            ->where('homework.is_delete', '=', 0);


        $filters = [
            'class.name' => strtolower(Request::get('class_name')),
            'subject.name' => strtolower(Request::get('subject_name')),
            'works.work_date' => strtolower(Request::get('work_date')),
            'works.submission_date' => strtolower(Request::get('submission_date')),
            'homework.description' => strtolower(Request::get('description')),
            'homework.created_at' => strtolower(Request::get('created_at')),
            'homework.updated_at' => strtolower(Request::get('updated_at')),
        ];

        foreach ($filters as $column => $value) {
            if (!empty($value)) {
                $results->where($column, 'like', '%' . $value . '%');
            }
        }

        return $results->orderBy('homework.id', 'desc')
            ->paginate($perpage);
    }

    public function getDocument(): string
    {
        return \App\Services\UploadService::url($this->document_file, '');
    }

    public function getHomework()
    {
        return $this->belongsTo(WorkModel::class, 'work_id', );
    }

    public static function getHomeworks(string $work_id, int $perpage)
    {
        $results = HomeworkModel::select(
            'homework.*',
            'users.name as student_name',
            'users.last_name as student_last_name',
            'homework.description as homework_description',
            'homework.document_file as homework_document_file',
            'homework.status as homework_status',
        )
            ->join('works', 'works.id', '=', 'homework.work_id')
            ->join('users', 'users.id', '=', 'homework.student_id')
            ->where('homework.work_id', '=', $work_id)
            ->where('homework.is_delete', '=', 0);


        $filters = [
            'users.name' => strtolower(Request::get('student_name')),
            'users.last_name' => strtolower(Request::get('student_last_name')),
            'homework.description' => strtolower(Request::get('description')),
        ];


        if (!empty(Request::get('submission_date_from'))) {
            $results->whereDate('submission_date', '>=', Request::get('submission_date_from'));
        }

        if (!empty(Request::get('submission_date_to'))) {
            $results->whereDate('submission_date', '<=', Request::get('submission_date_to'));
        }

        foreach ($filters as $column => $value) {
            if (!empty($value)) {
                $results->where($column, 'like', '%' . $value . '%');
            }
        }

        return $results->orderBy('homework.id', 'desc')
            ->paginate($perpage);
    }

    public function getStudent()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public static function getAllHomeworks(int $perpage)
    {
        $results = HomeworkModel::select(
            'homework.*',
            'student.name as student_name',
            'student.last_name as student_last_name',
            'created_by_user.name as created_by_name',
            'class.name as class_name',
            'subject.name as subject_name',
            'works.work_date as work_date',
            'works.submission_date as submission_date',
            'homework.description as homework_description',
            'homework.document_file as homework_document_file',
            'homework.status as homework_status'
        )
            ->join('users as student', 'student.id', '=', 'homework.student_id')
            ->join('works', 'works.id', '=', 'homework.work_id')
            ->join('users as created_by_user', 'created_by_user.id', '=', 'works.created_by')
            ->join('class', 'class.id', '=', 'works.class_id')
            ->join('subject', 'subject.id', '=', 'works.subject_id')
            ->where('homework.is_delete', '=', 0);

        $filters = [
            'users.name' => strtolower(Request::get('student_name')),
            'users.last_name' => strtolower(Request::get('student_last_name')),
            'homework.description' => strtolower(Request::get('description')),
            'class.name' => strtolower(Request::get('class_name')),
            'subject.name' => strtolower(Request::get('subject_name')),
            'works.work_date' => strtolower(Request::get('work_date')),
        ];


        if (!empty(Request::get('submission_date_from'))) {
            $results->whereDate('submission_date', '>=', Request::get('submission_date_from'));
        }

        if (!empty(Request::get('submission_date_to'))) {
            $results->whereDate('submission_date', '<=', Request::get('submission_date_to'));
        }

        foreach ($filters as $column => $value) {
            if (!empty($value)) {
                $results->where($column, 'like', '%' . $value . '%');
            }
        }


        return $results->orderBy('homework.id', 'desc')
            ->paginate($perpage);
    }

    public static function getTotalHomework()
    {
        return HomeworkModel::where('homework.is_delete', '=', 0)->count();
    }

    public static function getTotalHomeworkStudent()
    {
        return HomeworkModel::join('works', 'works.id', '=', 'homework.work_id')
            ->join('class', 'class.id', '=', 'works.class_id')
            ->join('subject', 'subject.id', '=', 'works.subject_id')
            ->where('homework.student_id', '=', Auth::user()->id)
            ->where('homework.is_delete', '=', 0)
            ->count();
    }

    public static function getTotalHomeworkParentStudent($student_ids)
    {
        return HomeworkModel::join('works', 'works.id', '=', 'homework.work_id')
            ->join('class', 'class.id', '=', 'works.class_id')
            ->join('subject', 'subject.id', '=', 'works.subject_id')
            ->whereIn('homework.student_id', $student_ids)
            ->where('homework.is_delete', '=', 0)
            ->count();
    }

}
