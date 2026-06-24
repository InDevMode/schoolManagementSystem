<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ajoute paypal_client_id et paypal_secret (API REST v2) sur settings et schools.
 * L'ancienne colonne paypal_email (API NVP dépréciée) est conservée pour compatibilité.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('paypal_client_id')->nullable()->after('paypal_email');
            $table->string('paypal_secret')->nullable()->after('paypal_client_id');
            $table->string('paypal_mode')->default('sandbox')->after('paypal_secret')
                  ->comment('sandbox ou live');
        });

        Schema::table('schools', function (Blueprint $table) {
            $table->string('paypal_client_id')->nullable()->after('paypal_email');
            $table->string('paypal_secret')->nullable()->after('paypal_client_id');
            $table->string('paypal_mode')->default('sandbox')->after('paypal_secret')
                  ->comment('sandbox ou live');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['paypal_client_id', 'paypal_secret', 'paypal_mode']);
        });

        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn(['paypal_client_id', 'paypal_secret', 'paypal_mode']);
        });
    }
};
