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
        Schema::create('users', function (Blueprint $table) {
            $table->id('_id');
            $table->string('name');
            $table->string('username')->unique(); // Unique username
            $table->string('fullname'); // Full name
            $table->tinyInteger('perm_products')->default(1); // Permission for products
            $table->tinyInteger('perm_categories')->default(1); // Permission for categories
            $table->tinyInteger('perm_transactions')->default(1); // Permission for transactions
            $table->tinyInteger('perm_users')->default(1); // Permission for users
            $table->tinyInteger('perm_settings')->default(1); // Permission for settings
            $table->text('status')->nullable(); // Status text, nullable
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
