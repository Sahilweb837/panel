<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (session()->has('user_id')) {
            $user = \App\Models\User::find(session('user_id'));
            if ($user) {
                // To avoid too many DB writes, we can update it only if it's more than 1 minute old
                try {
                    if (!$user->last_activity_at || now()->diffInMinutes($user->last_activity_at) >= 1) {
                        $user->last_activity_at = now();
                        $user->save();
                    }
                } catch (\Exception $e) {
                    // Gracefully ignore if the last_activity_at column doesn't exist yet (e.g., pending migrations)
                }
            }
        }
        
        return $next($request);
    }
}
