<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('fedapay_public_key')->nullable()->after('stripe_secret_key');
            $table->string('fedapay_secret_key')->nullable()->after('fedapay_public_key');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['fedapay_public_key', 'fedapay_secret_key']);
        });
    }
};
