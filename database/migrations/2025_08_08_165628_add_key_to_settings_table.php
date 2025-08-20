<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('kkiapay_private_key')->nullable()->after('kkiapay_public_key');
            $table->string('kkiapay_secret_key')->nullable()->after('kkiapay_private_key');
            $table->string('stripe_public_key')->nullable()->after('kkiapay_secret_key');
            $table->string('stripe_secret_key')->nullable()->after('stripe_public_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'kkiapay_private_key',
                'kkiapay_secret_key',
                'stripe_public_key',
                'stripe_secret_key',
            ]);
        });
    }
};
