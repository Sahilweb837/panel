<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('host_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('meeting_time');
            $table->string('room_id', 100)->unique();
            $table->enum('status', ['scheduled', 'active', 'ended', 'cancelled'])->default('scheduled');
            $table->boolean('invite_all')->default(false);
            $table->timestamps();

            $table->foreign('host_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['meeting_time', 'status']);
        });

        Schema::create('meeting_invites', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('meeting_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('status', ['invited', 'accepted', 'declined', 'joined'])->default('invited');
            $table->timestamps();

            $table->foreign('meeting_id')->references('id')->on('meetings')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['meeting_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_invites');
        Schema::dropIfExists('meetings');
    }
};
