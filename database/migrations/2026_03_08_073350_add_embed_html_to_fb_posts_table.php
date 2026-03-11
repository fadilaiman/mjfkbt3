<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fb_posts', function (Blueprint $table) {
            $table->text('embed_html')->nullable()->after('permalink_url');
            $table->string('fb_post_id', 100)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('fb_posts', function (Blueprint $table) {
            $table->dropColumn('embed_html');
            $table->string('fb_post_id', 100)->nullable(false)->change();
        });
    }
};
