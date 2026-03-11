<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tiktok_videos', function (Blueprint $table) {
            $table->id();
            $table->string('tiktok_url', 1000);
            $table->string('title', 500)->nullable();
            $table->string('thumbnail_url', 1000)->nullable();
            $table->text('oembed_html')->nullable();
            $table->string('author_name', 200)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_hidden')->default(false);
            $table->timestamp('oembed_fetched_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tiktok_videos');
    }
};
