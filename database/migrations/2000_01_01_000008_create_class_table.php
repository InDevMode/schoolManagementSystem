<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('school_id')->nullable()
                  ->comment('Multi-tenant — FK vers schools.id');
            $table->string('name');
            $table->integer('amount')->nullable()->comment('Frais de scolarité de la classe');
            $table->smallInteger('status')->default(0)->comment('0: Inactive, 1: Active');
            $table->smallInteger('is_delete')->default(0)->comment('0: actif, 1: supprimé');
            $table->uuid('created_by')->nullable();
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');

            $table->index('school_id');
            $table->index(['school_id', 'is_delete']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class');
    }
};
