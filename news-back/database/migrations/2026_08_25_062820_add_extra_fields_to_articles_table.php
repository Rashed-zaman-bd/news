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
        Schema::table('articles', function (Blueprint $table) {
            $table->string('article_author')->nullable()->after('featured_image');
            $table->string('article_area')->nullable()->after('article_author');
            $table->string('image_title')->nullable()->after('article_area');
            $table->string('image_author')->nullable()->after('image_title');

            $table->longText('content_two')->nullable()->after('content');
            $table->longText('content_three')->nullable()->after('content_two');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn([
                'article_author',
                'article_area',
                'image_title',
                'image_author',
                'content_two',
                'content_three',
            ]);
        });
    }
};
