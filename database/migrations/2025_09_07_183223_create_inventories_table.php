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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id('_id');
            $table->bigInteger('barcode')->nullable();
            $table->date('expiration_date')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->bigInteger('category_id')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('name');
            $table->integer('stock')->nullable();
            $table->integer('min_stock')->nullable();
            $table->string('img')->nullable();
            $table->timestamps();

            //$table->foreign('category_id')->references('_id')->on('categories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
