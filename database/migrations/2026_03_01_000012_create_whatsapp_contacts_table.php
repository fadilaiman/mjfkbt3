<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->string('role', 200);
            $table->string('wa_number', 50)->nullable();
            $table->string('wa_qr_id', 200)->nullable();
            $table->enum('category', ['kewangan', 'am', 'pendidikan', 'kebajikan'])->default('am');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_contacts');
    }
};
