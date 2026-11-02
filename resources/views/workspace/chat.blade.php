<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $activeChannel ? '#' . $activeChannel->name : ($activeConversation ? 'Direct Message' : $workspace->name) }} | Slack LMS Workspace</title>
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        slack: {
                            strip: '#2b092b',
                            stripActive: '#350d36',
                            sidebar: '#3f0e40',
                            sidebarHover: '#350d36',
                            sidebarText: '#bcabbc',
                            sidebarActive: '#1164a3',
                            sidebarBorder: '#522653',
                            bg: '#ffffff',
                            border: '#e2e8f0',
                            muted: '#616061',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    }
                }
            }
        }
    </script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #ffffff;
            color: #1d1c1d;
            height: 100vh;
            overflow: hidden;
        }

        /* Slack Scrollbars */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.4);
            border-radius: 999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(100, 116, 139, 0.7);
        }

        /* Active channel indicator */
        .channel-active {
            background-color: #1164a3 !important;
            color: #ffffff !important;
            font-weight: 600;
        }
        .channel-active i {
            color: #ffffff !important;
        }

        /* Lightbox Image */
        #slackLightboxModal.hidden { display: none; }
        #slackLightboxModal.flex { display: flex; }

        /* Floating Toasts */
        #workspaceToastContainer {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            pointer-events: none;
        }
        .workspace-toast {
            pointer-events: auto;
            min-width: 300px;
            max-width: 380px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.12);
            padding: 12px 14px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            animation: slideIn 0.25s ease-out;
        }
        @keyframes slideIn {
            from { transform: translateX(30px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>
</head>
<body class="flex h-screen overflow-hidden antialiased select-none">

    <!-- Global Floating Toast Container -->
    <div id="workspaceToastContainer"></div>

    <!-- ========================================================
         PANEL 1: FAR-LEFT WORKSPACE SWITCHER STRIP (~68px)
         ======================================================== -->
    <aside class="w-16 bg-[#2b092b] border-r border-[#3f0e40] flex flex-col items-center py-3 shrink-0 z-30 select-none">
        
        <!-- App / Home Icon -->
        <a href="{{ route('dashboard') }}" class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center mb-3 transition shadow-xs group" title="Return to LMS ERP Dashboard">
            <i class="fas fa-school text-sm text-pink-300 group-hover:scale-110 transition-transform"></i>
        </a>

        <div class="w-8 h-[1px] bg-white/10 mb-3"></div>

        <!-- Workspaces List -->
        <div class="flex-1 overflow-y-auto space-y-2.5 w-full flex flex-col items-center">
            @foreach($userWorkspaces as $ws)
                @php
                    $isActive = ($ws->id === $workspace->id);
                    $initials = strtoupper(substr($ws->name, 0, 2));
                @endphp
                <div class="relative group">
                    <!-- Left white pill active indicator -->
                    @if($isActive)
                        <div class="absolute -left-3 top-2 bottom-2 w-1.5 bg-white rounded-r-full"></div>
                    @endif

                    <a href="{{ route('workspace.show', $ws->slug) }}" 
                       class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-sm transition-all duration-150 {{ $isActive ? 'bg-white text-[#3f0e40] shadow-md ring-2 ring-white/50' : 'bg-white/15 hover:bg-white/25 text-white' }}"
                       title="{{ $ws->name }}">
                        @if($ws->logo)
                            <img src="{{ $ws->logo }}" alt="{{ $ws->name }}" class="w-full h-full rounded-xl object-cover" onerror="this.onerror=null; this.src='{{ asset('image.png') }}';">
                        @else
                            {{ $initials }}
                        @endif
                    </a>

                    <!-- Hover Tooltip -->
                    <div class="absolute left-14 top-1.5 hidden group-hover:block bg-slate-900 text-white text-xs font-semibold px-2.5 py-1 rounded-md shadow-lg whitespace-nowrap z-50">
                        {{ $ws->name }}
                    </div>
                </div>
            @endforeach

            <!-- + Add New Workspace Button -->
            <a href="{{ route('workspace.create') }}" 
               class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-sm transition border border-dashed border-white/25 hover:border-white/50"
               title="Create New Workspace">
                <i class="fas fa-plus text-xs"></i>
            </a>
        </div>

        <!-- Bottom User Profile Avatar with Online Green Dot -->
        <div class="mt-auto pt-2 flex flex-col items-center gap-2">
            <!-- NetChat Collaboration Suite Link -->
            <a href="{{ route('netchat.index') }}" class="w-9 h-9 rounded-xl bg-pink-600/30 hover:bg-pink-600/50 text-pink-200 flex items-center justify-center text-xs transition" title="Switch to NetChat View">
                <i class="fas fa-comments text-xs"></i>
            </a>

            <!-- User Avatar -->
            <div class="relative group cursor-pointer" onclick="openProfileModal()">
                <img src="{{ $currentUser->profile_pic ? asset('uploads/profiles/' . $currentUser->profile_pic) : asset('image.png') }}" 
                     alt="{{ $currentUser->name }}" 
                     class="w-10 h-10 rounded-xl object-cover border border-white/20 shadow-xs"
                     onerror="this.onerror=null; this.src='{{ asset('image.png') }}';">
                <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-500 rounded-full border-2 border-[#2b092b]"></span>
                
                <div class="absolute left-14 bottom-1 hidden group-hover:block bg-slate-900 text-white text-xs px-2.5 py-1 rounded-md shadow-lg whitespace-nowrap z-50">
                    {{ $currentUser->name }} (Online)
                </div>
            </div>
        </div>
    </aside>

    <!-- ========================================================
         PANEL 2: MIDDLE CHANNELS & DMS SIDEBAR (~260px)
         ======================================================== -->
    <aside id="slackChannelsSidebar" class="w-64 bg-[#3f0e40] text-[#bcabbc] flex flex-col shrink-0 border-r border-[#522653] transition-all duration-200 z-20">
        
        <!-- Workspace Header & Dropdown -->
        <div class="h-14 px-4 flex items-center justify-between border-b border-[#522653] relative group">
            <button type="button" onclick="toggleWorkspaceDropdown()" class="flex items-center gap-2 text-white font-bold text-base hover:text-white/90 truncate flex-1 text-left">
                <span class="truncate">{{ $workspace->name }}</span>
                <i class="fas fa-chevron-down text-xs text-[#bcabbc]"></i>
            </button>

            <!-- New Message Compose Button -->
            <button type="button" onclick="openNewDmModal()" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-xs transition shadow-2xs" title="New message / DM">
                <i class="fas fa-pen-to-square"></i>
            </button>

            <!-- Workspace Action Dropdown -->
            <div id="workspaceActionDropdown" class="hidden absolute left-4 top-14 w-60 bg-white text-slate-800 rounded-xl shadow-2xl border border-slate-200 py-1.5 z-50 text-xs">
                <div class="px-3 py-2 border-b border-slate-100">
                    <div class="font-bold text-slate-900">{{ $workspace->name }}</div>
                    <div class="text-[11px] text-slate-500">{{ $workspace->members->count() }} active members</div>
                </div>
                <button type="button" onclick="openInviteModal()" class="w-full text-left px-3.5 py-2 hover:bg-slate-50 flex items-center gap-2.5 font-medium">
                    <i class="fas fa-user-plus text-blue-600 w-4"></i>
                    <span>Invite people to {{ $workspace->name }}</span>
                </button>
                <button type="button" onclick="openNewChannelModal()" class="w-full text-left px-3.5 py-2 hover:bg-slate-50 flex items-center gap-2.5 font-medium">
                    <i class="fas fa-hashtag text-slate-500 w-4"></i>
                    <span>Create a channel</span>
                </button>
                <a href="{{ route('workspace.create') }}" class="block px-3.5 py-2 hover:bg-slate-50 flex items-center gap-2.5 font-medium text-slate-700">
                    <i class="fas fa-plus text-slate-500 w-4"></i>
                    <span>Create a new workspace</span>
                </a>
            </div>
        </div>

        <!-- Jump to / Search Shortcut (Ctrl+K) -->
        <div class="p-3">
            <button type="button" onclick="openSearchModal()" class="w-full py-1.5 px-3 rounded-lg bg-[#522653]/60 hover:bg-[#522653] text-[#bcabbc] hover:text-white text-xs flex items-center justify-between transition border border-white/5">
                <div class="flex items-center gap-2">
                    <i class="fas fa-search text-[11px]"></i>
                    <span>Jump to...</span>
                </div>
                <kbd class="text-[10px] bg-black/20 px-1.5 py-0.5 rounded font-mono">⌘K</kbd>
            </button>
        </div>

        <!-- Scrollable Navigation Tree -->
        <div class="flex-1 overflow-y-auto px-2 space-y-4 text-xs font-medium">
            
            <!-- Quick Links -->
            <div class="space-y-0.5">
                <button type="button" onclick="filterThreads()" class="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-md hover:bg-[#350d36] hover:text-white transition text-left">
                    <i class="far fa-comments text-sm w-4 text-center"></i>
                    <span>Threads</span>
                </button>
                <a href="{{ route('netchat.index') }}" class="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-md hover:bg-[#350d36] hover:text-white transition text-left">
                    <i class="fas fa-headphones text-sm w-4 text-center text-pink-300"></i>
                    <span>Huddles & Voice</span>
                </a>
            </div>

            <!-- CHANNELS SECTION -->
            <div>
                <div class="flex items-center justify-between px-2.5 py-1 text-[11px] font-bold tracking-wider uppercase text-[#bcabbc]/80 hover:text-white">
                    <span class="cursor-pointer" onclick="toggleSection('channelsList')">
                        <i class="fas fa-caret-down mr-1"></i> Channels
                    </span>
                    <button type="button" onclick="openNewChannelModal()" class="hover:text-white p-1" title="Add Channel">
                        <i class="fas fa-plus text-xs"></i>
                    </button>
                </div>

                <div id="channelsList" class="space-y-0.5 mt-1">
                    @foreach($channels as $ch)
                        @php
                            $isChActive = ($activeChannel && $activeChannel->id === $ch->id);
                        @endphp
                        <a href="{{ route('workspace.show', ['workspace' => $workspace->slug, 'channel' => $ch->slug]) }}" 
                           class="flex items-center justify-between px-2.5 py-1.5 rounded-md transition text-left group {{ $isChActive ? 'channel-active' : 'hover:bg-[#350d36] hover:text-white' }}">
                            <div class="flex items-center gap-2 truncate">
                                @if($ch->type === 'announcement')
                                    <i class="fas fa-bullhorn text-[11px] text-amber-400"></i>
                                @elseif($ch->type === 'private')
                                    <i class="fas fa-lock text-[11px]"></i>
                                @else
                                    <i class="fas fa-hashtag text-[11px]"></i>
                                @endif
                                <span class="truncate">{{ $ch->name }}</span>
                            </div>
                            @if($ch->type === 'announcement')
                                <span class="text-[9px] px-1 py-0.2 rounded bg-amber-500/20 text-amber-300 font-semibold">Announce</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- DIRECT MESSAGES SECTION -->
            <div>
                <div class="flex items-center justify-between px-2.5 py-1 text-[11px] font-bold tracking-wider uppercase text-[#bcabbc]/80 hover:text-white">
                    <span class="cursor-pointer" onclick="toggleSection('dmsList')">
                        <i class="fas fa-caret-down mr-1"></i> Direct Messages
                    </span>
                    <button type="button" onclick="openNewDmModal()" class="hover:text-white p-1" title="New DM">
                        <i class="fas fa-plus text-xs"></i>
                    </button>
                </div>

                <div id="dmsList" class="space-y-0.5 mt-1">
                    @forelse($workspaceMembers as $mUser)
                        @if($mUser->id !== $currentUser->id)
                            @php
                                $isDmActive = ($activeConversation && $activeConversation->type === 'direct' && $activeConversation->users->contains('id', $mUser->id));
                            @endphp
                            <form action="{{ route('workspace.conversations.store') }}" method="POST" class="m-0 p-0">
                                @csrf
                                <input type="hidden" name="workspace_id" value="{{ $workspace->id }}">
                                <input type="hidden" name="type" value="direct">
                                <input type="hidden" name="recipient_id" value="{{ $mUser->id }}">
                                
                                <button type="submit" class="w-full flex items-center gap-2 px-2.5 py-1.5 rounded-md transition text-left {{ $isDmActive ? 'channel-active' : 'hover:bg-[#350d36] hover:text-white' }}">
                                    <span class="w-2 h-2 rounded-full bg-emerald-400 shrink-0"></span>
                                    <span class="truncate">{{ $mUser->name }}</span>
                                    @if(strtolower($mUser->role?->name ?? '') === 'admin')
                                        <span class="ml-auto text-[9px] px-1 rounded bg-blue-500/20 text-blue-300">Admin</span>
                                    @endif
                                </button>
                            </form>
                        @endif
                    @empty
                        <div class="px-3 py-2 text-slate-400 text-[11px] italic">No other members yet. Invite your colleagues!</div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Footer / Workspace Info -->
        <div class="p-3 border-t border-[#522653] text-[11px] flex items-center justify-between text-[#bcabbc]/80">
            <span class="truncate">{{ $workspace->name }} • {{ $channels->count() }} channels</span>
            <button type="button" onclick="openInviteModal()" class="hover:text-white" title="Invite Member">
                <i class="fas fa-user-plus"></i>
            </button>
        </div>
    </aside>

    <!-- ========================================================
         PANEL 3: MAIN CHAT & REAL-TIME STREAM AREA
         ======================================================== -->
    <main class="flex-1 flex flex-col min-w-0 bg-white relative">
        
        <!-- Header Bar -->
        <header class="h-14 px-4 sm:px-6 border-b border-slate-200 flex items-center justify-between bg-white z-10 shrink-0">
            <!-- Left Channel Info -->
            <div class="flex items-center gap-2.5 min-w-0">
                <button type="button" onclick="toggleMobileSidebar()" class="md:hidden text-slate-600 hover:text-slate-900 p-1">
                    <i class="fas fa-bars"></i>
                </button>

                @if($activeChannel)
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="text-base font-bold text-slate-900 flex items-center gap-1.5 truncate">
                            @if($activeChannel->type === 'announcement')
                                <i class="fas fa-bullhorn text-sm text-amber-500"></i>
                            @elseif($activeChannel->type === 'private')
                                <i class="fas fa-lock text-sm text-slate-600"></i>
                            @else
                                <i class="fas fa-hashtag text-sm text-slate-400"></i>
                            @endif
                            <span class="truncate">{{ $activeChannel->name }}</span>
                        </span>
                        
                        <button type="button" class="text-slate-400 hover:text-amber-400 text-xs hidden sm:inline" title="Star channel">
                            <i class="far fa-star"></i>
                        </button>

                        @if($activeChannel->description)
                            <span class="hidden lg:inline text-xs text-slate-400 border-l border-slate-200 pl-2.5 truncate max-w-sm">
                                {{ $activeChannel->description }}
                            </span>
                        @endif
                    </div>
                @elseif($activeConversation)
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>
                        <span class="text-base font-bold text-slate-900 truncate">
                            @if($activeConversation->type === 'direct')
                                Direct: {{ $activeConversation->users->where('id', '!=', $currentUser->id)->first()->name ?? 'Direct Chat' }}
                            @else
                                {{ $activeConversation->name ?: 'Group Conversation' }}
                            @endif
                        </span>
                    </div>
                @endif
            </div>

            <!-- Right Header Actions (Huddle, Members, Search, Canvas) -->
            <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                <!-- Huddle Audio / Video Button -->
                <a href="{{ route('netchat.index') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 transition" title="Start Slack Huddle Call">
                    <i class="fas fa-headphones text-emerald-600"></i>
                    <span class="hidden sm:inline">Huddle</span>
                </a>

                <!-- Member Count & Drawer Toggle -->
                <button type="button" onclick="toggleMembersDrawer()" class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg hover:bg-slate-100 text-slate-600 text-xs font-semibold border border-slate-200 transition" title="Channel Members">
                    <i class="fas fa-user-group text-slate-500"></i>
                    <span>{{ $workspaceMembers->count() }}</span>
                </button>

                <!-- In-Channel Search -->
                <button type="button" onclick="openSearchModal()" class="w-8 h-8 rounded-lg hover:bg-slate-100 text-slate-500 flex items-center justify-center text-xs transition" title="Search in channel">
                    <i class="fas fa-search"></i>
                </button>

                <!-- Information Drawer Toggle -->
                <button type="button" onclick="toggleMembersDrawer()" class="w-8 h-8 rounded-lg hover:bg-slate-100 text-slate-500 flex items-center justify-center text-xs transition" title="Details">
                    <i class="fas fa-circle-info"></i>
                </button>
            </div>
        </header>

        <!-- Messages Stream -->
        <div class="flex-1 overflow-y-auto custom-scrollbar p-4 space-y-1 select-text" id="messagesStream">
            
            <!-- Channel Welcome Banner -->
            @if($activeChannel)
                <div class="mb-6 p-4 rounded-2xl bg-slate-50 border border-slate-100 text-slate-700">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-lg">
                            #
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900">This is the start of #{{ $activeChannel->name }}</h2>
                            <p class="text-xs text-slate-500">{{ $activeChannel->description ?: 'This channel is for collaboration, questions, and team discussions.' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-blue-600 font-semibold pt-2">
                        <button type="button" onclick="openInviteModal()" class="hover:underline flex items-center gap-1.5">
                            <i class="fas fa-user-plus text-xs"></i> Invite members to this channel
                        </button>
                    </div>
                </div>
            @endif

            <!-- Stream of messages -->
            <div id="messagesList" class="space-y-0.5">
                @forelse($messages as $msg)
                    @include('workspace.partials.message_card', ['msg' => $msg, 'currentUser' => $currentUser])
                @empty
                    <div class="text-center py-12 text-slate-400 text-xs italic" id="emptyPlaceholder">
                        No messages yet. Send the first message below!
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Slack Rich Message Composer -->
        <div class="p-4 pt-1 border-t border-slate-200 bg-white">
            <form id="workspaceMessageForm" onsubmit="sendWorkspaceMessage(event)" class="rounded-xl border border-slate-300 focus-within:border-slate-500 focus-within:ring-1 focus-within:ring-slate-400 transition bg-white shadow-2xs">
                
                <!-- Formatting Toolbar -->
                <div class="px-3 py-1.5 border-b border-slate-100 flex items-center gap-1 text-slate-500 text-xs">
                    <button type="button" onclick="formatText('bold')" class="w-7 h-7 rounded hover:bg-slate-100 flex items-center justify-center font-bold" title="Bold (Ctrl+B)">B</button>
                    <button type="button" onclick="formatText('italic')" class="w-7 h-7 rounded hover:bg-slate-100 flex items-center justify-center italic" title="Italic (Ctrl+I)">I</button>
                    <button type="button" onclick="formatText('strike')" class="w-7 h-7 rounded hover:bg-slate-100 flex items-center justify-center line-through" title="Strikethrough">S</button>
                    <div class="w-[1px] h-4 bg-slate-200 mx-1"></div>
                    <button type="button" onclick="formatText('code')" class="w-7 h-7 rounded hover:bg-slate-100 flex items-center justify-center font-mono text-[11px]" title="Code block">&lt;/&gt;</button>
                    <button type="button" onclick="formatText('bullet')" class="w-7 h-7 rounded hover:bg-slate-100 flex items-center justify-center" title="Bullet list">•</button>
                    <button type="button" onclick="triggerAttachmentPicker()" class="w-7 h-7 rounded hover:bg-slate-100 flex items-center justify-center ml-auto text-slate-600 hover:text-slate-900" title="Attach file or photo">
                        <i class="fas fa-paperclip"></i>
                    </button>
                </div>

                <!-- Textarea -->
                <div class="px-3 py-2">
                    <textarea id="composerInput" 
                              rows="2" 
                              placeholder="Message {{ $activeChannel ? '#' . $activeChannel->name : 'here' }}... (Enter to send, Shift+Enter for newline)"
                              class="w-full resize-none border-none outline-none text-xs sm:text-sm text-slate-900 placeholder-slate-400 bg-transparent custom-scrollbar"
                              onkeydown="handleComposerKeydown(event)"></textarea>
                </div>

                <!-- Attachment Preview Chip Bar -->
                <div id="attachmentPreviewContainer" class="hidden px-3 py-1.5 border-t border-slate-100 flex items-center gap-2">
                    <div class="inline-flex items-center gap-2 px-2.5 py-1 bg-blue-50 border border-blue-200 rounded-lg text-xs text-blue-700">
                        <i class="fas fa-file text-[11px]"></i>
                        <span id="attachmentFileName" class="truncate max-w-[200px]">file.pdf</span>
                        <button type="button" onclick="clearAttachment()" class="text-blue-500 hover:text-blue-800 ml-1">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <!-- Bottom Action Row -->
                <div class="px-3 py-1.5 flex items-center justify-between border-t border-slate-100/60 bg-slate-50/50 rounded-b-xl">
                    <div class="flex items-center gap-1.5">
                        <!-- Emoji Picker Button -->
                        <button type="button" onclick="openEmojiPicker(event)" class="w-7 h-7 rounded hover:bg-slate-200/60 text-slate-500 hover:text-slate-800 flex items-center justify-center text-xs" title="Insert emoji">
                            <i class="far fa-face-smile"></i>
                        </button>

                        <!-- @ Mention Trigger -->
                        <button type="button" onclick="insertMentionTrigger()" class="w-7 h-7 rounded hover:bg-slate-200/60 text-slate-500 hover:text-slate-800 flex items-center justify-center text-xs font-bold" title="Mention someone">
                            @
                        </button>
                    </div>

                    <!-- Hidden File Input -->
                    <input type="file" id="composerFileInput" class="hidden" onchange="handleFileSelected(event)">

                    <!-- Send Button -->
                    <button type="submit" id="sendBtn" class="px-3.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs flex items-center gap-1.5 shadow-2xs transition">
                        <span>Send</span>
                        <i class="fas fa-paper-plane text-[11px]"></i>
                    </button>
                </div>
            </form>
        </div>
    </main>

    <!-- ========================================================
         PANEL 4: RIGHT SLIDE-OUT DRAWER (THREADS & MEMBERS)
         ======================================================== -->
    <aside id="slackRightDrawer" class="w-96 bg-white border-l border-slate-200 flex flex-col shrink-0 hidden z-20 shadow-xl md:shadow-none">
        
        <!-- Drawer Top Header -->
        <div class="h-14 px-4 border-b border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fas fa-comments text-slate-600 text-sm" id="drawerIcon"></i>
                <h3 class="font-bold text-sm text-slate-900" id="drawerTitle">Thread</h3>
            </div>
            <button type="button" onclick="closeRightDrawer()" class="w-7 h-7 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center text-xs">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Thread Content Pane -->
        <div id="threadContentPane" class="flex-1 flex flex-col overflow-hidden">
            <!-- Parent Message Display -->
            <div class="p-3 bg-slate-50/70 border-b border-slate-200 overflow-y-auto max-h-56" id="threadParentBox">
                <!-- Injected via AJAX -->
            </div>

            <!-- Thread Replies Stream -->
            <div class="flex-1 overflow-y-auto p-3 space-y-2 custom-scrollbar" id="threadRepliesList">
                <!-- Injected via AJAX -->
            </div>

            <!-- Thread Reply Composer -->
            <div class="p-3 border-t border-slate-200 bg-white">
                <form id="threadReplyForm" onsubmit="sendThreadReply(event)">
                    <div class="rounded-xl border border-slate-300 focus-within:border-slate-500 p-2">
                        <textarea id="threadInput" rows="2" placeholder="Reply in thread..." class="w-full resize-none border-none outline-none text-xs text-slate-900 placeholder-slate-400"></textarea>
                        <div class="flex items-center justify-between pt-1">
                            <span class="text-[10px] text-slate-400">Shift+Enter for newline</span>
                            <button type="submit" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold">Reply</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Members Directory Content Pane -->
        <div id="membersContentPane" class="flex-1 flex flex-col overflow-hidden hidden">
            <div class="p-4 border-b border-slate-100">
                <div class="font-bold text-xs text-slate-500 uppercase tracking-wider mb-2">Members ({{ $workspaceMembers->count() }})</div>
                <input type="text" placeholder="Search members..." onkeyup="filterMembersList(this.value)" class="w-full px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs">
            </div>
            <div class="flex-1 overflow-y-auto p-2 space-y-1" id="membersDrawerList">
                @foreach($workspaceMembers as $mem)
                    <div class="flex items-center gap-2.5 p-2 rounded-lg hover:bg-slate-50 member-item cursor-pointer" onclick="openDirectMessage({{ $mem->id }})">
                        <img src="{{ $mem->profile_pic ? asset('uploads/profiles/' . $mem->profile_pic) : asset('image.png') }}" class="w-8 h-8 rounded-lg object-cover border border-slate-200" onerror="this.onerror=null; this.src='{{ asset('image.png') }}';">
                        <div class="min-w-0 flex-1">
                            <div class="font-semibold text-xs text-slate-900 truncate">{{ $mem->name }}</div>
                            <div class="text-[10px] text-slate-400 truncate">{{ $mem->email }}</div>
                        </div>
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    </div>
                @endforeach
            </div>
        </div>
    </aside>

    <!-- ========================================================
         MODALS: CREATE CHANNEL, INVITE, SEARCH & LIGHTBOX
         ======================================================== -->
    
    <!-- Modal: Create Channel -->
    <div id="newChannelModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900">Create a channel</h3>
                <button type="button" onclick="closeNewChannelModal()" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button>
            </div>
            <form action="{{ route('workspace.channels.store', $workspace->slug) }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Name</label>
                    <div class="relative flex items-center">
                        <span class="absolute left-3 text-slate-400 font-bold text-sm">#</span>
                        <input type="text" name="name" required placeholder="e.g. project-dev" class="w-full pl-8 pr-3 py-2 border border-slate-200 rounded-xl text-xs sm:text-sm focus:border-blue-500 outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Description (optional)</label>
                    <input type="text" name="description" placeholder="What's this channel about?" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs sm:text-sm focus:border-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Channel Visibility</label>
                    <select name="type" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs sm:text-sm focus:border-blue-500 outline-none">
                        <option value="public">Public - Anyone in {{ $workspace->name }} can view and join</option>
                        <option value="private">Private - Only invited members can view</option>
                        <option value="announcement">Announcement - Broadcast only (Admins can post)</option>
                    </select>
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" onclick="closeNewChannelModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl">Cancel</button>
                    <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-xs">Create Channel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Invite People -->
    <div id="inviteModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900">Invite people to {{ $workspace->name }}</h3>
                <button type="button" onclick="closeInviteModal()" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Email address</label>
                    <input type="email" id="inviteEmailInput" placeholder="colleague@institute.edu" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs sm:text-sm focus:border-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Role</label>
                    <select id="inviteRoleSelect" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs sm:text-sm">
                        <option value="member">Member</option>
                        <option value="admin">Workspace Admin</option>
                        <option value="guest">Guest</option>
                    </select>
                </div>
                <div id="inviteLinkResult" class="hidden p-3 bg-blue-50 border border-blue-200 rounded-xl">
                    <div class="text-[11px] font-semibold text-blue-700 mb-1">Invitation Link Generated:</div>
                    <input type="text" id="inviteGeneratedUrl" readonly class="w-full text-xs bg-white border border-blue-200 rounded px-2 py-1 font-mono text-slate-700 select-all" onclick="this.select()">
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" onclick="closeInviteModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl">Close</button>
                    <button type="button" onclick="generateWorkspaceInvite()" class="px-5 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-xs">Send Invite</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Search (⌘K) -->
    <div id="searchModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs hidden z-50 flex items-start justify-center pt-20 p-4" onclick="closeSearchModal()">
        <div class="bg-white rounded-2xl max-w-xl w-full shadow-2xl border border-slate-100 overflow-hidden" onclick="event.stopPropagation()">
            <div class="p-3 border-b border-slate-100 flex items-center gap-3">
                <i class="fas fa-search text-slate-400 text-sm pl-2"></i>
                <input type="text" id="globalWorkspaceSearch" placeholder="Search messages, channels, or files..." onkeyup="performWorkspaceSearch(this.value)" class="w-full outline-none text-sm text-slate-800 placeholder-slate-400">
                <kbd class="text-[10px] bg-slate-100 px-1.5 py-0.5 rounded text-slate-400">ESC</kbd>
            </div>
            <div class="max-h-80 overflow-y-auto p-2" id="searchResultsList">
                <div class="p-4 text-center text-xs text-slate-400">Type to search across channels and files in {{ $workspace->name }}</div>
            </div>
        </div>
    </div>

    <!-- Image Fullscreen Lightbox -->
    <div id="slackLightboxModal" class="fixed inset-0 bg-black/90 hidden items-center justify-center z-50 p-4" onclick="closeLightbox()">
        <div class="relative max-w-4xl max-h-[90vh] flex flex-col items-center" onclick="event.stopPropagation()">
            <div class="w-full flex items-center justify-between pb-2 text-white text-xs">
                <span id="lightboxCaption" class="truncate font-semibold"></span>
                <button type="button" onclick="closeLightbox()" class="text-white hover:text-slate-300 p-1"><i class="fas fa-times text-base"></i></button>
            </div>
            <img id="lightboxImg" src="" alt="Full view" class="max-h-[80vh] max-w-full rounded-xl object-contain shadow-2xl border border-white/10" onerror="this.onerror=null; this.src='{{ asset('image.png') }}';">
        </div>
    </div>

    <!-- Audio Chimes Synthesizer -->
    <script>
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        function playChime(type = 'ping') {
            try {
                if (audioCtx.state === 'suspended') audioCtx.resume();
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.connect(gain);
                gain.connect(audioCtx.destination);
                
                if (type === 'sent') {
                    osc.frequency.setValueAtTime(587.33, audioCtx.currentTime); // D5
                    osc.frequency.exponentialRampToValueAtTime(880, audioCtx.currentTime + 0.12); // A5
                    gain.gain.setValueAtTime(0.08, audioCtx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.2);
                    osc.start();
                    osc.stop(audioCtx.currentTime + 0.2);
                } else {
                    // Receive ping
                    osc.frequency.setValueAtTime(523.25, audioCtx.currentTime); // C5
                    osc.frequency.exponentialRampToValueAtTime(659.25, audioCtx.currentTime + 0.15); // E5
                    gain.gain.setValueAtTime(0.12, audioCtx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.25);
                    osc.start();
                    osc.stop(audioCtx.currentTime + 0.25);
                }
            } catch(e) {}
        }
    </script>

    <!-- Client-Side State & Controller Scripts -->
    <script>
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const CURRENT_USER_ID = {{ $currentUser->id }};
        const WORKSPACE_ID = {{ $workspace->id }};
        const ACTIVE_CHANNEL_ID = {{ $activeChannel ? $activeChannel->id : 'null' }};
        const ACTIVE_CONVERSATION_ID = {{ $activeConversation ? $activeConversation->id : 'null' }};
        
        let activeThreadId = {{ $activeThread ? $activeThread->id : 'null' }};
        let lastMessageId = {{ $messages->last() ? $messages->last()->id : 0 }};
        let lastThreadReplyId = 0;
        let selectedFile = null;

        // Auto-scroll messages to bottom on load
        function scrollStreamToBottom(smooth = false) {
            const stream = document.getElementById('messagesStream');
            if (stream) {
                stream.scrollTo({ top: stream.scrollHeight, behavior: smooth ? 'smooth' : 'auto' });
            }
        }
        window.addEventListener('DOMContentLoaded', () => scrollStreamToBottom());

        // Keyboard Shortcut ⌘K / Ctrl+K
        window.addEventListener('keydown', (e) => {
            if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                openSearchModal();
            }
            if (e.key === 'Escape') {
                closeSearchModal();
                closeLightbox();
                closeNewChannelModal();
                closeInviteModal();
            }
        });

        // Composer keydown (Enter to send, Shift+Enter for newline)
        function handleComposerKeydown(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                document.getElementById('workspaceMessageForm').dispatchEvent(new Event('submit', { cancelable: true }));
            }
        }

        // Send Workspace Message
        async function sendWorkspaceMessage(e) {
            e.preventDefault();
            const input = document.getElementById('composerInput');
            const messageText = input.value.trim();

            if (!messageText && !selectedFile) return;

            const formData = new FormData();
            formData.append('workspace_id', WORKSPACE_ID);
            if (ACTIVE_CHANNEL_ID) formData.append('channel_id', ACTIVE_CHANNEL_ID);
            if (ACTIVE_CONVERSATION_ID) formData.append('conversation_id', ACTIVE_CONVERSATION_ID);
            formData.append('message', messageText);
            if (selectedFile) formData.append('attachment', selectedFile);

            input.value = '';
            clearAttachment();

            try {
                const res = await fetch("{{ route('workspace.messages.store') }}", {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
                    body: formData,
                });
                const data = await res.json();
                if (data.success && data.html) {
                    document.getElementById('emptyPlaceholder')?.remove();
                    document.getElementById('messagesList').insertAdjacentHTML('beforeend', data.html);
                    lastMessageId = Math.max(lastMessageId, data.message.id);
                    playChime('sent');
                    scrollStreamToBottom(true);
                } else if (data.error) {
                    Swal.fire({ icon: 'warning', title: 'Could not send', text: data.error });
                }
            } catch(err) {
                console.error(err);
            }
        }

        // File attachment handling
        function triggerAttachmentPicker() {
            document.getElementById('composerFileInput')?.click();
        }
        function handleFileSelected(e) {
            if (e.target.files && e.target.files[0]) {
                selectedFile = e.target.files[0];
                document.getElementById('attachmentFileName').textContent = selectedFile.name;
                document.getElementById('attachmentPreviewContainer')?.classList.remove('hidden');
            }
        }
        function clearAttachment() {
            selectedFile = null;
            const input = document.getElementById('composerFileInput');
            if (input) input.value = '';
            document.getElementById('attachmentPreviewContainer')?.classList.add('hidden');
        }

        // Real-Time Polling Engine (Every 2.5s)
        setInterval(async () => {
            const chParam = ACTIVE_CHANNEL_ID ? `&channel_id=${ACTIVE_CHANNEL_ID}` : '';
            const convParam = ACTIVE_CONVERSATION_ID ? `&conversation_id=${ACTIVE_CONVERSATION_ID}` : '';
            const threadParam = activeThreadId ? `&thread_id=${activeThreadId}&last_thread_reply_id=${lastThreadReplyId}` : '';
            
            if (!chParam && !convParam) return;

            try {
                const res = await fetch(`{{ route('workspace.messages.poll') }}?workspace_id=${WORKSPACE_ID}&last_id=${lastMessageId}${chParam}${convParam}${threadParam}`);
                if (!res.ok) return;
                const data = await res.json();

                // Append new channel messages
                if (data.messages && data.messages.length > 0) {
                    const stream = document.getElementById('messagesStream');
                    const isNearBottom = stream ? (stream.scrollHeight - stream.scrollTop - stream.clientHeight < 160) : true;

                    data.messages.forEach(m => {
                        if (m.id > lastMessageId) {
                            lastMessageId = m.id;
                            if (m.sender_id !== CURRENT_USER_ID) {
                                document.getElementById('emptyPlaceholder')?.remove();
                                document.getElementById('messagesList').insertAdjacentHTML('beforeend', m.html);
                                playChime('receive');
                                showToastNotification(m.sender_name, m.preview);
                            }
                        }
                    });

                    if (isNearBottom) scrollStreamToBottom(true);
                }

                // Append new thread replies if drawer is open
                if (data.thread_replies && data.thread_replies.length > 0) {
                    data.thread_replies.forEach(r => {
                        if (r.id > lastThreadReplyId) {
                            lastThreadReplyId = r.id;
                            document.getElementById('threadRepliesList')?.insertAdjacentHTML('beforeend', r.html);
                        }
                    });
                }
            } catch(e) {}
        }, 2500);

        // Open Thread Drawer
        async function openThreadDrawer(msgId) {
            activeThreadId = msgId;
            lastThreadReplyId = 0;
            
            const drawer = document.getElementById('slackRightDrawer');
            document.getElementById('drawerTitle').textContent = 'Thread';
            document.getElementById('threadContentPane').classList.remove('hidden');
            document.getElementById('membersContentPane').classList.add('hidden');
            drawer.classList.remove('hidden');

            try {
                const res = await fetch(`/workspace/messages/${msgId}/replies`);
                const data = await res.json();
                if (data.success) {
                    document.getElementById('threadParentBox').innerHTML = data.parent_html;
                    const repliesContainer = document.getElementById('threadRepliesList');
                    repliesContainer.innerHTML = '';
                    if (data.replies && data.replies.length > 0) {
                        data.replies.forEach(r => {
                            repliesContainer.insertAdjacentHTML('beforeend', r.html);
                            lastThreadReplyId = Math.max(lastThreadReplyId, r.id);
                        });
                    }
                }
            } catch(err) {
                console.error(err);
            }
        }

        // Send Thread Reply
        async function sendThreadReply(e) {
            e.preventDefault();
            if (!activeThreadId) return;

            const input = document.getElementById('threadInput');
            const replyText = input.value.trim();
            if (!replyText) return;

            const formData = new FormData();
            formData.append('workspace_id', WORKSPACE_ID);
            formData.append('parent_id', activeThreadId);
            formData.append('message', replyText);

            input.value = '';

            try {
                const res = await fetch("{{ route('workspace.messages.store') }}", {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
                    body: formData,
                });
                const data = await res.json();
                if (data.success) {
                    lastThreadReplyId = Math.max(lastThreadReplyId, data.message.id);
                    document.getElementById('threadRepliesList').insertAdjacentHTML('beforeend', data.html);
                    playChime('sent');
                }
            } catch(err) {}
        }

        function closeRightDrawer() {
            activeThreadId = null;
            document.getElementById('slackRightDrawer')?.classList.add('hidden');
        }

        function toggleMembersDrawer() {
            const drawer = document.getElementById('slackRightDrawer');
            const isHidden = drawer.classList.contains('hidden');
            const membersPane = document.getElementById('membersContentPane');

            if (!isHidden && !membersPane.classList.contains('hidden')) {
                drawer.classList.add('hidden');
            } else {
                drawer.classList.remove('hidden');
                document.getElementById('drawerTitle').textContent = 'Channel Details & Members';
                document.getElementById('threadContentPane').classList.add('hidden');
                membersPane.classList.remove('hidden');
            }
        }

        // Emoji Reaction Toggle
        async function toggleMessageReaction(msgId, emoji) {
            try {
                const res = await fetch(`/workspace/messages/${msgId}/reaction`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                    },
                    body: JSON.stringify({ emoji: emoji })
                });
                const data = await res.json();
                if (data.success) {
                    const container = document.getElementById(`reactions-${msgId}`);
                    if (container && data.tallies) {
                        let html = '';
                        data.tallies.forEach(t => {
                            const userReacted = (t.user_reacted == 1);
                            html += `
                                <button type="button" onclick="toggleMessageReaction(${msgId}, '${t.emoji}')" class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs border transition ${userReacted ? 'bg-blue-50 border-blue-300 text-blue-700 font-semibold' : 'bg-slate-50 hover:bg-slate-100 border-slate-200 text-slate-600'}">
                                    <span>${t.emoji}</span>
                                    <span class="text-[11px] font-bold">${t.count}</span>
                                </button>
                            `;
                        });
                        html += `
                            <button type="button" onclick="openReactionPicker(event, ${msgId})" class="w-6 h-6 rounded-full hover:bg-slate-200/70 text-slate-400 hover:text-slate-700 flex items-center justify-center text-xs transition" title="Add reaction">
                                <i class="far fa-face-smile"></i>
                            </button>
                        `;
                        container.innerHTML = html;
                    }
                }
            } catch(e) {}
        }

        // Delete Message
        function deleteWorkspaceMessage(id) {
            Swal.fire({
                title: 'Delete message?',
                text: 'Are you sure you want to remove this message?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Delete'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    const res = await fetch(`/workspace/messages/${id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN }
                    });
                    if (res.ok) {
                        document.getElementById(`msg-${id}`)?.remove();
                        document.getElementById(`reply-${id}`)?.remove();
                    }
                }
            });
        }

        // Toasts
        function showToastNotification(senderName, previewText) {
            const container = document.getElementById('workspaceToastContainer');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = 'workspace-toast';
            toast.innerHTML = `
                <div class="w-8 h-8 rounded-lg bg-pink-100 text-pink-600 flex items-center justify-center font-bold text-xs shrink-0">
                    <i class="far fa-comment"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="font-bold text-slate-900 text-xs">${senderName}</div>
                    <div class="text-xs text-slate-600 truncate">${previewText}</div>
                </div>
            `;
            container.appendChild(toast);
            setTimeout(() => toast.remove(), 4000);
        }

        // Text formatting helper
        function formatText(type) {
            const el = document.getElementById('composerInput');
            const start = el.selectionStart;
            const end = el.selectionEnd;
            const text = el.value;
            const selected = text.substring(start, end);

            let replacement = selected;
            if (type === 'bold') replacement = `*${selected}*`;
            if (type === 'italic') replacement = `_${selected}_`;
            if (type === 'strike') replacement = `~${selected}~`;
            if (type === 'code') replacement = `\`${selected}\``;
            if (type === 'bullet') replacement = `\n• ${selected}`;

            el.value = text.substring(0, start) + replacement + text.substring(end);
            el.focus();
        }

        // Lightbox
        function openLightbox(url, caption) {
            const modal = document.getElementById('slackLightboxModal');
            document.getElementById('lightboxImg').src = url;
            document.getElementById('lightboxCaption').textContent = caption || 'Attachment';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        function closeLightbox() {
            const modal = document.getElementById('slackLightboxModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Modals
        function openNewChannelModal() { document.getElementById('newChannelModal')?.classList.remove('hidden'); }
        function closeNewChannelModal() { document.getElementById('newChannelModal')?.classList.add('hidden'); }
        function openInviteModal() { document.getElementById('inviteModal')?.classList.remove('hidden'); }
        function closeInviteModal() { document.getElementById('inviteModal')?.classList.add('hidden'); }
        function openSearchModal() { 
            document.getElementById('searchModal')?.classList.remove('hidden'); 
            setTimeout(() => document.getElementById('globalWorkspaceSearch')?.focus(), 50);
        }
        function closeSearchModal() { document.getElementById('searchModal')?.classList.add('hidden'); }
        function toggleWorkspaceDropdown() { document.getElementById('workspaceActionDropdown')?.classList.toggle('hidden'); }

        // Generate Invite Link
        async function generateWorkspaceInvite() {
            const email = document.getElementById('inviteEmailInput').value;
            const role = document.getElementById('inviteRoleSelect').value;
            if (!email) {
                Swal.fire({ icon: 'warning', title: 'Email required' });
                return;
            }

            try {
                const res = await fetch("{{ route('workspace.invite', $workspace->slug) }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                    body: JSON.stringify({ email: email, role: role })
                });
                const data = await res.json();
                if (data.success && data.invite_url) {
                    document.getElementById('inviteGeneratedUrl').value = data.invite_url;
                    document.getElementById('inviteLinkResult').classList.remove('hidden');
                    Swal.fire({ icon: 'success', title: 'Invitation Generated!', text: 'Share the link with your invitee.' });
                }
            } catch(e) {}
        }

        // Global Search
        async function performWorkspaceSearch(q) {
            if (!q.trim()) return;
            try {
                const res = await fetch(`{{ route('workspace.messages.search') }}?workspace_id=${WORKSPACE_ID}&q=${encodeURIComponent(q)}`);
                const data = await res.json();
                const container = document.getElementById('searchResultsList');
                if (data.results && data.results.length > 0) {
                    let html = '';
                    data.results.forEach(m => {
                        html += `
                            <div class="p-2.5 hover:bg-slate-50 rounded-xl cursor-pointer border-b border-slate-100 flex items-start gap-2.5">
                                <div class="w-7 h-7 rounded bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold shrink-0">#</div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-xs text-slate-900">${m.user?.name || 'Member'}</span>
                                        <span class="text-[10px] text-slate-400">${m.channel ? '#' + m.channel.name : 'Direct'}</span>
                                    </div>
                                    <p class="text-xs text-slate-600 truncate mt-0.5">${m.message || 'Attachment'}</p>
                                </div>
                            </div>
                        `;
                    });
                    container.innerHTML = html;
                } else {
                    container.innerHTML = '<div class="p-4 text-center text-xs text-slate-400">No matching messages found</div>';
                }
            } catch(e) {}
        }

        function toggleSection(id) {
            document.getElementById(id)?.classList.toggle('hidden');
        }

        function toggleMobileSidebar() {
            document.getElementById('slackChannelsSidebar')?.classList.toggle('-ml-64');
        }

        function openDirectMessage(userId) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ route('workspace.conversations.store') }}";
            form.innerHTML = `
                <input type="hidden" name="_token" value="${CSRF_TOKEN}">
                <input type="hidden" name="workspace_id" value="${WORKSPACE_ID}">
                <input type="hidden" name="type" value="direct">
                <input type="hidden" name="recipient_id" value="${userId}">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</body>
</html>
