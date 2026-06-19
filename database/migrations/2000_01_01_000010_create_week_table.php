<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table week — version consolidée (create + add_day).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('week', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable()->comment('Ex: Lundi, Mardi...');
            $table->integer('day')->nullable()->comment('Numéro du jour (1=Lundi, 7=Dimanche)');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('week');
    }
};
