<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->text('app_icon')->nullable()->change();
            $table->text('app_poster_35')->nullable()->change();
            $table->text('cover_image')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->string('app_icon', 255)->nullable()->change();
            $table->string('app_poster_35', 255)->nullable()->change();
            $table->string('cover_image', 255)->nullable()->change();
        });
    }
};
