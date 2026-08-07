<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('premium_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('token', 10)->unique();
            $table->string('device_fingerprint')->nullable();
            $table->string('device_name')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('expires_at');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('premium_tokens');
    }
};
