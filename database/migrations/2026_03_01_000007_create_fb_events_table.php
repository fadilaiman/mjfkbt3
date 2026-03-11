<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fb_events', function (Blueprint $table) {
            $table->id();
            $table->string('fb_event_id', 100)->unique();
            $table->string('name', 500);
            $table->text('description')->nullable();
            $table->datetime('start_time');
            $table->datetime('end_time')->nullable();
            $table->string('place', 500)->nullable();
            $table->string('cover_url', 1000)->nullable();
            $table->unsignedInteger('attending_count')->default(0);
            $table->boolean('is_hidden')->default(false);
            $table->string('fb_url', 1000)->nullable();
            $table->timestamp('fetched_at')->nullable();
            $table->timestamps();

            $table->index('start_time');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fb_events');
    }
};
