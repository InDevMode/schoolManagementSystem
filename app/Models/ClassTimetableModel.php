<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassTimetableModel extends Model
{
    use HasFactory;

    protected $table = 'class_timetable';

    protected $fillable = [
        'start_time',
        'end_time',
        'room_number',
    ];

    protected $hidden = [
        'class_id',
        'subject_id',
        'week_id',
    ];

    static public function getClassTimetable(int $class_id, int $subject_id, int $week_id)
    {
        return ClassTimetableModel::where('class_id', '=', $class_id)
            ->where('subject_id', '=', $subject_id)
            ->where('week_id', '=', $week_id)
            ->first();
    }

    static public function getSubject(int $class_id)
    {
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
            ->groupBy('class_subject.id')
            ->get();

    }
}
