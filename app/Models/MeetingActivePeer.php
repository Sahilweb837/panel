<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingActivePeer extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'user_id',
        'peer_id',
        'user_name',
        'last_seen_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
