<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ajoute deleted_at, deleted_by sur la table works pour le soft-delete restaurable.
 * L'indicateur is_delete existant est conservé pour compatibilité.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('works', function (Blueprint $table) {
            $table->timestamp('deleted_at')->nullable()->after('is_delete');
            $table->unsignedInteger('deleted_by')->nullable()->after('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::table('works', function (Blueprint $table) {
            $table->dropColumn(['deleted_at', 'deleted_by']);
        });
    }
};
