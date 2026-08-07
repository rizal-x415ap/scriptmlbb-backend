<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            if (!Schema::hasColumn('articles', 'template')) {
                $table->string('template')->default('standard')->after('category_id');
            }
            if (!Schema::hasColumn('articles', 'app_developer')) {
                $table->string('app_developer')->nullable()->after('template');
            }
            if (!Schema::hasColumn('articles', 'app_version')) {
                $table->string('app_version')->nullable()->after('app_developer');
            }
            if (!Schema::hasColumn('articles', 'app_size')) {
                $table->string('app_size')->nullable()->after('app_version');
            }
            if (!Schema::hasColumn('articles', 'app_min_android')) {
                $table->string('app_min_android')->nullable()->after('app_size');
            }
            if (!Schema::hasColumn('articles', 'app_download_url')) {
                $table->text('app_download_url')->nullable()->after('app_min_android');
            }
            if (!Schema::hasColumn('articles', 'app_screenshots')) {
                $table->json('app_screenshots')->nullable()->after('app_download_url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn([
                'template',
                'app_developer',
                'app_version',
                'app_size',
                'app_min_android',
                'app_download_url',
                'app_screenshots',
            ]);
        });
    }
};
