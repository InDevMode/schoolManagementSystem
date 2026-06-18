<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ajoute school_id à la table class pour le support multi-tenant.
 * Chaque classe appartient désormais à une école spécifique.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('class', function (Blueprint $table) {
            if (!Schema::hasColumn('class', 'school_id')) {
                $table->unsignedBigInteger('school_id')
                      ->nullable()
                      ->after('id')
                      ->comment('Référence à l\'école (multi-tenant)');

                $table->index('school_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('class', function (Blueprint $table) {
            if (Schema::hasColumn('class', 'school_id')) {
                $table->dropIndex(['school_id']);
                $table->dropColumn('school_id');
            }
        });
    }
};
