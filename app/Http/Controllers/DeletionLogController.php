<?php

namespace App\Http\Controllers;

use App\Models\DeletionLogModel;
use Inertia\Inertia;

class DeletionLogController extends Controller
{
    /**
     * Liste de toutes les suppressions — super admin uniquement
     */
    public function list()
    {
        return Inertia::render('SuperAdmin/DeletionLogs/Index', [
            'logs'       => DeletionLogModel::getAll(20),
            'tableNames' => DeletionLogModel::getTableNames(),
        ]);
    }

    /**
     * Détail d'une suppression (snapshot JSON)
     */
    public function show(int $id)
    {
        $log = DeletionLogModel::with('deleter')
            ->select('deletion_logs.*', 'users.name as deleter_name', 'users.last_name as deleter_last_name')
            ->leftJoin('users', 'users.id', '=', 'deletion_logs.deleted_by')
            ->where('deletion_logs.id', $id)
            ->first();

        if (!$log) abort(404);

        return response()->json(['log' => $log]);
    }
}
