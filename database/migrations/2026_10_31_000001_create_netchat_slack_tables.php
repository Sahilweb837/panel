<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Channels / Community Groups table
        if (!Schema::hasTable('chat_channels')) {
            Schema::create('chat_channels', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->unsignedBigInteger('parent_id')->nullable()->index();
                $table->string('topic')->nullable();
                $table->text('description')->nullable();
                $table->string('icon', 50)->default('#');
                $table->string('type', 30)->default('channel'); // channel, category, subchannel, dm
                $table->unsignedBigInteger('creator_id')->nullable();
                $table->boolean('is_favorite')->default(false);
                $table->boolean('is_active')->default(true);
                $table->integer('order_index')->default(0);
                $table->integer('tasks_count')->default(4);
                $table->integer('tags_count')->default(13);
                $table->string('status_badge', 50)->default('Active');
                $table->timestamps();

                $table->foreign('parent_id')->references('id')->on('chat_channels')->onDelete('cascade');
            });
        }

        // 2. Channel Members & Roles
        if (!Schema::hasTable('chat_channel_members')) {
            Schema::create('chat_channel_members', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('channel_id');
                $table->unsignedBigInteger('user_id');
                $table->string('role_badge', 50)->nullable()->default('Member'); // Design, Management, Development, etc.
                $table->string('designation', 100)->nullable(); // Art director, Product owner, UX/UI designer, Front-end dev
                $table->boolean('is_admin')->default(false);
                $table->boolean('is_pinned')->default(false);
                $table->timestamp('last_read_at')->nullable();
                $table->timestamps();

                $table->foreign('channel_id')->references('id')->on('chat_channels')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->unique(['channel_id', 'user_id']);
            });
        }

        // 3. Chat Messages (supports channels, threads, direct messages)
        if (!Schema::hasTable('chat_messages')) {
            Schema::create('chat_messages', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('channel_id')->nullable()->index();
                $table->unsignedBigInteger('sender_id')->index();
                $table->unsignedBigInteger('receiver_id')->nullable()->index(); // for DMs
                $table->unsignedBigInteger('parent_id')->nullable()->index(); // for thread replies
                $table->longText('message');
                $table->string('type', 30)->default('text'); // text, image, file, voice, link, system
                $table->json('mentions')->nullable(); // ['@UX/UI', '@Sophia']
                $table->json('attachments')->nullable(); // [{name, url, size, type}]
                $table->json('link_preview')->nullable(); // {title, subtitle, domain, url, button_text}
                $table->json('reactions')->nullable(); // {"👍": [1, 2], "❤️": [1], "😊": [2]}
                $table->string('voice_path')->nullable();
                $table->boolean('is_pinned')->default(false);
                $table->boolean('is_edited')->default(false);
                $table->timestamps();

                $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // 4. Huddle Sessions
        if (!Schema::hasTable('huddle_sessions')) {
            Schema::create('huddle_sessions', function (Blueprint $table) {
                $table->id();
                $table->string('room_id', 100)->unique();
                $table->unsignedBigInteger('channel_id')->nullable()->index();
                $table->unsignedBigInteger('host_id');
                $table->string('title')->nullable();
                $table->string('status', 30)->default('active'); // active, ended
                $table->timestamp('started_at')->nullable();
                $table->timestamp('ended_at')->nullable();
                $table->timestamps();

                $table->foreign('host_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // 5. Huddle Participants
        if (!Schema::hasTable('huddle_participants')) {
            Schema::create('huddle_participants', function (Blueprint $table) {
                $table->id();
                $table->string('room_id', 100)->index();
                $table->unsignedBigInteger('user_id');
                $table->string('user_name');
                $table->string('avatar_url')->nullable();
                $table->boolean('is_muted')->default(false);
                $table->boolean('is_screensharing')->default(false);
                $table->boolean('is_speaking')->default(false);
                $table->timestamp('last_seen_at');
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->unique(['room_id', 'user_id']);
            });
        }

        // 6. Huddle WebRTC Signaling (Signaling for audio & screen share)
        if (!Schema::hasTable('huddle_signals')) {
            Schema::create('huddle_signals', function (Blueprint $table) {
                $table->id();
                $table->string('room_id', 100)->index();
                $table->unsignedBigInteger('from_user_id');
                $table->unsignedBigInteger('to_user_id')->nullable(); // null = broadcast
                $table->string('type', 50); // 'offer', 'answer', 'candidate', 'user_joined', 'user_left', 'screen_share_on', 'screen_share_off'
                $table->longText('payload')->nullable();
                $table->boolean('is_processed')->default(false);
                $table->timestamps();

                $table->index(['room_id', 'to_user_id', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('huddle_signals');
        Schema::dropIfExists('huddle_participants');
        Schema::dropIfExists('huddle_sessions');
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_channel_members');
        Schema::dropIfExists('chat_channels');
    }
};
