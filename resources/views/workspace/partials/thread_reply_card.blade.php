@php
    $isMe = ($currentUser && $reply->user_id === $currentUser->id);
    $userAvatar = $reply->user?->profile_pic ? asset('uploads/profiles/' . $reply->user->profile_pic) : asset('image.png');
    $roleName = $reply->user?->role?->name ?? 'Member';
@endphp

<div class="thread-reply group relative flex gap-3 px-4 py-2 hover:bg-slate-50 transition-colors duration-150" id="reply-{{ $reply->id }}">
    <div class="shrink-0">
        <img src="{{ $userAvatar }}" 
             alt="{{ $reply->user?->name }}" 
             class="w-8 h-8 rounded-xl object-cover border border-slate-200"
             loading="lazy"
             onerror="this.onerror=null; this.src='{{ asset('image.png') }}';">
    </div>

    <div class="flex-1 min-w-0">
        <div class="flex items-baseline gap-2 mb-1">
            <span class="font-bold text-slate-900 text-xs hover:underline cursor-pointer">
                {{ $reply->user?->name ?? 'Member' }}
            </span>
            <span class="text-[10px] text-slate-400 font-medium">
                {{ $reply->created_at->format('g:i A') }}
            </span>
        </div>

        @if($reply->message)
            <div class="text-xs text-slate-800 leading-relaxed break-words whitespace-pre-wrap select-text">{!! preg_replace('/@([a-zA-Z0-9_\-\.]+)/', '<span class="bg-blue-100 text-blue-700 px-1 py-0.5 rounded font-semibold text-[11px]">@$1</span>', e($reply->message)) !!}</div>
        @endif

        @if($reply->attachments && $reply->attachments->isNotEmpty())
            <div class="mt-2 flex flex-wrap gap-2">
                @foreach($reply->attachments as $att)
                    @if($att->isImage())
                        <img src="{{ $att->url }}" 
                             alt="{{ $att->original_name }}" 
                             class="max-w-xs max-h-48 rounded-xl object-cover border border-slate-200 cursor-pointer shadow-2xs"
                             onclick="openLightbox('{{ $att->url }}', '{{ addslashes($att->original_name) }}')"
                             onerror="this.onerror=null; this.src='{{ asset('image.png') }}';">
                    @else
                        <a href="{{ $att->url }}" download="{{ $att->original_name }}" class="flex items-center gap-2 p-2 bg-white border border-slate-200 rounded-lg text-xs text-slate-700 hover:bg-slate-50">
                            <i class="fas fa-file text-blue-500"></i>
                            <span class="truncate max-w-[140px]">{{ $att->original_name }}</span>
                        </a>
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    <!-- Quick Reaction / Delete on hover -->
    <div class="absolute right-3 top-2 hidden group-hover:flex items-center bg-white border border-slate-200 rounded-lg shadow p-0.5 gap-0.5">
        <button type="button" onclick="toggleMessageReaction({{ $reply->id }}, '👍')" class="w-6 h-6 rounded hover:bg-slate-100 text-xs flex items-center justify-center">👍</button>
        @if($isMe)
            <button type="button" onclick="deleteWorkspaceMessage({{ $reply->id }})" class="w-6 h-6 rounded hover:bg-red-50 text-red-500 text-xs flex items-center justify-center"><i class="far fa-trash-can"></i></button>
        @endif
    </div>
</div>

