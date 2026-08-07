<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            if (!Schema::hasColumn('articles', 'app_icon')) {
                $table->string('app_icon')->nullable()->after('cover_image');
            }
            if (!Schema::hasColumn('articles', 'app_poster_35')) {
                $table->string('app_poster_35')->nullable()->after('app_icon');
            }
            if (!Schema::hasColumn('articles', 'download_links')) {
                $table->json('download_links')->nullable()->after('app_download_url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['app_icon', 'app_poster_35', 'download_links']);
        });
    }
};
