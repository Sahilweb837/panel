<?php

namespace App\Http\Controllers;

use App\Models\ChannelMember;
use App\Models\Conversation;
use App\Models\ConversationMember;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceChannel;
use App\Models\WorkspaceMember;
use App\Models\WorkspaceMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WorkspaceController extends Controller
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
     * List workspaces or redirect to the active workspace.
     */
    public function index(Request $request)
    {
        $currentUser = $this->getCurrentUser();

        // Auto-seed default workspace if none exist
        if (Workspace::count() === 0) {
            $this->seedDefaultWorkspace($currentUser);
        }

        // Get workspaces where current user is a member
        $userWorkspaces = Workspace::whereHas('members', function ($q) use ($currentUser) {
            $q->where('user_id', $currentUser->id)->where('status', 'active');
        })->get();

        // If user is not yet in any workspace, auto-join the first public workspace
        if ($userWorkspaces->isEmpty()) {
            $publicWs = Workspace::where('visibility', 'public')->first();
            if ($publicWs) {
                WorkspaceMember::firstOrCreate(
                    ['workspace_id' => $publicWs->id, 'user_id' => $currentUser->id],
                    ['role' => 'member', 'status' => 'active', 'joined_at' => now()]
                );
                $userWorkspaces = collect([$publicWs]);
            }
        }

        if ($request->wantsJson()) {
            return response()->json(['workspaces' => $userWorkspaces]);
        }

        // Redirect directly to the first workspace show view for instant Slack experience
        if ($userWorkspaces->isNotEmpty() && !$request->has('list')) {
            return redirect()->route('workspace.show', $userWorkspaces->first()->slug);
        }

        $allPublicWorkspaces = Workspace::where('visibility', 'public')->get();

        return view('workspace.index', compact('currentUser', 'userWorkspaces', 'allPublicWorkspaces'));
    }

    /**
     * Show form to create a new workspace.
     */
    public function create()
    {
        $currentUser = $this->getCurrentUser();
        return view('workspace.create', compact('currentUser'));
    }

    /**
     * Store a newly created workspace.
     */
    public function store(Request $request)
    {
        $currentUser = $this->getCurrentUser();

        $validated = $request->validate([
            'name'        => 'required|string|max:150',
            'description' => 'nullable|string|max:1000',
            'visibility'  => 'required|in:public,private',
            'logo'        => 'nullable|string',
        ]);

        $slug = Str::slug($validated['name']);
        if (Workspace::where('slug', $slug)->exists()) {
            $slug .= '-' . Str::random(4);
        }

        $workspace = Workspace::create([
            'name'        => $validated['name'],
            'slug'        => $slug,
            'description' => $validated['description'] ?? null,
            'logo'        => $validated['logo'] ?? null,
            'owner_id'    => $currentUser->id,
            'visibility'  => $validated['visibility'],
        ]);

        // Add creator as owner member
        WorkspaceMember::create([
            'workspace_id' => $workspace->id,
            'user_id'      => $currentUser->id,
            'role'         => 'owner',
            'status'       => 'active',
            'joined_at'    => now(),
        ]);

        // Default channels
        $general = WorkspaceChannel::create([
            'workspace_id' => $workspace->id,
            'name'         => 'general',
            'slug'         => 'general',
            'description'  => 'General discussions and team updates',
            'type'         => 'public',
            'created_by'   => $currentUser->id,
        ]);

        ChannelMember::create([
            'channel_id' => $general->id,
            'user_id'    => $currentUser->id,
            'role'       => 'admin',
            'joined_at'  => now(),
        ]);

        WorkspaceChannel::create([
            'workspace_id' => $workspace->id,
            'name'         => 'announcements',
            'slug'         => 'announcements',
            'description'  => 'Official institute and course announcements',
            'type'         => 'announcement',
            'created_by'   => $currentUser->id,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'workspace' => $workspace]);
        }

        return redirect()->route('workspace.show', $workspace->slug)->with('success', 'Workspace created successfully!');
    }

    /**
     * Main Slack-Style LMS Workspace Interface (4-Panel Architecture)
     */
    public function show(Request $request, $workspaceParam)
    {
        $currentUser = $this->getCurrentUser();

        // Find workspace by slug or ID
        $workspace = Workspace::where('slug', $workspaceParam)
            ->orWhere('id', $workspaceParam)
            ->firstOrFail();

        // Verify membership or auto-join if public
        $member = WorkspaceMember::where('workspace_id', $workspace->id)
            ->where('user_id', $currentUser->id)
            ->first();

        if (!$member) {
            if ($workspace->visibility === 'public') {
                $member = WorkspaceMember::create([
                    'workspace_id' => $workspace->id,
                    'user_id'      => $currentUser->id,
                    'role'         => 'member',
                    'status'       => 'active',
                    'joined_at'    => now(),
                ]);
            } else {
                return redirect()->route('workspace.index')->with('error', 'You must be invited to join this private workspace.');
            }
        }

        // 1. All workspaces for left sidebar workspace switcher strip
        $userWorkspaces = Workspace::whereHas('members', function ($q) use ($currentUser) {
            $q->where('user_id', $currentUser->id)->where('status', 'active');
        })->get();

        // 2. Channels in this workspace (Public, Announcement, and Private if member)
        $channels = WorkspaceChannel::where('workspace_id', $workspace->id)
            ->where('is_archived', false)
            ->where(function ($q) use ($currentUser) {
                $q->where('type', 'public')
                  ->orWhere('type', 'announcement')
                  ->orWhereHas('members', function ($mq) use ($currentUser) {
                      $mq->where('user_id', $currentUser->id);
                  });
            })
            ->withCount('members')
            ->orderBy('name', 'asc')
            ->get();

        // 3. Direct & Group Conversations in this workspace
        $conversations = Conversation::where('workspace_id', $workspace->id)
            ->whereHas('members', function ($q) use ($currentUser) {
                $q->where('user_id', $currentUser->id);
            })
            ->with(['users', 'members'])
            ->latest('updated_at')
            ->get();

        // 4. Resolve Active Channel or Active Conversation
        $activeChannel = null;
        $activeConversation = null;

        if ($request->has('channel')) {
            $chIdentifier = $request->query('channel');
            $activeChannel = WorkspaceChannel::where('workspace_id', $workspace->id)
                ->where(function ($q) use ($chIdentifier) {
                    $q->where('slug', $chIdentifier)->orWhere('id', $chIdentifier);
                })->first();
        } elseif ($request->has('conversation')) {
            $convId = $request->query('conversation');
            $activeConversation = Conversation::where('workspace_id', $workspace->id)
                ->where('id', $convId)
                ->first();
        }

        // Default to #general if nothing specified
        if (!$activeChannel && !$activeConversation) {
            $activeChannel = $channels->firstWhere('slug', 'general') ?: $channels->first();
        }

        // 5. Fetch Messages for Active Channel or Conversation
        $messages = collect();
        if ($activeChannel) {
            $messages = WorkspaceMessage::where('workspace_id', $workspace->id)
                ->where('channel_id', $activeChannel->id)
                ->whereNull('parent_id')
                ->with(['user.role', 'attachments', 'reactions.user', 'replies.user'])
                ->orderBy('created_at', 'asc')
                ->take(60)
                ->get();
        } elseif ($activeConversation) {
            $messages = WorkspaceMessage::where('workspace_id', $workspace->id)
                ->where('conversation_id', $activeConversation->id)
                ->whereNull('parent_id')
                ->with(['user.role', 'attachments', 'reactions.user', 'replies.user'])
                ->orderBy('created_at', 'asc')
                ->take(60)
                ->get();
        }

        // 6. Active Thread message (if ?thread=id is provided)
        $activeThread = null;
        if ($request->has('thread')) {
            $activeThread = WorkspaceMessage::with(['user.role', 'attachments', 'reactions.user', 'replies.user.role', 'replies.attachments', 'replies.reactions.user'])
                ->find($request->query('thread'));
        }

        // 7. Workspace members for mentions & quick direct messages
        $workspaceMembers = User::whereHas('workspaceMemberships', function ($q) use ($workspace) {
            $q->where('workspace_id', $workspace->id)->where('status', 'active');
        })->with('role')->get();

        return view('workspace.chat', compact(
            'currentUser',
            'workspace',
            'userWorkspaces',
            'channels',
            'conversations',
            'activeChannel',
            'activeConversation',
            'messages',
            'activeThread',
            'workspaceMembers'
        ));
    }

    /**
     * Auto-seed default workspace for existing LMS
     */
    private function seedDefaultWorkspace($adminUser)
    {
        $ws = Workspace::create([
            'name'        => 'Netcoder LMS',
            'slug'        => 'netcoder-lms',
            'description' => 'Official institute workspace for educators, students, and project teams.',
            'logo'        => asset('image.png'),
            'owner_id'    => $adminUser->id,
            'visibility'  => 'public',
        ]);

        // Add admin as owner
        WorkspaceMember::create([
            'workspace_id' => $ws->id,
            'user_id'      => $adminUser->id,
            'role'         => 'owner',
            'status'       => 'active',
            'joined_at'    => now(),
        ]);

        // Default channels
        $channelsData = [
            ['name' => 'general', 'slug' => 'general', 'description' => 'General campus discussions and updates', 'type' => 'public'],
            ['name' => 'announcements', 'slug' => 'announcements', 'description' => 'Important broadcasts and academic notices', 'type' => 'announcement'],
            ['name' => 'teachers', 'slug' => 'teachers', 'description' => 'Faculty coordination and curriculum planning', 'type' => 'public'],
            ['name' => 'students', 'slug' => 'students', 'description' => 'Student lounge and peer support', 'type' => 'public'],
            ['name' => 'web-development', 'slug' => 'web-development', 'description' => 'Code reviews, debugging questions, and project showcases', 'type' => 'public'],
        ];

        foreach ($channelsData as $c) {
            $channel = WorkspaceChannel::create([
                'workspace_id' => $ws->id,
                'name'         => $c['name'],
                'slug'         => $c['slug'],
                'description'  => $c['description'],
                'type'         => $c['type'],
                'created_by'   => $adminUser->id,
            ]);

            ChannelMember::create([
                'channel_id' => $channel->id,
                'user_id'    => $adminUser->id,
                'role'       => 'admin',
                'joined_at'  => now(),
            ]);
        }

        // Add all existing users to the workspace so everybody can collaborate immediately
        $allUsers = User::limit(50)->get();
        foreach ($allUsers as $u) {
            if ($u->id !== $adminUser->id) {
                WorkspaceMember::firstOrCreate(
                    ['workspace_id' => $ws->id, 'user_id' => $u->id],
                    ['role' => 'member', 'status' => 'active', 'joined_at' => now()]
                );
            }
        }
    }
}

