<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class DeletionLogModel extends Model
{
    protected $table = 'deletion_logs';

    public $timestamps = false;

    protected $fillable = [
        'table_name', 'record_id', 'record_data', 'deleted_by', 'reason', 'deleted_at',
    ];

    protected $casts = [
        'record_data' => 'array',
        'deleted_at'  => 'datetime',
    ];

    /**
     * Enregistre une suppression — appeler AVANT de mettre is_delete=1
     */
    public static function log(string $table, int $recordId, array $recordData, ?string $reason = null): void
    {
        self::create([
            'table_name'  => $table,
            'record_id'   => $recordId,
            'record_data' => $recordData,
            'deleted_by'  => Auth::id(),
            'reason'      => $reason,
            'deleted_at'  => now(),
        ]);
    }

    /**
     * Liste paginée — pour le super admin uniquement
     */
    public static function getAll(int $perPage)
    {
        $q = self::select('deletion_logs.*', 'users.name as deleter_name', 'users.last_name as deleter_last_name')
            ->leftJoin('users', 'users.id', '=', 'deletion_logs.deleted_by');

        if ($v = Request::get('table_name')) $q->where('deletion_logs.table_name', $v);
        if ($v = Request::get('deleted_by')) $q->where('deletion_logs.deleted_by', $v);
        if ($v = Request::get('date_from'))  $q->whereDate('deletion_logs.deleted_at', '>=', $v);
        if ($v = Request::get('date_to'))    $q->whereDate('deletion_logs.deleted_at', '<=', $v);
        if ($v = Request::get('search'))     $q->where('deletion_logs.reason', 'like', "%$v%");

        return $q->orderBy('deletion_logs.deleted_at', 'desc')->paginate($perPage);
    }

    public static function getTableNames()
    {
        return self::select('table_name')
            ->distinct()
            ->orderBy('table_name')
            ->pluck('table_name');
    }
}
