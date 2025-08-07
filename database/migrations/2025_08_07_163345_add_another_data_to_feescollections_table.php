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
        Schema::table('feescollections', function (Blueprint $table) {
            $table->text('payment_data')->nullable()->after('remark');
            $table->string('payment_status')->default('Pending')->after('payment_data');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feescollections', function (Blueprint $table) {
            $table->dropColumn([
                'payment_data',
                'payment_status',
            ]);
        });
    }
};
