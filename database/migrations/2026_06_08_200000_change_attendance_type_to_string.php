<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Changer d'abord le type de colonne de tinyint → varchar(20)
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('attendance_type', 20)->nullable()->change();
        });

        // 2. Puis migrer les anciennes valeurs numériques stockées en string vers les valeurs texte
        $map = [
            '0' => 'present',
            '1' => 'present',
            '2' => 'late',
            '3' => 'absent',
            '4' => 'half_day',
        ];

        foreach ($map as $old => $new) {
            DB::table('attendances')
                ->where('attendance_type', $old)
                ->update(['attendance_type' => $new]);
        }
    }

    public function down(): void
    {
        // Reconvertir les valeurs texte vers les entiers
        Schema::table('attendances', function (Blueprint $table) {
            $table->tinyInteger('attendance_type')->nullable()->change();
        });

        $reverseMap = [
            'present'  => 1,
            'late'     => 2,
            'absent'   => 3,
            'half_day' => 4,
        ];

        foreach ($reverseMap as $str => $int) {
            DB::table('attendances')
                ->where('attendance_type', $str)
                ->update(['attendance_type' => $int]);
        }
    }
};
