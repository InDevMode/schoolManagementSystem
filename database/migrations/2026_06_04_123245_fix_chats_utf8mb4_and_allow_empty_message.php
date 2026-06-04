<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * - Convertit la table chats en utf8mb4 pour supporter les emojis
     * - Modifie la colonne message pour autoriser les chaînes vides (fichiers sans texte)
     */
    public function up(): void
    {
        // Convertir la table en utf8mb4 (support complet emojis 4 octets)
        DB::statement('ALTER TABLE chats CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');

        // S'assurer que message autorise NULL et chaîne vide (déjà nullable mais on force)
        Schema::table('chats', function (Blueprint $table) {
            $table->longText('message')->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE chats CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci');
    }
};
