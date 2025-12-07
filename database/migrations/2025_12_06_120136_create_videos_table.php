<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 150);
            $table->text('slug')->nullable();
            $table->text('description')->nullable();
            $table->string('thumbnail', 255)->nullable();
            $table->string('storage_path', 255)->nullable();
            $table->string('external_url', 255)->nullable();
            $table->integer('duration_sec')->nullable();
            $table->boolean('is_external_secured')->default(false);
            $table->dateTime('external_signed_until')->nullable();
            $table->dateTime('external_security_checked_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active'], 'idx_videos_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
