<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Slack-Style LMS Workspace Suite.
     */
    public function up(): void
    {
        // A. Workspaces
        DB::statement("CREATE TABLE IF NOT EXISTS workspaces (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(150) NOT NULL,
            slug VARCHAR(180) NOT NULL UNIQUE,
            description TEXT NULL,
            logo VARCHAR(255) NULL,
            owner_id BIGINT UNSIGNED NOT NULL,
            visibility ENUM('private','public') DEFAULT 'private',
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            deleted_at TIMESTAMP NULL,
            INDEX (owner_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // B. Workspace members
        DB::statement("CREATE TABLE IF NOT EXISTS workspace_members (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            workspace_id BIGINT UNSIGNED NOT NULL,
            user_id BIGINT UNSIGNED NOT NULL,
            role ENUM('owner','admin','member','guest') DEFAULT 'member',
            status ENUM('active','invited','suspended') DEFAULT 'active',
            joined_at TIMESTAMP NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            UNIQUE KEY unique_workspace_member (workspace_id,user_id),
            INDEX (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // C. Channels
        DB::statement("CREATE TABLE IF NOT EXISTS workspace_channels (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            workspace_id BIGINT UNSIGNED NOT NULL,
            name VARCHAR(100) NOT NULL,
            slug VARCHAR(120) NOT NULL,
            description TEXT NULL,
            type ENUM('public','private','announcement') DEFAULT 'public',
            created_by BIGINT UNSIGNED NOT NULL,
            is_archived TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            deleted_at TIMESTAMP NULL,
            UNIQUE KEY unique_channel (workspace_id,slug),
            INDEX (workspace_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // D. Channel members
        DB::statement("CREATE TABLE IF NOT EXISTS channel_members (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            channel_id BIGINT UNSIGNED NOT NULL,
            user_id BIGINT UNSIGNED NOT NULL,
            role ENUM('admin','member') DEFAULT 'member',
            joined_at TIMESTAMP NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            UNIQUE KEY unique_channel_member (channel_id,user_id),
            INDEX (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // E. Direct and group conversations
        DB::statement("CREATE TABLE IF NOT EXISTS conversations (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            workspace_id BIGINT UNSIGNED NULL,
            type ENUM('direct','group') DEFAULT 'direct',
            name VARCHAR(150) NULL,
            direct_key VARCHAR(100) NULL UNIQUE,
            created_by BIGINT UNSIGNED NOT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            INDEX (workspace_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // F. Conversation members
        DB::statement("CREATE TABLE IF NOT EXISTS conversation_members (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            conversation_id BIGINT UNSIGNED NOT NULL,
            user_id BIGINT UNSIGNED NOT NULL,
            last_read_at TIMESTAMP NULL,
            is_muted TINYINT(1) DEFAULT 0,
            joined_at TIMESTAMP NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            UNIQUE KEY unique_conversation_member (conversation_id,user_id),
            INDEX (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // G. Main messaging table (channel messages, direct messages, group messages, threaded replies)
        DB::statement("CREATE TABLE IF NOT EXISTS workspace_messages (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            workspace_id BIGINT UNSIGNED NULL,
            channel_id BIGINT UNSIGNED NULL,
            conversation_id BIGINT UNSIGNED NULL,
            user_id BIGINT UNSIGNED NOT NULL,
            parent_id BIGINT UNSIGNED NULL,
            message_type ENUM('text','image','file','system') DEFAULT 'text',
            message TEXT NULL,
            is_edited TINYINT(1) DEFAULT 0,
            is_pinned TINYINT(1) DEFAULT 0,
            deleted_at TIMESTAMP NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            INDEX (workspace_id),
            INDEX (channel_id),
            INDEX (conversation_id),
            INDEX (user_id),
            INDEX (parent_id),
            INDEX (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // H. Message attachments
        DB::statement("CREATE TABLE IF NOT EXISTS workspace_message_attachments (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            message_id BIGINT UNSIGNED NOT NULL,
            user_id BIGINT UNSIGNED NOT NULL,
            original_name VARCHAR(255) NOT NULL,
            file_path VARCHAR(500) NOT NULL,
            mime_type VARCHAR(150) NULL,
            file_size BIGINT UNSIGNED DEFAULT 0,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            INDEX (message_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // I. Emoji reactions
        DB::statement("CREATE TABLE IF NOT EXISTS workspace_message_reactions (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            message_id BIGINT UNSIGNED NOT NULL,
            user_id BIGINT UNSIGNED NOT NULL,
            emoji VARCHAR(50) NOT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            UNIQUE KEY unique_reaction (message_id,user_id,emoji),
            INDEX (message_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // J. Message mentions
        DB::statement("CREATE TABLE IF NOT EXISTS workspace_message_mentions (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            message_id BIGINT UNSIGNED NOT NULL,
            mentioned_user_id BIGINT UNSIGNED NOT NULL,
            created_at TIMESTAMP NULL,
            UNIQUE KEY unique_mention (message_id,mentioned_user_id),
            INDEX (mentioned_user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // K. Workspace invitations
        DB::statement("CREATE TABLE IF NOT EXISTS workspace_invitations (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            workspace_id BIGINT UNSIGNED NOT NULL,
            email VARCHAR(255) NOT NULL,
            invited_by BIGINT UNSIGNED NOT NULL,
            token VARCHAR(100) NOT NULL UNIQUE,
            role ENUM('admin','member','guest') DEFAULT 'member',
            expires_at TIMESTAMP NULL,
            accepted_at TIMESTAMP NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            INDEX (workspace_id,email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // L. Notifications
        DB::statement("CREATE TABLE IF NOT EXISTS workspace_notifications (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT UNSIGNED NOT NULL,
            workspace_id BIGINT UNSIGNED NULL,
            message_id BIGINT UNSIGNED NULL,
            type VARCHAR(100) NOT NULL,
            title VARCHAR(255) NULL,
            body TEXT NULL,
            read_at TIMESTAMP NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            INDEX (user_id,read_at),
            INDEX (message_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workspace_notifications');
        Schema::dropIfExists('workspace_invitations');
        Schema::dropIfExists('workspace_message_mentions');
        Schema::dropIfExists('workspace_message_reactions');
        Schema::dropIfExists('workspace_message_attachments');
        Schema::dropIfExists('workspace_messages');
        Schema::dropIfExists('conversation_members');
        Schema::dropIfExists('conversations');
        Schema::dropIfExists('channel_members');
        Schema::dropIfExists('workspace_channels');
        Schema::dropIfExists('workspace_members');
        Schema::dropIfExists('workspaces');
    }
};

