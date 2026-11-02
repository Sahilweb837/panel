<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkspaceMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'workspace_messages';

    protected $fillable = [
        'workspace_id',
        'channel_id',
        'conversation_id',
        'user_id',
        'parent_id',
        'message_type',
        'message',
        'is_edited',
        'is_pinned',
    ];

    protected $casts = [
        'is_edited' => 'boolean',
        'is_pinned' => 'boolean',
    ];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class, 'workspace_id');
    }

    public function channel()
    {
        return $this->belongsTo(WorkspaceChannel::class, 'channel_id');
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class, 'conversation_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parent()
    {
        return $this->belongsTo(WorkspaceMessage::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(WorkspaceMessage::class, 'parent_id')->orderBy('created_at', 'asc');
    }

    public function attachments()
    {
        return $this->hasMany(WorkspaceMessageAttachment::class, 'message_id');
    }

    public function reactions()
    {
        return $this->hasMany(WorkspaceMessageReaction::class, 'message_id');
    }

    public function mentions()
    {
        return $this->hasMany(WorkspaceMessageMention::class, 'message_id');
    }
}

