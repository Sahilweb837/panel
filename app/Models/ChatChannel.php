<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatChannel extends Model
{
    use HasFactory;

    protected $table = 'chat_channels';

    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'topic',
        'description',
        'icon',
        'type',
        'creator_id',
        'is_favorite',
        'is_active',
        'order_index',
        'tasks_count',
        'tags_count',
        'status_badge',
    ];

    public function parent()
    {
        return $this->belongsTo(ChatChannel::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ChatChannel::class, 'parent_id')->orderBy('order_index');
    }

    public function members()
    {
        return $this->hasMany(ChatChannelMember::class, 'channel_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'chat_channel_members', 'channel_id', 'user_id')
                    ->withPivot('role_badge', 'designation', 'is_admin', 'is_pinned')
                    ->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'channel_id')->orderBy('created_at', 'asc');
    }

    public function activeHuddle()
    {
        return $this->hasOne(HuddleSession::class, 'channel_id')->where('status', 'active');
    }

    /**
     * Get full breadcrumb path array
     */
    public function getBreadcrumbsAttribute(): array
    {
        $crumbs = [];
        $curr = $this;
        while ($curr) {
            array_unshift($crumbs, [
                'id' => $curr->id,
                'name' => $curr->name,
                'slug' => $curr->slug,
                'icon' => $curr->icon,
            ]);
            $curr = $curr->parent;
        }
        return $crumbs;
    }
}
