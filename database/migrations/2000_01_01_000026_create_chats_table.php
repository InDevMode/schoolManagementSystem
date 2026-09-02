<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('receiver_id')->nullable();
            $table->uuid('sender_id')->nullable();
            $table->longText('message')->nullable()->comment('Supporte emojis (utf8)');
            $table->string('file')->nullable();
            $table->timestamp('created_date')->useCurrent();
            $table->smallInteger('status')->default(0)->comment('0: unread, 1: read');
            $table->smallInteger('is_delete')->default(0)->comment('0: actif, 1: supprimé');
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
