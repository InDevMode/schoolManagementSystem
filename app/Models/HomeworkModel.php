<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

}
