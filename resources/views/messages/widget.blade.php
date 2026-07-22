<style>
    .msg-widget-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        font-family: 'Poppins', 'Outfit', sans-serif;
    }
    .unread-row {
        background: rgba(255, 85, 50, 0.04) !important;
        font-weight: 700;
    }
    .priority-urgent {
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
    }
    .priority-important {
        background: rgba(245, 158, 11, 0.15);
        color: #f59e0b;
    }
    .priority-normal {
        background: rgba(59, 130, 246, 0.15);
        color: #3b82f6;
    }

    /* Chat UI Styles */
    .chat-widget-container {
        display: flex;
        height: 500px;
        background: #fff;
        border-radius: 16px;
        overflow: hidden;
    }
    .chat-widget-sidebar {
        width: 250px;
        background: #f8fafc;
        border-right: 1px solid var(--border);
        display: flex;
        flex-direction: column;
    }
    .chat-widget-sidebar-header {
        padding: 1rem 1.25rem;
        background: #fff;
        border-bottom: 1px solid var(--border);
        font-weight: 700;
    }
    .chat-widget-users {
        flex: 1;
        overflow-y: auto;
    }
    .chat-widget-user-item {
        display: flex;
        align-items: center;
        padding: 0.75rem 1.25rem;
        border-bottom: 1px solid rgba(0,0,0,0.02);
        cursor: pointer;
        text-decoration: none;
        color: var(--text);
        transition: all 0.2s ease;
    }
    .chat-widget-user-item:hover, .chat-widget-user-item.active {
        background: rgba(255, 85, 50, 0.05);
        border-left: 4px solid var(--primary);
    }
    .chat-widget-avatar {
        width: 36px;
        height: 36px;
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
    .chat-widget-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: #ffffff;
    }
    .chat-widget-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        background: #fff;
    }
    .chat-widget-body {
        flex: 1;
        padding: 1.5rem;
        overflow-y: auto;
        background: #f1f5f9;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    .chat-widget-bubble {
        max-width: 70%;
        padding: 0.75rem 1rem;
        border-radius: 14px;
        position: relative;
        font-size: 0.9rem;
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
    .chat-widget-time {
        font-size: 0.65rem;
        opacity: 0.7;
        margin-top: 4px;
        display: block;
        text-align: right;
    }
    .bubble-received .chat-widget-time {
        color: #94a3b8;
    }
    .chat-widget-footer {
        padding: 1rem 1.5rem;
        background: #fff;
        border-top: 1px solid var(--border);
    }
    .empty-chat-widget {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        background: #f8fafc;
    }
    .empty-chat-widget i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }
    
    /* Mobile Responsiveness for Widget */
    @media (max-width: 768px) {
        .chat-widget-container {
            flex-direction: column;
            height: 650px;
        }
        .chat-widget-sidebar {
            width: 100%;
            height: 200px;
            border-right: none;
            border-bottom: 1px solid var(--border);
            flex: none;
        }
        .chat-widget-users {
            display: flex;
            flex-direction: row;
            overflow-x: auto;
            overflow-y: hidden;
            padding-bottom: 5px;
        }
        .chat-widget-user-item {
            flex-direction: column;
            border-bottom: none;
            border-right: 1px solid rgba(0,0,0,0.02);
            padding: 0.5rem;
            min-width: 80px;
            text-align: center;
        }
        .chat-widget-user-item:hover, .chat-widget-user-item.active {
            border-left: none;
            border-bottom: 3px solid var(--primary);
            background: rgba(255, 85, 50, 0.05);
        }
        .chat-widget-avatar {
            margin-right: 0;
            margin-bottom: 0.25rem;
        }
        .chat-widget-main {
            height: 450px;
            flex: none;
        }
        .msg-widget-card .nav-pills {
            flex-wrap: nowrap;
            overflow-x: auto;
            white-space: nowrap;
        }
        .table-responsive {
            border: 0;
        }
        .table-light-head th {
            white-space: nowrap;
        }
    }
