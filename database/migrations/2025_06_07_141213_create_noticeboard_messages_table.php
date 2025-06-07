<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('noticeboard_messages', function (Blueprint $table) {
            $table->id();
            $table->Integer('communicates_id')->unsigned()->nullable()->foreign('communicates_id')->references('id')->on('communicates')->onDelete('cascade');
            $table->tinyInteger('message_to')->comment('user_type');
            $table->Integer('created_by')->unsigned()->nullable()->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('noticeboard_messages');
    }
};
