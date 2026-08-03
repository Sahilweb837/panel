<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\Department;
use App\Models\User;
use App\Models\MeetingParticipant;
use App\Models\MeetingMessage;
use App\Models\MeetingFile;
use App\Models\Message;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    public function index()
    {
        $userId = session('user_id');
        $user = User::find($userId);
        $departments = Department::all();
        $staff = User::whereNotIn('role_id', [1, 2])->get();

        if ($user->role_id == 1 || $user->role_id == 2) { // Admin/SuperAdmin
            $meetings = Meeting::with('department', 'creator')->orderBy('meeting_date', 'desc')->orderBy('start_time', 'desc')->get();
            return view('meetings.index', compact('meetings', 'departments', 'staff'));
        } else {
            // Staff
            $participations = MeetingParticipant::where('user_id', $userId)->pluck('meeting_id');
            $meetings = Meeting::whereIn('id', $participations)->with('department', 'creator')->orderBy('meeting_date', 'desc')->orderBy('start_time', 'desc')->get();
            return view('meetings.staff_index', compact('meetings', 'departments', 'staff'));
        }
    }

    public function create()
    {
        $departments = Department::all();
        $staff = User::whereNotIn('role_id', [1, 2])->get();
        return view('meetings.create', compact('departments', 'staff'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'meeting_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'meeting_mode' => 'required|in:Online,Offline,Hybrid',
            'location' => 'nullable|string',
            'participants' => 'array',
        ]);

        $link = null;
        if (in_array($request->meeting_mode, ['Online', 'Hybrid'])) {
            $link = route('meetings.join', ['id' => uniqid('meet-')]);
        }

        $meeting = Meeting::create([
            'title' => $request->title,
            'description' => $request->description,
            'department_id' => $request->department_id,
            'meeting_date' => $request->meeting_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'meeting_mode' => $request->meeting_mode,
            'meeting_link' => $link,
            'location' => $request->location,
            'created_by' => session('user_id'),
            'status' => 'Scheduled',
        ]);

        if ($request->participants) {
            foreach ($request->participants as $userId) {
                MeetingParticipant::create([
                    'meeting_id' => $meeting->id,
                    'user_id' => $userId,
                    'invitation_status' => 'Pending',
                ]);

                if ($link) {
                    Message::create([
                        'sender_id' => session('user_id'),
                        'receiver_id' => $userId,
                        'subject' => 'Meeting Invitation: ' . $meeting->title,
                        'body' => "You have been invited to an online meeting.\n\nDate: " . $request->meeting_date . "\nTime: " . $request->start_time . "\nMode: " . $request->meeting_mode . "\nJoin Link: " . $link,
                        'priority' => 'normal',
                        'is_read' => false,
                    ]);
                }
            }
        }

        if ($link) {
            MeetingMessage::create([
                'meeting_id' => $meeting->id,
                'sender_id' => session('user_id'),
                'message' => 'The online meeting room is ready. You can join the video call using this link: ' . $link,
            ]);
        }

        return redirect()->route('meetings.index')->with('success', 'Meeting scheduled successfully.');
    }

    public function show(Meeting $meeting)
    {
        $meeting->load(['participants.user', 'department', 'minutes.uploader', 'messages.sender', 'files.uploader']);
        
        $userId = session('user_id');
        $isParticipant = $meeting->participants->contains('user_id', $userId) || $meeting->created_by == $userId;

        if (!$isParticipant) {
            abort(403, 'Unauthorized access to this meeting.');
        }

        return view('meetings.show', compact('meeting'));
    }

    public function storeMessage(Request $request, Meeting $meeting)
    {
        $request->validate([
            'message' => 'nullable|string',
            'attachment' => 'nullable|file|max:25600|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,zip,rar,mp4',
        ]);

        if (!$request->message && !$request->hasFile('attachment')) {
            return response()->json(['success' => false, 'error' => 'Message or attachment is required.']);
        }

        $userId = session('user_id');

        $attachmentName = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/meetings'), $attachmentName);
        }

        $msg = MeetingMessage::create([
            'meeting_id' => $meeting->id,
            'sender_id' => $userId,
            'message' => $request->message,
            'attachment' => $attachmentName,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $msg->id,
                'message' => $msg->message,
                'sender' => User::find($userId)->name,
                'sender_id' => $userId,
                'time' => $msg->created_at->format('h:i A'),
                'attachment' => $attachmentName ? asset('uploads/meetings/' . $attachmentName) : null,
            ]
        ]);
    }

    public function getMessages(Meeting $meeting)
    {
        $messages = $meeting->messages()->with('sender')->orderBy('created_at', 'asc')->get();
        return response()->json(['success' => true, 'messages' => $messages->map(function($msg) {
            return [
                'id' => $msg->id,
                'message' => $msg->message,
                'sender' => $msg->sender->name ?? 'Unknown',
                'sender_id' => $msg->sender_id,
                'time' => $msg->created_at->format('h:i A'),
                'attachment' => $msg->attachment ? asset('uploads/meetings/' . $msg->attachment) : null,
            ];
        })]);
    }

    public function storeFile(Request $request, Meeting $meeting)
    {
        $request->validate([
            'file' => 'required|file|max:25600|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,zip,rar,mp4',
        ]);

        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        $savedName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $fileSize = round($file->getSize() / 1024, 2) . ' KB';
        $fileType = $file->getClientOriginalExtension();
        
        $file->move(public_path('uploads/meetings/files'), $savedName);

        MeetingFile::create([
            'meeting_id' => $meeting->id,
            'uploaded_by' => session('user_id'),
            'file_name' => $fileName,
            'file_path' => $savedName,
            'file_size' => $fileSize,
            'file_type' => $fileType,
        ]);

        return back()->with('success', 'File uploaded successfully.');
    }

    public function updateStatus(Request $request, MeetingParticipant $participant)
    {
        if ($participant->user_id != session('user_id')) {
            abort(403);
        }

        $request->validate(['status' => 'required|in:Accepted,Declined']);
        $participant->update(['invitation_status' => $request->status]);

        return back()->with('success', 'Invitation ' . $request->status);
    }

    public function destroy(Meeting $meeting)
    {
        // Delete chat message attachments
        foreach ($meeting->messages as $message) {
            if ($message->attachment) {
                $filePath = public_path('uploads/meetings/' . $message->attachment);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }
        }

        // Delete meeting files
        foreach ($meeting->files as $file) {
            if ($file->file_path) {
                $filePath = public_path('uploads/meetings/files/' . $file->file_path);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }
        }

        $meeting->delete();

        return redirect()->route('meetings.index')->with('success', 'Meeting deleted successfully.');
    }

    public function joinMeeting($id)
    {
        $meeting = Meeting::where('meeting_link', 'like', '%' . $id)->first();
        return view('meetings.join', compact('id', 'meeting'));
    }

    public function heartbeat(Request $request, $id)
    {
        $userId = session('user_id');
        $userName = session('user_name', 'Participant');
        $peerId = $request->input('peer_id');

        if ($userId && $peerId) {
            \App\Models\MeetingActivePeer::updateOrCreate(
                [
                    'room_id' => $id,
                    'user_id' => $userId,
                ],
                [
                    'peer_id' => $peerId,
                    'user_name' => $userName,
                    'last_seen_at' => now(),
                ]
            );
        }

        // Clean up stale peers who haven't updated in 12 seconds
        \App\Models\MeetingActivePeer::where('last_seen_at', '<', now()->subSeconds(12))->delete();

        // Get all active peers for this room
        $activePeers = \App\Models\MeetingActivePeer::where('room_id', $id)
            ->where('user_id', '!=', $userId) // exclude self
            ->get(['peer_id', 'user_name', 'user_id']);

        return response()->json([
            'success' => true,
            'peers' => $activePeers
        ]);
    }
}

