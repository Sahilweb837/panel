<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatChannelMember extends Model
{
    use HasFactory;

    protected $table = 'chat_channel_members';

    protected $fillable = [
        'channel_id',
        'user_id',
        'role_badge',
        'designation',
        'is_admin',
        'is_pinned',
        'last_read_at',
    ];

    public function channel()
    {
        return $this->belongsTo(ChatChannel::class, 'channel_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
