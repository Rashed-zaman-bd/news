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
        Schema::create('front_page_ads', function (Blueprint $table) {
            $table->id();
            $table->string('image');           // storage path
            $table->string('name')->nullable();            // internal/display name, e.g. "IPDC Deposit Banner"
            $table->string('provider')->nullable();        // advertiser/sponsor name, e.g. "IPDC Finance"
            $table->string('link_url')->nullable(); // where the ad click should go
            $table->enum('placement', ['top','middle','middle-two','middle-three','middle-four','middle-five','middle-six','middle-seven','middle-eight','middle-nine','middle-ten','sidebar','sidebar-two','sidebar-three', 'sidebar-four', 'sidebar-five','sidebar-six'])->default('top');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->unsignedInteger('impressions')->default(0);
            $table->unsignedInteger('clicks')->default(0);
            $table->timestamps();

            $table->index(['placement', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('front_page_ads');
    }
};
