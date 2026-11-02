<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkspaceMessageAttachment extends Model
{
    use HasFactory;

    protected $table = 'workspace_message_attachments';

    protected $fillable = [
        'message_id',
        'user_id',
        'original_name',
        'file_path',
        'mime_type',
        'file_size',
    ];

    public function message()
    {
        return $this->belongsTo(WorkspaceMessage::class, 'message_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type ?? '', 'image/');
    }

    public function getUrlAttribute(): string
    {
        return asset($this->file_path);
    }
}

