<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ajoute les colonnes de personnalisation du background de la page d'authentification.
 * Configurable par le super admin pour les périodes de fêtes, etc.
 *
 * auth_bg_type  : 'gradient' | 'image' | 'video' | 'particles'
 * auth_bg_value : valeur CSS du gradient, URL image/vidéo, ou config JSON des particules
 * auth_bg_label : étiquette descriptive (ex: "Noël 2025", "Bonne Année")
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('auth_bg_type')->default('gradient')
                  ->comment('Type de fond: gradient, image, video, particles')
                  ->after('logo');
            $table->text('auth_bg_value')->nullable()
                  ->comment('Valeur CSS du gradient ou URL image/vidéo')
                  ->after('auth_bg_type');
            $table->string('auth_bg_label')->nullable()
                  ->comment('Étiquette descriptive ex: Noël 2025')
                  ->after('auth_bg_value');
            $table->string('auth_bg_overlay')->nullable()
                  ->comment('Couleur overlay rgba pour les images/vidéos')
                  ->after('auth_bg_label');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['auth_bg_type', 'auth_bg_value', 'auth_bg_label', 'auth_bg_overlay']);
        });
    }
};
