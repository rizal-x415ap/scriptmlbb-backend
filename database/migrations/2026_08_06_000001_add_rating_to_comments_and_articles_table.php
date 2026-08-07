<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('comments', 'rating')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->unsignedTinyInteger('rating')->nullable()->after('content');
            });
        }

        if (!Schema::hasColumn('articles', 'rating_average')) {
            Schema::table('articles', function (Blueprint $table) {
                $table->decimal('rating_average', 3, 2)->default(5.00)->after('likes_count');
                $table->unsignedInteger('ratings_count')->default(1)->after('rating_average');
            });
        }
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn('rating');
        });
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['rating_average', 'ratings_count']);
        });
    }
};
