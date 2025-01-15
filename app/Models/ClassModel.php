<?php

namespace App\Models;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Request;

class ClassModel extends Model
{
    use HasFactory;

    protected $table = 'class';

    protected $fillable = [
        'name',
        'status',
        'created_by',
    ];

    protected $hidden = [
        'is_delete',
    ];

    /**
     * @param int $id
     * @return ClassModel|null
     */
    public static function getSingle(int $id): ?ClassModel
    {
        return ClassModel::find($id);
    }

    /**
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public static function getAllClass(int $perPage): LengthAwarePaginator
    {
        $results = ClassModel::select('class.*', 'users.name as created_by_name')
            ->join('users', 'users.id', '=', 'class.created_by');

        $filters = [
            'class.name' => strtolower(Request::get('name')),
            'users.name' => strtolower(Request::get('created_by')), //TODO A REVOIR ULTERIEUREMENT
            'class.created_at' => strtolower(Request::get('created_at')),
            'class.updated_at' => strtolower(Request::get('updated_at')),
        ];

        foreach ($filters as $column => $value) {
            if (!empty($value)) {
                $results->where($column, 'like', '%' . $value . '%');
            }
        }

        $status = Request::get('status');
        if (in_array($status, ['0', '1'], true)) {
            $results->where('class.status', $status);
        }

        return $results->where('class.is_delete', 0)
            ->orderBy('class.id', 'desc')
            ->paginate($perPage);
    }

    static public function getClass()
    {
        return ClassModel::select('class.*')
            ->join('users', 'users.id', '=', 'class.created_by')
            ->where('class.is_delete', 0)
            ->where('class.status', 0)
            ->orderBy('class.name', 'asc')
            ->get();
    }

    /**
     * @param string $name
     * @return ClassModel|null
     */
    public static function getNameSingle(string $name): ?ClassModel
    {
        return ClassModel::where('name', $name)->first();
    }

    /**
     * @param string $name
     * @param int $id
     * @return ClassModel|null
     */
    public static function checkNameSingle(string $name, int $id): ?ClassModel
    {
        return ClassModel::where('name', $name)
            ->where('id', '!=', $id)
            ->first();
    }

}
