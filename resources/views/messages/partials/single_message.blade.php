@php
    $sender = $msg->sender;
    $senderName = $sender ? $sender->name : 'System';
    $senderRole = $sender?->role?->role_name ?? 'Team Member';
    $senderInitials = strtoupper(substr($senderName, 0, 1));
    $profilePic = $sender && $sender->profile_pic ? asset('uploads/profiles/'.$sender->profile_pic) : null;
    $timeDiff = $msg->created_at ? $msg->created_at->diffForHumans(null, true, true) : 'just now';
    
    // Avatar colors
    $colors = ['#ff5532', '#10b981', '#3b82f6', '#8b5cf6', '#f59e0b', '#06b6d4', '#ec4899'];
    $avatarColor = $colors[crc32($senderName) % count($colors)];
    
    $reactions = $msg->reactions ?? [];
    if (is_string($reactions)) {
        $reactions = json_decode($reactions, true) ?? [];
    }
    
    $mentions = $msg->mentions ?? [];
    if (is_string($mentions)) {
        $mentions = json_decode($mentions, true) ?? [];
    }
    
    $attachments = $msg->attachments ?? [];
    if (is_string($attachments)) {
        $attachments = json_decode($attachments, true) ?? [];
    }

    $linkPreview = $msg->link_preview ?? null;
    if (is_string($linkPreview)) {
        $linkPreview = json_decode($linkPreview, true) ?? null;
    }

    $currentUserId = isset($currentUser) ? $currentUser->id : (session('user_id') ?? 1);
@endphp

