<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table chats — messagerie interne.
 * Fusionne : create_chats + add_created_date + fix_utf8mb4
 * Le charset utf8mb4 est défini au niveau connexion dans config/database.php.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('receiver_id')->nullable();
            $table->unsignedBigInteger('sender_id')->nullable();
            $table->longText('message')->nullable()->comment('Supporte emojis (utf8mb4)');
            $table->string('file')->nullable();
            $table->timestamp('created_date')->useCurrent();
            $table->tinyInteger('status')->default(0)->comment('0: unread, 1: read');
            $table->tinyInteger('is_delete')->default(0)->comment('0: actif, 1: supprimé');
            $table->timestamps();

            $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
