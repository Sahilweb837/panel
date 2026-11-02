@php
    $isMe = ($currentUser && $msg->user_id === $currentUser->id);
    $userAvatar = $msg->user?->profile_pic ? asset('uploads/profiles/' . $msg->user->profile_pic) : asset('image.png');
    $roleName = $msg->user?->role?->name ?? 'Member';
    $replyCount = $msg->replies ? $msg->replies->count() : 0;
    $reactionsGrouped = $msg->reactions->groupBy('emoji');
@endphp

<div class="slack-message group relative flex gap-3 px-4 py-2 hover:bg-slate-50/80 transition-colors duration-150 {{ !empty($isThread) ? 'border-b border-slate-100 pb-3' : '' }}" 
     id="msg-{{ $msg->id }}" 
     data-id="{{ $msg->id }}"
     data-user-id="{{ $msg->user_id }}">
    
    <!-- User Avatar -->
    <div class="shrink-0 relative">
        <img src="{{ $userAvatar }}" 
             alt="{{ $msg->user?->name }}" 
             class="w-9 h-9 rounded-xl object-cover border border-slate-200/80 shadow-2xs"
             loading="lazy"
             onerror="this.onerror=null; this.src='{{ asset('image.png') }}';">
    </div>

    <!-- Message Body Area -->
    <div class="flex-1 min-w-0">
        <!-- Header: Name + Role + Timestamp -->
        <div class="flex items-baseline gap-2 mb-1">
            <span class="font-bold text-slate-900 text-xs sm:text-sm hover:underline cursor-pointer" onclick="openDirectMessage({{ $msg->user_id }})">
                {{ $msg->user?->name ?? 'Unknown Member' }}
            </span>

            @if(in_array(strtolower($roleName), ['admin', 'super admin', 'faculty', 'teacher']))
                <span class="px-1.5 py-0.2 rounded text-[10px] font-semibold bg-blue-50 text-blue-600 border border-blue-200/60">
                    {{ $roleName }}
                </span>
            @endif

            <span class="text-[11px] text-slate-400 font-medium">
                {{ $msg->created_at->format('g:i A') }}
            </span>

            @if($msg->is_edited)
                <span class="text-[10px] text-slate-400 italic">(edited)</span>
            @endif

            @if($msg->is_pinned)
                <span class="text-[10px] text-amber-500 font-semibold flex items-center gap-1">
                    <i class="fas fa-thumbtack text-[9px]"></i> Pinned
                </span>
            @endif
        </div>

        <!-- Text Content -->
        @if($msg->message)
            <div class="text-xs sm:text-sm text-slate-800 leading-relaxed break-words whitespace-pre-wrap select-text">{!! preg_replace('/@([a-zA-Z0-9_\-\.]+)/', '<span class="bg-blue-100/80 text-blue-700 px-1.5 py-0.5 rounded font-semibold text-xs">@$1</span>', e($msg->message)) !!}</div>
        @endif

        <!-- File / Image Attachments -->
        @if($msg->attachments && $msg->attachments->isNotEmpty())
            <div class="mt-2 flex flex-wrap gap-2.5">
                @foreach($msg->attachments as $att)
                    @if($att->isImage())
                        <div class="relative group/att rounded-xl overflow-hidden border border-slate-200 bg-slate-100 max-w-sm max-h-72 shadow-2xs">
                            <img src="{{ $att->url }}" 
                                 alt="{{ $att->original_name }}" 
                                 class="w-full h-full object-cover max-h-72 cursor-pointer hover:scale-102 transition-transform duration-200"
                                 loading="lazy"
                                 onclick="openLightbox('{{ $att->url }}', '{{ addslashes($att->original_name) }}')"
                                 onerror="this.onerror=null; this.src='{{ asset('image.png') }}';">
                            <a href="{{ $att->url }}" download="{{ $att->original_name }}" class="absolute top-2 right-2 w-7 h-7 bg-slate-900/70 hover:bg-slate-900 text-white rounded-lg flex items-center justify-center text-xs opacity-0 group-hover/att:opacity-100 transition shadow">
                                <i class="fas fa-download"></i>
                            </a>
                        </div>
                    @else
                        <a href="{{ $att->url }}" download="{{ $att->original_name }}" class="flex items-center gap-3 p-3 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl max-w-xs transition text-left text-slate-700 group/file">
                            <div class="w-9 h-9 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                                <i class="fas fa-file-lines text-base"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-xs font-semibold truncate group-hover/file:text-blue-600">{{ $att->original_name }}</div>
                                <div class="text-[10px] text-slate-400">{{ round($att->file_size / 1024, 1) }} KB</div>
                            </div>
                            <i class="fas fa-arrow-down-to-bracket text-xs text-slate-400 group-hover/file:text-slate-700"></i>
                        </a>
                    @endif
                @endforeach
            </div>
        @endif

        <!-- Emoji Reactions Pill Bar -->
        <div class="mt-2 flex flex-wrap items-center gap-1.5" id="reactions-{{ $msg->id }}">
            @foreach($reactionsGrouped as $emoji => $reactList)
                @php
                    $userReacted = $reactList->where('user_id', $currentUser->id)->isNotEmpty();
                @endphp
                <button type="button" 
                        onclick="toggleMessageReaction({{ $msg->id }}, '{{ $emoji }}')"
                        class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs border transition {{ $userReacted ? 'bg-blue-50 border-blue-300 text-blue-700 font-semibold' : 'bg-slate-50 hover:bg-slate-100 border-slate-200 text-slate-600' }}">
                    <span>{{ $emoji }}</span>
                    <span class="text-[11px] font-bold">{{ $reactList->count() }}</span>
                </button>
            @endforeach

            <!-- Quick Add Reaction Button -->
            <button type="button" 
                    onclick="openReactionPicker(event, {{ $msg->id }})" 
                    class="w-6 h-6 rounded-full hover:bg-slate-200/70 text-slate-400 hover:text-slate-700 flex items-center justify-center text-xs transition" 
                    title="Add reaction">
                <i class="far fa-face-smile"></i>
            </button>
        </div>

        <!-- Thread Replies Counter (Slack signature feature) -->
        @if(empty($isThread) && $replyCount > 0)
            <div class="mt-2">
                <button type="button" 
                        onclick="openThreadDrawer({{ $msg->id }})" 
                        class="inline-flex items-center gap-2 text-xs font-semibold text-blue-600 hover:text-blue-700 hover:underline py-1 px-2 rounded-lg hover:bg-blue-50 transition">
                    <i class="fas fa-reply text-[11px] transform rotate-180"></i>
                    <span>{{ $replyCount }} {{ Str::plural('reply', $replyCount) }}</span>
                    <span class="text-[10px] text-slate-400 font-normal">Last reply {{ $msg->replies->last()->created_at->diffForHumans(null, true) }} ago</span>
                </button>
            </div>
        @endif
    </div>

    <!-- Hover Quick Action Floating Toolbar (Slack-style) -->
    <div class="absolute right-4 -top-3 hidden group-hover:flex items-center bg-white border border-slate-200/90 rounded-xl shadow-md p-0.5 gap-0.5 z-20">
        <!-- Quick Emoji Reaction -->
        <button type="button" onclick="toggleMessageReaction({{ $msg->id }}, '👍')" class="w-7 h-7 rounded-lg hover:bg-slate-100 text-slate-600 flex items-center justify-center text-xs" title="React 👍">
            👍
        </button>
        <button type="button" onclick="toggleMessageReaction({{ $msg->id }}, '❤️')" class="w-7 h-7 rounded-lg hover:bg-slate-100 text-slate-600 flex items-center justify-center text-xs" title="React ❤️">
            ❤️
        </button>
        <button type="button" onclick="openReactionPicker(event, {{ $msg->id }})" class="w-7 h-7 rounded-lg hover:bg-slate-100 text-slate-500 hover:text-slate-800 flex items-center justify-center text-xs" title="More reactions">
            <i class="far fa-face-smile"></i>
        </button>

        <!-- Reply in Thread (Slack Signature) -->
        @if(empty($isThread))
            <button type="button" onclick="openThreadDrawer({{ $msg->id }})" class="w-7 h-7 rounded-lg hover:bg-slate-100 text-slate-500 hover:text-slate-800 flex items-center justify-center text-xs" title="Reply in thread">
                <i class="fas fa-message"></i>
            </button>
        @endif

        <!-- Delete if author or admin -->
        @if($isMe || ($currentUser && $currentUser->role && strtolower($currentUser->role->name) === 'admin'))
            <button type="button" onclick="deleteWorkspaceMessage({{ $msg->id }})" class="w-7 h-7 rounded-lg hover:bg-red-50 text-slate-400 hover:text-red-600 flex items-center justify-center text-xs" title="Delete message">
                <i class="far fa-trash-can"></i>
            </button>
        @endif
    </div>
</div>

