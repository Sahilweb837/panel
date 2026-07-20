<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StaffStatusController extends Controller
{
    public function ping(Request $request)
    {
        $userId = session('user_id');

        if ($userId) {
            User::where('id', $userId)->update([
                'last_seen_at' => Carbon::now()
            ]);
        }

        // Return online staff list (active in last 3 minutes)
        $threshold = Carbon::now()->subMinutes(3);

        $onlineUsers = User::with('role')
            ->where('last_seen_at', '>=', $threshold)
            ->get(['id', 'name', 'email', 'role_id', 'last_seen_at']);

        $onlineStaffList = $onlineUsers->map(function ($u) {
            return [
                'id' => $u->id,
                'name' => $u->name,
                'role' => $u->role?->role_name ?? 'User',
                'role_slug' => $u->role?->slug ?? 'user',
                'is_online' => true
            ];
        });

        return response()->json([
            'success' => true,
            'current_user_id' => $userId,
            'online_staff' => $onlineStaffList
        ]);
    }
}
