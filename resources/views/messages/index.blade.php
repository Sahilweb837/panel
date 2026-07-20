@extends('layouts.app')

@section('title', 'Internal Message & Notice Suite')
@section('page-title', 'Internal Message & Notice Center')

@section('content')
<style>
    .msg-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
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
</style>

<div class="messages-container">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="toolbar mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
        <ul class="nav nav-pills gap-2" id="msgTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active px-4 py-2 fw-bold" id="inbox-tab" data-bs-toggle="pill" data-bs-target="#inbox" type="button" role="tab">
                    <i class="fas fa-inbox me-2"></i>Inbox
                    @if($unreadCount > 0)
                        <span class="badge bg-danger rounded-pill ms-2">{{ $unreadCount }}</span>
                    @endif
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link px-4 py-2 fw-bold" id="sent-tab" data-bs-toggle="pill" data-bs-target="#sent" type="button" role="tab">
                    <i class="fas fa-paper-plane me-2"></i>Sent Items
                </button>
            </li>
        </ul>

        <button type="button" class="button button-primary py-2 px-4" data-bs-toggle="modal" data-bs-target="#composeModal">
            <i class="fas fa-edit me-2"></i>Compose Message / Notice
        </button>
    </div>

    <div class="tab-content" id="msgTabsContent">
        <!-- INBOX TAB -->
        <div class="tab-pane fade show active" id="inbox" role="tabpanel">
            <div class="msg-card p-0 overflow-hidden">
                <div class="table-responsive">
                    <table class="table premium-table align-middle mb-0">
                        <thead>
                            <tr class="table-light-head">
                                <th class="ps-4">Status</th>
                                <th>Sender</th>
                                <th>Subject</th>
                                <th>Priority</th>
                                <th>Date Received</th>
                                <th class="pe-4 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inboxMessages as $msg)
                                <tr class="{{ !$msg->is_read ? 'unread-row' : '' }}">
                                    <td class="ps-4">
                                        @if(!$msg->is_read)
                                            <span class="badge bg-danger rounded-pill" style="font-size:0.65rem;">UNREAD</span>
                                        @else
                                            <span class="badge bg-light text-muted border" style="font-size:0.65rem;">READ</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark-title">{{ $msg->sender?->name ?? 'System Announcement' }}</div>
                                        <small class="text-muted">{{ $msg->sender?->role?->role_name ?? 'Notice' }}</small>
                                    </td>
                                    <td>
                                        <a href="#" class="view-msg-btn text-decoration-none text-dark-title fw-bold"
                                           data-id="{{ $msg->id }}"
                                           data-subject="{{ $msg->subject }}"
                                           data-sender="{{ $msg->sender?->name ?? 'System Announcement' }}"
                                           data-body="{{ $msg->body }}"
                                           data-priority="{{ $msg->priority }}"
                                           data-date="{{ $msg->created_at->format('M d, Y h:i A') }}">
                                            {{ $msg->subject }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge priority-{{ strtolower($msg->priority) }} px-2 py-1">
                                            {{ $msg->priority }}
                                        </span>
                                    </td>
                                    <td class="text-muted small">{{ $msg->created_at->format('M d, Y h:i A') }}</td>
                                    <td class="pe-4 text-end">
                                        <button class="button button-secondary btn-sm py-1 px-3 view-msg-btn"
                                                data-id="{{ $msg->id }}"
                                                data-subject="{{ $msg->subject }}"
                                                data-sender="{{ $msg->sender?->name ?? 'System Announcement' }}"
                                                data-body="{{ $msg->body }}"
                                                data-priority="{{ $msg->priority }}"
                                                data-date="{{ $msg->created_at->format('M d, Y h:i A') }}">
                                            <i class="fas fa-eye me-1"></i>Read
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fas fa-envelope-open fa-2x mb-2 d-block"></i>No messages in inbox.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- SENT ITEMS TAB -->
        <div class="tab-pane fade" id="sent" role="tabpanel">
            <div class="msg-card p-0 overflow-hidden">
                <div class="table-responsive">
                    <table class="table premium-table align-middle mb-0">
                        <thead>
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
                                    <td class="fw-bold text-dark-title">{{ $msg->subject }}</td>
                                    <td>
                                        <span class="badge priority-{{ strtolower($msg->priority) }} px-2 py-1">
                                            {{ $msg->priority }}
                                        </span>
                                    </td>
                                    <td class="text-muted small">{{ $msg->created_at->format('M d, Y h:i A') }}</td>
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
                                        <i class="fas fa-paper-plane fa-2x mb-2 d-block"></i>No sent messages yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- COMPOSE MESSAGE MODAL -->
<div class="modal fade" id="composeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #ff5532 0%, #e04423 100%);">
                <h6 class="modal-title fw-bold mb-0"><i class="fas fa-paper-plane me-2"></i>Compose Message / Broadcast Notice</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('messages.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Recipient Type</label>
                            <select name="recipient_type" id="recipient_type" class="form-input" required>
                                <option value="user">Specific Registered User</option>
                                <option value="role">Broadcast to Role Group</option>
                            </select>
                        </div>

                        <div class="col-md-6" id="user_select_box">
                            <label class="form-label fw-bold small">Select User</label>
                            <select name="receiver_id" class="form-input">
                                <option value="">-- Choose User --</option>
                                @foreach($recipients as $user)
                                    <option value="{{ $user->id }}">
                                        {{ $user->name }} ({{ $user->role?->role_name ?? 'User' }} - {{ $user->email ?? $user->username }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6" id="role_select_box" style="display: none;">
                            <label class="form-label fw-bold small">Select Broadcast Target Role</label>
                            <select name="receiver_role" class="form-input">
                                <option value="all">📢 All System Users (Staff, Students & Admins)</option>
                                <option value="staff">👔 All Staff Members</option>
                                <option value="student">🎓 All Students</option>
                                <option value="admin">🛡️ All Admins / Super Admins</option>
                            </select>
                        </div>

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
                            <input type="text" name="subject" class="form-input" placeholder="e.g. Monthly Staff Meeting Notice / Fee Inquiry" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small">Message Body</label>
                            <textarea name="body" rows="5" class="form-input" placeholder="Type your message details here..." required></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small"><i class="fas fa-paperclip me-1 text-primary"></i>Image / Picture Attachment (Optional)</label>
                            <input type="file" name="attachment" accept="image/*" class="form-input">
                            <small class="text-muted d-block mt-1" style="font-size:0.75rem;">Supported formats: JPG, PNG, GIF, WEBP (Max 5MB)</small>
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
<div class="modal fade" id="viewMsgModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px;">
            <div class="modal-header">
                <h6 class="modal-title fw-bold text-dark-title mb-0" id="viewModalSubject"></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3 p-3 rounded-3 border bg-light" style="font-size: 0.82rem;">
                    <div>
                        <span class="text-muted fw-bold">From:</span>
                        <strong id="viewModalSender" class="text-dark-title"></strong>
                    </div>
                    <div class="text-end">
                        <span id="viewModalPriority" class="badge px-2 py-1"></span>
                        <small id="viewModalDate" class="text-muted d-block mt-1"></small>
                    </div>
                </div>
                <div class="p-3 border rounded-3 bg-white" style="min-height: 120px; font-size: 0.95rem; white-space: pre-wrap;" id="viewModalBody"></div>

                <div id="viewModalAttachmentContainer" class="mt-3 text-center" style="display:none;">
                    <div class="p-2 border rounded-3 bg-light d-inline-block">
                        <img id="viewModalAttachmentImg" src="" alt="Picture Attachment" loading="lazy" style="max-width: 100%; max-height: 300px; border-radius: 10px; object-fit: contain;" />
                        <a id="viewModalAttachmentLink" href="" target="_blank" class="d-block mt-2 text-decoration-none small fw-bold text-primary">
                            <i class="fas fa-external-link-alt me-1"></i>View Full Image / Attachment
                        </a>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="button button-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const recipTypeSelect = document.getElementById('recipient_type');
        const userBox = document.getElementById('user_select_box');
        const roleBox = document.getElementById('role_select_box');

        recipTypeSelect.addEventListener('change', function() {
            if (this.value === 'role') {
                userBox.style.display = 'none';
                roleBox.style.display = 'block';
            } else {
                userBox.style.display = 'block';
                roleBox.style.display = 'none';
            }
        });

        // View Message Logic
        const viewModalEl = document.getElementById('viewMsgModal');
        const viewModal = new bootstrap.Modal(viewModalEl);

        document.querySelectorAll('.view-msg-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const id = this.dataset.id;
                const subject = this.dataset.subject;
                const sender = this.dataset.sender;
                const body = this.dataset.body;
                const priority = this.dataset.priority;
                const date = this.dataset.date;
                const attachment = this.dataset.attachment;

                document.getElementById('viewModalSubject').innerText = subject;
                document.getElementById('viewModalSender').innerText = sender;
                document.getElementById('viewModalBody').innerText = body;
                document.getElementById('viewModalDate').innerText = date;

                const prioEl = document.getElementById('viewModalPriority');
                prioEl.innerText = priority;
                prioEl.className = 'badge px-2 py-1 priority-' + priority.toLowerCase();

                const attachContainer = document.getElementById('viewModalAttachmentContainer');
                const attachImg = document.getElementById('viewModalAttachmentImg');
                const attachLink = document.getElementById('viewModalAttachmentLink');

                if (attachment && attachment.trim() !== '') {
                    attachImg.src = attachment;
                    attachLink.href = attachment;
                    attachContainer.style.display = 'block';
                } else {
                    attachContainer.style.display = 'none';
                }

                viewModal.show();

                // Safely mark message as read via AJAX
                fetch(`/messages/${id}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(res => {
                    if (res.ok && res.headers.get('content-type')?.includes('application/json')) {
                        return res.json();
                    }
                    return null;
                })
                .catch(err => console.error(err));
            });
        });
    });
</script>
@endsection
