<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->Integer('receiver_id')->unsigned()->nullable()->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
            $table->Integer('sender_id')->unsigned()->nullable()->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->longText('message')->nullable();
            $table->string('file')->nullable();
            $table->tinyInteger('status')->default('0')->comment('0: unread, 1: read');
            $table->tinyInteger('is_delete')->default(0)->comment('0: isntDeleted, 1: Deleted');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
