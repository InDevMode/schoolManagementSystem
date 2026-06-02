<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            // kkiapay_public_key doit être ajoutée EN PREMIER car les colonnes suivantes
            // sont positionnées après elle.
            $table->string('kkiapay_public_key')->nullable()->after('paypal_email');
            $table->string('kkiapay_private_key')->nullable()->after('kkiapay_public_key');
            $table->string('kkiapay_secret_key')->nullable()->after('kkiapay_private_key');
            $table->string('stripe_public_key')->nullable()->after('kkiapay_secret_key');
            $table->string('stripe_secret_key')->nullable()->after('stripe_public_key');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'kkiapay_public_key',
                'kkiapay_private_key',
                'kkiapay_secret_key',
                'stripe_public_key',
                'stripe_secret_key',
            ]);
        });
    }
};
