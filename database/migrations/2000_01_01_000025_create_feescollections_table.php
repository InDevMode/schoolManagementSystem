<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table feescollections — suivi des paiements de scolarité.
 * Fusionne : create_feescollections + add_is_payment + add_payment_data_status
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feescollections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->integer('total_amount')->nullable();
            $table->integer('paid_amount')->nullable();
            $table->integer('remaning_amount')->nullable();
            $table->string('payment_type')->nullable();
            $table->text('remark')->nullable();
            $table->text('payment_data')->nullable();
            $table->string('payment_status')->default('Pending');
            $table->tinyInteger('is_payment')->default(0)->comment('0: Not Paid, 1: Paid');
            $table->tinyInteger('is_delete')->default(0)->comment('0: actif, 1: supprimé');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('class_id')->references('id')->on('class')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feescollections');
    }
};
