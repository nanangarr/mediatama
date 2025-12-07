<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('video_accesses', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('request_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('video_id');
            $table->unsignedBigInteger('approved_by');

            $table->integer('approved_minutes');
            $table->integer('grace_minutes')->default(0);

            $table->dateTime('start_at');
            $table->dateTime('end_at');

            $table->enum('status', ['active', 'expired', 'revoked'])->default('active');

            $table->integer('video_duration_sec_snapshot')->default(0);

            $table->timestamps();

            $table->index(['customer_id', 'video_id', 'status', 'start_at', 'end_at'], 'idx_access_active_window');
            $table->index(['request_id'], 'idx_access_request');
            $table->index(['video_id'], 'idx_access_video');

            $table->foreign('request_id')->references('id')->on('access_requests')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('video_id')->references('id')->on('videos')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('video_accesses');
    }
};
