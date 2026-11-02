<?php

namespace App\Http\Controllers;

use App\Models\ChatChannel;
use App\Models\ChatChannelMember;
use App\Models\ChatMessage;
use App\Models\HuddleSession;
use App\Models\HuddleParticipant;
use App\Models\HuddleSignal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NetChatController extends Controller
{
    /**
     * Get current authenticated user
     */
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
     * Main Slack-like NetChat interface
     */
    public function index(Request $request)
    {
        $currentUser = $this->getCurrentUser();
        if (!$currentUser) {
            return redirect()->route('login');
        }

        $isStandalone = $request->query('mode') === 'standalone' || $request->query('window') === 'popup';

        // 1. Fetch all top-level channels and hierarchical children
        $allChannels = ChatChannel::with(['children.children', 'members.user'])
            ->whereNull('parent_id')
            ->orderBy('order_index')
            ->get();

        // 2. Determine active channel or DM
        $activeChannelSlug = $request->query('channel');
        $activeDmUserId    = $request->query('dm');
        $activeChannel     = null;
        $activeDmUser      = null;

        if ($activeDmUserId) {
            $activeDmUser = User::with('role')->find($activeDmUserId);
        }

        if (!$activeDmUser) {
            if ($activeChannelSlug) {
                $activeChannel = ChatChannel::with(['parent.parent', 'members.user'])->where('slug', $activeChannelSlug)->first();
            }
            if (!$activeChannel) {
                // Default to UI-kit design if exists, else first channel
                $activeChannel = ChatChannel::with(['parent.parent', 'members.user'])
                    ->where('slug', 'website-v3-ui-kit-design')
                    ->first() ?? ChatChannel::first();
            }
        }

        // 3. Fetch messages for active context with pagination / lazy loading
        $messages = collect();
        $hasOlderMessages = false;
        $initialLimit = 35;

        if ($activeChannel) {
            $rawMessages = ChatMessage::with('sender.role')
                ->where('channel_id', $activeChannel->id)
                ->whereNull('parent_id')
                ->orderBy('created_at', 'desc')
                ->take($initialLimit)
                ->get();
            $messages = $rawMessages->reverse()->values();

            if ($messages->isNotEmpty()) {
                $oldestLoadedId = $messages->first()->id;
                $hasOlderMessages = ChatMessage::where('channel_id', $activeChannel->id)
                    ->whereNull('parent_id')
                    ->where('id', '<', $oldestLoadedId)
                    ->exists();
            }
        } elseif ($activeDmUser) {
            $currentId = $currentUser->id;
            $dmId = $activeDmUser->id;
            $rawMessages = ChatMessage::with('sender.role')
                ->where(function ($q) use ($currentId, $dmId) {
                    $q->where('sender_id', $currentId)->where('receiver_id', $dmId);
                })
                ->orWhere(function ($q) use ($currentId, $dmId) {
                    $q->where('sender_id', $dmId)->where('receiver_id', $currentId);
                })
                ->orderBy('created_at', 'desc')
                ->take($initialLimit)
                ->get();
            $messages = $rawMessages->reverse()->values();

            if ($messages->isNotEmpty()) {
                $oldestLoadedId = $messages->first()->id;
                $hasOlderMessages = ChatMessage::where(function ($q) use ($currentId, $dmId) {
                    $q->where('sender_id', $currentId)->where('receiver_id', $dmId);
                })->orWhere(function ($q) use ($currentId, $dmId) {
                    $q->where('sender_id', $dmId)->where('receiver_id', $currentId);
                })->where('id', '<', $oldestLoadedId)->exists();
            }
        }

        // 4. Fetch all workspace users for Direct Messages and Mentions
        $workspaceUsers = User::with('role')
            ->where('id', '!=', $currentUser->id)
            ->where('status', true)
            ->orderBy('name')
            ->get();

        // 5. Favorites: Sophia Wilson (DM) and #front-end (Channel)
        $favoriteUser = User::where('name', 'like', '%Sophia%')->first();
        $favoriteChannel = ChatChannel::where('slug', 'front-end')->first();

        // 6. Active Huddle check
        $activeHuddle = null;
        if ($activeChannel) {
            $activeHuddle = HuddleSession::with('participants')
                ->where('channel_id', $activeChannel->id)
                ->where('status', 'active')
                ->first();
        }

        // 7. Right sidebar details
        $creator = null;
        if ($activeChannel && $activeChannel->creator_id) {
            $creator = User::find($activeChannel->creator_id);
        }
        if (!$creator && $activeChannel) {
            $creator = User::first();
        }

        // Channel members
        $channelMembers = collect();
        if ($activeChannel) {
            $channelMembers = ChatChannelMember::with('user')
                ->where('channel_id', $activeChannel->id)
                ->get();
            if ($channelMembers->isEmpty()) {
                // Return some workspace users as fallback
                $channelMembers = $workspaceUsers->take(5);
            }
        }

        return view('messages.netchat', compact(
            'currentUser',
            'isStandalone',
            'allChannels',
            'activeChannel',
            'activeDmUser',
            'messages',
            'hasOlderMessages',
            'workspaceUsers',
            'favoriteUser',
            'favoriteChannel',
            'activeHuddle',
            'creator',
            'channelMembers'
        ));
    }

    /**
     * Lazy load older chat messages for infinite scrolling history
     */
    public function loadOlderMessages(Request $request)
    {
        $currentUser = $this->getCurrentUser();
        $beforeId    = (int) $request->query('before_id');
        $channelId   = $request->query('channel_id');
        $dmUserId    = $request->query('dm_user_id');
        $limit       = (int) $request->query('limit', 25);

        if (!$beforeId) {
            return response()->json(['messages' => [], 'has_more' => false]);
        }

        $query = ChatMessage::with('sender.role')->where('id', '<', $beforeId);

        if ($channelId) {
            $query->where('channel_id', $channelId)->whereNull('parent_id');
        } elseif ($dmUserId) {
            $currentId = $currentUser->id;
            $query->where(function ($q) use ($currentId, $dmUserId) {
                $q->where('sender_id', $currentId)->where('receiver_id', $dmUserId);
            })->orWhere(function ($q) use ($currentId, $dmUserId) {
                $q->where('sender_id', $dmUserId)->where('receiver_id', $currentId);
            });
        } else {
            return response()->json(['messages' => [], 'has_more' => false]);
        }

        $rawOlder = $query->orderBy('created_at', 'desc')->take($limit)->get();
        $olderMessages = $rawOlder->reverse()->values();

        $oldestId = $olderMessages->first()?->id;
        $hasMore = false;
        if ($oldestId) {
            $hasMore = ChatMessage::where('id', '<', $oldestId)
                ->when($channelId, fn($q) => $q->where('channel_id', $channelId)->whereNull('parent_id'))
                ->when($dmUserId, function($q) use ($currentUser, $dmUserId) {
                    $currentId = $currentUser->id;
                    $q->where(function ($sub) use ($currentId, $dmUserId) {
                        $sub->where('sender_id', $currentId)->where('receiver_id', $dmUserId);
                    })->orWhere(function ($sub) use ($currentId, $dmUserId) {
                        $sub->where('sender_id', $dmUserId)->where('receiver_id', $currentId);
                    });
                })
                ->exists();
        }

        $rendered = [];
        foreach ($olderMessages as $msg) {
            $rendered[] = [
                'id'   => $msg->id,
                'html' => view('messages.partials.single_message', [
                    'msg'         => $msg,
                    'currentUser' => $currentUser,
                ])->render(),
            ];
        }

        return response()->json([
            'success'   => true,
            'messages'  => $rendered,
            'has_more'  => $hasMore,
            'oldest_id' => $oldestId,
        ]);
    }

    /**
     * Send a new chat message
     */
    public function sendMessage(Request $request)
    {
        $currentUser = $this->getCurrentUser();
        if (!$currentUser) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'channel_id'  => 'nullable|exists:chat_channels,id',
            'receiver_id' => 'nullable|exists:users,id',
            'parent_id'   => 'nullable|exists:chat_messages,id',
            'message'     => 'nullable|string',
            'attachment'  => 'nullable|file|max:10240',
        ]);

        $messageText = trim($request->input('message') ?? '');

        // Handle attachment
        $attachments = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $uploadDir = public_path('uploads/chat_attachments');
            if (!file_exists($uploadDir)) {
                @mkdir($uploadDir, 0777, true);
            }
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $attachments = [[
                'name' => $file->getClientOriginalName(),
                'url'  => asset('uploads/chat_attachments/' . $filename),
                'size' => round($file->getSize() / 1024, 1) . ' KB',
                'type' => $file->getClientMimeType(),
            ]];
        }

        if (empty($messageText) && empty($attachments)) {
            return response()->json(['error' => 'Message or attachment required'], 422);
        }

        // Auto-detect mentions (e.g. @Sophia, @Diana T., @UX/UI)
        $mentions = [];
        preg_match_all('/@([a-zA-Z0-9_\-\.\/]+(?:\s[a-zA-Z0-9_\-\.]+)?)/', $messageText, $matches);
        if (!empty($matches[0])) {
            $mentions = array_unique($matches[0]);
        }

        // Auto-detect link preview (like Figma card or URLs)
        $linkPreview = null;
        if (preg_match('/(https?:\/\/[^\s]+)/', $messageText, $urlMatch)) {
            $url = $urlMatch[0];
            if (Str::contains($url, 'figma.com')) {
                $linkPreview = [
                    'title'       => 'Design System & UI Preview',
                    'subtitle'    => 'www.figma.com',
                    'url'         => $url,
                    'icon'        => 'figma',
                    'button_text' => 'Quick view',
                ];
            } else {
                $host = parse_url($url, PHP_URL_HOST) ?? 'website.com';
                $linkPreview = [
                    'title'       => 'Shared Resource Link',
                    'subtitle'    => $host,
                    'url'         => $url,
                    'icon'        => 'globe',
                    'button_text' => 'Open link',
                ];
            }
        }

        $chatMsg = ChatMessage::create([
            'channel_id'   => $request->channel_id,
            'receiver_id'  => $request->receiver_id,
            'sender_id'    => $currentUser->id,
            'parent_id'    => $request->parent_id,
            'message'      => $messageText,
            'type'         => $attachments ? 'file' : 'text',
            'mentions'     => $mentions ? array_values($mentions) : null,
            'attachments'  => $attachments,
            'link_preview' => $linkPreview,
            'reactions'    => [],
        ]);

        $chatMsg->load('sender.role');

        return response()->json([
            'success' => true,
            'message' => $chatMsg,
            'html'    => view('messages.partials.single_message', [
                'msg'         => $chatMsg,
                'currentUser' => $currentUser,
            ])->render(),
        ]);
    }

    /**
     * Upload and send a voice note
     */
    public function uploadVoice(Request $request)
    {
        $currentUser = $this->getCurrentUser();
        if (!$currentUser) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'audio'       => 'required|file|max:15360',
            'channel_id'  => 'nullable|exists:chat_channels,id',
            'receiver_id' => 'nullable|exists:users,id',
        ]);

        $audioFile = $request->file('audio');
        $uploadDir = public_path('uploads/chat_voice');
        if (!file_exists($uploadDir)) {
            @mkdir($uploadDir, 0777, true);
        }
        $filename = 'voice_' . time() . '_' . uniqid() . '.webm';
        $audioFile->move($uploadDir, $filename);

        $chatMsg = ChatMessage::create([
            'channel_id'  => $request->channel_id,
            'receiver_id' => $request->receiver_id,
            'sender_id'   => $currentUser->id,
            'message'     => 'Voice message (' . ($request->duration ?? '0:15') . ')',
            'type'        => 'voice',
            'voice_path'  => asset('uploads/chat_voice/' . $filename),
            'reactions'   => [],
        ]);

        $chatMsg->load('sender.role');

        return response()->json([
            'success' => true,
            'message' => $chatMsg,
            'html'    => view('messages.partials.single_message', [
                'msg'         => $chatMsg,
                'currentUser' => $currentUser,
            ])->render(),
        ]);
    }

    /**
     * Poll new messages since last ID
     */
    public function poll(Request $request)
    {
        $currentUser = $this->getCurrentUser();
        $lastId      = (int) $request->query('last_id', 0);
        $channelId   = $request->query('channel_id');
        $dmUserId    = $request->query('dm_user_id');

        $query = ChatMessage::with('sender.role')->where('id', '>', $lastId);

        if ($channelId) {
            $query->where('channel_id', $channelId);
        } elseif ($dmUserId) {
            $currentId = $currentUser->id;
            $query->where(function ($q) use ($currentId, $dmUserId) {
                $q->where('sender_id', $currentId)->where('receiver_id', $dmUserId);
            })->orWhere(function ($q) use ($currentId, $dmUserId) {
                $q->where('sender_id', $dmUserId)->where('receiver_id', $currentId);
            });
        } else {
            return response()->json(['messages' => []]);
        }

        $newMessages = $query->orderBy('created_at', 'asc')->get();

        $rendered = [];
        foreach ($newMessages as $msg) {
            $rendered[] = [
                'id'         => $msg->id,
                'html'       => view('messages.partials.single_message', [
                    'msg'         => $msg,
                    'currentUser' => $currentUser,
                ])->render(),
                'sender_id'  => $msg->sender_id,
                'created_at' => $msg->created_at->toIso8601String(),
            ];
        }

        // Also check if there is an active Huddle in this channel
        $activeHuddle = null;
        if ($channelId) {
            $huddle = HuddleSession::with('participants')->where('channel_id', $channelId)->where('status', 'active')->first();
            if ($huddle) {
                $activeHuddle = [
                    'room_id'           => $huddle->room_id,
                    'participant_count' => $huddle->participants->count(),
                    'participants'      => $huddle->participants->map(fn($p) => [
                        'user_id'          => $p->user_id,
                        'name'             => $p->user_name,
                        'is_muted'         => $p->is_muted,
                        'is_screensharing' => $p->is_screensharing,
                        'is_speaking'      => $p->is_speaking,
                    ]),
                ];
            }
        }

        return response()->json([
            'messages'     => $rendered,
            'activeHuddle' => $activeHuddle,
        ]);
    }

    /**
     * Toggle reaction on a message
     */
    public function toggleReaction(Request $request, $id)
    {
        $currentUser = $this->getCurrentUser();
        $emoji = $request->input('emoji', '👍');

        $message = ChatMessage::findOrFail($id);
        $reactions = $message->reactions ?? [];

        if (!isset($reactions[$emoji])) {
            $reactions[$emoji] = [];
        }

        $userId = $currentUser->id;
        if (in_array($userId, $reactions[$emoji])) {
            $reactions[$emoji] = array_values(array_diff($reactions[$emoji], [$userId]));
            if (empty($reactions[$emoji])) {
                unset($reactions[$emoji]);
            }
        } else {
            $reactions[$emoji][] = $userId;
        }

        $message->reactions = $reactions;
        $message->save();

        return response()->json([
            'success'   => true,
            'reactions' => $reactions,
        ]);
    }

    /**
     * Start or Join a Slack Huddle (Voice & Screen Sharing)
     */
    public function startHuddle(Request $request)
    {
        $currentUser = $this->getCurrentUser();
        $channelId   = $request->input('channel_id');
        $roomId      = $request->input('room_id') ?? ('huddle_' . ($channelId ? 'ch_' . $channelId : 'dm_' . $currentUser->id . '_' . time()));

        $channel = $channelId ? ChatChannel::find($channelId) : null;

        $session = HuddleSession::firstOrCreate(
            ['room_id' => $roomId],
            [
                'channel_id' => $channelId,
                'host_id'    => $currentUser->id,
                'title'      => 'Huddle in ' . ($channel ? '#' . $channel->name : 'Direct Chat'),
                'status'     => 'active',
                'started_at' => now(),
            ]
        );

        if ($session->status !== 'active') {
            $session->status = 'active';
            $session->ended_at = null;
            $session->save();
        }

        // Add or update participant
        $participant = HuddleParticipant::updateOrCreate(
            [
                'room_id' => $roomId,
                'user_id' => $currentUser->id,
            ],
            [
                'user_name'        => $currentUser->name,
                'avatar_url'       => $currentUser->profile_pic ? asset('uploads/profiles/' . $currentUser->profile_pic) : null,
                'is_muted'         => false,
                'is_screensharing' => false,
                'is_speaking'      => false,
                'last_seen_at'     => now(),
            ]
        );

        // Notify other participants via signal
        HuddleSignal::create([
            'room_id'      => $roomId,
            'from_user_id' => $currentUser->id,
            'to_user_id'   => null, // broadcast
            'type'         => 'user_joined',
            'payload'      => json_encode([
                'user_id'   => $currentUser->id,
                'user_name' => $currentUser->name,
            ]),
        ]);

        $participants = HuddleParticipant::where('room_id', $roomId)
            ->where('last_seen_at', '>=', now()->subSeconds(30))
            ->get();

        return response()->json([
            'success'      => true,
            'room_id'      => $roomId,
            'channel_name' => $channel ? $channel->name : 'Team Call',
            'participants' => $participants,
            'current_user' => [
                'id'   => $currentUser->id,
                'name' => $currentUser->name,
            ],
        ]);
    }

    /**
     * Leave Huddle
     */
    public function leaveHuddle(Request $request)
    {
        $currentUser = $this->getCurrentUser();
        $roomId      = $request->input('room_id');

        if ($roomId) {
            HuddleParticipant::where('room_id', $roomId)->where('user_id', $currentUser->id)->delete();

            HuddleSignal::create([
                'room_id'      => $roomId,
                'from_user_id' => $currentUser->id,
                'to_user_id'   => null,
                'type'         => 'user_left',
                'payload'      => json_encode([
                    'user_id'   => $currentUser->id,
                    'user_name' => $currentUser->name,
                ]),
            ]);

            // Check if any active participants remain
            $remaining = HuddleParticipant::where('room_id', $roomId)->count();
            if ($remaining === 0) {
                HuddleSession::where('room_id', $roomId)->update(['status' => 'ended', 'ended_at' => now()]);
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Send WebRTC signal for Huddle audio/screen share
     */
    public function postHuddleSignal(Request $request)
    {
        $currentUser = $this->getCurrentUser();
        $request->validate([
            'room_id' => 'required|string',
            'type'    => 'required|string',
        ]);

        $signal = HuddleSignal::create([
            'room_id'      => $request->room_id,
            'from_user_id' => $currentUser->id,
            'to_user_id'   => $request->to_user_id,
            'type'         => $request->type,
            'payload'      => is_array($request->payload) ? json_encode($request->payload) : $request->payload,
        ]);

        // If screen sharing state changed, update participant record
        if ($request->type === 'screen_share_state') {
            $isSharing = (bool) ($request->is_screensharing ?? false);
            HuddleParticipant::where('room_id', $request->room_id)
                ->where('user_id', $currentUser->id)
                ->update(['is_screensharing' => $isSharing, 'last_seen_at' => now()]);
        }

        // If mute state changed
        if ($request->type === 'mute_state') {
            $isMuted = (bool) ($request->is_muted ?? false);
            HuddleParticipant::where('room_id', $request->room_id)
                ->where('user_id', $currentUser->id)
                ->update(['is_muted' => $isMuted, 'last_seen_at' => now()]);
        }

        return response()->json(['success' => true, 'id' => $signal->id]);
    }

    /**
     * Poll WebRTC signals & active participant heartbeat
     */
    public function pollHuddleSignals(Request $request)
    {
        $currentUser = $this->getCurrentUser();
        $roomId      = $request->query('room_id');
        $lastId      = (int) $request->query('last_signal_id', 0);

        if (!$roomId) {
            return response()->json(['signals' => [], 'participants' => []]);
        }

        // Heartbeat
        HuddleParticipant::where('room_id', $roomId)
            ->where('user_id', $currentUser->id)
            ->update(['last_seen_at' => now()]);

        // Cleanup stale participants (> 30s)
        HuddleParticipant::where('room_id', $roomId)
            ->where('last_seen_at', '<', now()->subSeconds(30))
            ->delete();

        // Fetch new signals intended for this user or broadcast, from others
        $signals = HuddleSignal::where('room_id', $roomId)
            ->where('id', '>', $lastId)
            ->where('from_user_id', '!=', $currentUser->id)
            ->where(function ($q) use ($currentUser) {
                $q->whereNull('to_user_id')->orWhere('to_user_id', $currentUser->id);
            })
            ->orderBy('id', 'asc')
            ->get();

        $participants = HuddleParticipant::where('room_id', $roomId)->get();

        return response()->json([
            'signals'      => $signals,
            'participants' => $participants,
        ]);
    }

    /**
     * Create a new channel or subchannel
     */
    public function createChannel(Request $request)
    {
        $currentUser = $this->getCurrentUser();
        $request->validate([
            'name'      => 'required|string|max:100',
            'topic'     => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:chat_channels,id',
            'icon'      => 'nullable|string|max:10',
        ]);

        $slug = Str::slug($request->name);
        if ($request->parent_id) {
            $parent = ChatChannel::find($request->parent_id);
            if ($parent) {
                $slug = $parent->slug . '-' . $slug;
            }
        }

        // Ensure unique slug
        $baseSlug = $slug;
        $counter = 1;
        while (ChatChannel::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        $channel = ChatChannel::create([
            'name'         => $request->name,
            'slug'         => $slug,
            'parent_id'    => $request->parent_id,
            'topic'        => $request->topic ?? 'New discussion channel',
            'icon'         => $request->icon ?? ($request->parent_id ? '↳' : '#'),
            'type'         => $request->parent_id ? 'subchannel' : 'channel',
            'creator_id'   => $currentUser->id,
            'order_index'  => ChatChannel::max('order_index') + 1,
            'status_badge' => 'Active',
            'tasks_count'  => 0,
            'tags_count'   => 0,
        ]);

        // Add creator as member
        ChatChannelMember::create([
            'channel_id'  => $channel->id,
            'user_id'     => $currentUser->id,
            'role_badge'  => 'Management',
            'designation' => 'Creator',
            'is_admin'    => true,
        ]);

        return response()->json([
            'success' => true,
            'channel' => $channel,
            'redirect' => route('netchat.index', ['channel' => $channel->slug]),
        ]);
    }
}
