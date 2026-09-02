<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('communicates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('school_id')->nullable()->comment('Multi-tenant');
            $table->string('title')->nullable();
            $table->date('notice_date')->nullable();
            $table->date('publish_date')->nullable();
            $table->longText('message')->nullable();
            $table->smallInteger('is_delete')->default(0)->comment('0: actif, 1: supprimé');
            $table->smallInteger('is_active')->default(1)->comment('1: visible, 0: masqué');
            $table->timestamp('deleted_at')->nullable();
            $table->string('deleted_reason', 255)->nullable();
            $table->timestamp('email_sent_at')->nullable();
            $table->uuid('created_by')->nullable();
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->index('school_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('communicates');
    }
};
