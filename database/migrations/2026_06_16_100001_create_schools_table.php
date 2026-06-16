<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Crée la table `schools` — chaque école est une entité multi-tenant.
 * La table `settings` reste pour la config globale du super admin.
 * Chaque école a ses propres settings (clés de paiement, logo, etc.).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->id();

            // Informations générales
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

            // Session / période académique
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

        // Migrer les données de settings (id=1) vers la première école si settings existe
        if (Schema::hasTable('settings')) {
            $setting = DB::table('settings')->where('id', 1)->first();
            if ($setting) {
                DB::table('schools')->insert([
                    'school_name'        => $setting->school_name ?? 'Mon École',
                    'school_type'        => $setting->school_type ?? null,
                    'school_code'        => \Illuminate\Support\Str::slug($setting->school_name ?? 'mon-ecole') . '-' . time(),
                    'address'            => $setting->address ?? null,
                    'phone'              => $setting->phone ?? null,
                    'email'              => $setting->email ?? null,
                    'uai_number'         => $setting->uai_number ?? null,
                    'logo'               => $setting->logo ?? null,
                    'favicon'            => $setting->favicon ?? null,
                    'paypal_email'       => $setting->paypal_email ?? null,
                    'kkiapay_public_key'  => $setting->kkiapay_public_key ?? null,
                    'kkiapay_private_key' => $setting->kkiapay_private_key ?? null,
                    'kkiapay_secret_key'  => $setting->kkiapay_secret_key ?? null,
                    'stripe_public_key'  => $setting->stripe_public_key ?? null,
                    'stripe_secret_key'  => $setting->stripe_secret_key ?? null,
                    'fedapay_public_key' => $setting->fedapay_public_key ?? null,
                    'fedapay_secret_key' => $setting->fedapay_secret_key ?? null,
                    'status'             => 1,
                    'is_delete'          => 0,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
