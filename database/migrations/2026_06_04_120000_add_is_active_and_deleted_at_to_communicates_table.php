<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('communicates', function (Blueprint $table) {
            $table->tinyInteger('is_active')->default(1)->after('is_delete')
                  ->comment('1: active (notification visible), 0: inactive');
            $table->timestamp('deleted_at')->nullable()->after('is_active')
                  ->comment('Soft-delete timestamp for history');
            $table->string('deleted_reason', 255)->nullable()->after('deleted_at')
                  ->comment('Optional reason when admin deletes');
        });
    }

    public function down(): void
    {
        Schema::table('communicates', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'deleted_at', 'deleted_reason']);
        });
    }
};
