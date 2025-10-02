<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;

class SubjectModel extends Model
{
    use HasFactory;

    protected $table = 'subject';

    protected $fillable = [
        'name',
        'type',
        'created_by',
        'status',
    ];

    protected $hidden = [
        'is_delete',
    ];

    public static function getSingle(int $id): ?SubjectModel
    {
        return SubjectModel::find($id);
    }

    public static function getAllSubject(int $perPage): LengthAwarePaginator
    {
        $results = SubjectModel::select('subject.*', 'users.name as created_by_name')
            ->join('users', 'users.id', '=', 'subject.created_by');

        $filters = [
            'subject.name' => strtolower(Request::get('name')),
            'subject.type' => strtolower(Request::get('type')),
            'subject.created_at' => strtolower(Request::get('created_at')),
            'subject.updated_at' => strtolower(Request::get('updated_at')),
        ];

        foreach ($filters as $column => $value) {
            if (!empty($value)) {
                $results->where($column, 'like', '%' . $value . '%');
            }
        }

        $status = Request::get('status');
        if (in_array($status, ['0', '1'], true)) {
            $results->where('subject.status', $status);
        }

        return $results->where('subject.is_delete', 0)
            ->orderBy('subject.id', 'desc')
            ->groupBy('subject.id')
            ->paginate($perPage);
    }

    public static function getSubject()
    {
        return SubjectModel::select('subject.*')
            ->join('users', 'users.id', '=', 'subject.created_by')
            ->where('subject.is_delete', '=', 0)
            ->orderBy('subject.id', 'desc')
            ->get();
    }

    public static function getNameSingle(string $name): ?SubjectModel
    {
        return SubjectModel::where('name', '=', $name)
            ->where('is_delete', '=', 0)
            ->first();
    }

    public static function checkNameSingle(string $name, int $id): ?SubjectModel
    {
        return SubjectModel::where('name', '=',$name)
            ->where('id', '!=', $id)
            ->where('is_delete', '=',0)
            ->first();
    }

    public static function getTotalSubject()
    {
        return SubjectModel::where('is_delete', 0)->count();
    }

}
