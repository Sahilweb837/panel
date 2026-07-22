@extends('layouts.app')

@section('title', 'Live Chat')
@section('page-title', 'Live Chat')

@section('content')
<style>
    .chat-container {
        display: flex;
        height: calc(100vh - 180px);
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        overflow: hidden;
        border: 1px solid var(--border);
    }
    
    .chat-sidebar {
        width: 320px;
        background: #f8fafc;
        border-right: 1px solid var(--border);
        display: flex;
        flex-direction: column;
    }

    .chat-sidebar-header {
        padding: 1.5rem;
        background: #fff;
        border-bottom: 1px solid var(--border);
        font-weight: 700;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .chat-users {
        flex: 1;
        overflow-y: auto;
    }

    .chat-user-item {
        display: flex;
        align-items: center;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid rgba(0,0,0,0.02);
        cursor: pointer;
        text-decoration: none;
        color: var(--text);
        transition: all 0.2s ease;
    }

    .chat-user-item:hover, .chat-user-item.active {
        background: rgba(255, 85, 50, 0.05);
        border-left: 4px solid var(--primary);
    }

    .chat-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: var(--primary);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        margin-right: 1rem;
        flex-shrink: 0;
    }

    .chat-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: #ffffff;
    }

    .chat-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        background: #fff;
    }

    .chat-body {
        flex: 1;
        padding: 1.5rem;
        overflow-y: auto;
        background: #f1f5f9;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .chat-bubble {
        max-width: 70%;
        padding: 1rem 1.25rem;
        border-radius: 18px;
        position: relative;
        font-size: 0.95rem;
        line-height: 1.4;
    }

    .bubble-sent {
        align-self: flex-end;
        background: var(--primary);
        color: #fff;
        border-bottom-right-radius: 4px;
    }

    .bubble-received {
        align-self: flex-start;
        background: #fff;
        color: var(--text);
        border: 1px solid var(--border);
        border-bottom-left-radius: 4px;
    }

    .chat-time {
        font-size: 0.7rem;
        opacity: 0.7;
        margin-top: 5px;
        display: block;
        text-align: right;
    }

    .bubble-received .chat-time {
        color: #94a3b8;
    }

    .chat-footer {
        padding: 1rem 1.5rem;
        background: #fff;
        border-top: 1px solid var(--border);
    }

    .empty-chat {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        background: #f8fafc;
    }

    .empty-chat i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }
</style>

<div class="mb-3 d-flex justify-content-between align-items-center">
    <h5 class="mb-0 fw-bold"><i class="fas fa-comments me-2"></i>Live Chat</h5>
    <a href="{{ route('messages.index') }}" class="button button-secondary py-1 px-3"><i class="fas fa-arrow-left me-2"></i>Back to Inbox</a>
</div>

<div class="chat-container">
    <!-- Sidebar -->
    <div class="chat-sidebar">
        <div class="chat-sidebar-header">
            Contacts
        </div>
        <div class="chat-users">
            @php
                $groupedRecipients = $recipients->groupBy(function($user) {
                    return $user->role->role_name ?? 'Users';
                });
            @endphp

            @foreach($groupedRecipients as $roleName => $users)
                <div class="px-3 py-2 bg-light fw-bold text-muted" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">
                    {{ $roleName }} ({{ $users->count() }})
                </div>
                @foreach($users as $user)
                    <a href="{{ route('messages.chat', $user->id) }}" class="chat-user-item {{ isset($selectedUser) && $selectedUser->id == $user->id ? 'active' : '' }}">
                        <div class="chat-avatar" style="width: 38px; height: 38px; font-size: 0.9rem;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="overflow-hidden">
                            <div class="fw-bold text-truncate text-dark" style="font-size: 0.9rem;">{{ $user->name }}</div>
                        </div>
                    </a>
                @endforeach
            @endforeach
        </div>
    </div>

    <!-- Main Chat Area -->
    @if(isset($selectedUser))
        <div class="chat-main">
            <div class="chat-header">
                <div class="chat-avatar" style="width: 36px; height: 36px;">
                    {{ strtoupper(substr($selectedUser->name, 0, 1)) }}
                </div>
                <div>
                    <h6 class="mb-0 fw-bold">{{ $selectedUser->name }}</h6>
                    <small class="text-muted">{{ $selectedUser->role?->role_name ?? 'User' }}</small>
                </div>
            </div>
            
            <div class="chat-body" id="chatBody">
                @if($chatMessages->isEmpty())
                    <div class="text-center text-muted my-auto">
                        <p>No messages yet. Say hi!</p>
                    </div>
                @else
                    @foreach($chatMessages as $msg)
                        <div class="chat-bubble {{ $msg->sender_id == session('user_id') ? 'bubble-sent' : 'bubble-received' }}">
                            {{ $msg->body }}
                            <span class="chat-time">{{ $msg->created_at->format('h:i A') }}</span>
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="chat-footer">
                <form action="{{ route('messages.chat.store') }}" method="POST" class="d-flex gap-2">
                    @csrf
                    <input type="hidden" name="receiver_id" value="{{ $selectedUser->id }}">
                    <input type="text" name="body" class="form-control rounded-pill px-4 py-2 border-0" style="background: #f1f5f9;" placeholder="Type your message..." required autocomplete="off" autofocus>
                    <button type="submit" class="button button-primary rounded-pill px-4"><i class="fas fa-paper-plane"></i></button>
                </form>
            </div>
        </div>
    @else
        <div class="empty-chat">
            <i class="fas fa-comments"></i>
            <h5>Select a conversation</h5>
            <p>Choose a contact from the sidebar to start chatting</p>
        </div>
    @endif
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const chatBody = document.getElementById('chatBody');
        if (chatBody) {
            chatBody.scrollTop = chatBody.scrollHeight;
        }
    });
</script>
@endsection
