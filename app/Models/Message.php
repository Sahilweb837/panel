<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'receiver_role',
        'subject',
        'body',
        'priority',
        'is_read',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeForUser($query, $userId, $userRoleSlug = null)
    {
        return $query->where(function ($q) use ($userId, $userRoleSlug) {
            $q->where('receiver_id', $userId)
              ->orWhere('receiver_role', 'all');

            if ($userRoleSlug) {
                $q->orWhere('receiver_role', $userRoleSlug);
                if (in_array($userRoleSlug, ['super-admin', 'superadmin', 'root-admin'])) {
                    $q->orWhere('receiver_role', 'admin');
                }
                if ($userRoleSlug === 'staff') {
                    $q->orWhereIn('receiver_role', ['admin', 'employee']);
                }
            }
        });
    }
}
