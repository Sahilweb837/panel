<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkspaceNotification extends Model
{
    use HasFactory;

    protected $table = 'workspace_notifications';

    protected $fillable = [
        'user_id',
        'workspace_id',
        'message_id',
        'type',
        'title',
        'body',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class, 'workspace_id');
    }

    public function message()
    {
        return $this->belongsTo(WorkspaceMessage::class, 'message_id');
    }
}

