<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table communicates (tableau d'affichage / noticeboard) — version consolidée.
 * Fusionne : create_communicates + add_is_active_deleted_at + add_email_sent_at
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('communicates', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->date('notice_date')->nullable();
            $table->date('publish_date')->nullable();
            $table->longText('message')->nullable();
            $table->tinyInteger('is_delete')->default(0)->comment('0: actif, 1: supprimé');
            $table->tinyInteger('is_active')->default(1)
                  ->comment('1: visible, 0: masqué');
            $table->timestamp('deleted_at')->nullable()
                  ->comment('Soft-delete timestamp');
            $table->string('deleted_reason', 255)->nullable();
            $table->timestamp('email_sent_at')->nullable()
                  ->comment('Null = pas encore envoyé par email');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('communicates');
    }
};
