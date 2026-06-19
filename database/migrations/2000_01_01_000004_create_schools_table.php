<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table schools — entité multi-tenant.
 * Chaque école a ses propres paramètres, clés de paiement, logo, etc.
 * Remplace l'ancienne table settings (settings est conservée pour rétrocompatibilité).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('school_name');
            $table->string('school_type')->nullable()->comment('Ex: Lycée, Collège, Primaire...');
            $table->string('school_code')->unique()->comment('Code unique identifiant l\'école (slug)');
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('uai_number')->nullable()->comment('Numéro UAI / identifiant officiel');
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();

            // Clés de paiement
            $table->string('paypal_email')->nullable();
            $table->string('kkiapay_public_key')->nullable();
            $table->string('kkiapay_private_key')->nullable();
            $table->string('kkiapay_secret_key')->nullable();
            $table->string('stripe_public_key')->nullable();
            $table->string('stripe_secret_key')->nullable();
            $table->string('fedapay_public_key')->nullable();
            $table->string('fedapay_secret_key')->nullable();

            // Session académique
            $table->string('academic_year')->nullable()->comment('Ex: 2025-2026');
            $table->string('period_type')->nullable()->comment('trimestre, semestre...');

            // Statut
            $table->tinyInteger('status')->default(1)->comment('0: inactif, 1: actif');
            $table->tinyInteger('is_delete')->default(0)->comment('0: actif, 1: soft delete');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index('school_code');
            $table->index(['status', 'is_delete']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
