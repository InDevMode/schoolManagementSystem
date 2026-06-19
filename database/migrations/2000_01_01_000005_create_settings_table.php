<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table settings — conservée pour rétrocompatibilité avec le code existant (SettingModel, PeriodModel).
 * La table schools est désormais la source de vérité principale.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('school_name')->nullable();
            $table->string('school_type')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('uai_number')->nullable();
            $table->string('paypal_email')->nullable();
            $table->string('kkiapay_public_key')->nullable();
            $table->string('kkiapay_private_key')->nullable();
            $table->string('kkiapay_secret_key')->nullable();
            $table->string('stripe_public_key')->nullable();
            $table->string('stripe_secret_key')->nullable();
            $table->string('fedapay_public_key')->nullable();
            $table->string('fedapay_secret_key')->nullable();
            $table->string('favicon')->nullable();
            $table->string('logo')->nullable();
            $table->string('period_type')->nullable()->comment('trimestre, semestre...');
            $table->tinyInteger('status')->default(0)->comment('0: Inactive, 1: Active');
            $table->tinyInteger('is_delete')->default(0)->comment('0: actif, 1: supprimé');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
