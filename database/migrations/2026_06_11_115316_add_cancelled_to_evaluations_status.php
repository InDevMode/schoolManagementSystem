<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Modifier l'ENUM pour ajouter 'cancelled'
        DB::statement("ALTER TABLE evaluations MODIFY COLUMN status ENUM('draft','open','closed','validated','cancelled') NOT NULL DEFAULT 'open'");
    }

    public function down(): void
    {
        // Repasser à l'ancien ENUM sans 'cancelled'
        // Les lignes avec status='cancelled' seront ignorées (attention à les traiter avant)
        DB::statement("ALTER TABLE evaluations MODIFY COLUMN status ENUM('draft','open','closed','validated') NOT NULL DEFAULT 'open'");
    }
};
