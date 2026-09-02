<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deletion_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('table_name')->comment('Nom de la table concernée');
            // record_id est un uuid (string) car il pointe vers n'importe quelle table UUID
            $table->string('record_id', 36)->comment('UUID de l\'enregistrement supprimé');
            $table->jsonb('record_data')->nullable()->comment('Snapshot des données au moment de la suppression');
            $table->uuid('deleted_by')->nullable();
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
