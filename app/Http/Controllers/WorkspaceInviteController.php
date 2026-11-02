<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceInvitation;
use App\Models\WorkspaceMember;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WorkspaceInviteController extends Controller
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
     * Create workspace invitation
     */
    public function invite(Request $request, $workspaceParam)
    {
        $currentUser = $this->getCurrentUser();
        $workspace = Workspace::where('slug', $workspaceParam)->orWhere('id', $workspaceParam)->firstOrFail();

        if (!$workspace->hasMember($currentUser->id)) {
            return response()->json(['error' => 'You must be a member to invite others.'], 403);
        }

        $request->validate([
            'email' => 'required|email',
            'role'  => 'nullable|in:admin,member,guest',
        ]);

        $invitation = WorkspaceInvitation::create([
            'workspace_id' => $workspace->id,
            'email'        => strtolower(trim($request->email)),
            'invited_by'   => $currentUser->id,
            'token'        => Str::random(40),
            'role'         => $request->role ?? 'member',
            'expires_at'   => now()->addDays(7),
        ]);

        $inviteUrl = route('workspace.invite.accept', $invitation->token);

        return response()->json([
            'success'    => true,
            'invite_url' => $inviteUrl,
            'invitation' => $invitation,
        ]);
    }

    /**
     * Accept workspace invitation
     */
    public function accept(Request $request, $token)
    {
        $currentUser = $this->getCurrentUser();

        $invitation = WorkspaceInvitation::where('token', $token)->firstOrFail();

        if ($invitation->isExpired()) {
            return redirect()->route('workspace.index')->with('error', 'This invitation has expired.');
        }

        $workspace = Workspace::findOrFail($invitation->workspace_id);

        WorkspaceMember::firstOrCreate([
            'workspace_id' => $workspace->id,
            'user_id'      => $currentUser->id,
        ], [
            'role'      => $invitation->role ?? 'member',
            'status'    => 'active',
            'joined_at' => now(),
        ]);

        $invitation->accepted_at = now();
        $invitation->save();

        return redirect()->route('workspace.show', $workspace->slug)->with('success', "Welcome to {$workspace->name}!");
    }
}

