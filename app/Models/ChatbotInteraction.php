<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotInteraction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'query',
        'response',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
