<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Workspace extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'workspaces';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo',
        'owner_id',
        'visibility',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($workspace) {
            if (empty($workspace->slug)) {
                $workspace->slug = Str::slug($workspace->name) . '-' . Str::random(5);
            }
        });
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members()
    {
        return $this->hasMany(WorkspaceMember::class, 'workspace_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'workspace_members', 'workspace_id', 'user_id')
            ->withPivot(['role', 'status', 'joined_at'])
            ->withTimestamps();
    }

    public function channels()
    {
        return $this->hasMany(WorkspaceChannel::class, 'workspace_id');
    }

    public function publicChannels()
    {
        return $this->hasMany(WorkspaceChannel::class, 'workspace_id')->where('type', 'public');
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class, 'workspace_id');
    }

    public function messages()
    {
        return $this->hasMany(WorkspaceMessage::class, 'workspace_id');
    }

    public function invitations()
    {
        return $this->hasMany(WorkspaceInvitation::class, 'workspace_id');
    }

    public function hasMember($userId): bool
    {
        return $this->members()->where('user_id', $userId)->where('status', 'active')->exists();
    }

    public function memberRole($userId): ?string
    {
        return $this->members()->where('user_id', $userId)->value('role');
    }

    public function isAdminOrOwner($userId): bool
    {
        return in_array($this->memberRole($userId), ['owner', 'admin']);
    }
}

