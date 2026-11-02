<?php

namespace App\Http\Controllers;

use App\Models\ChannelMember;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceChannel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChannelController extends Controller
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
     * Store a newly created channel in the workspace.
     */
    public function store(Request $request, $workspaceParam)
    {
        $currentUser = $this->getCurrentUser();

        $workspace = Workspace::where('slug', $workspaceParam)
            ->orWhere('id', $workspaceParam)
            ->firstOrFail();

        // Check if user is member of workspace
        if (!$workspace->hasMember($currentUser->id)) {
            return response()->json(['error' => 'You are not a member of this workspace.'], 403);
        }

        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
            'type'        => 'required|in:public,private,announcement',
        ]);

        $slug = Str::slug($validated['name']);
        if (WorkspaceChannel::where('workspace_id', $workspace->id)->where('slug', $slug)->exists()) {
            $slug .= '-' . Str::random(4);
        }

        $channel = WorkspaceChannel::create([
            'workspace_id' => $workspace->id,
            'name'         => $validated['name'],
            'slug'         => $slug,
            'description'  => $validated['description'] ?? null,
            'type'         => $validated['type'],
            'created_by'   => $currentUser->id,
        ]);

        // Add creator to channel
        ChannelMember::create([
            'channel_id' => $channel->id,
            'user_id'    => $currentUser->id,
            'role'       => 'admin',
            'joined_at'  => now(),
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'channel' => $channel]);
        }

        return redirect()->route('workspace.show', [
            'workspace' => $workspace->slug,
            'channel'   => $channel->slug,
        ])->with('success', "Channel #{$channel->name} created!");
    }

    /**
     * Show channel details or redirect to workspace with active channel
     */
    public function show(Request $request, $workspaceParam, $channelParam)
    {
        $workspace = Workspace::where('slug', $workspaceParam)->orWhere('id', $workspaceParam)->firstOrFail();
        $channel = WorkspaceChannel::where('workspace_id', $workspace->id)
            ->where(function ($q) use ($channelParam) {
                $q->where('slug', $channelParam)->orWhere('id', $channelParam);
            })->firstOrFail();

        return redirect()->route('workspace.show', [
            'workspace' => $workspace->slug,
            'channel'   => $channel->slug,
        ]);
    }

    /**
     * Join channel
     */
    public function join(Request $request, $workspaceParam, $channelId)
    {
        $currentUser = $this->getCurrentUser();
        $workspace = Workspace::where('slug', $workspaceParam)->orWhere('id', $workspaceParam)->firstOrFail();
        $channel = WorkspaceChannel::where('workspace_id', $workspace->id)->findOrFail($channelId);

        if ($channel->type === 'private') {
            return response()->json(['error' => 'Private channels require an invitation.'], 403);
        }

        ChannelMember::firstOrCreate(
            ['channel_id' => $channel->id, 'user_id' => $currentUser->id],
            ['role' => 'member', 'joined_at' => now()]
        );

        return response()->json(['success' => true, 'channel' => $channel]);
    }
}

