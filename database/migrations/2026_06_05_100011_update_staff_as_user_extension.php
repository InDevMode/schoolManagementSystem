<?php

use Illuminate\Database\Migrations\Migration;

/**
 * Cette migration a été remplacée par 2026_06_05_100015_fix_fk_types_new_tables.php
 * qui crée correctement toutes les tables avec unsignedBigInteger pour les FK.
 * Ici on ne fait rien pour éviter les conflits.
 */
return new class extends Migration {
    public function up(): void
    {
        // Remplacé par migration 100015 — ne rien faire
    }

    public function down(): void
    {
        // Rien à annuler
    }
};
