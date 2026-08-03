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
        Schema::create('meeting_active_peers', function (Blueprint $table) {
            $table->id();
            $table->string('room_id');
            $table->unsignedBigInteger('user_id');
            $table->string('peer_id');
            $table->string('user_name');
            $table->timestamp('last_seen_at');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['room_id', 'last_seen_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_active_peers');
    }
};
