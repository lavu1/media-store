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
        Schema::create('device_file_links', function (Blueprint $table) {
            $table->id();
            $table->string('device_id')->index();
            $table->string('file_url');
            $table->string('file_type'); // cover_letter or cv
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_file_links');
    }
};
