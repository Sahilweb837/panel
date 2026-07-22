<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Meeting extends Model
{
    protected $fillable = [
        'host_id', 'title', 'description', 'meeting_time',
        'room_id', 'status', 'invite_all',
    ];

    protected $casts = [
        'meeting_time' => 'datetime',
        'invite_all'   => 'boolean',
    ];

    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    public function invites(): HasMany
    {
        return $this->hasMany(MeetingInvite::class);
    }

    public function invitedUsers()
    {
        return $this->hasManyThrough(User::class, MeetingInvite::class, 'meeting_id', 'id', 'id', 'user_id');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('meeting_time', '>=', now())->where('status', 'scheduled');
    }
}
