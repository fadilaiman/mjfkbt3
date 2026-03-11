<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prayer_times', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('zone_code', 10)->default('SGR01');
            $table->time('subuh');
            $table->time('syuruk');
            $table->time('zohor');
            $table->time('asar');
            $table->time('maghrib');
            $table->time('isyak');
            $table->string('hijri_date')->nullable();
            $table->timestamp('fetched_at')->nullable();
            $table->timestamps();

            $table->unique(['date', 'zone_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prayer_times');
    }
};
