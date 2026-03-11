<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_files', function (Blueprint $table) {
            $table->id();
            $table->string('original_name', 500);
            $table->string('stored_name', 500);
            $table->string('path', 1000);
            $table->string('disk', 50)->default('public');
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('file_size');
            $table->enum('type', ['pdf', 'image']);
            $table->unsignedBigInteger('uploaded_by');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('uploaded_by')
                ->references('id')
                ->on('admin_users')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_files');
    }
};
