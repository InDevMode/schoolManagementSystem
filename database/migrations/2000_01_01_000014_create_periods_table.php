<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periods', function (Blueprint $table) {
            $table->uuid('id')->primary();
            // settings_id conservé pour rétrocompatibilité (bigInt car settings.id = bigIncrements)
            $table->unsignedBigInteger('settings_id')->nullable()
                  ->comment('FK → settings.id (rétrocompat)');
            $table->uuid('school_id')->nullable()
                  ->comment('Multi-tenant — FK vers schools.id');
            $table->string('name')->nullable();
            $table->string('type')->default('trimestre')
                  ->comment('Type de période scolaire : trimestre, semestre');
            $table->smallInteger('order_number')->default(1)
                  ->comment('Numéro d\'ordre dans l\'année (1, 2, 3...)');
            $table->string('school_year', 9)->nullable()->comment('Ex: 2025-2026');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_current')->default(false);
            $table->smallInteger('status')->default(0)->comment('0: Inactive, 1: Active');
            $table->smallInteger('is_delete')->default(0)->comment('0: actif, 1: supprimé');
            $table->uuid('created_by')->nullable();
            $table->timestamps();

            $table->foreign('settings_id')->references('id')->on('settings')->onDelete('set null');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periods');
    }
};
