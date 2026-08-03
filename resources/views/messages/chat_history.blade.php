@php $lastDate = null; @endphp
@forelse($chatMessages as $msg)
    @php
        $msgDate = $msg->created_at->toDateString();
        $isMine = $msg->sender_id == session('user_id');
    @endphp
    @if($msgDate !== $lastDate)
        <div class="chat-day-divider text-center my-4 font-semibold text-slate-400 text-xs flex items-center gap-2">
            <span class="flex-grow h-px bg-slate-200"></span>
            {{ \Carbon\Carbon::parse($msgDate)->isToday() ? 'Today' : \Carbon\Carbon::parse($msgDate)->format('M d, Y') }}
            <span class="flex-grow h-px bg-slate-200"></span>
        </div>
        @php $lastDate = $msgDate; @endphp
    @endif

    <div class="flex gap-3 max-w-xl {{ $isMine ? 'ml-auto flex-row-reverse' : '' }}" data-msg-id="{{ $msg->id }}">
        @if(!$isMine)
            @if($selectedChatUser->profile_pic)
                <img src="{{ asset('uploads/profiles/'.$selectedChatUser->profile_pic) }}" class="w-8 h-8 rounded-full object-cover shrink-0">
            @else
                <div class="w-8 h-8 text-white rounded-full flex items-center justify-center font-bold text-xs shrink-0" style="background:{{ ['#ff5532','#10b981','#3b82f6','#8b5cf6','#f59e0b'][crc32($selectedChatUser->name) % 5] }}">
                    {{ strtoupper(substr($selectedChatUser->name, 0, 1)) }}
                </div>
            @endif
        @endif
        
        <div class="flex flex-col {{ $isMine ? 'items-end' : 'items-start' }}">
            <div class="flex items-baseline gap-2 mb-1 {{ $isMine ? 'flex-row-reverse' : '' }}">
                <span class="text-[10px] font-bold text-slate-600">{{ $isMine ? 'You' : $selectedChatUser->name }}</span>
                <span class="text-[9px] text-slate-400">{{ $msg->created_at->format('h:i A') }}</span>
            </div>
            <div class="p-3 shadow-sm border {{ $isMine ? 'bg-primary text-white border-transparent message-bubble-out' : 'bg-white text-slate-700 border-slate-100 message-bubble-in' }}">
                @if($msg->body === '[VIDEO_CALL_INVITE]')
                    <div class="text-center p-1">
                        <i class="fas fa-video fa-2x mb-2 {{ $isMine ? 'text-white' : 'text-success' }}"></i><br>
                        <button class="btn btn-xs {{ $isMine ? 'btn-light' : 'btn-success' }} fw-bold" style="font-size:0.75rem;" data-bs-toggle="modal" data-bs-target="#jitsiModal">
                            Join Video Call
                        </button>
                    </div>
                @else
                    <p class="text-sm m-0" style="white-space: pre-wrap; word-break: break-word;">{{ $msg->body }}</p>
                @endif

                @if($msg->attachment)
                    <div class="mt-2 pt-2 border-t {{ $isMine ? 'border-white/20' : 'border-slate-100' }}">
                        <a href="{{ asset('uploads/messages/'.$msg->attachment) }}" target="_blank" class="text-xs font-semibold hover:underline inline-flex items-center gap-1 {{ $isMine ? 'text-white' : 'text-primary' }}">
                            <i class="fas fa-paperclip"></i> View Attached Resource
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@empty
    <div class="flex flex-col items-center justify-center py-20 text-slate-400">
        <i class="fas fa-comments fa-3x mb-3 opacity-40"></i>
        <h5 class="font-bold text-slate-600 text-sm">Start the conversation</h5>
        <p class="text-xs">Send a direct message to begin collaboration.</p>
    </div>
@endforelse
