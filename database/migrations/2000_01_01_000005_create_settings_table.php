<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table settings — paramètres globaux (super admin).
 * Conserve un id bigInt auto-increment (singleton id=1 référencé dans le code).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            // id BIGINT auto-increment intentionnel : le code référence settings.id = 1
            $table->bigIncrements('id');
            $table->string('school_name')->nullable();
            $table->string('school_type')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('uai_number')->nullable();

            // ── Passerelles de paiement ────────────────────────────────────
            $table->string('paypal_email')->nullable();
            $table->string('paypal_client_id')->nullable();
            $table->string('paypal_secret')->nullable();
            $table->string('paypal_mode')->default('sandbox')->comment('sandbox ou live');
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

            // ── Auth background ────────────────────────────────────────────
            $table->string('auth_bg_type')->default('gradient')
                  ->comment('Type de fond: gradient, image, video, particles');
            $table->text('auth_bg_value')->nullable()
                  ->comment('Valeur CSS du gradient ou URL image/vidéo');
            $table->string('auth_bg_label')->nullable()
                  ->comment('Étiquette descriptive ex: Noël 2025');
            $table->string('auth_bg_overlay')->nullable()
                  ->comment('Couleur overlay rgba pour les images/vidéos');

            $table->smallInteger('status')->default(0)->comment('0: Inactive, 1: Active');
            $table->smallInteger('is_delete')->default(0)->comment('0: actif, 1: supprimé');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
