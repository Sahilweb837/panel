<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\ConversationMember;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\Request;

class ConversationController extends Controller
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
     * Start direct or group conversation in a workspace.
     */
    public function store(Request $request)
    {
        $currentUser = $this->getCurrentUser();

        $request->validate([
            'workspace_id'    => 'required|exists:workspaces,id',
            'type'            => 'required|in:direct,group',
            'recipient_id'    => 'nullable|required_if:type,direct|exists:users,id',
            'recipient_ids'   => 'nullable|array',
            'name'            => 'nullable|string|max:150',
        ]);

        $workspace = Workspace::findOrFail($request->workspace_id);

        if ($request->type === 'direct') {
            $recipientId = (int) $request->recipient_id;
            $conv = Conversation::findOrCreateDirect($workspace->id, $currentUser->id, $recipientId);
        } else {
            // Group conversation
            $conv = Conversation::create([
                'workspace_id' => $workspace->id,
                'type'         => 'group',
                'name'         => $request->name ?: 'Group Chat',
                'created_by'   => $currentUser->id,
            ]);

            // Add creator
            ConversationMember::create([
                'conversation_id' => $conv->id,
                'user_id'         => $currentUser->id,
                'joined_at'       => now(),
            ]);

            // Add recipients
            if (!empty($request->recipient_ids)) {
                foreach ($request->recipient_ids as $rId) {
                    if ((int)$rId !== $currentUser->id) {
                        ConversationMember::firstOrCreate([
                            'conversation_id' => $conv->id,
                            'user_id'         => (int)$rId,
                        ], ['joined_at' => now()]);
                    }
                }
            }
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'conversation' => $conv]);
        }

        return redirect()->route('workspace.show', [
            'workspace'    => $workspace->slug,
            'conversation' => $conv->id,
        ]);
    }
}

