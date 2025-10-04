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
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->string('days')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('education_background')->nullable();
            $table->text('work_experience')->nullable();
            $table->text('skills')->nullable();
            $table->string('status')->default('pending'); // pending, processing, completed
            $table->string('cv_file_path')->nullable(); // Path to generated CV file
            $table->text('additional_notes')->nullable();
            $table->timestamps();
            //$table->foreign('customer_id')->references('_id')->on('customers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};