</style>

<div class="messages-widget-container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0"><i class="fas fa-envelope-open-text text-first me-2"></i>Messages & Communications</h5>
        <div class="d-flex gap-2">
            <a href="{{ route('messages.full') }}" class="button button-secondary py-1 px-3">
                <i class="fas fa-expand-arrows-alt me-1"></i>Full Screen App
            </a>
            <button type="button" class="button button-primary py-1 px-3" data-bs-toggle="modal" data-bs-target="#composeWidgetModal">
                <i class="fas fa-edit me-1"></i>Compose
            </button>
        </div>
    </div>

    <div class="msg-widget-card p-0 overflow-hidden">
        <!-- Tabs Header -->
        <div class="bg-light border-bottom p-2 d-flex">
            <ul class="nav nav-pills gap-2" id="msgWidgetTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active px-3 py-1 fw-bold" id="widget-inbox-tab" data-bs-toggle="pill" data-bs-target="#widget-inbox" type="button" role="tab">
                        <i class="fas fa-inbox me-1"></i>Inbox
                        @if($unreadCount > 0)
                            <span class="badge bg-danger rounded-pill ms-1">{{ $unreadCount }}</span>
                        @endif
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-3 py-1 fw-bold" id="widget-sent-tab" data-bs-toggle="pill" data-bs-target="#widget-sent" type="button" role="tab">
                        <i class="fas fa-paper-plane me-1"></i>Sent Items
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-3 py-1 fw-bold" id="widget-chat-tab" data-bs-toggle="pill" data-bs-target="#widget-chat" type="button" role="tab">
                        <i class="fas fa-comments me-1"></i>Live Chat
                    </button>
                </li>
            </ul>
        </div>

        <!-- Tabs Content -->
        <div class="tab-content" id="msgWidgetTabsContent">
            
            <!-- INBOX TAB -->
            <div class="tab-pane fade show active" id="widget-inbox" role="tabpanel">
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table premium-table align-middle mb-0">
                        <thead style="position: sticky; top: 0; background: #fff; z-index: 10;">
                            <tr class="table-light-head">
                                <th class="ps-4">Status</th>
                                <th>Sender</th>
                                <th>Subject</th>
                                <th>Priority</th>
                                <th>Date</th>
                                <th class="pe-4 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inboxMessages as $msg)
                                <tr class="{{ !$msg->is_read ? 'unread-row' : '' }}">
                                    <td class="ps-4">
                                        @if(!$msg->is_read)
                                            <span class="badge bg-danger rounded-pill" style="font-size:0.65rem;">NEW</span>
                                        @else
                                            <span class="badge bg-light text-muted border" style="font-size:0.65rem;">READ</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark-title">{{ $msg->sender?->name ?? 'System' }}</div>
                                        <small class="text-muted">{{ $msg->sender?->role?->role_name ?? 'Notice' }}</small>
                                    </td>
                                    <td>
                                        <a href="javascript:void(0)" class="view-msg-btn text-decoration-none text-dark-title fw-bold"
                                           data-bs-toggle="modal"
                                           data-bs-target="#viewWidgetMsgModal"
                                           data-id="{{ $msg->id }}"
                                           data-subject="{{ $msg->subject }}"
                                           data-sender="{{ $msg->sender?->name ?? 'System' }}"
                                           data-priority="{{ $msg->priority }}"
                                           data-date="{{ $msg->created_at->format('M d, Y h:i A') }}">
                                            {{ Str::limit($msg->subject, 35) }}
                                        </a>
                                        <div id="widget-msg-body-{{ $msg->id }}" style="display: none;">{{ $msg->body }}</div>
                                    </td>
                                    <td>
                                        <span class="badge priority-{{ strtolower($msg->priority) }} px-2 py-1">
                                            {{ $msg->priority }}
                                        </span>
                                    </td>
                                    <td class="text-muted small">{{ $msg->created_at->diffForHumans() }}</td>
                                    <td class="pe-4 text-end">
                                        <button class="button button-secondary btn-sm py-1 px-2 view-msg-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#viewWidgetMsgModal"
                                                data-id="{{ $msg->id }}"
                                                data-subject="{{ $msg->subject }}"
                                                data-sender="{{ $msg->sender?->name ?? 'System Announcement' }}"
                                                data-priority="{{ $msg->priority }}"
                                                data-date="{{ $msg->created_at->format('M d, Y h:i A') }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fas fa-envelope-open-text fa-2x mb-2 d-block opacity-50"></i>No messages in inbox.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- SENT TAB -->
            <div class="tab-pane fade" id="widget-sent" role="tabpanel">
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table premium-table align-middle mb-0">
                        <thead style="position: sticky; top: 0; background: #fff; z-index: 10;">
                            <tr class="table-light-head">
                                <th class="ps-4">Recipient</th>
                                <th>Subject</th>
                                <th>Priority</th>
                                <th>Date Sent</th>
                                <th class="pe-4 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sentMessages as $msg)
                                <tr>
                                    <td class="ps-4">
                                        @if($msg->receiver)
                                            <strong class="text-dark-title">{{ $msg->receiver->name }}</strong>
                                            <small class="text-muted d-block">({{ $msg->receiver->role?->role_name ?? 'User' }})</small>
                                        @else
                                            <span class="badge bg-primary text-uppercase">Broadcast: {{ $msg->receiver_role }}</span>
                                        @endif
                                    </td>
                                    <td class="fw-bold text-dark-title">{{ Str::limit($msg->subject, 40) }}</td>
                                    <td>
                                        <span class="badge priority-{{ strtolower($msg->priority) }} px-2 py-1">
                                            {{ $msg->priority }}
                                        </span>
                                    </td>
                                    <td class="text-muted small">{{ $msg->created_at->diffForHumans() }}</td>
                                    <td class="pe-4 text-end">
                                        <form action="{{ route('messages.destroy', $msg->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this message?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="button button-danger btn-sm py-1 px-2">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="fas fa-paper-plane fa-2x mb-2 d-block opacity-50"></i>No sent messages.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- LIVE CHAT TAB -->
            <div class="tab-pane fade" id="widget-chat" role="tabpanel">
                <div class="chat-widget-container">
                    <!-- Sidebar -->
                    <div class="chat-widget-sidebar">
                        <div class="chat-widget-sidebar-header text-muted small text-uppercase">
                            Contacts
                        </div>
                        <div class="chat-widget-users" style="padding-top: 5px;">
                            @php
                                $groupedRecipients = $recipients->groupBy(function($user) {
                                    return $user->role->role_name ?? 'Users';
                                });
                            @endphp

                            @foreach($groupedRecipients as $roleName => $users)
                                <div class="px-3 py-1 bg-light fw-bold text-muted d-none d-md-block" style="font-size: 0.7rem; text-transform: uppercase;">
                                    {{ $roleName }}
                                </div>
                                @foreach($users as $user)
                                    <a href="?chat_user={{ $user->id }}#widget-chat" class="chat-widget-user-item {{ (request('chat_user') == $user->id) ? 'active' : '' }}" title="{{ $user->name }} ({{ $roleName }})">
                                        <div class="chat-widget-avatar">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div class="overflow-hidden">
                                            <div class="fw-bold text-truncate text-dark" style="font-size: 0.85rem;">{{ Str::limit($user->name, 12) }}</div>
                                            <div class="small text-muted d-md-none" style="font-size: 0.65rem;">{{ Str::limit($roleName, 10) }}</div>
                                        </div>
                                    </a>
                                @endforeach
                            @endforeach
                        </div>
                    </div>

                    <!-- Main Chat Area -->
                    @if(request('chat_user') && $selectedChatUser)
                        <div class="chat-widget-main">
                            <div class="chat-widget-header">
                                <div class="chat-widget-avatar">
                                    {{ strtoupper(substr($selectedChatUser->name, 0, 1)) }}
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $selectedChatUser->name }}</h6>
                                    <small class="text-muted" style="font-size: 0.75rem;">{{ $selectedChatUser->role?->role_name ?? 'User' }}</small>
                                </div>
                            </div>
                            
                            <div class="chat-widget-body" id="chatWidgetBody">
                                @if($chatMessages->isEmpty())
                                    <div class="text-center text-muted my-auto">
                                        <p class="small">No messages yet. Say hi!</p>
                                    </div>
                                @else
                                    @foreach($chatMessages as $msg)
                                        <div class="chat-widget-bubble {{ $msg->sender_id == session('user_id') ? 'bubble-sent' : 'bubble-received' }}">
                                            {{ $msg->body }}
                                            <span class="chat-widget-time">{{ $msg->created_at->format('h:i A') }}</span>
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                            <div class="chat-widget-footer">
                                <form id="widgetChatForm" action="{{ route('messages.chat.store') }}" method="POST" class="d-flex gap-2">
                                    @csrf
                                    <input type="hidden" name="receiver_id" value="{{ $selectedChatUser->id }}">
                                    <input type="text" name="body" class="form-control rounded-pill px-3 py-1 border-0" style="background: #f1f5f9; font-size: 0.9rem;" placeholder="Type a message..." required autocomplete="off">
                                    <button type="submit" class="button button-primary rounded-pill px-3 py-1"><i class="fas fa-paper-plane"></i></button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="empty-chat-widget">
                            <i class="fas fa-comments"></i>
                            <h6>Select a conversation</h6>
                            <p class="small">Choose a contact to start chatting</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- COMPOSE MESSAGE MODAL -->
