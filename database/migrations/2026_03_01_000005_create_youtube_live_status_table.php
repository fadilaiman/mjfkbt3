<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('youtube_live_status', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_live')->default(false);
            $table->string('video_id', 20)->nullable();
            $table->string('title', 500)->nullable();
            $table->unsignedInteger('concurrent_viewers')->default(0);
            $table->timestamp('checked_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('youtube_live_status');
    }
};
