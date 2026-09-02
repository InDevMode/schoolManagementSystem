<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeekModel extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'week';

    protected $fillable = [
        'name',
    ];

    public static function getAllWeek()
    {
        return WeekModel::get();
    }

    public static function getWeekUsingName(string $weekName)
    {
        return WeekModel::where('name', $weekName)->first();
    }

    public static function getTotalWeek()
    {
        return WeekModel::count();
    }

}