<div class="modal fade" id="composeWidgetModal" tabindex="-1" aria-hidden="true" style="font-family: 'Poppins', 'Outfit', sans-serif;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #ff5532 0%, #e04423 100%);">
                <h6 class="modal-title fw-bold mb-0"><i class="fas fa-paper-plane me-2"></i>Compose Message / Broadcast</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="widgetComposeForm" action="{{ route('messages.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        @if(isset($isAdmin) && $isAdmin)
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Recipient Type</label>
                                <select name="recipient_type" id="widget_recipient_type" class="form-input" required>
                                    <option value="user">Specific Registered User</option>
                                    <option value="role">Broadcast to Role Group</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6" id="widget_user_select_box">
                        @else
                            <input type="hidden" name="recipient_type" value="user">
                            <div class="col-12" id="widget_user_select_box">
                        @endif
                            <label class="form-label fw-bold small">Select Recipient</label>
                            <select name="receiver_id" class="form-input" required>
                                <option value="">-- Choose User --</option>
                                @foreach($recipients as $user)
                                    <option value="{{ $user->id }}">
                                        {{ $user->name }} ({{ $user->role?->role_name ?? 'User' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @if(isset($isAdmin) && $isAdmin)
                        <div class="col-md-6" id="widget_role_select_box" style="display: none;">
                            <label class="form-label fw-bold small">Select Broadcast Target Role</label>
                            <select name="receiver_role" class="form-input">
                                <option value="all">📢 All System Users</option>
                                <option value="staff">👔 All Staff Members</option>
                                <option value="student">🎓 All Students</option>
                                <option value="admin">🛡️ All Admins</option>
                            </select>
                        </div>
                        @endif

                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Priority Level</label>
                            <select name="priority" class="form-input" required>
                                <option value="Normal">Normal</option>
                                <option value="Important">Important</option>
                                <option value="Urgent">🚨 Urgent</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small">Subject</label>
                            <input type="text" name="subject" class="form-input" placeholder="e.g. Monthly Report" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small">Message Body</label>
                            <textarea name="body" rows="4" class="form-input" placeholder="Type your message details here..." required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="button button-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="button button-primary px-4"><i class="fas fa-paper-plane me-2"></i>Send Message</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- VIEW MESSAGE MODAL -->
<div class="modal fade" id="viewWidgetMsgModal" tabindex="-1" aria-hidden="true" style="font-family: 'Poppins', 'Outfit', sans-serif;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; overflow: hidden;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border-bottom: none;">
                <h6 class="modal-title fw-bold mb-0" style="font-size: 1.1rem;"><i class="fas fa-envelope-open-text me-2"></i><span id="viewWidgetModalSubject"></span></h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 p-3 rounded-4 bg-white shadow-sm" style="border: 1px solid var(--border);">
                    <div class="d-flex align-items-center mb-2 mb-md-0">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold me-3 shadow-sm" style="width: 48px; height: 48px; background: linear-gradient(135deg, #8b5cf6, #6d28d9); font-size: 1.2rem;" id="viewWidgetModalAvatar">
                            M
                        </div>
                        <div>
                            <span class="text-muted small fw-bold text-uppercase letter-spacing-1">From</span>
                            <div id="viewWidgetModalSender" class="text-dark-title fw-bold fs-6"></div>
                        </div>
                    </div>
                    <div class="text-md-end border-top border-md-0 pt-2 pt-md-0 mt-2 mt-md-0 border-light">
                        <span id="viewWidgetModalPriority" class="badge px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.75rem;"></span>
                        <div id="viewWidgetModalDate" class="text-muted small fw-bold mt-2"><i class="far fa-clock me-1"></i></div>
                    </div>
                </div>
                
                <div class="p-4 rounded-4 bg-white shadow-sm position-relative" style="min-height: 150px; font-size: 0.95rem; line-height: 1.6; border: 1px solid var(--border);">
                    <div class="position-absolute" style="top: 10px; right: 15px; opacity: 0.05; font-size: 4rem;">
                        <i class="fas fa-quote-right"></i>
                    </div>
                    <div id="viewWidgetModalBody" style="white-space: pre-wrap; color: var(--text-color, #334155); position: relative; z-index: 1;"></div>
                </div>
            </div>
            <div class="modal-footer bg-white border-top-0">
                <button type="button" class="button button-secondary px-4 py-2" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle Compose Recipients
        const recipTypeSelect = document.getElementById('widget_recipient_type');
        const userBox = document.getElementById('widget_user_select_box');
        const roleBox = document.getElementById('widget_role_select_box');

        if (recipTypeSelect) {
            recipTypeSelect.addEventListener('change', function() {
                if (this.value === 'role') {
                    if (userBox) userBox.style.display = 'none';
                    if (roleBox) roleBox.style.display = 'block';
                } else {
                    if (userBox) userBox.style.display = 'block';
                    if (roleBox) roleBox.style.display = 'none';
                }
            });
        }

        // View Message Logic (Using native Bootstrap modal events for reliability)
        const viewModalEl = document.getElementById('viewWidgetMsgModal');
        if (viewModalEl) {
            viewModalEl.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const id = button.dataset.id;
                const subject = button.dataset.subject;
                const sender = button.dataset.sender;
                const bodyEl = document.getElementById('widget-msg-body-' + id);
                const body = bodyEl ? bodyEl.innerText || bodyEl.textContent : '';
                const priority = button.dataset.priority;
                const date = button.dataset.date;

                document.getElementById('viewWidgetModalSubject').innerText = subject;
                document.getElementById('viewWidgetModalSender').innerText = sender;
                
                const avatarEl = document.getElementById('viewWidgetModalAvatar');
                if(avatarEl) {
                    avatarEl.innerText = sender ? sender.charAt(0).toUpperCase() : 'S';
                }

                document.getElementById('viewWidgetModalBody').innerText = body;
                document.getElementById('viewWidgetModalDate').innerHTML = `<i class="far fa-clock me-1"></i> ${date}`;

                const prioEl = document.getElementById('viewWidgetModalPriority');
                prioEl.innerText = priority.toUpperCase();
                
                let bgClass = '';
                if (priority.toLowerCase() === 'urgent') bgClass = 'bg-danger';
                else if (priority.toLowerCase() === 'important') bgClass = 'bg-warning text-dark';
                else bgClass = 'bg-primary';
                prioEl.className = 'badge px-3 py-2 rounded-pill shadow-sm ' + bgClass;

                // Mark message as read via AJAX
                fetch(`/messages/${id}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                }).then(res => res.json())
                  .then(data => {
                      if(data.success) {
                          const tr = button.closest('tr');
                          if (tr) {
                              tr.classList.remove('unread-row');
                              const badge = tr.querySelector('.badge');
                              if (badge && badge.innerText === 'NEW') {
                                  badge.className = 'badge bg-light text-muted border';
                                  badge.innerText = 'READ';
                              }
                          }
                      }
                  })
                  .catch(err => console.error(err));
            });
        }

        // Keep chat scrolled to bottom
        const chatWidgetBody = document.getElementById('chatWidgetBody');
        if (chatWidgetBody) {
            chatWidgetBody.scrollTop = chatWidgetBody.scrollHeight;
        }

        // Show chat tab if chat_user is in URL
        if (window.location.search.includes('chat_user=')) {
            const chatTab = new bootstrap.Tab(document.getElementById('widget-chat-tab'));
            chatTab.show();
            // scroll to the widget
            setTimeout(() => {
                document.querySelector('.messages-widget-container').scrollIntoView({ behavior: 'smooth' });
            }, 500);
        }

        // Compose Form AJAX Submit
        const composeForm = document.getElementById('widgetComposeForm');
        if (composeForm) {
            composeForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(composeForm);
                const submitBtn = composeForm.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
                submitBtn.disabled = true;

                fetch(composeForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                    if (data.success) {
                        composeForm.reset();
                        bootstrap.Modal.getInstance(document.getElementById('composeWidgetModal')).hide();
                        
                        // Add to sent items table dynamically
                        const sentTableBody = document.querySelector('#widget-sent tbody');
                        if (sentTableBody) {
                            // Check if empty state row exists and remove it
                            const emptyRow = sentTableBody.querySelector('td[colspan="5"]');
                            if (emptyRow) emptyRow.closest('tr').remove();
                            
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td class="ps-4">
                                    <span class="badge bg-primary text-uppercase">Just Sent</span>
                                </td>
                                <td class="fw-bold text-dark-title">${data.data.subject || 'Message'}</td>
                                <td>
                                    <span class="badge priority-${(data.data.priority || 'normal').toLowerCase()} px-2 py-1">
                                        ${data.data.priority || 'Normal'}
                                    </span>
                                </td>
                                <td class="text-muted small">Just now</td>
                                <td class="pe-4 text-end">
                                    <button class="button button-danger btn-sm py-1 px-2" disabled><i class="fas fa-check"></i></button>
                                </td>
                            `;
                            sentTableBody.insertBefore(tr, sentTableBody.firstChild);
                        }
                    }
                })
                .catch(err => {
                    console.error(err);
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
            });
        }

        // Chat Form AJAX Submit
        const chatForm = document.getElementById('widgetChatForm');
        if (chatForm) {
            chatForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(chatForm);
                const input = chatForm.querySelector('input[name="body"]');
                const submitBtn = chatForm.querySelector('button[type="submit"]');
                
                if (!input.value.trim()) return;

                const bodyText = input.value;
                input.value = '';
                submitBtn.disabled = true;

                fetch(chatForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    submitBtn.disabled = false;
                    if (data.success && chatWidgetBody) {
                        const newBubble = document.createElement('div');
                        newBubble.className = 'chat-widget-bubble bubble-sent';
                        newBubble.innerHTML = `
                            ${data.data.body}
                            <span class="chat-widget-time">${data.data.time}</span>
                        `;
                        chatWidgetBody.appendChild(newBubble);
                        chatWidgetBody.scrollTop = chatWidgetBody.scrollHeight;
                    }
                })
                .catch(err => {
                    console.error(err);
                    submitBtn.disabled = false;
                });
            });
        }
    });
</script>
