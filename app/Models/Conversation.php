<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $table = 'conversations';

    protected $fillable = [
        'workspace_id',
        'type',
        'name',
        'direct_key',
        'created_by',
    ];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class, 'workspace_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members()
    {
        return $this->hasMany(ConversationMember::class, 'conversation_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'conversation_members', 'conversation_id', 'user_id')
            ->withPivot(['last_read_at', 'is_muted', 'joined_at'])
            ->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(WorkspaceMessage::class, 'conversation_id')->whereNull('parent_id');
    }

    /**
     * Get or create a direct conversation between two users
     */
    public static function findOrCreateDirect($workspaceId, $user1Id, $user2Id)
    {
        $min = min($user1Id, $user2Id);
        $max = max($user1Id, $user2Id);
        $directKey = "dm_{$workspaceId}_{$min}_{$max}";

        $conv = static::firstOrCreate(
            ['direct_key' => $directKey],
            [
                'workspace_id' => $workspaceId,
                'type'         => 'direct',
                'created_by'   => $user1Id,
            ]
        );

        // Ensure both users are members
        ConversationMember::firstOrCreate(
            ['conversation_id' => $conv->id, 'user_id' => $user1Id],
            ['joined_at' => now()]
        );
        ConversationMember::firstOrCreate(
            ['conversation_id' => $conv->id, 'user_id' => $user2Id],
            ['joined_at' => now()]
        );

        return $conv;
    }
}

