<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title', 500);
            $table->text('description')->nullable();
            $table->datetime('start_datetime');
            $table->datetime('end_datetime')->nullable();
            $table->string('location', 500)->nullable();
            $table->string('cover_image_path', 1000)->nullable();
            $table->enum('source', ['manual', 'facebook'])->default('manual');
            $table->string('fb_event_id', 100)->nullable();
            $table->boolean('is_published')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->index('start_datetime');
            $table->index('source');

            $table->foreign('created_by')
                ->references('id')
                ->on('admin_users')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
