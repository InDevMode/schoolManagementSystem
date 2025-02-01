<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassTimetableModel extends Model
{
    use HasFactory;

    protected $table = 'class_timetable';

    protected $fillable = [
        'name',
    ];


    static public function getClassTimetable(int $perPage)
    {
        return $perPage;
    }

    static public function getSubject(int $class_id){
        return ClassSubjectModel::select(
            'class_subject.*',
            'subject.name as subject_name',
            'subject.type as subject_type')
            ->join('subject', 'subject.id', '=', 'class_subject.subject_id')
            ->join('class', 'class.id', '=', 'class_subject.class_id')
            ->join('users', 'users.id', '=', 'class_subject.created_by')
            ->where('class_subject.class_id', '=', $class_id)
            ->where('class_subject.is_delete', '=', 0)
            ->where('class_subject.status', '=', 1)
            ->orderBy('class_subject.id', 'desc')
            ->get();

    }

}
