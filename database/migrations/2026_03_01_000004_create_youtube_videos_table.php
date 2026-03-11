<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('youtube_videos', function (Blueprint $table) {
            $table->id();
            $table->string('video_id', 20)->unique();
            $table->string('title', 500);
            $table->text('description')->nullable();
            $table->string('thumbnail_url', 500)->nullable();
            $table->datetime('published_at');
            $table->string('duration', 20)->nullable();
            $table->unsignedBigInteger('view_count')->default(0);
            $table->unsignedBigInteger('like_count')->default(0);
            $table->boolean('is_live')->default(false);
            $table->boolean('is_hidden')->default(false);
            $table->timestamp('fetched_at')->nullable();
            $table->timestamps();

            $table->index('published_at');
            $table->index('is_live');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('youtube_videos');
    }
};
