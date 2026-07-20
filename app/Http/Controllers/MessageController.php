<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $userId = session('user_id');
        $userRoleSlug = session('user_role_slug');

        $inboxMessages = Message::with('sender')
            ->forUser($userId, $userRoleSlug)
            ->latest()
            ->get();

        $sentMessages = Message::with('receiver')
            ->where('sender_id', $userId)
            ->latest()
            ->get();

        $unreadCount = $inboxMessages->where('is_read', false)->count();

        // Users to select as recipient
        $recipients = User::with('role')
            ->where('id', '!=', $userId)
            ->where('status', true)
            ->orderBy('name')
            ->get();

        return view('messages.index', compact('inboxMessages', 'sentMessages', 'unreadCount', 'recipients'));
    }

    public function store(Request $request)
    {
        $userId = session('user_id');

        $request->validate([
            'recipient_type' => 'required|in:user,role',
            'receiver_id' => 'required_if:recipient_type,user|nullable|exists:users,id',
            'receiver_role' => 'required_if:recipient_type,role|nullable|in:all,staff,student,admin',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'priority' => 'required|in:Normal,Important,Urgent',
        ]);

        Message::create([
            'sender_id' => $userId,
            'receiver_id' => $request->recipient_type === 'user' ? $request->receiver_id : null,
            'receiver_role' => $request->recipient_type === 'role' ? $request->receiver_role : null,
            'subject' => $request->subject,
            'body' => $request->body,
            'priority' => $request->priority,
            'is_read' => false,
        ]);

        return redirect()->route('messages.index')->with('success', 'Message sent successfully.');
    }

    public function markAsRead(Request $request, $id)
    {
        $userId = session('user_id');
        $userRoleSlug = session('user_role_slug');

        $message = Message::findOrFail($id);

        if ($message->receiver_id === $userId || $message->receiver_role === 'all' || $message->receiver_role === $userRoleSlug) {
            $message->is_read = true;
            $message->save();
        }

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Message marked as read.');
    }

    public function destroy($id)
    {
        $userId = session('user_id');
        $message = Message::findOrFail($id);

        if ($message->sender_id === $userId || $message->receiver_id === $userId || in_array(session('user_role_slug'), ['super-admin', 'superadmin', 'root-admin'])) {
            $message->delete();
            return redirect()->route('messages.index')->with('success', 'Message deleted successfully.');
        }

        return redirect()->route('messages.index')->with('error', 'Unauthorized to delete this message.');
    }
}
