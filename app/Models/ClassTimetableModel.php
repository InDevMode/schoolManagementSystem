<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassTimetableModel extends Model
{
    use HasFactory;


    static public function getClassTimetable(int $perPage)
    {
        return $perPage;
    }

}
