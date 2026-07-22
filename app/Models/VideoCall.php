<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoCall extends Model
{
    protected $fillable = [
        'caller_id', 'receiver_id', 'room_id', 'status',
        'offer_sdp', 'answer_sdp', 'started_at', 'ended_at',
        'duration_seconds', 'call_type',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at'   => 'datetime',
    ];

    public function caller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'caller_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function durationFormatted(): string
    {
        $s = $this->duration_seconds;
        return sprintf('%02d:%02d', intdiv($s, 60), $s % 60);
    }
}
