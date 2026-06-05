<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveTypeModel extends Model
{
    use HasFactory;

    protected $table = 'leave_types';

    protected $fillable = ['name', 'description', 'color'];

    protected $hidden = ['is_delete'];

    public static function getSingle(int $id): ?self
    {
        return self::find($id);
    }

    public static function getAll()
    {
        return self::where('is_delete', 0)->orderBy('name')->get();
    }

    public static function getAllPaginated(int $perPage)
    {
        return self::where('is_delete', 0)->orderBy('name')->paginate($perPage);
    }

    public static function getNameSingle(string $name): ?self
    {
        return self::where('name', $name)->where('is_delete', 0)->first();
    }

    public static function checkNameSingle(string $name, int $id): ?self
    {
        return self::where('name', $name)->where('id', '!=', $id)->where('is_delete', 0)->first();
    }
}
