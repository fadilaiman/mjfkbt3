<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title', 500);
            $table->text('body');
            $table->enum('category', ['aktiviti', 'pengumuman', 'kebajikan', 'am'])->default('am');
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_published')->default(true);
            $table->string('attachment_path', 1000)->nullable();
            $table->enum('attachment_type', ['pdf', 'image'])->nullable();
            $table->datetime('published_at');
            $table->datetime('expires_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->index('published_at');
            $table->index(['is_pinned', 'published_at']);

            $table->foreign('created_by')
                ->references('id')
                ->on('admin_users')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
