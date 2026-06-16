<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Ajoute school_id sur la table users.
 * - super_admin (user_type=0) : school_id = null (global)
 * - Tous les autres : school_id = id de l'école à laquelle ils appartiennent
 *
 * Migration douce : les utilisateurs existants sont rattachés à l'école id=1 si elle existe.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('school_id')
                  ->nullable()
                  ->after('created_by')
                  ->comment('FK → schools.id — null pour le super admin');

            $table->index('school_id');
        });

        // Rattacher les utilisateurs existants (non super_admin) à l'école id=1
        $firstSchool = DB::table('schools')->orderBy('id')->first();
        if ($firstSchool) {
            DB::table('users')
                ->where('user_type', '!=', 0)
                ->whereNull('school_id')
                ->update(['school_id' => $firstSchool->id]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['school_id']);
            $table->dropColumn('school_id');
        });
    }
};
