<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\EmployeeConnection;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class EmployeeConnectionController extends Controller
{
    public function index()
    {
        $userId = session('user_id');
        $pendingConnections = EmployeeConnection::where('recipient_id', $userId)->where('status', 'pending')->with('requester')->get();
        $connectedUsers = EmployeeConnection::where(function($query) use ($userId) {
                $query->where('requester_id', $userId)
                      ->orWhere('recipient_id', $userId);
            })
            ->where('status', 'accepted')
            ->get();
            
        return view('connections.index', compact('pendingConnections', 'connectedUsers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
        ]);

        $userId = session('user_id');

        if ($userId == $request->recipient_id) {
            return back()->with('error', 'You cannot connect with yourself.');
        }

        $existing = EmployeeConnection::where(function($query) use ($userId, $request) {
                $query->where('requester_id', $userId)
                      ->where('recipient_id', $request->recipient_id);
            })->orWhere(function($query) use ($userId, $request) {
                $query->where('requester_id', $request->recipient_id)
                      ->where('recipient_id', $userId);
            })->first();

        if ($existing) {
            return back()->with('error', 'Connection request already exists.');
        }

        EmployeeConnection::create([
            'requester_id' => $userId,
            'recipient_id' => $request->recipient_id,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Connection request sent.');
    }

    public function update(Request $request, EmployeeConnection $connection)
    {
        if ($connection->recipient_id !== session('user_id')) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:accepted,rejected',
        ]);

        $connection->update(['status' => $request->status]);

        return back()->with('success', 'Connection request ' . $request->status . '.');
    }
}
