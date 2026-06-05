<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('periods', function (Blueprint $table) {
            // Type de période : semestre (écoles publiques) ou trimestre (écoles privées)
            $table->enum('type', ['semestre', 'trimestre'])->default('trimestre')->after('name');
            // Numéro d'ordre dans l'année (1er semestre, 2ème trimestre, etc.)
            $table->unsignedTinyInteger('order_number')->default(1)->after('type');
            // Année scolaire (ex: 2025-2026)
            $table->string('school_year', 9)->nullable()->after('order_number');
        });
    }

    public function down(): void
    {
        Schema::table('periods', function (Blueprint $table) {
            $table->dropColumn(['type', 'order_number', 'school_year']);
        });
    }
};
