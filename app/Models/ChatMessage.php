<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $table = 'chat_messages';

    protected $fillable = [
        'channel_id',
        'sender_id',
        'receiver_id',
        'parent_id',
        'message',
        'type',
        'mentions',
        'attachments',
        'link_preview',
        'reactions',
        'voice_path',
        'is_pinned',
        'is_edited',
    ];

    protected $casts = [
        'mentions' => 'array',
        'attachments' => 'array',
        'link_preview' => 'array',
        'reactions' => 'array',
        'is_pinned' => 'boolean',
        'is_edited' => 'boolean',
    ];

    public function channel()
    {
        return $this->belongsTo(ChatChannel::class, 'channel_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function parent()
    {
        return $this->belongsTo(ChatMessage::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(ChatMessage::class, 'parent_id')->orderBy('created_at', 'asc');
    }
}