<div class="slack-message-item group relative flex items-start gap-3.5 px-6 py-2.5 hover:bg-slate-50/70 transition-colors duration-150" id="msg-item-{{ $msg->id }}" data-msg-id="{{ $msg->id }}">
    <!-- Avatar -->
    <div class="relative shrink-0 mt-0.5">
        @if($profilePic)
            <img src="{{ $profilePic }}" alt="{{ $senderName }}" class="w-9 h-9 rounded-full object-cover shadow-xs border border-slate-100">
        @else
            <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs text-white shadow-xs" style="background-color: {{ $avatarColor }};">
                {{ $senderInitials }}
            </div>
        @endif
    </div>

    <!-- Content -->
    <div class="flex-grow min-w-0 pr-12">
        <!-- Message Header -->
        <div class="flex items-center gap-2 mb-1">
            <span class="font-bold text-sm text-slate-900 tracking-tight cursor-pointer hover:underline">{{ $senderName }}</span>
            <span class="text-[11px] font-medium text-slate-400">{{ $timeDiff }}</span>
        </div>

        <!-- Message Body -->
        <div class="text-[13.5px] leading-relaxed text-slate-700 whitespace-pre-line break-words">
            @php
                $formattedMessage = e($msg->message);
                // Highlight mentions with Slack style pills
                $formattedMessage = preg_replace(
                    '/(@[a-zA-Z0-9_\-\.\/]+(?:\s[a-zA-Z0-9_\-\.]+)?)/',
                    '<span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 cursor-pointer transition-colors">$1</span>',
                    $formattedMessage
                );
            @endphp
            {!! $formattedMessage !!}
        </div>

        <!-- Voice Message Player -->
        @if($msg->type === 'voice' && $msg->voice_path)
            <div class="mt-2.5 inline-flex items-center gap-3 px-3.5 py-2 bg-slate-100/90 rounded-2xl border border-slate-200/80 max-w-sm">
                <button type="button" class="w-8 h-8 rounded-full bg-slate-900 text-white flex items-center justify-center hover:bg-slate-800 transition-colors voice-play-btn" onclick="togglePlayVoice(this, '{{ $msg->voice_path }}')">
                    <i class="fas fa-play text-xs"></i>
                </button>
                <div class="flex-grow">
                    <div class="text-[11px] font-semibold text-slate-700">Voice Note</div>
                    <div class="w-36 h-1.5 bg-slate-300 rounded-full overflow-hidden mt-1 relative">
                        <div class="voice-progress-bar h-full bg-indigo-600 rounded-full" style="width: 0%;"></div>
                    </div>
                </div>
                <span class="text-[10px] font-mono text-slate-500 voice-duration">0:15</span>
            </div>
        @endif

        <!-- Link Preview Card (Figma Style) -->
        @if($linkPreview)
            <div class="mt-2.5 max-w-md bg-white rounded-xl border border-slate-200/90 p-3.5 shadow-2xs hover:shadow-xs transition-shadow duration-150">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-lg bg-orange-50 border border-orange-100 flex items-center justify-center shrink-0">
                            @if(isset($linkPreview['icon']) && $linkPreview['icon'] === 'figma')
                                <svg class="w-5 h-5 text-orange-600" viewBox="0 0 38 57" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M19 28.5C19 23.2533 23.2533 19 28.5 19C33.7467 19 38 23.2533 38 28.5C38 33.7467 33.7467 38 28.5 38C23.2533 38 19 33.7467 19 28.5Z" fill="#1ABCFE"/>
                                    <path d="M0 47.5C0 42.2533 4.25329 38 9.5 38H19V47.5C19 52.7467 14.7467 57 9.5 57C4.25329 57 0 52.7467 0 47.5Z" fill="#0ACF83"/>
                                    <path d="M19 0V19H28.5C33.7467 19 38 14.7467 38 9.5C38 4.25329 33.7467 0 28.5 0H19Z" fill="#FF7262"/>
                                    <path d="M0 9.5C0 14.7467 4.25329 19 9.5 19H19V0H9.5C4.25329 0 0 4.25329 0 9.5Z" fill="#F24E1E"/>
                                    <path d="M0 28.5C0 33.7467 4.25329 38 9.5 38H19V19H9.5C4.25329 19 0 23.2533 0 28.5Z" fill="#A259FF"/>
                                </svg>
                            @else
                                <i class="fas fa-link text-indigo-600"></i>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <h5 class="text-xs font-bold text-slate-800 truncate mb-0.5">{{ $linkPreview['title'] ?? 'Shared Link' }}</h5>
                            <p class="text-[11px] text-slate-400 truncate mb-0 font-medium">{{ $linkPreview['subtitle'] ?? 'website.com' }}</p>
                        </div>
                    </div>
                    <a href="{{ $linkPreview['url'] ?? '#' }}" target="_blank" rel="noopener noreferrer" class="shrink-0 text-xs font-semibold px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 transition-colors shadow-2xs text-decoration-none">
                        {{ $linkPreview['button_text'] ?? 'Quick view' }}
                    </a>
                </div>
            </div>
        @endif

        <!-- File Attachments -->
        @if(!empty($attachments))
            <div class="mt-2.5 flex flex-wrap gap-2">
                @foreach($attachments as $att)
                    @php
                        $isImg = in_array(strtolower(pathinfo($att['name'] ?? '', PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
                    @endphp
                    @if($isImg)
                        <div class="rounded-xl overflow-hidden border border-slate-200 max-w-xs shadow-2xs group/img relative">
                            <img src="{{ $att['url'] }}" alt="{{ $att['name'] }}" class="max-h-48 w-auto object-cover cursor-pointer hover:opacity-95 transition-opacity" onclick="window.open('{{ $att['url'] }}', '_blank')">
                            <div class="absolute bottom-1 right-1 bg-black/70 text-white text-[9px] px-1.5 py-0.5 rounded font-mono">{{ $att['size'] ?? '' }}</div>
                        </div>
                    @else
                        <a href="{{ $att['url'] }}" target="_blank" download class="inline-flex items-center gap-2 px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 transition-colors text-decoration-none">
                            <i class="fas fa-file-alt text-slate-400"></i>
                            <span class="truncate max-w-[160px]">{{ $att['name'] }}</span>
                            <span class="text-[10px] text-slate-400 font-mono">{{ $att['size'] ?? '' }}</span>
                            <i class="fas fa-download text-slate-400 ms-1"></i>
                        </a>
                    @endif
                @endforeach
            </div>
        @endif

        <!-- Reactions Row -->
        <div class="mt-2 flex flex-wrap items-center gap-1.5" id="reactions-{{ $msg->id }}">
            @if(!empty($reactions))
                @foreach($reactions as $emoji => $uids)
                    @php
                        $hasReacted = in_array($currentUserId, $uids);
                        $count = count($uids);
                    @endphp
                    @if($count > 0)
                        <button type="button" class="reaction-pill inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium border transition-colors {{ $hasReacted ? 'bg-indigo-50 border-indigo-200 text-indigo-700 font-bold' : 'bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100' }}" onclick="toggleReaction({{ $msg->id }}, '{{ $emoji }}')">
                            <span>{{ $emoji }}</span>
                            @if($count > 1)
                                <span class="text-[11px]">{{ $count }}</span>
                            @endif
                        </button>
                    @endif
                @endforeach
            @endif

            <!-- Add reaction button (visible on hover) -->
            <button type="button" class="add-reaction-btn opacity-0 group-hover:opacity-100 inline-flex items-center justify-center w-6 h-6 rounded-full border border-transparent hover:border-slate-200 hover:bg-slate-100 text-slate-400 hover:text-slate-600 text-xs transition-all" onclick="showReactionPicker(event, {{ $msg->id }})" title="Add reaction">
                <i class="far fa-smile"></i>
            </button>
        </div>
    </div>

    <!-- Quick Floating Action Bar on Hover -->
    <div class="absolute right-4 -top-3.5 hidden group-hover:flex items-center bg-white border border-slate-200 rounded-lg shadow-sm px-1 py-0.5 gap-0.5 z-10">
        <button type="button" class="p-1 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded text-xs" onclick="toggleReaction({{ $msg->id }}, '👍')" title="Thumbs up">
            👍
        </button>
        <button type="button" class="p-1 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded text-xs" onclick="toggleReaction({{ $msg->id }}, '❤️')" title="Heart">
            ❤️
        </button>
        <button type="button" class="p-1 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded text-xs" onclick="toggleReaction({{ $msg->id }}, '😊')" title="Smile">
            😊
        </button>
        <div class="w-px h-3.5 bg-slate-200 mx-0.5"></div>
        <button type="button" class="p-1 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded text-xs" onclick="replyInThread({{ $msg->id }}, '{{ addslashes($senderName) }}')" title="Reply in thread">
            <i class="far fa-comment-dots"></i>
        </button>
        <button type="button" class="p-1 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded text-xs" onclick="copyMessageText({{ $msg->id }})" title="Copy text">
            <i class="far fa-copy"></i>
        </button>
    </div>
</div>
