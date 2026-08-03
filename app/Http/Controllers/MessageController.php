<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    private function getRecipients($userId, $userRoleSlug)
    {
        $isAdmin = in_array($userRoleSlug, ['super-admin', 'superadmin', 'root-admin', 'admin', 'subadmin', 'sub-admin']);
        $query = User::with('role')->where('id', '!=', $userId)->where('status', true);

        if (!$isAdmin) {
            if ($userRoleSlug === 'staff') {
                $query->whereHas('role', function ($q) {
                    $q->whereIn('slug', ['super-admin', 'superadmin', 'root-admin', 'admin', 'subadmin', 'sub-admin', 'student', 'staff']);
                });
            } elseif ($userRoleSlug === 'student') {
                $query->whereHas('role', function ($q) {
                    $q->whereIn('slug', ['super-admin', 'superadmin', 'root-admin', 'admin', 'subadmin', 'sub-admin', 'staff']);
                });
            }
        }

        return $query->orderBy('name')->get();
    }

    /**
     * Standard messages index
     */
    public function index(Request $request)
    {
        return redirect()->route('messages.full');
    }

    /**
     * Full Slack-like messages page
     */
    public function fullPage(Request $request)
    {
        $userId   = session('user_id');
        $roleSlug = session('user_role_slug');
        $isAdmin  = in_array($roleSlug, ['super-admin', 'superadmin', 'root-admin', 'admin', 'subadmin', 'sub-admin']);

        $inboxMessages = Message::with(['sender', 'receiver'])->forUser($userId, $roleSlug)->latest()->get();
        $sentMessages  = Message::with(['sender', 'receiver'])->where('sender_id', $userId)->latest()->get();
        $unreadCount   = $inboxMessages->where('is_read', false)->count();
        $recipients    = $this->getRecipients($userId, $roleSlug);

        $chatUserId      = $request->query('chat_user');
        $selectedChatUser = null;
        $chatMessages    = collect();

        if ($chatUserId) {
            $selectedChatUser = User::with('role')->find($chatUserId);
            if ($selectedChatUser) {
                $chatMessages = Message::with(['sender', 'receiver'])
                    ->where(function ($q) use ($userId, $chatUserId) {
                        $q->where('sender_id', $userId)->where('receiver_id', $chatUserId);
                    })
                    ->orWhere(function ($q) use ($userId, $chatUserId) {
                        $q->where('sender_id', $chatUserId)->where('receiver_id', $userId);
                    })
                    ->orderBy('created_at', 'asc')
                    ->get();

                Message::where('sender_id', $chatUserId)
                    ->where('receiver_id', $userId)
                    ->where('is_read', false)
                    ->update(['is_read' => true]);
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'html' => view('messages.chat_history', compact('chatMessages', 'selectedChatUser', 'chatUserId'))->render(),
                'unreadCount' => $unreadCount,
                'chatUserId' => $chatUserId,
                'userName' => $selectedChatUser ? $selectedChatUser->name : '',
                'userRole' => $selectedChatUser ? ($selectedChatUser->role?->role_name ?? 'Portal Member') : '',
                'profilePic' => $selectedChatUser && $selectedChatUser->profile_pic ? asset('uploads/profiles/'.$selectedChatUser->profile_pic) : null,
                'initials' => $selectedChatUser ? strtoupper(substr($selectedChatUser->name, 0, 1)) : '',
                'email' => $selectedChatUser ? $selectedChatUser->email : '',
                'username' => $selectedChatUser ? ($selectedChatUser->username ?: '-') : '',
                'joined' => $selectedChatUser ? $selectedChatUser->created_at->format('M Y') : '',
                'course' => $selectedChatUser && $selectedChatUser->student && $selectedChatUser->student->course ? $selectedChatUser->student->course->name : ($selectedChatUser && $selectedChatUser->student ? 'Enrolled' : null)
            ]);
        }

        return view('messages.full', compact(
            'inboxMessages', 'sentMessages', 'unreadCount', 'recipients',
            'isAdmin', 'chatMessages', 'selectedChatUser', 'chatUserId'
        ));
    }

    /**
     * Store a composed message
     */
    public function store(Request $request)
    {
        $userId   = session('user_id');
        $roleSlug = session('user_role_slug');
        $isAdmin  = in_array($roleSlug, ['super-admin', 'superadmin', 'root-admin', 'admin', 'subadmin', 'sub-admin']);


        $request->validate([
            'recipient_type' => 'required|in:user,role',
            'receiver_id'    => 'required_if:recipient_type,user|nullable|exists:users,id',
            'receiver_role'  => 'required_if:recipient_type,role|nullable|in:all,staff,student,admin',
            'subject'        => 'required|string|max:255',
            'body'           => 'required|string',
            'priority'       => 'required|in:Normal,Important,Urgent',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/messages'), $filename);
            $attachmentPath = $filename;
        }

        $message = Message::create([
            'sender_id'    => $userId,
            'receiver_id'  => $request->recipient_type === 'user' ? $request->receiver_id : null,
            'receiver_role'=> $request->recipient_type === 'role' ? $request->receiver_role : null,
            'subject'      => $request->subject,
            'body'         => $request->body,
            'priority'     => $request->priority,
            'is_read'      => false,
            'attachment'   => $attachmentPath,
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'data' => $message->load('sender', 'receiver')]);
        }

        return back()->with('success', 'Message sent successfully.');
    }

    /**
     * Mark message as read
     */
    public function markAsRead(Request $request, $id)
    {
        $userId   = session('user_id');
        $roleSlug = session('user_role_slug');
        $message  = Message::findOrFail($id);

        if ($message->receiver_id === $userId || $message->receiver_role === 'all' || $message->receiver_role === $roleSlug) {
            $message->update(['is_read' => true]);
        }

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Message marked as read.');
    }

    /**
     * Delete a message
     */
    public function destroy($id)
    {
        $userId  = session('user_id');
        $message = Message::findOrFail($id);

        $canDelete = $message->sender_id === $userId
            || $message->receiver_id === $userId
            || in_array(session('user_role_slug'), ['super-admin', 'superadmin', 'root-admin']);

        if ($canDelete) {
            $message->delete();
            if (request()->ajax()) return response()->json(['success' => true]);
            return back()->with('success', 'Message deleted.');
        }

        if (request()->ajax()) return response()->json(['error' => 'Unauthorized'], 403);
        return back()->with('error', 'Unauthorized.');
    }

    /**
     * Chat page
     */
    public function chat($userId = null)
    {
        $currentUserId = session('user_id');
        $roleSlug      = session('user_role_slug');
        $isAdmin       = in_array($roleSlug, ['super-admin', 'superadmin', 'root-admin', 'admin', 'subadmin', 'sub-admin']);
        $recipients    = $this->getRecipients($currentUserId, $roleSlug);

        $selectedUser = null;
        $chatMessages = collect();

        if ($userId) {
            $selectedUser = User::findOrFail($userId);
            $chatMessages = Message::with(['sender', 'receiver'])
                ->where(function ($q) use ($currentUserId, $userId) {
                    $q->where('sender_id', $currentUserId)->where('receiver_id', $userId);
                })
                ->orWhere(function ($q) use ($currentUserId, $userId) {
                    $q->where('sender_id', $userId)->where('receiver_id', $currentUserId);
                })
                ->orderBy('created_at', 'asc')
                ->get();

            Message::where('sender_id', $userId)->where('receiver_id', $currentUserId)->where('is_read', false)->update(['is_read' => true]);
        }

        return view('messages.chat', compact('recipients', 'selectedUser', 'chatMessages', 'isAdmin'));
    }

    /**
     * Store a chat message
     */
    public function storeChat(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'body'        => 'required|string',
            'attachment'  => 'nullable|file|max:5120',
        ]);

        $currentUserId = session('user_id');
        
        $attachmentName = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/messages'), $attachmentName);
        }

        $message = Message::create([
            'sender_id'    => $currentUserId,
            'receiver_id'  => $request->receiver_id,
            'receiver_role'=> null,
            'subject'      => 'Chat Message',
            'body'         => $request->body,
            'priority'     => 'Normal',
            'is_read'      => false,
            'attachment'   => $attachmentName,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data'    => [
                    'id'         => $message->id,
                    'body'       => $message->body,
                    'time'       => $message->created_at->format('h:i A'),
                    'sender_id'  => $currentUserId,
                    'attachment' => $attachmentName ? asset('uploads/messages/' . $attachmentName) : null,
                ],
            ]);
        }

        return back()->with('success', 'Message sent!');
    }

    /**
     * AJAX long-poll – new chat messages since last_id
     */
    public function pollChat(Request $request, $userId)
    {
        $currentUserId = session('user_id');
        $lastId        = (int) $request->query('last_id', 0);

        $messages = Message::with('sender')
            ->where('id', '>', $lastId)
            ->where(function ($q) use ($currentUserId, $userId) {
                $q->where(function ($q2) use ($currentUserId, $userId) {
                    $q2->where('sender_id', $currentUserId)->where('receiver_id', $userId);
                })->orWhere(function ($q2) use ($currentUserId, $userId) {
                    $q2->where('sender_id', $userId)->where('receiver_id', $currentUserId);
                });
            })
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn ($msg) => [
                'id'      => $msg->id,
                'body'    => e($msg->body),
                'time'    => $msg->created_at->format('h:i A'),
                'is_mine' => $msg->sender_id === $currentUserId,
                'sender'  => $msg->sender?->name ?? 'System',
            ]);

        if ($messages->count()) {
            Message::where('sender_id', $userId)->where('receiver_id', $currentUserId)->where('is_read', false)->update(['is_read' => true]);
        }

        return response()->json(['messages' => $messages]);
    }

    /**
     * AJAX – current unread count badge
     */
    public function unreadCount()
    {
        $userId   = session('user_id');
        $roleSlug = session('user_role_slug');
        return response()->json(['count' => Message::forUser($userId, $roleSlug)->unread()->count()]);
    }

    /**
     * AJAX – poll new inbox messages since last_id
     */
    public function pollInbox(Request $request)
    {
        $userId   = session('user_id');
        $roleSlug = session('user_role_slug');
        $lastId   = (int) $request->query('last_id', 0);

        $messages = Message::with('sender')
            ->forUser($userId, $roleSlug)
            ->where('id', '>', $lastId)
            ->latest()
            ->get()
            ->map(fn ($msg) => [
                'id'       => $msg->id,
                'subject'  => $msg->subject,
                'sender'   => $msg->sender?->name ?? 'System',
                'priority' => $msg->priority,
                'is_read'  => $msg->is_read,
                'time'     => $msg->created_at->diffForHumans(),
                'body'     => $msg->body,
            ]);

        return response()->json(['messages' => $messages]);
    }

    /**
     * AJAX – update / edit message body
     */
    public function updateMessage(Request $request, $id)
    {
        $request->validate([
            'body' => 'required|string',
        ]);

        $userId = session('user_id');
        $message = Message::findOrFail($id);

        if ($message->sender_id !== $userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $message->body = $request->body;
        $message->save();

        return response()->json([
            'success' => true,
            'message' => 'Message updated successfully.',
            'data' => [
                'id'   => $message->id,
                'body' => $message->body,
            ]
        ]);
    }
}
