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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id('_id');
            $table->bigInteger('order')->nullable();
            $table->string('ref_number')->nullable();
            $table->decimal('discount', 10, 2)->nullable();
            $table->bigInteger('customer_id')->nullable();
            $table->integer('status')->nullable();
            $table->decimal('subtotal', 10, 2)->nullable();
            $table->decimal('tax', 10, 2)->nullable();
            $table->integer('order_type')->nullable();
            $table->json('items')->nullable();
            $table->json('customer')->nullable();
            $table->dateTime('date')->nullable();
            $table->string('payment_type')->nullable();
            $table->text('payment_info')->nullable();
            $table->decimal('total', 10, 2)->nullable();
            $table->decimal('paid', 10, 2)->nullable();
            $table->decimal('change', 10, 2)->nullable();
            $table->integer('till')->nullable();
            $table->string('user')->nullable();
            $table->integer('user_id')->nullable();
            $table->timestamps();

            //$table->foreign('customer_id')->references('_id')->on('customers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
