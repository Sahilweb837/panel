<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HuddleSignal extends Model
{
    use HasFactory;

    protected $table = 'huddle_signals';

    protected $fillable = [
        'room_id',
        'from_user_id',
        'to_user_id',
        'type',
        'payload',
        'is_processed',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }
}
