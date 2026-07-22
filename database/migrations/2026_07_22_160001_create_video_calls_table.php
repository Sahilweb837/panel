<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('video_calls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('caller_id');
            $table->unsignedBigInteger('receiver_id')->nullable();
            $table->string('room_id', 100)->unique();
            $table->enum('status', ['pending', 'active', 'rejected', 'missed', 'ended'])->default('pending');
            $table->longText('offer_sdp')->nullable();
            $table->longText('answer_sdp')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->unsignedInteger('duration_seconds')->default(0);
            $table->string('call_type', 20)->default('video'); // video, audio
            $table->timestamps();

            $table->foreign('caller_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['receiver_id', 'status']);
            $table->index(['caller_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('video_calls');
    }
};
