<?php

namespace App\Models;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        return ClassModel::select('class.*', 'users.name as created_by_name')
            ->join('users', 'users.id', '=', 'class.created_by')
            ->where('class.is_delete', '=', 0)
            ->orderBy('class.id', 'desc')
            ->paginate($perPage);
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

    /**
     * @return BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
