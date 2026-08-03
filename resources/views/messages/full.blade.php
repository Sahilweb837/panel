@extends('layouts.app')

<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    .ql-toolbar {
        border-radius: 8px 8px 0 0;
        background: #f8fafc;
        border-color: var(--border, #E2E8F0) !important;
    }
    .ql-container {
        border-radius: 0 0 8px 8px;
        border-color: var(--border, #E2E8F0) !important;
        font-family: inherit;
    }
    .ql-editor {
        min-height: 120px;
    }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    
    .message-bubble-in { border-radius: 4px 16px 16px 16px; }
    .message-bubble-out { border-radius: 16px 16px 4px 16px; }
    .active-chat-item { background-color: #f1f5f9; border-left: 4px solid var(--first-color, #b02e00); }
</style>

@section('title', 'Collaboration Hub — Messages')
@section('page-title', 'Collaboration Hub')

@section('content')
@php
    $studentRecipients = $recipients->filter(fn($u) => $u->role?->slug === 'student');
    $staffRecipients = $recipients->filter(fn($u) => in_array($u->role?->slug, ['staff', 'super-admin', 'superadmin', 'root-admin', 'admin', 'subadmin', 'sub-admin']));
@endphp

<!-- Toast Notification for Copying -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1080;">
    <div id="copyToast" class="toast align-items-center text-white bg-dark border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body py-2 px-3">
                <i class="fas fa-check-circle text-success me-2"></i><span id="toastMessage">Copied to clipboard!</span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<!-- Main Messenger Interface container -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 flex overflow-hidden w-full" style="height: calc(100vh - 160px);">
    
    <!-- LEFT PANEL: Chat List / Contacts -->
    <div class="w-80 border-r border-slate-100 flex flex-col bg-white shrink-0">
        <div class="p-4 flex items-center justify-between border-b border-slate-100">
            <h2 class="text-lg font-bold text-slate-800">Conversations</h2>
            <button class="p-1.5 rounded-full hover:bg-slate-50 transition-colors text-primary" data-bs-toggle="modal" data-bs-target="#composeModal" title="New Message">
                <i class="fas fa-edit text-lg"></i>
            </button>
        </div>
        
        <div class="flex-grow overflow-y-auto custom-scrollbar">
            <!-- Navigation links -->
            <div class="px-4 py-2 mt-2">
                <a href="{{ route('messages.full') }}" class="flex items-center justify-between px-3 py-2 rounded-lg text-sm font-semibold {{ !request('view') && !request('chat_user') ? 'bg-primary/5 text-primary' : 'text-slate-600 hover:bg-slate-50' }}">
                    <span class="flex items-center gap-2">
                        <i class="fas fa-inbox"></i> Inbox
                    </span>
                    @if($unreadCount > 0)
                        <span class="bg-primary text-white text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $unreadCount }}</span>
                    @endif
                </a>
                <a href="{{ route('messages.full', ['view' => 'sent']) }}" class="flex items-center gap-2 px-3 py-2 mt-1 rounded-lg text-sm font-semibold {{ request('view') === 'sent' ? 'bg-primary/5 text-primary' : 'text-slate-600 hover:bg-slate-50' }}">
                    <i class="fas fa-paper-plane"></i> Sent Items
                </a>
            </div>

            <!-- Direct Messages: Staff & Admins -->
            @if($staffRecipients->count() > 0)
                <div class="px-4 py-3 bg-slate-50 text-[10px] font-bold tracking-widest text-slate-400 uppercase mt-2">Faculty & Staff</div>
                <div class="space-y-0.5">
                    @foreach($staffRecipients as $user)
                        @php
                            $unreadFromUser = $inboxMessages->where('sender_id', $user->id)->where('is_read', false)->count();
                            $isCurrentChat = request('chat_user') == $user->id;
                        @endphp
                        <a href="{{ route('messages.full', ['chat_user' => $user->id]) }}" class="chat-contact-link flex items-center gap-3 p-3 cursor-pointer hover:bg-slate-50 transition-colors {{ $isCurrentChat ? 'active-chat-item' : '' }}" data-user-id="{{ $user->id }}" data-href="{{ route('messages.full', ['chat_user' => $user->id]) }}">
                            <div class="relative shrink-0">
                                @if($user->profile_pic)
                                    <img src="{{ asset('uploads/profiles/'.$user->profile_pic) }}" class="w-9 h-9 rounded-full object-cover">
                                @else
                                    <div class="w-9 h-9 text-white rounded-full flex items-center justify-center font-bold text-sm" style="background: {{ ['#ff5532','#10b981','#3b82f6','#8b5cf6','#f59e0b'][crc32($user->name) % 5] }}">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 border-2 border-white rounded-full"></div>
                            </div>
                            <div class="flex-grow overflow-hidden">
                                <div class="flex justify-between items-baseline">
                                    <h4 class="text-xs font-bold text-slate-700 truncate">{{ $user->name }}</h4>
                                    <span class="text-[9px] text-slate-400">{{ $user->role?->role_name ?? 'Staff' }}</span>
                                </div>
                                <p class="text-[11px] text-slate-400 truncate">Click to open chat session</p>
                            </div>
                            @if($unreadFromUser > 0)
                                <span class="bg-primary text-white text-[9px] w-5 h-5 flex items-center justify-center rounded-full font-bold">{{ $unreadFromUser }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            @endif

            <!-- Direct Messages: Students -->
            @if($studentRecipients->count() > 0)
                <div class="px-4 py-3 bg-slate-50 text-[10px] font-bold tracking-widest text-slate-400 uppercase mt-2">Registered Students</div>
                <div class="space-y-0.5">
                    @foreach($studentRecipients as $user)
                        @php
                            $unreadFromUser = $inboxMessages->where('sender_id', $user->id)->where('is_read', false)->count();
                            $isCurrentChat = request('chat_user') == $user->id;
                        @endphp
                        <a href="{{ route('messages.full', ['chat_user' => $user->id]) }}" class="chat-contact-link flex items-center gap-3 p-3 cursor-pointer hover:bg-slate-50 transition-colors {{ $isCurrentChat ? 'active-chat-item' : '' }}" data-user-id="{{ $user->id }}" data-href="{{ route('messages.full', ['chat_user' => $user->id]) }}">
                            <div class="relative shrink-0">
                                @if($user->profile_pic)
                                    <img src="{{ asset('uploads/profiles/'.$user->profile_pic) }}" class="w-9 h-9 rounded-full object-cover">
                                @else
                                    <div class="w-9 h-9 text-white rounded-full flex items-center justify-center font-bold text-sm" style="background: {{ ['#8b5cf6','#ff5532','#3b82f6','#10b981','#f59e0b'][crc32($user->name) % 5] }}">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 border-2 border-white rounded-full"></div>
                            </div>
                            <div class="flex-grow overflow-hidden">
                                <div class="flex justify-between items-baseline">
                                    <h4 class="text-xs font-bold text-slate-700 truncate">{{ $user->name }}</h4>
                                    <span class="text-[9px] text-slate-400">Student</span>
                                </div>
                                <p class="text-[11px] text-slate-400 truncate">Adm No: {{ $user->student?->admission_no ?? $user->username }}</p>
                            </div>
                            @if($unreadFromUser > 0)
                                <span class="bg-primary text-white text-[9px] w-5 h-5 flex items-center justify-center rounded-full font-bold">{{ $unreadFromUser }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- CENTER PANEL: Chat Canvas / Messages Area -->
    <div class="flex-grow flex flex-col bg-slate-50 relative">
        @if(request('chat_user') && $selectedChatUser)
            <!-- Chat Header -->
            <div class="h-16 border-b border-slate-100 bg-white flex items-center justify-between px-6 shrink-0 z-10 shadow-sm">
                <div class="flex items-center gap-3">
                    @if($selectedChatUser->profile_pic)
                        <img src="{{ asset('uploads/profiles/'.$selectedChatUser->profile_pic) }}" class="w-10 h-10 rounded-full object-cover">
                    @else
                        <div class="w-10 h-10 text-white rounded-full flex items-center justify-center font-bold" style="background: var(--first-color);">
                            {{ strtoupper(substr($selectedChatUser->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <h2 class="text-sm font-bold text-slate-800">{{ $selectedChatUser->name }}</h2>
                        <p class="text-[10px] text-emerald-500 font-semibold flex items-center gap-1">
                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> 
                            {{ $selectedChatUser->role?->role_name ?? 'Portal Member' }}
                        </p>
                    </div>
                </div>
                
                <div class="flex items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-success d-inline-flex align-items-center gap-1 px-3 py-1.5 rounded-lg font-semibold" style="font-size:0.8rem;" onclick="startVideoCall()">
                        <i class="fas fa-video"></i> Start Video Call
                    </button>
                    <a href="{{ route('messages.full') }}" class="btn btn-sm btn-outline-secondary px-3 py-1.5 rounded-lg font-semibold" style="font-size:0.8rem;">
                        <i class="fas fa-arrow-left"></i> Exit Chat
                    </a>
                </div>
            </div>

            <!-- Messages History Grid -->
            <div class="flex-grow overflow-y-auto p-6 space-y-6 custom-scrollbar" id="chatScrollArea">
                <div class="chat-messages" id="chatMessages">
                    @include('messages.chat_history')
                </div>
            </div>

            <!-- Messages Input Box -->
            <div class="p-4 bg-white border-t border-slate-100 shrink-0">
                <form id="chatForm" action="{{ route('messages.chat.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="receiver_id" value="{{ $selectedChatUser->id }}">
                    
                    <div class="bg-slate-50 border border-slate-200 rounded-xl focus-within:ring-2 focus-within:ring-primary/20 focus-within:border-primary transition-all p-2">
                        <textarea name="body" id="chatInput" rows="2" class="w-full bg-transparent border-0 focus:ring-0 text-sm resize-none py-2 px-3 custom-scrollbar" placeholder="Message {{ $selectedChatUser->name }}..." required></textarea>
                        
                        <div class="flex items-center justify-between mt-2 pt-2 border-t border-slate-200/50">
                            <div class="flex items-center gap-1">
                                <label class="p-2 text-slate-500 hover:text-primary hover:bg-slate-100 rounded-lg cursor-pointer transition-all" title="Attach file">
                                    <i class="fas fa-paperclip"></i>
                                    <input type="file" name="attachment" style="display: none;" onchange="document.getElementById('chatInput').placeholder = this.files[0] ? this.files[0].name + ' selected...' : 'Message {{ $selectedChatUser->name }}...'">
                                </label>
                            </div>
                            <button type="submit" class="bg-primary text-white p-2 rounded-lg flex items-center justify-center hover:brightness-110 active:scale-95 transition-all">
                                <i class="fas fa-paper-plane text-xs"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        @elseif(request('view') === 'sent')
            <!-- SENT MESSAGES VIEW -->
            <div class="h-16 border-b border-slate-100 bg-white flex items-center justify-between px-6 shrink-0 z-10 shadow-sm">
                <div>
                    <h2 class="text-sm font-bold text-slate-800"><i class="fas fa-paper-plane text-slate-400 me-2"></i>Sent Items</h2>
                    <p class="text-[10px] text-slate-400 font-semibold">{{ $sentMessages->count() }} messages sent</p>
                </div>
                <button class="btn btn-sm btn-primary px-3 py-1.5 rounded-lg font-semibold" style="font-size:0.8rem;" data-bs-toggle="modal" data-bs-target="#composeModal">
                    <i class="fas fa-plus"></i> Compose Broadcast
                </button>
            </div>
            
            <div class="flex-grow overflow-y-auto p-4 space-y-2 custom-scrollbar">
                @forelse($sentMessages as $msg)
                    <div class="bg-white p-4 rounded-xl border border-slate-100 hover:border-slate-200 shadow-sm transition-all flex items-start gap-4">
                        <div class="w-10 h-10 text-white rounded-lg flex items-center justify-center font-bold shrink-0" style="background:{{ ['#10b981','#3b82f6','#8b5cf6','#f59e0b','#ff5532'][crc32($msg->receiver?->name ?? 'All') % 5] }}">
                            {{ strtoupper(substr($msg->receiver?->name ?? $msg->receiver_role ?? 'A', 0, 1)) }}
                        </div>
                        <div class="flex-grow min-w-0">
                            <div class="flex justify-between items-baseline mb-1">
                                <span class="font-bold text-sm text-slate-800">To: {{ $msg->receiver?->name ?? '📢 '.ucfirst($msg->receiver_role ?? 'All Recipients') }}</span>
                                <span class="text-[10px] text-slate-400">{{ $msg->created_at->diffForHumans() }}</span>
                            </div>
                            <h4 class="font-semibold text-xs text-slate-600 truncate mb-1">{{ $msg->subject }}</h4>
                            <p class="text-xs text-slate-500 line-clamp-2">{!! strip_tags($msg->body) !!}</p>
                            @if($msg->attachment)
                                <div class="mt-2">
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-primary bg-primary/5 px-2 py-0.5 rounded">
                                        <i class="fas fa-paperclip"></i> Attachment Included
                                    </span>
                                </div>
                            @endif
                        </div>
                        <form action="{{ route('messages.destroy', $msg->id) }}" method="POST" class="ms-2 shrink-0">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger p-2" onclick="return confirm('Delete message from system logs?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-20 text-slate-400">
                        <i class="fas fa-paper-plane fa-3x mb-3 opacity-40"></i>
                        <h5 class="font-bold text-slate-600 text-sm">No sent messages</h5>
                        <p class="text-xs">Broadcast messages sent by you will appear here.</p>
                    </div>
                @endforelse
            </div>

        @else
            <!-- INBOX VIEW -->
            <div class="h-16 border-b border-slate-100 bg-white flex items-center justify-between px-6 shrink-0 z-10 shadow-sm">
                <div>
                    <h2 class="text-sm font-bold text-slate-800"><i class="fas fa-inbox text-slate-400 me-2"></i>Inbox Folder</h2>
                    <p class="text-[10px] text-slate-400 font-semibold">{{ $inboxMessages->count() }} messages total</p>
                </div>
                <button class="btn btn-sm btn-primary px-3 py-1.5 rounded-lg font-semibold" style="font-size:0.8rem;" data-bs-toggle="modal" data-bs-target="#composeModal">
                    <i class="fas fa-plus"></i> Compose Message
                </button>
            </div>

            <div class="flex-grow overflow-y-auto p-4 space-y-2 custom-scrollbar">
                @forelse($inboxMessages as $msg)
                    <div class="bg-white p-4 rounded-xl border border-slate-100 hover:border-slate-200 hover:shadow-md transition-all flex items-start gap-4 cursor-pointer {{ !$msg->is_read ? 'border-l-4 border-l-primary bg-primary/[0.01]' : '' }}"
                         onclick="openInboxMsg({{ $msg->id }}, '{{ addslashes($msg->subject) }}', '{{ addslashes($msg->sender?->name ?? 'System') }}', '{{ $msg->priority }}', '{{ $msg->created_at->format('M d, Y h:i A') }}', document.getElementById('msg-body-{{ $msg->id }}').innerHTML, '{{ $msg->attachment ? asset('uploads/messages/'.$msg->attachment) : '' }}')">
                        
                        <div class="w-10 h-10 text-white rounded-lg flex items-center justify-center font-bold shrink-0" style="background:{{ ['#ff5532','#10b981','#3b82f6','#8b5cf6','#f59e0b'][crc32($msg->sender?->name ?? 'System') % 5] }}">
                            {{ strtoupper(substr($msg->sender?->name ?? 'S', 0, 1)) }}
                        </div>
                        <div class="flex-grow min-w-0">
                            <div class="flex justify-between items-baseline mb-1">
                                <span class="font-bold text-sm text-slate-800">
                                    {{ $msg->sender?->name ?? 'System Admin' }}
                                    @if(!$msg->is_read)
                                        <span class="ms-2 bg-primary text-white text-[9px] font-bold px-1.5 py-0.5 rounded">NEW</span>
                                    @endif
                                </span>
                                <span class="text-[10px] text-slate-400">{{ $msg->created_at->diffForHumans() }}</span>
                            </div>
                            <h4 class="font-semibold text-xs text-slate-600 truncate mb-1">{{ $msg->subject }}</h4>
                            <p class="text-xs text-slate-500 line-clamp-2">{!! strip_tags($msg->body) !!}</p>
                            
                            <div class="mt-2 flex gap-2">
                                <span class="text-[9px] font-bold uppercase tracking-wide px-2 py-0.5 rounded {{ $msg->priority === 'Urgent' ? 'bg-rose-50 text-rose-600' : ($msg->priority === 'Important' ? 'bg-amber-50 text-amber-600' : 'bg-slate-50 text-slate-600') }}">
                                    {{ $msg->priority }}
                                </span>
                                @if($msg->attachment)
                                    <span class="inline-flex items-center gap-1 text-[9px] font-bold text-primary bg-primary/5 px-2 py-0.5 rounded">
                                        <i class="fas fa-paperclip"></i> Attached File
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div id="msg-body-{{ $msg->id }}" style="display:none;">{{ $msg->body }}</div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-20 text-slate-400">
                        <i class="fas fa-inbox fa-3x mb-3 opacity-40"></i>
                        <h5 class="font-bold text-slate-600 text-sm">Inbox is empty</h5>
                        <p class="text-xs">Broadcast notices and messages sent to you will appear here.</p>
                    </div>
                @endforelse
            </div>
        @endif
    </div>

    <!-- RIGHT PANEL: Contextual Details Bar (Visible only when in active chat view) -->
    @if(request('chat_user') && $selectedChatUser)
        <div class="w-72 border-l border-slate-100 bg-white hidden xl:flex flex-col shrink-0">
            <div class="p-6 text-center border-b border-slate-100">
                @if($selectedChatUser->profile_pic)
                    <img src="{{ asset('uploads/profiles/'.$selectedChatUser->profile_pic) }}" class="w-20 h-20 rounded-2xl object-cover mx-auto mb-3 shadow-sm border border-slate-100">
                @else
                    <div class="w-20 h-20 text-white rounded-2xl flex items-center justify-center font-bold text-3xl mx-auto mb-3 shadow-sm" style="background: var(--first-color);">
                        {{ strtoupper(substr($selectedChatUser->name, 0, 1)) }}
                    </div>
                @endif
                <h3 class="font-bold text-slate-800 text-sm">{{ $selectedChatUser->name }}</h3>
                <p class="text-xs text-slate-400 mt-1">{{ $selectedChatUser->role?->role_name ?? 'Portal Member' }}</p>
                <p class="text-[10px] text-slate-400 mt-0.5">{{ $selectedChatUser->email }}</p>
            </div>
            
            <div class="flex-grow overflow-y-auto p-6 space-y-6 custom-scrollbar">
                <div>
                    <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Workspace Details</h4>
                    <div class="space-y-3">
                        <div class="flex items-center gap-2 text-xs text-slate-600">
                            <i class="fas fa-user-circle w-4 text-slate-400"></i>
                            <span>Username: <strong>{{ $selectedChatUser->username ?: '-' }}</strong></span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-slate-600">
                            <i class="fas fa-calendar-alt w-4 text-slate-400"></i>
                            <span>Joined: {{ $selectedChatUser->created_at->format('M Y') }}</span>
                        </div>
                        @if($selectedChatUser->student)
                            <div class="flex items-center gap-2 text-xs text-slate-600">
                                <i class="fas fa-graduation-cap w-4 text-slate-400"></i>
                                <span>Course: {{ $selectedChatUser->student->course?->name ?? 'Enrolled' }}</span>
                            </div>
                        @endif
                    </div>
                </div>
                
                <div>
                    <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Notice Guidelines</h4>
                    <p class="text-[11px] text-slate-400 leading-relaxed">Please follow communication rules. All messages, resources, and live chats are subject to administrative guidelines.</p>
                </div>
            </div>
        </div>
    @elseif(!request('chat_user'))
        <!-- RIGHT PANEL: Message Reader for Inbox Views -->
        <div class="w-80 border-l border-slate-100 bg-white hidden lg:flex flex-col shrink-0" id="detailPanel">
            <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <span class="text-xs font-bold text-slate-600" id="detailPanelTitle">
                    <i class="fas fa-info-circle text-slate-400 me-2"></i>Message Inspector
                </span>
            </div>
            <div class="flex-grow overflow-y-auto p-6 custom-scrollbar" id="detailBody">
                <div class="flex flex-col items-center justify-center h-full text-slate-400 text-center">
                    <i class="fas fa-mouse-pointer fa-2x mb-2 opacity-40"></i>
                    <p class="text-xs m-0">Click any message in the list to preview details & content here.</p>
                </div>
            </div>
        </div>
    @endif
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
                            <div id="full_quill_editor" style="height: 150px; background: #fff; border-radius: 0 0 8px 8px; border: 1px solid var(--border, #E2E8F0);"></div>
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
let currentChatUserId = {{ $selectedChatUser ? $selectedChatUser->id : 'null' }};

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
                    setTimeout(() => location.reload(), 1000);
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
            if (!currentChatUserId) return;
            fetch(`/api/messages/poll/${currentChatUserId}?last_id=${lastMsgId}`, {
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

        // Fast AJAX contact switching without loading
        document.querySelectorAll('.chat-contact-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const userId = this.dataset.userId;
                const href = this.dataset.href;
                
                // Highlight active contact
                document.querySelectorAll('.chat-contact-link').forEach(l => l.classList.remove('active-chat-item'));
                this.classList.add('active-chat-item');
                
                // Show temporary loading indicator in chat window
                const chatArea = document.getElementById('chatMessages');
                if (chatArea) {
                    chatArea.innerHTML = `
                        <div class="flex flex-col items-center justify-center py-20 text-slate-400">
                            <i class="fas fa-spinner fa-spin fa-2x mb-3 text-primary"></i>
                            <p class="text-xs">Loading conversation...</p>
                        </div>`;
                }
                
                fetch(href, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        currentChatUserId = d.chatUserId;
                        
                        // Push URL history state
                        history.pushState(null, '', href);
                        
                        // Update chat header details
                        const headerTitle = document.querySelector('.chat-widget-main h2') || document.querySelector('#chatMessages').closest('.flex-grow').querySelector('h2');
                        if (headerTitle) headerTitle.textContent = d.userName;
                        
                        const headerRole = document.querySelector('.chat-widget-main p') || document.querySelector('#chatMessages').closest('.flex-grow').querySelector('p');
                        if (headerRole) {
                            headerRole.innerHTML = `<span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> ${d.userRole}`;
                        }
                        
                        const headerImgContainer = document.querySelector('.chat-widget-main img')?.parentNode || document.querySelector('#chatMessages').closest('.flex-grow').querySelector('.flex.items-center.gap-3');
                        if (headerImgContainer) {
                            const oldImg = headerImgContainer.querySelector('img') || headerImgContainer.querySelector('div.w-10');
                            if (oldImg) {
                                if (d.profilePic) {
                                    const img = document.createElement('img');
                                    img.src = d.profilePic;
                                    img.className = 'w-10 h-10 rounded-full object-cover';
                                    oldImg.replaceWith(img);
                                } else {
                                    const initialsDiv = document.createElement('div');
                                    initialsDiv.className = 'w-10 h-10 text-white rounded-full flex items-center justify-center font-bold';
                                    initialsDiv.style.background = 'var(--first-color)';
                                    initialsDiv.textContent = d.initials;
                                    oldImg.replaceWith(initialsDiv);
                                }
                            }
                        }
                        
                        // Render messages HTML
                        if (chatArea) {
                            chatArea.innerHTML = d.html;
                            scrollToBottom();
                        }
                        
                        // Update active action on chat form
                        const form = document.getElementById('chatForm');
                        if (form) {
                            form.action = `{{ route('messages.chat.store') }}`;
                            const receiverInput = form.querySelector('input[name="receiver_id"]') || document.createElement('input');
                            receiverInput.type = 'hidden';
                            receiverInput.name = 'receiver_id';
                            receiverInput.value = d.chatUserId;
                            if (!form.querySelector('input[name="receiver_id"]')) {
                                form.appendChild(receiverInput);
                            }
                        }
                        
                        // Reset last message ID
                        lastMsgId = getLastMsgId();
                    }
                })
                .catch(err => {
                    console.error('Failed to load chat:', err);
                    window.location.href = href; // Fallback to full load on error
                });
            });
        });
    }
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
    div.className = 'flex gap-3 max-w-xl ' + (isMine ? 'ml-auto flex-row-reverse' : '');
    div.dataset.msgId = msg.id;
    let attachmentHtml = '';
    if (msg.attachment) {
        attachmentHtml = `
        <div class="mt-2 pt-2 border-t ${isMine ? 'border-white/20' : 'border-slate-100'}">
            <a href="${msg.attachment}" target="_blank" class="text-xs font-semibold hover:underline inline-flex items-center gap-1 ${isMine ? 'text-white' : 'text-primary'}">
                <i class="fas fa-paperclip"></i> View Attached Resource
            </a>
        </div>`;
    }

    let bodyHtml = '';
    if (msg.body === '[VIDEO_CALL_INVITE]') {
        bodyHtml = `
            <div class="text-center p-1">
                <i class="fas fa-video fa-2x mb-2 ${isMine ? 'text-white' : 'text-success'}"></i><br>
                <button class="btn btn-xs ${isMine ? 'btn-light' : 'btn-success'} fw-bold" style="font-size:0.75rem;" data-bs-toggle="modal" data-bs-target="#jitsiModal">
                    Join Video Call
                </button>
            </div>
        `;
    } else {
        bodyHtml = `<p class="text-sm m-0" style="white-space: pre-wrap; word-break: break-word;">${escHtml(msg.body)}</p>`;
    }

    if (!isMine) {
        div.innerHTML = `
            <div class="w-8 h-8 text-white rounded-full flex items-center justify-center font-bold text-xs shrink-0 bg-primary">${msg.sender ? msg.sender[0].toUpperCase() : '?'}</div>
            <div class="flex flex-col items-start">
                <div class="flex items-baseline gap-2 mb-1">
                    <span class="text-[10px] font-bold text-slate-600">${msg.sender || 'Sender'}</span>
                    <span class="text-[9px] text-slate-400">${msg.time}</span>
                </div>
                <div class="p-3 shadow-sm border bg-white text-slate-700 border-slate-100 message-bubble-in">
                    ${bodyHtml}${attachmentHtml}
                </div>
            </div>`;
    } else {
        div.innerHTML = `
            <div class="flex flex-col items-end">
                <div class="flex items-baseline gap-2 mb-1 flex-row-reverse">
                    <span class="text-[10px] font-bold text-slate-600">You</span>
                    <span class="text-[9px] text-slate-400">${msg.time}</span>
                </div>
                <div class="p-3 shadow-sm border bg-primary text-white border-transparent message-bubble-out">
                    ${bodyHtml}${attachmentHtml}
                </div>
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
            const badge = document.getElementById('topbarUnreadBadge');
            if (badge) {
                let count = parseInt(badge.textContent) - 1;
                badge.textContent = count > 0 ? count : '';
                if (count <= 0) badge.style.display = 'none';
            }
        }
    });

    const panel = document.getElementById('detailPanel');
    const panelBody = document.getElementById('detailBody');
    const panelTitle = document.getElementById('detailPanelTitle');

    if (panelBody && window.innerWidth > 991) {
        panelTitle.innerHTML = `<i class="fas fa-envelope-open text-primary me-2"></i>Message Reader`;
        let attHtml = '';
        if(attachment && attachment !== '') {
            attHtml = `
            <div class="mt-4 pt-3 border-t border-slate-100">
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Attachment</div>
                <a href="${attachment}" target="_blank" class="btn btn-sm btn-outline-primary py-1.5 px-3" style="font-size:0.8rem;"><i class="fas fa-paperclip me-2"></i>View Attachment</a>
            </div>`;
        }
        panelBody.innerHTML = `
            <div class="mb-4">
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Subject</div>
                <h3 class="font-bold text-sm text-slate-800">${subject}</h3>
            </div>
            <div class="mb-4 flex justify-between items-end border-b border-slate-100 pb-3">
                <div>
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">From</div>
                    <div class="font-semibold text-xs text-slate-700">${sender}</div>
                </div>
                <div class="text-end shrink-0">
                    <div class="text-[9px] text-slate-400 mb-1">${date}</div>
                    <span class="text-[9px] font-bold uppercase px-2 py-0.5 rounded ${priority === 'Urgent' ? 'bg-rose-50 text-rose-600' : (priority === 'Important' ? 'bg-amber-50 text-amber-600' : 'bg-slate-50 text-slate-600')}">${priority}</span>
                </div>
            </div>
            <div class="text-xs text-slate-600 leading-relaxed">${body}</div>
            ${attHtml}
        `;
    } else {
        // Mobile – modal fallback
        document.getElementById('modalSubject').innerText = subject;
        document.getElementById('modalSender').innerText = sender;
        document.getElementById('modalDate').innerText = date;
        const mp = document.getElementById('modalPriority');
        mp.className = 'prio-badge prio-' + priority; mp.innerText = priority;
        let attHtml = '';
        if(attachment && attachment !== '') {
            attHtml = `
            <div class="mt-4 pt-3 border-t border-slate-100">
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Attachment</div>
                <a href="${attachment}" target="_blank" class="btn btn-sm btn-outline-primary py-1.5 px-3" style="font-size:0.8rem;"><i class="fas fa-paperclip me-2"></i>View Attachment</a>
            </div>`;
        }
        document.getElementById('modalBody').innerHTML = body + attHtml;
        new bootstrap.Modal(document.getElementById('msgDetailModal')).show();
    }
}

/* ─── Toast notification ─── */
function showToast(msg, type) {
    const t = document.createElement('div');
    t.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:9999;padding:12px 20px;border-radius:12px;color:#fff;font-weight:600;font-size:0.9rem;box-shadow:0 8px 24px rgba(0,0,0,.15);';
    t.style.background = type === 'success' ? '#10b981' : '#ef4444';
    t.innerHTML = `<i class="fas fa-${type === 'success' ? 'check' : 'times'} me-2"></i>${msg}`;
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 3500);
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

    const formData = new FormData(chatForm);
    formData.set('body', '[VIDEO_CALL_INVITE]');
    formData.delete('attachment');

    fetch(chatForm.action, {
        method: 'POST', body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    }).then(r => r.json()).then(d => {
        if (d.success) {
            appendBubble(d.data, true);
            const jitsiModal = new bootstrap.Modal(document.getElementById('jitsiModal'));
            jitsiModal.show();
        }
    });
}
</script>
@endif

@endsection
