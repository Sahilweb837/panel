<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceChannel;
use App\Models\WorkspaceMessage;
use App\Models\WorkspaceMessageAttachment;
use App\Models\WorkspaceMessageMention;
use App\Models\WorkspaceMessageReaction;
use App\Models\WorkspaceNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WorkspaceMessageController extends Controller
{
    private function getCurrentUser()
    {
        $userId = session('user_id') ?? auth()->id() ?? 1;
        $user = User::with('role')->find($userId);
        if (!$user) {
            $user = User::first();
        }
        return $user;
    }

    /**
     * Post a new message or thread reply
     */
    public function store(Request $request)
    {
        $currentUser = $this->getCurrentUser();

        $request->validate([
            'workspace_id'    => 'required|exists:workspaces,id',
            'channel_id'      => 'nullable|exists:workspace_channels,id',
            'conversation_id' => 'nullable|exists:conversations,id',
            'parent_id'       => 'nullable|exists:workspace_messages,id',
            'message'         => 'nullable|string',
            'attachment'      => 'nullable|file|max:25600', // 25MB max
        ]);

        if (empty($request->message) && !$request->hasFile('attachment')) {
            return response()->json(['error' => 'Message or attachment required.'], 422);
        }

        $workspace = Workspace::findOrFail($request->workspace_id);

        // Announcement channel restriction: only owner or admin can post
        if ($request->channel_id) {
            $channel = WorkspaceChannel::find($request->channel_id);
            if ($channel && $channel->type === 'announcement' && !$workspace->isAdminOrOwner($currentUser->id)) {
                return response()->json(['error' => 'Only administrators can post in announcement channels.'], 403);
            }
        }

        $messageType = 'text';
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $mime = $file->getMimeType();
            $messageType = str_starts_with($mime, 'image/') ? 'image' : 'file';
        }

        // Create main message record
        $msg = WorkspaceMessage::create([
            'workspace_id'    => $workspace->id,
            'channel_id'      => $request->channel_id,
            'conversation_id' => $request->conversation_id,
            'user_id'         => $currentUser->id,
            'parent_id'       => $request->parent_id,
            'message_type'    => $messageType,
            'message'         => $request->message ?? '',
        ]);

        // Process File Attachment if uploaded
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $originalName = $file->getClientOriginalName();
            $mime = $file->getMimeType();
            $size = $file->getSize();

            $destDir = public_path('uploads/workspace_attachments');
            if (!file_exists($destDir)) {
                @mkdir($destDir, 0777, true);
            }

            $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $file->move($destDir, $fileName);

            WorkspaceMessageAttachment::create([
                'message_id'    => $msg->id,
                'user_id'       => $currentUser->id,
                'original_name' => $originalName,
                'file_path'     => 'uploads/workspace_attachments/' . $fileName,
                'mime_type'     => $mime,
                'file_size'     => $size,
            ]);
        }

        // Parse @mentions
        if (!empty($request->message)) {
            preg_match_all('/@([a-zA-Z0-9_\-\.]+)/', $request->message, $matches);
            if (!empty($matches[1])) {
                foreach ($matches[1] as $username) {
                    $mentionedUser = User::where('name', 'like', "%{$username}%")->first();
                    if ($mentionedUser && $mentionedUser->id !== $currentUser->id) {
                        WorkspaceMessageMention::firstOrCreate([
                            'message_id'        => $msg->id,
                            'mentioned_user_id' => $mentionedUser->id,
                        ], [
                            'created_at' => now(),
                        ]);

                        WorkspaceNotification::create([
                            'user_id'      => $mentionedUser->id,
                            'workspace_id' => $workspace->id,
                            'message_id'   => $msg->id,
                            'type'         => 'mention',
                            'title'        => "{$currentUser->name} mentioned you",
                            'body'         => Str::limit($msg->message, 100),
                        ]);
                    }
                }
            }
        }

        // Eager load relationships for instant response
        $msg->load(['user.role', 'attachments', 'reactions.user', 'replies.user']);

        return response()->json([
            'success' => true,
            'message' => $msg,
            'html'    => view('workspace.partials.message_card', [
                'msg'         => $msg,
                'currentUser' => $currentUser,
            ])->render(),
        ]);
    }

    /**
     * Poll messages for real-time updates without page reload
     */
    public function poll(Request $request)
    {
        $currentUser = $this->getCurrentUser();
        $workspaceId = $request->query('workspace_id');
        $channelId   = $request->query('channel_id');
        $convId      = $request->query('conversation_id');
        $lastId      = (int) $request->query('last_id', 0);
        $threadId    = $request->query('thread_id');

        $query = WorkspaceMessage::where('workspace_id', $workspaceId)
            ->where('id', '>', $lastId)
            ->whereNull('parent_id')
            ->with(['user.role', 'attachments', 'reactions.user', 'replies.user']);

        if ($channelId) {
            $query->where('channel_id', $channelId);
        } elseif ($convId) {
            $query->where('conversation_id', $convId);
        } else {
            return response()->json(['messages' => []]);
        }

        $newMessages = $query->orderBy('created_at', 'asc')->get();

        $rendered = [];
        foreach ($newMessages as $msg) {
            $rendered[] = [
                'id'            => $msg->id,
                'sender_id'     => $msg->user_id,
                'sender_name'   => $msg->user?->name ?? 'Member',
                'preview'       => Str::limit($msg->message ?: 'Sent an attachment', 60),
                'sender_avatar' => $msg->user?->profile_pic ? asset('uploads/profiles/' . $msg->user->profile_pic) : asset('image.png'),
                'html'          => view('workspace.partials.message_card', [
                    'msg'         => $msg,
                    'currentUser' => $currentUser,
                ])->render(),
            ];
        }

        // If a thread drawer is open, also poll thread replies
        $threadReplies = [];
        if ($threadId) {
            $lastThreadReplyId = (int) $request->query('last_thread_reply_id', 0);
            $replies = WorkspaceMessage::where('parent_id', $threadId)
                ->where('id', '>', $lastThreadReplyId)
                ->with(['user.role', 'attachments', 'reactions.user'])
                ->orderBy('created_at', 'asc')
                ->get();

            foreach ($replies as $r) {
                $threadReplies[] = [
                    'id'   => $r->id,
                    'html' => view('workspace.partials.thread_reply_card', [
                        'reply'       => $r,
                        'currentUser' => $currentUser,
                    ])->render(),
                ];
            }
        }

        return response()->json([
            'messages'       => $rendered,
            'thread_replies' => $threadReplies,
        ]);
    }

    /**
     * Get replies for a thread
     */
    public function replies(Request $request, $messageId)
    {
        $currentUser = $this->getCurrentUser();
        $parent = WorkspaceMessage::with(['user.role', 'attachments', 'reactions.user'])
            ->findOrFail($messageId);

        $replies = WorkspaceMessage::where('parent_id', $messageId)
            ->with(['user.role', 'attachments', 'reactions.user'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success'     => true,
            'parent'      => $parent,
            'parent_html' => view('workspace.partials.message_card', [
                'msg'         => $parent,
                'currentUser' => $currentUser,
                'isThread'    => true,
            ])->render(),
            'replies'     => $replies->map(fn($r) => [
                'id'   => $r->id,
                'html' => view('workspace.partials.thread_reply_card', [
                    'reply'       => $r,
                    'currentUser' => $currentUser,
                ])->render(),
            ]),
        ]);
    }

    /**
     * Toggle Emoji Reaction on a message
     */
    public function toggleReaction(Request $request, $messageId)
    {
        $currentUser = $this->getCurrentUser();
        $emoji = trim($request->input('emoji', '👍'));

        $reaction = WorkspaceMessageReaction::where('message_id', $messageId)
            ->where('user_id', $currentUser->id)
            ->where('emoji', $emoji)
            ->first();

        if ($reaction) {
            $reaction->delete();
        } else {
            WorkspaceMessageReaction::create([
                'message_id' => $messageId,
                'user_id'    => $currentUser->id,
                'emoji'      => $emoji,
            ]);
        }

        // Return updated tallies
        $tallies = WorkspaceMessageReaction::where('message_id', $messageId)
            ->selectRaw('emoji, count(*) as count, max(case when user_id = ? then 1 else 0 end) as user_reacted', [$currentUser->id])
            ->groupBy('emoji')
            ->get();

        return response()->json([
            'success' => true,
            'tallies' => $tallies,
        ]);
    }

    /**
     * Search messages and attachments across workspace
     */
    public function search(Request $request)
    {
        $workspaceId = $request->query('workspace_id');
        $q = trim($request->query('q', ''));

        if (empty($q)) {
            return response()->json(['results' => []]);
        }

        $results = WorkspaceMessage::where('workspace_id', $workspaceId)
            ->where(function ($query) use ($q) {
                $query->where('message', 'like', "%{$q}%")
                      ->orWhereHas('attachments', function ($aq) use ($q) {
                          $aq->where('original_name', 'like', "%{$q}%");
                      });
            })
            ->with(['user', 'channel', 'conversation', 'attachments'])
            ->latest()
            ->take(20)
            ->get();

        return response()->json(['results' => $results]);
    }

    /**
     * Delete message (Soft delete)
     */
    public function destroy(Request $request, $messageId)
    {
        $currentUser = $this->getCurrentUser();
        $msg = WorkspaceMessage::findOrFail($messageId);

        // Only author or workspace admin can delete
        $workspace = Workspace::find($msg->workspace_id);
        if ($msg->user_id !== $currentUser->id && (!$workspace || !$workspace->isAdminOrOwner($currentUser->id))) {
            return response()->json(['error' => 'Permission denied.'], 403);
        }

        $msg->delete();
        return response()->json(['success' => true]);
    }
}

