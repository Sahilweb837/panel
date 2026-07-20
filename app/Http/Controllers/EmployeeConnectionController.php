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
        $pendingConnections = Auth::user()->receivedConnections()->where('status', 'pending')->with('requester')->get();
        $connectedUsers = EmployeeConnection::where(function($query) {
                $query->where('requester_id', Auth::id())
                      ->orWhere('recipient_id', Auth::id());
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

        if (Auth::id() == $request->recipient_id) {
            return back()->with('error', 'You cannot connect with yourself.');
        }

        $existing = EmployeeConnection::where(function($query) use ($request) {
                $query->where('requester_id', Auth::id())
                      ->where('recipient_id', $request->recipient_id);
            })->orWhere(function($query) use ($request) {
                $query->where('requester_id', $request->recipient_id)
                      ->where('recipient_id', Auth::id());
            })->first();

        if ($existing) {
            return back()->with('error', 'Connection request already exists.');
        }

        EmployeeConnection::create([
            'requester_id' => Auth::id(),
            'recipient_id' => $request->recipient_id,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Connection request sent.');
    }

    public function update(Request $request, EmployeeConnection $connection)
    {
        if ($connection->recipient_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:accepted,rejected',
        ]);

        $connection->update(['status' => $request->status]);

        return back()->with('success', 'Connection request ' . $request->status . '.');
    }
}
