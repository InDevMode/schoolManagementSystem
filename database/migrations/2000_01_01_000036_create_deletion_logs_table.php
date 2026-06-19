<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table deletion_logs — journal d'audit des suppressions (soft ou définitives).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deletion_logs', function (Blueprint $table) {
            $table->id();
            $table->string('table_name')->comment('Nom de la table concernée');
            $table->unsignedBigInteger('record_id')->comment('ID de l\'enregistrement supprimé');
            $table->json('record_data')->nullable()->comment('Snapshot des données au moment de la suppression');
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->string('reason')->nullable();
            $table->timestamp('deleted_at');

            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');

            $table->index(['table_name', 'record_id']);
            $table->index('deleted_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deletion_logs');
    }
};
