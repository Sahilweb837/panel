<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HuddleParticipant extends Model
{
    use HasFactory;

    protected $table = 'huddle_participants';

    protected $fillable = [
        'room_id',
        'user_id',
        'user_name',
        'avatar_url',
        'is_muted',
        'is_screensharing',
        'is_speaking',
        'last_seen_at',
    ];

    protected $casts = [
        'is_muted' => 'boolean',
        'is_screensharing' => 'boolean',
        'is_speaking' => 'boolean',
        'last_seen_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
