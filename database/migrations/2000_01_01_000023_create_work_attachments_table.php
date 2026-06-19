<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table work_attachments — pièces jointes multiples pour les works.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_id');
            $table->string('file_name')->comment('Nom d\'affichage (original)');
            $table->string('file_path')->comment('Chemin stocké sur disque');
            $table->string('file_ext', 20)->nullable();
            $table->unsignedBigInteger('file_size')->nullable()->comment('Taille en octets');
            $table->tinyInteger('is_delete')->default(0)->comment('0: actif, 1: supprimé');
            $table->timestamps();

            $table->foreign('work_id')->references('id')->on('works')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_attachments');
    }
};
