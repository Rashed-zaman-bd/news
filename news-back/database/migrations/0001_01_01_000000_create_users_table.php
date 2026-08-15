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
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();

            // Role & permissions
            $table->enum('role', ['admin', 'editor', 'author', 'reader'])->default('reader');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');

            // Profile
            $table->string('avatar')->nullable();
            $table->text('bio')->nullable();
            $table->string('phone')->nullable();

            // Author-specific (if authors publish under their name)
            $table->string('designation')->nullable(); // e.g. "Staff Correspondent"

            // Subscription (if you plan premium/paid articles later)
            $table->boolean('is_subscribed')->default(false);
            $table->timestamp('subscription_expires_at')->nullable();

            // OAuth / Social Login
            $table->string('provider')->nullable();
            $table->string('provider_id')->nullable();

            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

       
       
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
