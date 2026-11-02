<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminActivityLog extends Model
{
    use HasFactory;

    protected $table = 'admin_activity_logs';

    protected $fillable = [
        'user_id',
        'user_name',
        'user_role',
        'action',
        'method',
        'route',
        'ip_address',
        'user_agent',
        'request_data',
        'status_code',
    ];

    protected $casts = [
        'request_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper to quickly log an activity.
     */
    public static function record(string $action, ?int $userId = null, ?string $userName = null, ?string $userRole = null, ?array $data = null, ?int $statusCode = 200): self
    {
        $request = request();

        return static::create([
            'user_id' => $userId ?? session('user_id'),
            'user_name' => $userName ?? session('user_name', 'System'),
            'user_role' => $userRole ?? session('user_role_slug', 'unknown'),
            'action' => $action,
            'method' => $request ? $request->method() : 'CLI',
            'route' => $request ? $request->path() : null,
            'ip_address' => $request ? $request->ip() : null,
            'user_agent' => $request ? substr($request->userAgent() ?? '', 0, 500) : null,
            'request_data' => $data,
            'status_code' => $statusCode,
        ]);
    }
}
