<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleModel extends Model
{
    use HasFactory;

    protected $table = 'schedules';

    protected $fillable = [
        'exam_date',
        'start_time',
        'end_time',
        'room_number',
        'full_marks',
        'passing_marks',
        'created_by',
    ];

    protected $hidden = [
        'exam_id',
        'class_id',
        'subject_id',
        'is_delete'
    ];

    static public function getExamSchedule(int $exam_id, int $class_id, int $subject_id)
    {
        return ScheduleModel::where('exam_id', '=', $exam_id)
            ->where('class_id', '=', $class_id)
            ->where('subject_id', '=', $subject_id)
            ->first();
    }

    static public function deleteExamSchedule(int $exam_id, int $class_id)
    {
        return ScheduleModel::where('exam_id', '=', $exam_id)
            ->where('class_id', '=', $class_id)
            ->delete();
    }

}
