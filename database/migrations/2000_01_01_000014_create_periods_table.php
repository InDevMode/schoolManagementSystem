<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table periods — version consolidée.
 * Fusionne : create_periods + add_created_by + add_type_order_school_year
 *
 * Note : settings_id est conservé pour rétrocompatibilité avec PeriodModel et EvaluationSeeder.
 * Lors d'une migration future vers multi-tenant complet, remplacer par school_id.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periods', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('settings_id')->nullable()
                  ->comment('FK → settings.id (rétrocompat) — utiliser school_id à terme');
            $table->string('name')->nullable();
            $table->enum('type', ['semestre', 'trimestre'])->default('trimestre')
                  ->comment('Type de période scolaire');
            $table->unsignedTinyInteger('order_number')->default(1)
                  ->comment('Numéro d\'ordre dans l\'année (1, 2, 3...)');
            $table->string('school_year', 9)->nullable()->comment('Ex: 2025-2026');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_current')->default(false);
            $table->tinyInteger('status')->default(0)->comment('0: Inactive, 1: Active');
            $table->tinyInteger('is_delete')->default(0)->comment('0: actif, 1: supprimé');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('settings_id')->references('id')->on('settings')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periods');
    }
};
