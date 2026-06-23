<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration consolidée — correctifs multi-tenant.
 *
 * Ajoute school_id sur les tables qui en manquaient :
 *   - communicates  (noticeboard)
 *   - periods       (sessions académiques)
 *
 * Modifie staff_events pour supporter les types d'événements personnalisés
 * par école (remplace l'enum fixe par une string + table event_type_customs).
 *
 * NOTE : leave_types.school_id existait déjà dans la migration originale.
 *        On vérifie juste qu'il est présent avant de l'ajouter.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── communicates : ajouter school_id ───────────────────────────────
        if (Schema::hasTable('communicates') && ! Schema::hasColumn('communicates', 'school_id')) {
            Schema::table('communicates', function (Blueprint $table) {
                $table->unsignedBigInteger('school_id')
                      ->nullable()
                      ->after('id')
                      ->comment('Multi-tenant — FK vers schools.id');
                $table->index('school_id');
            });
        }

        // ── periods : ajouter school_id ────────────────────────────────────
        if (Schema::hasTable('periods') && ! Schema::hasColumn('periods', 'school_id')) {
            Schema::table('periods', function (Blueprint $table) {
                $table->unsignedBigInteger('school_id')
                      ->nullable()
                      ->after('id')
                      ->comment('Multi-tenant — FK vers schools.id (remplace settings_id à terme)');
                $table->index('school_id');
            });
        }

        // ── staff_events : remplacer l'enum fixe par une string ────────────
        // Le changement d'enum n'est pas réversible proprement sur tous les SGBD.
        // On ajoute une colonne event_type_custom (string) pour les types libres
        // et on garde l'enum pour la rétrocompatibilité.
        if (Schema::hasTable('staff_events') && ! Schema::hasColumn('staff_events', 'custom_event_type_id')) {
            Schema::table('staff_events', function (Blueprint $table) {
                $table->unsignedBigInteger('custom_event_type_id')
                      ->nullable()
                      ->after('event_type')
                      ->comment('FK vers event_type_customs.id — null = type prédéfini');
            });
        }

        // ── event_type_customs : types d'événements personnalisés par école ─
        if (! Schema::hasTable('event_type_customs')) {
            Schema::create('event_type_customs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('school_id')
                      ->comment('Multi-tenant — chaque école gère ses propres types');
                $table->string('name');
                $table->string('color', 7)->default('#6366f1')
                      ->comment('Couleur hex pour le calendrier');
                $table->string('description')->nullable();
                $table->tinyInteger('is_delete')->default(0)->comment('0: actif, 1: supprimé');
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');

                $table->index(['school_id', 'is_delete']);
            });
        }

        // ── leave_types : school_id (déjà dans la migration originale,
        //    mais on s'assure qu'il existe bien) ───────────────────────────
        if (Schema::hasTable('leave_types') && ! Schema::hasColumn('leave_types', 'school_id')) {
            Schema::table('leave_types', function (Blueprint $table) {
                $table->unsignedBigInteger('school_id')
                      ->nullable()
                      ->after('id')
                      ->comment('Multi-tenant');
                $table->index('school_id');
            });
        }
    }

    public function down(): void
    {
        // event_type_customs
        Schema::dropIfExists('event_type_customs');

        // Retirer school_id de communicates
        if (Schema::hasTable('communicates') && Schema::hasColumn('communicates', 'school_id')) {
            Schema::table('communicates', function (Blueprint $table) {
                $table->dropIndex(['school_id']);
                $table->dropColumn('school_id');
            });
        }

        // Retirer school_id de periods
        if (Schema::hasTable('periods') && Schema::hasColumn('periods', 'school_id')) {
            Schema::table('periods', function (Blueprint $table) {
                $table->dropIndex(['school_id']);
                $table->dropColumn('school_id');
            });
        }

        // Retirer custom_event_type_id de staff_events
        if (Schema::hasTable('staff_events') && Schema::hasColumn('staff_events', 'custom_event_type_id')) {
            Schema::table('staff_events', function (Blueprint $table) {
                $table->dropColumn('custom_event_type_id');
            });
        }
    }
};
