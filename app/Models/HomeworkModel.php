<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class HomeworkModel extends Model
{
    use HasFactory;

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

    public function getDocument()
    {
        return (!empty($this->document_file) && file_exists('upload/practicalworks/' . $this->document_file)) ? url('upload/practicalworks/' . $this->document_file) : '';
    }

    public function getHomework()
    {
        return $this->belongsTo(WorkModel::class, 'work_id', );
    }

}
