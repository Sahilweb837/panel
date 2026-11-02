<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkspaceMessageMention extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'workspace_message_mentions';

    protected $fillable = [
        'message_id',
        'mentioned_user_id',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function message()
    {
        return $this->belongsTo(WorkspaceMessage::class, 'message_id');
    }

    public function mentionedUser()
    {
        return $this->belongsTo(User::class, 'mentioned_user_id');
    }
}

