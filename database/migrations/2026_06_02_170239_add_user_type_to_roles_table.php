<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            // user_type lié à ce rôle (null = rôle système sans user_type dédié)
            // Ex: 0 = super_admin, 1 = admin, 2 = teacher, 3 = student, 4 = parent
            //     5+ = rôles custom (comptable, délégué, etc.)
            $table->unsignedSmallInteger('user_type')->nullable()->after('guard_name');

            // Description optionnelle du rôle
            $table->string('description')->nullable()->after('user_type');
        });

        // Initialiser les user_type des rôles système existants
        DB::table('roles')->where('name', 'super_admin')->update(['user_type' => 0]);
        DB::table('roles')->where('name', 'admin')->update(['user_type' => 1]);
        DB::table('roles')->where('name', 'teacher')->update(['user_type' => 2]);
        DB::table('roles')->where('name', 'student')->update(['user_type' => 3]);
        DB::table('roles')->where('name', 'parent')->update(['user_type' => 4]);
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn(['user_type', 'description']);
        });
    }
};
