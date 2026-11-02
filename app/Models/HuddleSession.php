<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HuddleSession extends Model
{
    use HasFactory;

    protected $table = 'huddle_sessions';

    protected $fillable = [
        'room_id',
        'channel_id',
        'host_id',
        'title',
        'status',
        'started_at',
        'ended_at',
    ];

    public function channel()
    {
        return $this->belongsTo(ChatChannel::class, 'channel_id');
    }

    public function host()
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    public function participants()
    {
        return $this->hasMany(HuddleParticipant::class, 'room_id', 'room_id');
    }

    public function signals()
    {
        return $this->hasMany(HuddleSignal::class, 'room_id', 'room_id');
    }
}
