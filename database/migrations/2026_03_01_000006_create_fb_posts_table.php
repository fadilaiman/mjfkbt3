<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fb_posts', function (Blueprint $table) {
            $table->id();
            $table->string('fb_post_id', 100)->unique();
            $table->text('message')->nullable();
            $table->string('story', 500)->nullable();
            $table->string('full_picture', 1000)->nullable();
            $table->string('permalink_url', 1000)->nullable();
            $table->enum('post_type', ['status', 'photo', 'video', 'link', 'event'])->default('status');
            $table->datetime('published_at');
            $table->boolean('is_hidden')->default(false);
            $table->timestamp('fetched_at')->nullable();
            $table->timestamps();

            $table->index('published_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fb_posts');
    }
};
