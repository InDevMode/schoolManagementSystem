<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table noticeboard_messages — destinataires d'une communication.
 * message_to correspond au user_type ciblé.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('noticeboard_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('communicates_id')->nullable();
            $table->tinyInteger('message_to')->comment('user_type ciblé : 1=admin, 2=teacher, 3=student, 4=parent');
            $table->timestamps();

            $table->foreign('communicates_id')->references('id')->on('communicates')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('noticeboard_messages');
    }
};
