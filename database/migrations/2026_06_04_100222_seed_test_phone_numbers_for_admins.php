<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ajoute des numéros de téléphone de test aux utilisateurs admin (user_type = 1)
     * qui n'en ont pas encore.
     */
    public function up(): void
    {
        $phones = [
            '+229 97 12 34 56',
            '+229 96 23 45 67',
            '+229 95 34 56 78',
            '+229 64 45 67 89',
            '+229 67 56 78 90',
            '+229 62 67 89 01',
            '+229 97 78 90 12',
            '+229 96 89 01 23',
            '+229 95 90 12 34',
            '+229 64 01 23 45',
        ];

        $admins = DB::table('users')
            ->where('user_type', 1)
            ->where('is_delete', 0)
            ->whereNull('mobile_number')
            ->orWhere(function ($q) {
                $q->where('user_type', 1)
                  ->where('is_delete', 0)
                  ->where('mobile_number', '');
            })
            ->get();

        foreach ($admins as $index => $admin) {
            DB::table('users')
                ->where('id', $admin->id)
                ->update(['mobile_number' => $phones[$index % count($phones)]]);
        }
    }

    public function down(): void
    {
        // Ne supprime pas les numéros réels — ne fait rien au rollback
    }
};
