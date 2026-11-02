<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class WorkspaceChannel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'workspace_channels';

    protected $fillable = [
        'workspace_id',
        'name',
        'slug',
        'description',
        'type',
        'created_by',
        'is_archived',
    ];

    protected $casts = [
        'is_archived' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($channel) {
            if (empty($channel->slug)) {
                $channel->slug = Str::slug($channel->name);
            }
        });
    }

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
        return $this->hasMany(ChannelMember::class, 'channel_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'channel_members', 'channel_id', 'user_id')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(WorkspaceMessage::class, 'channel_id')->whereNull('parent_id');
    }

    public function hasMember($userId): bool
    {
        // Public channels are accessible to all workspace members
        if ($this->type === 'public' || $this->type === 'announcement') {
            return true;
        }
        return $this->members()->where('user_id', $userId)->exists();
    }
}

