<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table class — version consolidée.
 * Fusionne : create_class + add_amount + add_school_id
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id')->nullable()
                  ->comment('Multi-tenant — FK vers schools.id');
            $table->string('name');
            $table->integer('amount')->nullable()->comment('Frais de scolarité de la classe');
            $table->tinyInteger('status')->default(0)->comment('0: Inactive, 1: Active');
            $table->tinyInteger('is_delete')->default(0)->comment('0: actif, 1: supprimé');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index('school_id');
            $table->index(['school_id', 'is_delete']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class');
    }
};
