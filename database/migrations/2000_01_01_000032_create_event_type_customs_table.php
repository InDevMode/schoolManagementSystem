<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table event_type_customs — types d'événements personnalisés par école.
 * Extraite de la migration patch 000037 (fusionnée ici).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_type_customs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('school_id')
                  ->comment('Multi-tenant — chaque école gère ses propres types');
            $table->string('name');
            $table->string('color', 7)->default('#6366f1')
                  ->comment('Couleur hex pour le calendrier');
            $table->string('description')->nullable();
            $table->smallInteger('is_delete')->default(0)->comment('0: actif, 1: supprimé');
            $table->uuid('created_by')->nullable();
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');

            $table->index(['school_id', 'is_delete']);
        });

        // Ajouter la FK sur staff_events maintenant que event_type_customs existe
        Schema::table('staff_events', function (Blueprint $table) {
            $table->foreign('custom_event_type_id')
                  ->references('id')->on('event_type_customs')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('staff_events', function (Blueprint $table) {
            $table->dropForeign(['custom_event_type_id']);
        });
        Schema::dropIfExists('event_type_customs');
    }
};
