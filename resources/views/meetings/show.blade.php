@extends('layouts.app')

@section('title', 'Meeting Details')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="m-0"><i class="fas fa-info-circle text-primary me-2"></i> Meeting Workspace</h4>
            <a href="{{ session('user_id') == $meeting->created_by ? route('meetings.index') : route('meetings.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    @include('layouts.alerts')

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white pt-3 pb-0 border-bottom">
                    <ul class="nav nav-tabs border-bottom-0" id="meetingTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold text-dark px-4" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab"><i class="fas fa-info-circle me-1"></i> Overview</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold text-dark px-4" id="chat-tab" data-bs-toggle="tab" data-bs-target="#chat" type="button" role="tab"><i class="fas fa-comments me-1"></i> Discussion</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold text-dark px-4" id="files-tab" data-bs-toggle="tab" data-bs-target="#files" type="button" role="tab"><i class="fas fa-folder-open me-1"></i> Documents</button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="meetingTabsContent">
                        
                        <!-- OVERVIEW TAB -->
                        <div class="tab-pane fade show active" id="overview" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <h4 class="fw-bold mb-1">{{ $meeting->title }}</h4>
                                <span class="badge bg-{{ $meeting->status == 'Scheduled' ? 'warning' : ($meeting->status == 'Completed' ? 'success' : 'secondary') }} fs-6">
                                    {{ $meeting->status }}
                                </span>
                            </div>
                            <div class="row mb-4">
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <div class="p-3 bg-light rounded text-center h-100">
                                        <i class="far fa-calendar-alt text-primary fs-3 mb-2"></i>
                                        <div class="small text-muted fw-bold text-uppercase">Date</div>
                                        <div class="fw-bold fs-6">{{ \Carbon\Carbon::parse($meeting->meeting_date)->format('M d, Y') }}</div>
                                    </div>
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <div class="p-3 bg-light rounded text-center h-100">
                                        <i class="far fa-clock text-primary fs-3 mb-2"></i>
                                        <div class="small text-muted fw-bold text-uppercase">Time</div>
                                        <div class="fw-bold fs-6">{{ \Carbon\Carbon::parse($meeting->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($meeting->end_time)->format('h:i A') }}</div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="p-3 bg-light rounded text-center h-100">
                                        <i class="fas {{ $meeting->meeting_mode == 'Online' ? 'fa-video' : ($meeting->meeting_mode == 'Offline' ? 'fa-map-marker-alt' : 'fa-random') }} text-primary fs-3 mb-2"></i>
                                        <div class="small text-muted fw-bold text-uppercase">Mode</div>
                                        <div class="fw-bold fs-6">{{ $meeting->meeting_mode }}</div>
                                        @if($meeting->meeting_mode != 'Online' && $meeting->location)
                                            <div class="small text-muted mt-1">{{ $meeting->location }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <h5 class="fw-bold mb-3">Agenda / Description</h5>
                            <div class="p-4 bg-light rounded mb-4" style="white-space: pre-wrap;">{{ $meeting->description ?: 'No agenda provided.' }}</div>

                            @if(in_array($meeting->meeting_mode, ['Online', 'Hybrid']))
                                <div class="text-center mt-4 p-4 border rounded" style="background: rgba(16, 185, 129, 0.05); border-color: rgba(16, 185, 129, 0.2) !important;">
                                    <h5 class="fw-bold mb-3"><i class="fas fa-video text-success me-2"></i> Online Meeting Room</h5>
                                    <p class="text-muted mb-4">This meeting includes an online virtual room. You can join the room directly from here.</p>
                                    <a href="{{ $meeting->meeting_link }}" target="_blank" class="btn btn-success btn-lg px-5 fw-bold shadow-sm" style="border-radius: 30px;">
                                        <i class="fas fa-sign-in-alt me-2"></i> Join Custom Video Call
                                    </a>
                                </div>
                            @endif
                        </div>
                        
                        <!-- CHAT TAB -->
                        <div class="tab-pane fade" id="chat" role="tabpanel">
                            <div class="chat-container d-flex flex-column h-100" style="min-height: 400px; max-height: 600px;">
                                <div class="chat-messages flex-grow-1 overflow-auto p-3 bg-light rounded mb-3" id="chatMessages">
                                    <div class="text-center text-muted small py-3"><i class="fas fa-lock"></i> Chat is secure and only visible to participants.</div>
                                    <!-- Messages rendered via JS -->
                                </div>
                                <div class="chat-input-area mt-auto">
                                    <form id="chatForm">
                                        @csrf
                                        <div class="input-group">
                                            <label class="input-group-text bg-white cursor-pointer" for="chatAttachment" title="Attach file (Max 25MB)">
                                                <i class="fas fa-paperclip text-muted"></i>
                                            </label>
                                            <input type="file" class="d-none" id="chatAttachment" name="attachment" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.zip,.rar,.mp4">
                                            <input type="text" class="form-control border-start-0" id="chatInput" name="message" placeholder="Type a message to participants...">
                                            <button class="btn btn-primary px-4" type="submit" id="chatSubmitBtn"><i class="fas fa-paper-plane"></i></button>
                                        </div>
                                        <div class="small text-muted mt-1 d-none" id="attachmentNameDisplay"></div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- DOCUMENTS TAB -->
                        <div class="tab-pane fade" id="files" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="fw-bold m-0">Official Meeting Documents</h5>
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadDocModal">
                                    <i class="fas fa-upload me-1"></i> Upload Document
                                </button>
                            </div>
                            
                            <div class="row">
                                @forelse($meeting->files as $file)
                                <div class="col-md-6 mb-3">
                                    <div class="card border shadow-sm">
                                        <div class="card-body p-3 d-flex align-items-center">
                                            <div class="me-3">
                                                @if(in_array(strtolower($file->file_type), ['pdf'])) <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                                @elseif(in_array(strtolower($file->file_type), ['doc','docx'])) <i class="fas fa-file-word fa-2x text-primary"></i>
                                                @elseif(in_array(strtolower($file->file_type), ['xls','xlsx'])) <i class="fas fa-file-excel fa-2x text-success"></i>
                                                @elseif(in_array(strtolower($file->file_type), ['jpg','jpeg','png'])) <i class="fas fa-file-image fa-2x text-info"></i>
                                                @else <i class="fas fa-file-alt fa-2x text-secondary"></i>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1 overflow-hidden">
                                                <div class="fw-bold text-truncate" title="{{ $file->file_name }}">{{ $file->file_name }}</div>
                                                <div class="small text-muted">{{ $file->file_size }} • Uploaded by {{ $file->uploader->name }}</div>
                                            </div>
                                            <a href="{{ asset('uploads/meetings/files/' . $file->file_path) }}" download class="btn btn-sm btn-light border ms-2">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="col-12 text-center py-5">
                                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                    <p class="text-muted m-0">No documents uploaded for this meeting.</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Participants Sidebar -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white pt-4 pb-3 border-0">
                    <h5 class="fw-bold m-0"><i class="fas fa-users text-primary me-2"></i> Invited Participants</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex align-items-center p-3 border-bottom">
                            <div class="avatar-sm me-3" style="width:40px;height:40px;border-radius:50%;background:#3b82f6;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:bold;">
                                {{ strtoupper(substr($meeting->creator->name, 0, 1)) }}
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">{{ $meeting->creator->name }} <span class="badge bg-primary ms-1" style="font-size:0.6rem;">Organizer</span></div>
                                <div class="small text-muted">{{ $meeting->creator->email }}</div>
                            </div>
                        </li>
                        
                        @forelse($meeting->participants as $participant)
                        <li class="list-group-item d-flex align-items-center p-3">
                            <div class="avatar-sm me-3" style="width:40px;height:40px;border-radius:50%;background:var(--first-color);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:bold;">
                                {{ strtoupper(substr($participant->user->name, 0, 1)) }}
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">{{ $participant->user->name }}</div>
                                <div class="small text-muted">{{ $participant->user->email }}</div>
                            </div>
                            <div>
                                @if($participant->invitation_status == 'Pending')
                                    <span class="badge bg-warning text-dark"><i class="fas fa-clock"></i> Pending</span>
                                @elseif($participant->invitation_status == 'Accepted')
                                    <span class="badge bg-success"><i class="fas fa-check"></i> Accepted</span>
                                @else
                                    <span class="badge bg-danger"><i class="fas fa-times"></i> Declined</span>
                                @endif
                            </div>
                        </li>
                        @empty
                        <li class="list-group-item p-4 text-center text-muted">
                            No additional participants invited.
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Document Upload Modal -->
<div class="modal fade" id="uploadDocModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('meetings.files.store', $meeting->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Upload Official Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info py-2 small">
                        <i class="fas fa-info-circle me-1"></i> Max file size: 25MB. Supported: PDF, Office docs, Images, ZIP.
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select File</label>
                        <input type="file" name="file" class="form-control" required accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.zip,.rar,.mp4">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-upload me-1"></i> Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Meeting Chat Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatTab = document.getElementById('chat-tab');
    const chatContainer = document.getElementById('chatMessages');
    const chatForm = document.getElementById('chatForm');
    const chatInput = document.getElementById('chatInput');
    const chatAttachment = document.getElementById('chatAttachment');
    const attachmentDisplay = document.getElementById('attachmentNameDisplay');
    const currentUserId = {{ session('user_id') }};
    
    let chatLoaded = false;

    // Load messages when tab is shown
    chatTab.addEventListener('shown.bs.tab', function () {
        if(!chatLoaded) {
            fetchMessages();
            chatLoaded = true;
            
            // Poll for new messages every 5 seconds (simulating realtime without websockets)
            setInterval(fetchMessages, 5000);
        }
    });

    chatAttachment.addEventListener('change', function() {
        if(this.files && this.files[0]) {
            let size = this.files[0].size / 1024 / 1024;
            if(size > 25) {
                alert("File exceeds the 25MB limit.");
                this.value = '';
                attachmentDisplay.classList.add('d-none');
                return;
            }
            attachmentDisplay.textContent = '📎 Attached: ' + this.files[0].name;
            attachmentDisplay.classList.remove('d-none');
        } else {
            attachmentDisplay.classList.add('d-none');
        }
    });

    function fetchMessages() {
        fetch(`{{ route('meetings.chat.index', $meeting->id) }}`)
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    renderMessages(data.messages);
                }
            });
    }

    function renderMessages(messages) {
        // Keep the security warning
        const warningHtml = '<div class="text-center text-muted small py-3"><i class="fas fa-lock"></i> Chat is secure and only visible to participants.</div>';
        let html = warningHtml;
        
        messages.forEach(msg => {
            const isMe = msg.sender_id == currentUserId;
            html += `
            <div class="d-flex mb-3 ${isMe ? 'justify-content-end' : ''}">
                <div class="${isMe ? 'text-end' : ''}" style="max-width: 75%;">
                    <div class="small text-muted mb-1 px-2">${isMe ? 'You' : msg.sender} • ${msg.time}</div>
                    <div class="p-3 shadow-sm ${isMe ? 'bg-primary text-white rounded-start rounded-top' : 'bg-white rounded-end rounded-top'}">
                        ${msg.message ? `<div>${msg.message}</div>` : ''}
                        ${msg.attachment ? `
                            <a href="${msg.attachment}" download class="btn btn-sm ${isMe ? 'btn-light' : 'btn-primary'} mt-2 w-100 text-start">
                                <i class="fas fa-paperclip"></i> Download Attachment
                            </a>
                        ` : ''}
                    </div>
                </div>
            </div>`;
        });
        
        chatContainer.innerHTML = html;
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }

    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const btn = document.getElementById('chatSubmitBtn');
        const formData = new FormData(this);
        
        if(!chatInput.value && !chatAttachment.files[0]) return;
        
        btn.disabled = true;
        
        fetch(`{{ route('meetings.chat.store', $meeting->id) }}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                chatInput.value = '';
                chatAttachment.value = '';
                attachmentDisplay.classList.add('d-none');
                fetchMessages();
            } else {
                alert(data.error || 'Error sending message');
            }
        })
        .catch(err => {
            alert('Upload failed. Note max limit is 25MB.');
            console.error(err);
        })
        .finally(() => {
            btn.disabled = false;
        });
    });
});
</script>
@endsection
