<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('school_id')->nullable()->comment('Multi-tenant');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('event_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            // Enum remplacé par string pour permettre les types personnalisés
            $table->string('event_type')->default('academic')
                  ->comment('academic, cultural, administrative, exam, ceremony, trip');
            $table->uuid('custom_event_type_id')->nullable()
                  ->comment('FK vers event_type_customs.id — null = type prédéfini');
            $table->string('location')->nullable();
            $table->smallInteger('is_delete')->default(0)->comment('0: actif, 1: supprimé');
            $table->uuid('created_by')->nullable();
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_events');
    }
};
