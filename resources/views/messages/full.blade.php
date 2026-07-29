@extends('layouts.app')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    .ql-toolbar {
        border-radius: 8px 8px 0 0;
        background: #f8fafc;
        border-color: var(--border) !important;
    }
    .ql-container {
        border-radius: 0 0 8px 8px;
        border-color: var(--border) !important;
        font-family: inherit;
    }
    .ql-editor {
        min-height: 120px;
    }
</style>

@section('title', 'Netcoder SMS — Messages')
@section('page-title', 'Messages')

@section('content')
<style>
/* ── Slack-like full messages layout ── */
.sms-layout {
    display: flex;
    height: calc(100vh - 130px);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0,0,0,0.06), 0 0 0 1px rgba(0,0,0,0.04);
    border: none;
    background: var(--surface);
    font-family: 'Outfit', 'Inter', sans-serif;
    position: relative;
}

/* Sidebar Overlay for Mobile */
.sidebar-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.4);
    backdrop-filter: blur(2px);
    z-index: 998;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s ease, visibility 0.3s ease;
}
.sidebar-overlay.open {
    opacity: 1;
    visibility: visible;
}

/* Sidebar */
.sms-sidebar {
    width: 260px;
    min-width: 220px;
    background: var(--bg);
    border-right: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.sms-sidebar-header {
    padding: 16px 14px 10px;
    border-bottom: 1px solid var(--border);
}

.sms-workspace-name {
    font-weight: 800;
    font-size: 1rem;
    color: var(--text);
    display: flex;
    align-items: center;
    gap: 8px;
}

.sms-workspace-name .ws-icon {
    width: 28px; height: 28px;
    border-radius: 6px;
    background: var(--first-color);
    display: inline-flex; align-items: center; justify-content: center;
    color: #fff; font-size: 0.75rem; font-weight: 700;
}

.sms-sidebar-section {
    padding: 8px 10px 4px;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--muted);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.sms-sidebar-section button {
    background: none; border: none; color: var(--muted);
    cursor: pointer; padding: 2px 4px; border-radius: 4px;
    font-size: 0.85rem; transition: color .2s, background .2s;
}
.sms-sidebar-section button:hover { color: var(--first-color); background: rgba(255,85,50,.08); }

.sms-sidebar-list { list-style: none; margin: 0; padding: 0 6px; }
.sms-sidebar-list li a {
    display: flex; align-items: center; gap: 8px;
    padding: 8px 12px; border-radius: 10px;
    font-size: 0.88rem; font-weight: 500; color: var(--text);
    text-decoration: none; transition: all 0.2s ease;
    position: relative;
    margin-bottom: 2px;
}
.sms-sidebar-list li a:hover, .sms-sidebar-list li a.active {
    background: linear-gradient(90deg, rgba(255,85,50,.1) 0%, transparent 100%);
    color: var(--first-color);
    transform: translateX(4px);
}
.sms-sidebar-list li a .user-avatar-sm {
    width: 26px; height: 26px; border-radius: 50%;
    background: var(--first-color); color: #fff;
    font-size: 0.7rem; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.sms-sidebar-list li a .unread-dot {
    width: 8px; height: 8px; border-radius: 50%;
    background: var(--first-color); margin-left: auto; flex-shrink: 0;
}
.sms-sidebar-list li a .unread-badge {
    margin-left: auto; background: var(--first-color); color: #fff;
    font-size: 0.65rem; font-weight: 700;
    padding: 1px 6px; border-radius: 10px;
}

.sms-sidebar-scroll { overflow-y: auto; flex: 1; }

/* Main content area */
.sms-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* Channel/chat header */
.sms-main-header {
    padding: 14px 20px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: var(--surface);
}
.sms-main-header .header-title {
    font-weight: 700; font-size: 1rem; color: var(--text);
    display: flex; align-items: center; gap: 8px;
}
.sms-main-header .header-sub { font-size: 0.78rem; color: var(--muted); }

/* Content area (inbox / chat) */
.sms-content {
    flex: 1; overflow-y: auto; padding: 16px 20px;
}

/* Inbox table */
.inbox-msg-row {
    display: flex; align-items: flex-start; gap: 12px;
    padding: 12px 14px; border-radius: 12px; margin-bottom: 6px;
    cursor: pointer; transition: all 0.2s ease;
    border: 1px solid transparent;
    background: var(--surface);
}
.inbox-msg-row:hover { 
    background: var(--surface-soft); 
    border-color: rgba(0,0,0,0.05); 
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.03);
}
.inbox-msg-row.unread { background: rgba(255,85,50,.03); border-color: rgba(255,85,50,.15); }
.inbox-avatar {
    width: 40px; height: 40px; border-radius: 10px;
    background: var(--first-color); color: #fff;
    font-size: 1rem; font-weight: 700; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
}
.inbox-body { flex: 1; min-width: 0; }
.inbox-sender { font-weight: 700; font-size: 0.9rem; color: var(--text); }
.inbox-subject { font-size: 0.85rem; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.inbox-time { font-size: 0.75rem; color: var(--muted); }
.inbox-unread-tag { font-size: 0.65rem; background: var(--first-color); color: #fff; padding: 1px 6px; border-radius: 6px; font-weight: 700; }

/* Chat messages */
.chat-messages { display: flex; flex-direction: column; gap: 4px; padding-bottom: 8px; }
.chat-day-divider {
    text-align: center; margin: 12px 0 8px;
    font-size: 0.72rem; color: var(--muted); font-weight: 600;
    display: flex; align-items: center; gap: 10px;
}
.chat-day-divider::before, .chat-day-divider::after {
    content: ''; flex: 1; height: 1px; background: var(--border);
}
.chat-bubble-group { display: flex; gap: 10px; align-items: flex-end; margin-bottom: 2px; }
.chat-bubble-group.mine { flex-direction: row-reverse; }
.chat-bubble-group .avatar-sm {
    width: 28px; height: 28px; border-radius: 50%;
    background: var(--first-color); color: #fff;
    font-size: 0.7rem; font-weight: 700; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    align-self: flex-end; margin-bottom: 2px;
}
.chat-bubble {
    max-width: 65%;
    width: fit-content;
    min-width: 80px;
    padding: 10px 16px;
    border-radius: 18px;
    font-size: 0.9rem;
    line-height: 1.5;
    word-break: break-word;
    white-space: pre-wrap;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.chat-bubble.theirs {
    background: var(--surface-soft);
    border-bottom-left-radius: 4px;
    color: var(--text);
}
.chat-bubble.mine-bubble {
    background: linear-gradient(135deg, var(--first-color), #e04423);
    color: #fff;
    border-bottom-right-radius: 4px;
    box-shadow: 0 4px 12px rgba(255,85,50,0.25);
}
.chat-time { font-size: 0.65rem; color: var(--muted); margin-top: 2px; text-align: right; }
.chat-bubble-group.mine .chat-time { text-align: left; }

/* Input bar */
.sms-input-bar {
    padding: 12px 16px;
    border-top: 1px solid var(--border);
    background: var(--surface);
}
.sms-input-bar form {
    display: flex; gap: 8px; align-items: flex-end;
}
.sms-input-bar .chat-input {
    flex: 1;
    border: 1.5px solid var(--border);
    border-radius: 12px;
    padding: 10px 16px;
    font-size: 0.9rem;
    background: var(--surface-soft);
    color: var(--text);
    resize: none;
    outline: none;
    transition: border-color .2s;
    min-height: 44px; max-height: 120px;
    line-height: 1.5;
    font-family: inherit;
}
.sms-input-bar .chat-input:focus { border-color: var(--first-color); }
.sms-send-btn {
    width: 46px; height: 46px; border-radius: 14px;
    background: linear-gradient(135deg, var(--first-color), #e04423); border: none; color: #fff;
    font-size: 1rem; cursor: pointer; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    transition: all .2s;
    box-shadow: 0 4px 12px rgba(255,85,50,0.25);
}
.sms-send-btn:hover { transform: translateY(-2px) scale(1.02); box-shadow: 0 6px 16px rgba(255,85,50,0.35); }
.sms-send-btn:active { transform: scale(0.97); }

/* Empty state */
.sms-empty {
    flex: 1; display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    color: var(--muted); gap: 12px; padding: 2rem;
}
.sms-empty i { font-size: 3rem; opacity: 0.3; }
.sms-empty h5 { font-weight: 700; color: var(--text); margin: 0; }
.sms-empty p { margin: 0; font-size: 0.9rem; text-align: center; }

/* Right panel – message detail */
.sms-detail-panel {
    width: 320px; min-width: 260px;
    border-left: 1px solid var(--border);
    background: var(--bg);
    display: flex; flex-direction: column;
    overflow: hidden;
}
.sms-detail-panel .detail-header {
    padding: 14px 16px; border-bottom: 1px solid var(--border);
    font-weight: 700; font-size: 0.9rem; color: var(--text);
}
.sms-detail-panel .detail-body { flex: 1; overflow-y: auto; padding: 16px; }

/* Mode tabs */
.sms-mode-tabs {
    display: flex; gap: 0;
    border-bottom: 1px solid var(--border);
    background: var(--bg);
}
.sms-mode-tab {
    flex: 1; padding: 12px 0;
    text-align: center; font-size: 0.82rem; font-weight: 600;
    color: var(--muted); cursor: pointer;
    border-bottom: 2px solid transparent;
    transition: color .2s, border-color .2s;
    background: none; border-top: none; border-left: none; border-right: none;
}
.sms-mode-tab:hover { color: var(--text); }
.sms-mode-tab.active { color: var(--first-color); border-bottom-color: var(--first-color); }

/* Priority badges */
.prio-Normal   { background: rgba(16,185,129,.12); color: #059669; }
.prio-Important{ background: rgba(245,158,11,.12); color: #d97706; }
.prio-Urgent   { background: rgba(239,68,68,.12);  color: #dc2626; }
.prio-badge { padding: 2px 8px; border-radius: 6px; font-size: 0.7rem; font-weight: 700; }

@media (max-width: 768px) {
    .sms-sidebar { 
        position: absolute;
        z-index: 999;
        height: 100%;
        left: -260px;
        transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 4px 0 24px rgba(0,0,0,0.15);
        background: var(--surface);
    }
    .sms-sidebar.open { left: 0; }
    .sms-detail-panel { display: none; }
}
@media (max-width: 600px) {
    .sms-main-header { padding: 12px 14px; }
    .sms-content { padding: 14px; }
}
</style>

<div class="sms-layout">
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
    {{-- ── SIDEBAR ─────────────────────────────────────── --}}
    <div class="sms-sidebar" id="smsSidebar">
        <div class="sms-sidebar-header">
            <div class="sms-workspace-name">
                <span class="ws-icon">NC</span>
                Netcoder SMS
            </div>
            <div class="mt-1" style="font-size:0.75rem; color:var(--muted);">
                {{ session('user_name') }} &bull; {{ session('user_role') }}
            </div>
        </div>

        <div class="sms-sidebar-scroll">
            {{-- Views --}}
            <div class="sms-sidebar-section">
                <span>Navigation</span>
            </div>
            <ul class="sms-sidebar-list">
                <li>
                    <a href="{{ route('messages.full') }}" class="{{ !request('view') && !request('chat_user') ? 'active' : '' }}">
                        <i class="fas fa-inbox" style="width:18px;"></i>
                        Inbox
                        @if($unreadCount > 0)
                            <span class="unread-badge">{{ $unreadCount }}</span>
                        @endif
                    </a>
                </li>
                <li>
                    <a href="{{ route('messages.full', ['view' => 'sent']) }}" class="{{ request('view') === 'sent' ? 'active' : '' }}">
                        <i class="fas fa-paper-plane" style="width:18px;"></i>
                        Sent
                    </a>
                </li>
            </ul>

            {{-- Direct Messages --}}
            <div class="sms-sidebar-section">
                <span>Direct Messages</span>
                <button data-bs-toggle="modal" data-bs-target="#composeModal" title="New Message">
                    <i class="fas fa-edit"></i>
                </button>
            </div>
            <ul class="sms-sidebar-list" id="dmList">
                @forelse($recipients as $user)
                    @php
                        $unreadFromUser = $inboxMessages->where('sender_id', $user->id)->where('is_read', false)->count();
                    @endphp
                    <li>
                        <a href="{{ route('messages.full', ['chat_user' => $user->id]) }}"
                           class="{{ request('chat_user') == $user->id ? 'active' : '' }}">
                            <div class="user-avatar-sm" style="background: {{ ['#ff5532','#10b981','#3b82f6','#8b5cf6','#f59e0b'][crc32($user->name) % 5] }}">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <span class="text-truncate">{{ $user->name }}</span>
                            @if($unreadFromUser > 0)
                                <span class="unread-badge">{{ $unreadFromUser }}</span>
                            @endif
                        </a>
                    </li>
                @empty
                    <li><span style="padding:8px 12px; font-size:0.8rem; color:var(--muted); display:block;">No contacts</span></li>
                @endforelse
            </ul>
        </div>
    </div>

    {{-- ── MAIN AREA ─────────────────────────────────────── --}}
    <div class="sms-main">

        @if(request('chat_user') && $selectedChatUser)
            {{-- ── CHAT VIEW ── --}}
            <div class="sms-main-header">
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-light d-md-none p-1 me-1 border-0 bg-transparent text-muted shadow-none" onclick="toggleSidebar()">
                        <i class="fas fa-bars fa-lg"></i>
                    </button>
                    <div class="header-title">
                        @if($selectedChatUser->profile_pic)
                            <img src="{{ asset('uploads/profiles/'.$selectedChatUser->profile_pic) }}" class="user-avatar-sm" style="width:32px;height:32px;border-radius:50%;object-fit:cover;">
                        @else
                            <div class="user-avatar-sm" style="width:32px;height:32px;font-size:0.9rem;border-radius:50%;background:var(--first-color);color:#fff;display:flex;align-items:center;justify-content:center;">
                                {{ strtoupper(substr($selectedChatUser->name, 0, 1)) }}
                            </div>
                        @endif
                        {{ $selectedChatUser->name }}
                        <span style="background:rgba(16,185,129,.12);color:#059669;padding:2px 8px;border-radius:6px;font-size:0.72rem;font-weight:700;">
                            {{ $selectedChatUser->role?->role_name ?? 'User' }}
                        </span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="button button-success py-1 px-3" style="font-size:0.8rem;" onclick="startVideoCall()">
                        <i class="fas fa-video me-1"></i> Video Call
                    </button>
                    <a href="{{ route('messages.full') }}" class="button button-secondary py-1 px-3" style="font-size:0.8rem;">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>

            <div class="sms-content" id="chatScrollArea">
                <div class="chat-messages" id="chatMessages">
                    @php $lastDate = null; @endphp
                    @forelse($chatMessages as $msg)
                        @php
                            $msgDate = $msg->created_at->toDateString();
                            $isMine = $msg->sender_id == session('user_id');
                        @endphp
                        @if($msgDate !== $lastDate)
                            <div class="chat-day-divider">
                                {{ \Carbon\Carbon::parse($msgDate)->isToday() ? 'Today' : \Carbon\Carbon::parse($msgDate)->format('M d, Y') }}
                            </div>
                            @php $lastDate = $msgDate; @endphp
                        @endif
                        <div class="chat-bubble-group {{ $isMine ? 'mine' : '' }}" data-msg-id="{{ $msg->id }}">
                            @if(!$isMine)
                                @if($selectedChatUser->profile_pic)
                                    <img src="{{ asset('uploads/profiles/'.$selectedChatUser->profile_pic) }}" class="avatar-sm" style="object-fit:cover;">
                                @else
                                    <div class="avatar-sm" style="background:{{ ['#ff5532','#10b981','#3b82f6','#8b5cf6','#f59e0b'][crc32($selectedChatUser->name) % 5] }}">
                                        {{ strtoupper(substr($selectedChatUser->name, 0, 1)) }}
                                    </div>
                                @endif
                            @endif
                            <div>
                                <div class="chat-bubble {{ $isMine ? 'mine-bubble' : 'theirs' }}">
                                    @if($msg->body === '[VIDEO_CALL_INVITE]')
                                        <div class="text-center">
                                            <i class="fas fa-video fa-2x mb-2 {{ $isMine ? 'text-white' : 'text-success' }}"></i><br>
                                            <button class="button {{ $isMine ? 'button-light' : 'button-success' }} btn-sm" data-bs-toggle="modal" data-bs-target="#jitsiModal">
                                                Join Video Call
                                            </button>
                                        </div>
                                    @else
                                        {{ $msg->body }}
                                    @endif
                                    @if($msg->attachment)
                                        <div class="mt-2 pt-2" style="border-top:1px solid rgba(255,255,255,0.2);">
                                            <a href="{{ asset('uploads/messages/'.$msg->attachment) }}" target="_blank" class="text-decoration-none" style="font-size:0.8rem; color:inherit;">
                                                <i class="fas fa-paperclip me-1"></i> View Attachment
                                            </a>
                                        </div>
                                    @endif
                                </div>
                                <div class="chat-time">{{ $msg->created_at->format('h:i A') }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="sms-empty" style="height:300px;">
                            <i class="fas fa-comments"></i>
                            <h5>Start the conversation</h5>
                            <p>Send a message to {{ $selectedChatUser->name }}</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="sms-input-bar">
                <form id="chatForm" action="{{ route('messages.chat.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="receiver_id" value="{{ $selectedChatUser->id }}">
                    <label class="btn btn-light mb-0 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; border-radius: 12px; cursor: pointer; border: 1.5px solid var(--border);">
                        <i class="fas fa-paperclip text-muted"></i>
                        <input type="file" name="attachment" style="display: none;" onchange="document.getElementById('chatInput').placeholder = this.files[0] ? this.files[0].name + ' selected...' : 'Message {{ $selectedChatUser->name }}...'">
                    </label>
                    <textarea name="body" id="chatInput" rows="1" class="chat-input" placeholder="Message {{ $selectedChatUser->name }}..." required></textarea>
                    <button type="submit" class="sms-send-btn"><i class="fas fa-paper-plane"></i></button>
                </form>
            </div>

        @elseif(request('view') === 'sent')
            {{-- ── SENT VIEW ── --}}
            <div class="sms-main-header">
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-light d-md-none p-1 me-1 border-0 bg-transparent text-muted shadow-none" onclick="toggleSidebar()">
                        <i class="fas fa-bars fa-lg"></i>
                    </button>
                    <div>
                        <div class="header-title"><i class="fas fa-paper-plane text-muted me-2"></i>Sent Items</div>
                        <div class="header-sub">{{ $sentMessages->count() }} messages sent</div>
                    </div>
                </div>
                <button class="button button-primary py-1 px-3" style="font-size:0.8rem;" data-bs-toggle="modal" data-bs-target="#composeModal">
                    <i class="fas fa-edit me-1"></i> Compose
                </button>
            </div>
            <div class="sms-content">
                @forelse($sentMessages as $msg)
                    <div class="inbox-msg-row">
                        <div class="inbox-avatar" style="background:{{ ['#10b981','#3b82f6','#8b5cf6','#f59e0b','#ff5532'][crc32($msg->receiver?->name ?? 'All') % 5] }}">
                            {{ strtoupper(substr($msg->receiver?->name ?? $msg->receiver_role ?? 'A', 0, 1)) }}
                        </div>
                        <div class="inbox-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="inbox-sender">
                                    To: {{ $msg->receiver?->name ?? '📢 '.ucfirst($msg->receiver_role ?? 'All') }}
                                </div>
                                <span class="inbox-time">{{ $msg->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="inbox-subject">{{ $msg->subject }}</div>
                            <div style="font-size:0.78rem;color:var(--muted);margin-top:2px;">
                                <span class="prio-badge prio-{{ $msg->priority }}">{{ $msg->priority }}</span>
                            </div>
                        </div>
                        <form action="{{ route('messages.destroy', $msg->id) }}" method="POST" class="ms-2">
                            @csrf @method('DELETE')
                            <button type="submit" class="button button-danger py-1 px-2" style="font-size:0.75rem;" onclick="return confirm('Delete?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="sms-empty">
                        <i class="fas fa-paper-plane"></i>
                        <h5>No sent messages</h5>
                        <p>Compose your first message</p>
                    </div>
                @endforelse
            </div>

        @else
            {{-- ── INBOX VIEW ── --}}
            <div class="sms-main-header">
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-light d-md-none p-1 me-1 border-0 bg-transparent text-muted shadow-none" onclick="toggleSidebar()">
                        <i class="fas fa-bars fa-lg"></i>
                    </button>
                    <div>
                        <div class="header-title">
                            <i class="fas fa-inbox text-muted me-2"></i>Inbox
                            @if($unreadCount > 0)
                                <span class="prio-badge prio-Urgent">{{ $unreadCount }} unread</span>
                            @endif
                        </div>
                        <div class="header-sub">{{ $inboxMessages->count() }} messages</div>
                    </div>
                </div>
                <button class="button button-primary py-1 px-3" style="font-size:0.8rem;" data-bs-toggle="modal" data-bs-target="#composeModal">
                    <i class="fas fa-edit me-1"></i> Compose
                </button>
            </div>

            <div class="sms-content">
                @forelse($inboxMessages as $msg)
                    <div class="inbox-msg-row {{ !$msg->is_read ? 'unread' : '' }}"
                         onclick="openInboxMsg({{ $msg->id }}, '{{ addslashes($msg->subject) }}', '{{ addslashes($msg->sender?->name ?? 'System') }}', '{{ $msg->priority }}', '{{ $msg->created_at->format('M d, Y h:i A') }}', document.getElementById('msg-body-{{ $msg->id }}').innerHTML, '{{ $msg->attachment ? asset('uploads/messages/'.$msg->attachment) : '' }}')"
                         style="cursor:pointer;">
                        <div class="inbox-avatar" style="background:{{ ['#ff5532','#10b981','#3b82f6','#8b5cf6','#f59e0b'][crc32($msg->sender?->name ?? 'S') % 5] }}">
                            {{ strtoupper(substr($msg->sender?->name ?? 'S', 0, 1)) }}
                        </div>
                        <div class="inbox-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="inbox-sender d-flex gap-2 align-items-center">
                                    {{ $msg->sender?->name ?? 'System' }}
                                    @if(!$msg->is_read)
                                        <span class="inbox-unread-tag">NEW</span>
                                    @endif
                                </div>
                                <span class="inbox-time">{{ $msg->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="inbox-subject fw-semibold">{{ $msg->subject }}
                                @if($msg->attachment) <i class="fas fa-paperclip ms-1 text-primary"></i> @endif
                            </div>
                            <div style="font-size:0.78rem;color:var(--muted);margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                {!! Str::limit(strip_tags($msg->body), 80) !!}
                            </div>
                        </div>
                        {{-- hidden body for JS --}}
                        <div id="msg-body-{{ $msg->id }}" style="display:none;">{{ $msg->body }}</div>
                    </div>
                @empty
                    <div class="sms-empty">
                        <i class="fas fa-inbox"></i>
                        <h5>Inbox is empty</h5>
                        <p>Messages sent to you will appear here</p>
                    </div>
                @endforelse
            </div>
        @endif
    </div>

    {{-- ── RIGHT DETAIL PANEL ── --}}
    <div class="sms-detail-panel" id="detailPanel">
        <div class="detail-header d-flex justify-content-between align-items-center">
            <span id="detailPanelTitle"><i class="fas fa-info-circle text-muted me-2"></i>Details</span>
            <button onclick="closeDetail()" style="background:none;border:none;color:var(--muted);cursor:pointer;font-size:1rem;"><i class="fas fa-times"></i></button>
        </div>
        <div class="detail-body" id="detailBody">
            <div class="sms-empty">
                <i class="fas fa-mouse-pointer"></i>
                <p>Click a message to read it</p>
            </div>
        </div>
    </div>
</div>

{{-- ── COMPOSE MODAL ── --}}
<div class="modal fade" id="composeModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:18px;">
            <div class="modal-header text-white" style="background:linear-gradient(135deg,#ff5532,#e04423);border-radius:18px 18px 0 0;">
                <h6 class="modal-title fw-bold mb-0"><i class="fas fa-edit me-2"></i>New Message</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="composeForm" action="{{ route('messages.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Send To</label>
                            <select name="recipient_type" id="recipientType" class="form-input" required>
                                <option value="user">Specific Person</option>
                                <option value="role">Broadcast to Role</option>
                            </select>
                        </div>

                        <div class="col-md-6" id="userSelectBox">
                            <label class="form-label fw-bold small">Recipient</label>
                            <select name="receiver_id" class="form-input" required>
                                <option value="">-- Choose Person --</option>
                                @foreach($recipients as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->role?->role_name ?? 'User' }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6" id="roleSelectBox" style="display:none;">
                            <label class="form-label fw-bold small">Broadcast Target</label>
                            <select name="receiver_role" class="form-input">
                                <option value="all">📢 All System Users</option>
                                <option value="staff">👔 All Staff</option>
                                <option value="student">🎓 All Students</option>
                                <option value="admin">🛡️ All Admins</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Priority</label>
                            <select name="priority" class="form-input" required>
                                <option value="Normal">Normal</option>
                                <option value="Important">⚡ Important</option>
                                <option value="Urgent">🚨 Urgent</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small">Subject</label>
                            <input type="text" name="subject" class="form-input" placeholder="Subject..." required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">Message</label>
                            <input type="hidden" name="body" id="full_message_body">
                            <div id="full_quill_editor" style="height: 150px; background: #fff; border-radius: 0 0 8px 8px; border: 1px solid var(--border);"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">Attachment (Optional)</label>
                            <input type="file" name="attachment" class="form-control" accept="image/*,.pdf,.doc,.docx,.zip">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="button button-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="composeSendBtn" class="button button-primary px-4">
                        <i class="fas fa-paper-plane me-2"></i>Send Message
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── MESSAGE DETAIL MODAL (mobile) ── --}}
<div class="modal fade" id="msgDetailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:18px;">
            <div class="modal-header">
                <h6 class="modal-title fw-bold" id="modalSubject"></h6>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3 p-3 rounded-3 border bg-light" style="font-size:0.82rem;">
                    <div><strong>From:</strong> <span id="modalSender"></span></div>
                    <div class="text-end">
                        <span id="modalPriority" class="prio-badge"></span>
                        <small id="modalDate" class="text-muted d-block mt-1"></small>
                    </div>
                </div>
                <div id="modalBody" class="p-3 border rounded-3 bg-white" style="min-height:100px;white-space:pre-wrap;font-size:0.95rem;"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
const CSRF = '{{ csrf_token() }}';
@if(request('chat_user') && $selectedChatUser)
const CHAT_USER_ID = {{ $selectedChatUser->id }};
@endif

document.addEventListener('DOMContentLoaded', function () {

    /* ─── Initialize Quill Editor for Compose Modal ─── */
    var fullQuill = null;
    if (document.getElementById('full_quill_editor')) {
        fullQuill = new Quill('#full_quill_editor', {
            theme: 'snow',
            placeholder: 'Write your message details here...',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline', 'strike'],
                    ['blockquote', 'code-block'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'script': 'sub'}, { 'script': 'super' }],
                    [{ 'indent': '-1'}, { 'indent': '+1' }],
                    [{ 'direction': 'rtl' }],
                    [{ 'size': ['small', false, 'large', 'huge'] }],
                    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'align': [] }],
                    ['link', 'image', 'video'],
                    ['clean']
                ]
            }
        });
    }

    /* ─── Compose form AJAX ─── */
    const composeForm = document.getElementById('composeForm');
    const recipType   = document.getElementById('recipientType');
    if (composeForm) {
        if (recipType) {
            recipType.addEventListener('change', function () {
                document.getElementById('userSelectBox').style.display = this.value === 'role' ? 'none' : '';
                const rb = document.getElementById('roleSelectBox');
                if (rb) rb.style.display = this.value === 'role' ? '' : 'none';
                document.querySelector('[name="receiver_id"]').required = this.value !== 'role';
            });
        }

        composeForm.addEventListener('submit', function (e) {
            e.preventDefault();
            if(fullQuill) {
                document.getElementById('full_message_body').value = fullQuill.root.innerHTML;
            }
            const btn = document.getElementById('composeSendBtn');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending…'; btn.disabled = true;
            fetch(composeForm.action, {
                method: 'POST', body: new FormData(composeForm),
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            }).then(r => r.json()).then(d => {
                btn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Send Message'; btn.disabled = false;
                if (d.success) {
                    bootstrap.Modal.getInstance(document.getElementById('composeModal')).hide();
                    composeForm.reset();
                    if(fullQuill) fullQuill.setContents([]);
                    showToast('Message sent!', 'success');
                }
            }).catch(() => { btn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Send Message'; btn.disabled = false; });
        });
    }

    /* ─── Chat form AJAX ─── */
    const chatForm = document.getElementById('chatForm');
    const chatInput = document.getElementById('chatInput');
    const chatMessages = document.getElementById('chatMessages');
    const scrollArea = document.getElementById('chatScrollArea');

    if (chatInput) {
        // Auto-resize textarea
        chatInput.addEventListener('input', function () {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });
        // Send on Enter (Shift+Enter = newline)
        chatInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                chatForm.dispatchEvent(new Event('submit'));
            }
        });
    }

    if (chatForm) {
        scrollToBottom();

        chatForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const body = chatInput.value.trim();
            if (!body) return;
            const formData = new FormData(chatForm);
            chatInput.value = ''; chatInput.style.height = 'auto';

            fetch(chatForm.action, {
                method: 'POST', body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            }).then(r => r.json()).then(d => {
                if (d.success) appendBubble(d.data, true);
            });
        });

        // Poll for new messages every 3 seconds
        let lastMsgId = getLastMsgId();
        setInterval(() => {
            fetch(`/api/messages/poll/${CHAT_USER_ID}?last_id=${lastMsgId}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            }).then(r => r.json()).then(d => {
                if (d.messages && d.messages.length) {
                    d.messages.forEach(msg => {
                        if (!msg.is_mine) appendBubble(msg, false);
                        if (msg.id > lastMsgId) lastMsgId = msg.id;
                    });
                }
            });
        }, 3000);
    }

    /* ─── Unread badge polling (global) ─── */
    @if(!request('chat_user'))
    setInterval(() => {
        fetch('/api/messages/unread-count', { headers: { 'X-Requested-With': 'XMLHttpRequest' }})
            .then(r => r.json()).then(d => {
                const badge = document.getElementById('globalUnreadBadge');
                if (badge) badge.textContent = d.count > 0 ? d.count : '';
            });
    }, 8000);
    @endif
});

function getLastMsgId() {
    const bubbles = document.querySelectorAll('#chatMessages [data-msg-id]');
    let last = 0;
    bubbles.forEach(b => { const id = parseInt(b.dataset.msgId); if (id > last) last = id; });
    return last;
}

function appendBubble(msg, isMine) {
    const wrap = document.getElementById('chatMessages');
    const div = document.createElement('div');
    div.className = 'chat-bubble-group' + (isMine ? ' mine' : '');
    div.dataset.msgId = msg.id;
    let attachmentHtml = '';
    if (msg.attachment) {
        attachmentHtml = `
        <div class="mt-2 pt-2" style="border-top:1px solid rgba(255,255,255,0.2);">
            <a href="${msg.attachment}" target="_blank" class="${isMine ? 'text-white' : ''} text-decoration-none" style="font-size:0.8rem;">
                <i class="fas fa-paperclip me-1"></i> View Attachment
            </a>
        </div>`;
    }

    
    let bodyHtml = '';
    if (msg.body === '[VIDEO_CALL_INVITE]') {
        bodyHtml = `
            <div class="text-center">
                <i class="fas fa-video fa-2x mb-2 ${isMine ? 'text-white' : 'text-success'}"></i><br>
                <button class="button ${isMine ? 'button-light' : 'button-success'} btn-sm" data-bs-toggle="modal" data-bs-target="#jitsiModal">
                    Join Video Call
                </button>
            </div>
        `;
    } else {
        bodyHtml = escHtml(msg.body);
    }

    if (!isMine) {
        div.innerHTML = `
            <div class="avatar-sm" style="background:var(--first-color);">${msg.sender ? msg.sender[0].toUpperCase() : '?'}</div>
            <div>
                <div class="chat-bubble theirs">${bodyHtml}${attachmentHtml}</div>
                <div class="chat-time">${msg.time}</div>
            </div>`;
    } else {
        div.innerHTML = `
            <div>
                <div class="chat-bubble mine-bubble">${bodyHtml}${attachmentHtml}</div>
                <div class="chat-time">${msg.time}</div>
            </div>`;
    }
    wrap.appendChild(div);
    scrollToBottom();
}

function scrollToBottom() {
    const area = document.getElementById('chatScrollArea');
    if (area) area.scrollTop = area.scrollHeight;
}

function escHtml(s) {
    const d = document.createElement('div');
    d.appendChild(document.createTextNode(s));
    return d.innerHTML;
}

/* ─── Inbox message detail ─── */
function openInboxMsg(id, subject, sender, priority, date, body, attachment) {
    // Mark as read
    fetch(`/messages/${id}/read`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' }
    }).then(r => r.json()).then(d => {
        if (d.success) {
            const row = document.querySelector(`[onclick*="openInboxMsg(${id},"]`);
            if (row) row.classList.remove('unread');
        }
    });

    // On desktop – right panel
    const panel = document.getElementById('detailPanel');
    const panelBody = document.getElementById('detailBody');
    const panelTitle = document.getElementById('detailPanelTitle');

    if (panelBody && window.innerWidth > 768) {
        panelTitle.innerHTML = `<i class="fas fa-envelope-open text-first me-2"></i>Message`;
        let attHtml = '';
        if(attachment && attachment !== '') {
            attHtml = `
            <div class="mt-4 pt-3 border-top border-light">
                <div style="font-size:0.7rem;font-weight:700;text-transform:uppercase;color:var(--muted);margin-bottom:8px;">Attachment</div>
                <a href="${attachment}" target="_blank" class="button button-secondary py-1 px-3" style="font-size:0.8rem;"><i class="fas fa-paperclip me-2"></i>View Attachment</a>
            </div>`;
        }
        panelBody.innerHTML = `
            <div class="mb-3">
                <div style="font-size:0.7rem;font-weight:700;text-transform:uppercase;color:var(--muted);margin-bottom:4px;">Subject</div>
                <div style="font-weight:700;font-size:1.05rem;">${subject}</div>
            </div>
            <div class="mb-4 d-flex justify-content-between align-items-end border-bottom border-light pb-3">
                <div>
                    <div style="font-size:0.7rem;font-weight:700;text-transform:uppercase;color:var(--muted);margin-bottom:4px;">From</div>
                    <div class="fw-semibold">${sender}</div>
                </div>
                <div class="text-end">
                    <div style="font-size:0.75rem;color:var(--muted);margin-bottom:4px;">${date}</div>
                    <span class="prio-badge prio-${priority}">${priority}</span>
                </div>
            </div>
            <div style="font-size:0.95rem;line-height:1.6;white-space:pre-wrap;">${body}</div>
            ${attHtml}
        `;
    } else {
        // Mobile – modal
        document.getElementById('modalSubject').innerText = subject;
        document.getElementById('modalSender').innerText = sender;
        document.getElementById('modalDate').innerText = date;
        const mp = document.getElementById('modalPriority');
        mp.className = 'prio-badge prio-' + priority; mp.innerText = priority;
        let attHtml = '';
        if(attachment && attachment !== '') {
            attHtml = `
            <div class="mt-4 pt-3 border-top border-light">
                <div style="font-size:0.7rem;font-weight:700;text-transform:uppercase;color:var(--muted);margin-bottom:8px;">Attachment</div>
                <a href="${attachment}" target="_blank" class="button button-secondary py-1 px-3" style="font-size:0.8rem;"><i class="fas fa-paperclip me-2"></i>View Attachment</a>
            </div>`;
        }
        document.getElementById('modalBody').innerHTML = body + attHtml;
        new bootstrap.Modal(document.getElementById('msgDetailModal')).show();
    }
}

function closeDetail() {
    const body = document.getElementById('detailBody');
    if (body) body.innerHTML = `<div class="sms-empty"><i class="fas fa-mouse-pointer"></i><p>Click a message to read it</p></div>`;
}

/* ─── Toast notification ─── */
function showToast(msg, type) {
    const t = document.createElement('div');
    t.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:9999;padding:12px 20px;border-radius:12px;color:#fff;font-weight:600;font-size:0.9rem;box-shadow:0 8px 24px rgba(0,0,0,.15);animation:fadeIn .3s ease;';
    t.style.background = type === 'success' ? '#10b981' : '#ef4444';
    t.innerHTML = `<i class="fas fa-${type === 'success' ? 'check' : 'times'} me-2"></i>${msg}`;
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 3500);
}

function toggleSidebar() {
    document.getElementById('smsSidebar').classList.toggle('open');
    document.getElementById('sidebarOverlay').classList.toggle('open');
}
</script>

<!-- Jitsi Video Call Modal -->
@if(request('chat_user') && $selectedChatUser)
<div class="modal fade" id="jitsiModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width: 90vw;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; overflow: hidden; background: #1a1a1a;">
            <div class="modal-header border-0 bg-dark text-white px-4 py-3">
                <h6 class="modal-title fw-bold m-0"><i class="fas fa-video me-2 text-success"></i> Video Call with {{ $selectedChatUser->name }}</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" id="closeJitsiBtn"></button>
            </div>
            <div class="modal-body p-0" style="height: 75vh;">
                <div id="meet" style="width: 100%; height: 100%;"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://meet.jit.si/external_api.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const jitsiModal = document.getElementById('jitsiModal');
    let api = null;

    if(jitsiModal) {
        jitsiModal.addEventListener('shown.bs.modal', function () {
            const domain = 'meet.jit.si';
            const roomName = 'FeesManager-Call-' + Math.min({{ session('user_id') }}, {{ $selectedChatUser->id }}) + '-' + Math.max({{ session('user_id') }}, {{ $selectedChatUser->id }});
            
            const options = {
                roomName: roomName,
                width: '100%',
                height: '100%',
                parentNode: document.querySelector('#meet'),
                userInfo: {
                    displayName: '{{ session('user_name', 'User') }}'
                },
                configOverwrite: { 
                    prejoinPageEnabled: false,
                    startWithAudioMuted: false,
                    startWithVideoMuted: false
                }
            };
            api = new JitsiMeetExternalAPI(domain, options);
        });

        jitsiModal.addEventListener('hidden.bs.modal', function () {
            if (api) {
                api.dispose();
                api = null;
            }
        });
    }
});

function startVideoCall() {
    const chatForm = document.getElementById('chatForm');
    if (!chatForm) return;

    // Send the invite message
    const formData = new FormData(chatForm);
    formData.set('body', '[VIDEO_CALL_INVITE]');
    formData.delete('attachment'); // don't send attachments with the call invite

    fetch(chatForm.action, {
        method: 'POST', body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    }).then(r => r.json()).then(d => {
        if (d.success) {
            appendBubble(d.data, true);
            // Open the modal for the caller
            const jitsiModal = new bootstrap.Modal(document.getElementById('jitsiModal'));
            jitsiModal.show();
        }
    });
}
</script>
@endif

@endsection
