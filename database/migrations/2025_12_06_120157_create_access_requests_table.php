<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('access_requests', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('video_id');

            $table->integer('requested_minutes')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            $table->unsignedBigInteger('reviewer_id')->nullable();
            $table->dateTime('reviewed_at')->nullable();
            $table->string('reason', 255)->nullable();

            $table->dateTime('requested_at');
            $table->timestamps();

            $table->index(['customer_id', 'video_id'], 'idx_req_customer_video');
            $table->index(['status'], 'idx_req_status');

            $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('video_id')->references('id')->on('videos')->onDelete('cascade');
            $table->foreign('reviewer_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('access_requests');
    }
};
