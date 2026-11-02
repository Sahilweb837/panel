@php
    $pageTitle = 'NetChat — Slack Workspace';
    $isPopup = request('mode') === 'standalone' || request('window') === 'popup';
@endphp

@if(!$isPopup)
    @extends('layouts.app')
    @section('title', $pageTitle)
    @section('content')
@else
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle }} — Conceptzilla</title>
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Tailwind CSS CDN for exact styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        slack: {
                            rail: '#dfe6f9',
                            railHover: '#d0dcfa',
                            activeRail: '#4a154b',
                            badge: '#f43f5e',
                            textMuted: '#616061',
                            border: '#e2e8f0',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; }
    </style>
</head>
<body class="bg-slate-100 overflow-hidden select-none m-0 p-0 text-slate-800">
@endif

<style>
    /* Slack Theme Custom Utilities */
    .netchat-container {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        letter-spacing: -0.011em;
    }
    .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    
    .waveform-bar {
        animation: waveAnim 1.2s ease-in-out infinite alternate;
    }
    @keyframes waveAnim {
        0% { height: 4px; }
        100% { height: 20px; }
    }
    .huddle-glow {
        box-shadow: 0 0 20px rgba(16, 185, 129, 0.35);
    }
    .channel-tree-line {
        position: relative;
    }
    .channel-tree-line::before {
        content: '';
        position: absolute;
        left: 10px;
        top: -10px;
        bottom: 12px;
        width: 1.5px;
        background: #e2e8f0;
    }
    .window-traffic-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        transition: transform 0.15s;
    }
    .window-traffic-dot:hover {
        transform: scale(1.15);
    }
</style>

<!-- Main Workspace App Shell -->
<div class="netchat-container w-full bg-white flex flex-col overflow-hidden {{ $isPopup ? 'h-screen' : 'rounded-2xl border border-slate-200/90 shadow-sm' }}" style="{{ !$isPopup ? 'height: calc(100vh - 120px);' : '' }}" id="netchatApp">

    @if($isPopup)
    <!-- Top macOS Desktop Window Chrome -->
    <div class="h-9 bg-slate-50 border-b border-slate-200/80 flex items-center justify-between px-3.5 select-none shrink-0">
        <!-- Traffic lights -->
        <div class="flex items-center gap-2">
            <span class="window-traffic-dot bg-[#ff5f56] cursor-pointer" onclick="window.close()" title="Close"></span>
            <span class="window-traffic-dot bg-[#ffbd2e] cursor-pointer" onclick="minimizeWindow()" title="Minimize"></span>
            <span class="window-traffic-dot bg-[#27c93f] cursor-pointer" onclick="toggleMaximize()" title="Maximize"></span>
        </div>
        <!-- Window title -->
        <div class="text-xs font-semibold text-slate-500 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            <span>Conceptzilla — NetChat Desktop</span>
        </div>
        <!-- Actions -->
        <div class="flex items-center gap-2 text-xs text-slate-400">
            <button onclick="window.location.reload()" class="hover:text-slate-700 p-1" title="Reload"><i class="fas fa-rotate-right"></i></button>
        </div>
    </div>
    @endif

    <!-- Body Layout: 3-4 Columns (Icon Rail + Left Sidebar + Chat Center + Right Info) -->
    <div class="flex-grow flex overflow-hidden w-full relative">

        <!-- COLUMN 1: Far-Left Lavender / Purple Icon Rail (matches photo) -->
        <div class="w-[64px] bg-[#e3e8f8] border-r border-[#d5def5] flex flex-col items-center py-4 justify-between shrink-0 select-none z-20">
            <!-- Top brand icons -->
            <div class="flex flex-col items-center gap-3.5 w-full">
                <!-- Conceptzilla Logo (Black rounded square with white C) -->
                <button type="button" class="w-10 h-10 rounded-xl bg-black text-white flex items-center justify-center font-black text-xl shadow-xs hover:scale-105 active:scale-95 transition-all" title="Conceptzilla Workspace">
                    <span>C</span>
                </button>

                <!-- Workspace switcher / sync icon -->
                <button type="button" class="w-10 h-10 rounded-xl bg-transparent hover:bg-black/5 text-slate-700 flex items-center justify-center text-base transition-colors" title="Switch Workspace">
                    <svg class="w-5 h-5 text-slate-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                        <path d="M3 3v5h5"/>
                        <path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"/>
                        <path d="M16 21h5v-5"/>
                    </svg>
                </button>

                <!-- Slack Chat Bubble Active Icon -->
                <button type="button" class="w-10 h-10 rounded-xl bg-white shadow-2xs text-indigo-700 flex items-center justify-center text-base transition-all border border-indigo-100" title="Active Channels">
                    <svg class="w-5 h-5 text-slate-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                </button>

                <!-- Plus button (Add channel / Workspace) -->
                <button type="button" onclick="openCreateChannelModal()" class="w-10 h-10 rounded-xl bg-transparent hover:bg-black/5 text-slate-600 flex items-center justify-center text-lg transition-colors" title="Create New Channel">
                    <i class="fas fa-plus"></i>
                </button>
            </div>

            <!-- Bottom User Profile Avatar with bright green online dot -->
            <div class="relative group cursor-pointer" onclick="toggleUserStatusModal()">
                @if($currentUser->profile_pic)
                    <img src="{{ asset('uploads/profiles/'.$currentUser->profile_pic) }}" alt="{{ $currentUser->name }}" class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-xs">
                @else
                    <div class="w-10 h-10 rounded-full bg-slate-900 text-white font-bold flex items-center justify-center text-sm border-2 border-white shadow-xs">
                        {{ strtoupper(substr($currentUser->name, 0, 1)) }}
                    </div>
                @endif
                <span class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 border-2 border-white rounded-full"></span>
            </div>
        </div>

        <!-- COLUMN 2: Secondary Left Sidebar (White Navigation Tree) -->
        <div class="w-[245px] bg-white border-r border-slate-200/80 flex flex-col shrink-0 select-none overflow-hidden">
            <!-- Header: Workspace Name & Dropdown + Search -->
            <div class="h-14 border-b border-slate-100 px-4 flex items-center justify-between shrink-0">
                <button type="button" class="flex items-center gap-2 font-bold text-slate-900 text-[15px] hover:text-indigo-600 transition-colors truncate">
                    <span>Conceptzilla</span>
                    <i class="fas fa-chevron-down text-[10px] text-slate-400"></i>
                </button>
                <button type="button" class="w-7 h-7 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center text-xs transition-colors" onclick="focusSearch()">
                    <i class="fas fa-magnifying-glass"></i>
                </button>
            </div>

            <!-- Scrollable Nav List -->
            <div class="flex-grow overflow-y-auto custom-scrollbar px-3 py-3 space-y-4">

                <!-- Section 1: Standard Slack System Links -->
                <div class="space-y-0.5 text-xs font-semibold text-slate-700">
                    <a href="#" class="flex items-center justify-between px-2.5 py-1.5 rounded-lg hover:bg-slate-100/80 transition-colors text-decoration-none text-slate-700 group">
                        <span class="flex items-center gap-2.5">
                            <i class="fas fa-sparkles text-slate-400 group-hover:text-pink-500 transition-colors text-xs"></i>
                            <span>Assistant</span>
                        </span>
                        <span class="bg-pink-100 text-pink-600 font-bold text-[9px] px-1.5 py-0.5 rounded-md tracking-wider">NEW</span>
                    </a>

                    <a href="#" class="flex items-center justify-between px-2.5 py-1.5 rounded-lg hover:bg-slate-100/80 transition-colors text-decoration-none text-slate-700">
                        <span class="flex items-center gap-2.5">
                            <i class="far fa-edit text-slate-400 text-xs"></i>
                            <span>Drafts</span>
                        </span>
                    </a>

                    <a href="#" class="flex items-center justify-between px-2.5 py-1.5 rounded-lg hover:bg-slate-100/80 transition-colors text-decoration-none text-slate-700">
                        <span class="flex items-center gap-2.5">
                            <i class="far fa-bookmark text-slate-400 text-xs"></i>
                            <span>Saved items</span>
                        </span>
                    </a>

                    <a href="#" class="flex items-center justify-between px-2.5 py-1.5 rounded-lg hover:bg-slate-100/80 transition-colors text-decoration-none text-slate-700">
                        <span class="flex items-center gap-2.5">
                            <i class="far fa-envelope-open text-slate-400 text-xs"></i>
                            <span>Inbox</span>
                        </span>
                        <span class="bg-slate-100 text-slate-500 font-bold text-[10px] px-1.5 py-0.5 rounded-full">6</span>
                    </a>

                    <a href="#direct-messages-section" class="flex items-center justify-between px-2.5 py-1.5 rounded-lg hover:bg-slate-100/80 transition-colors text-decoration-none text-slate-700">
                        <span class="flex items-center gap-2.5">
                            <i class="far fa-comment-dots text-slate-400 text-xs"></i>
                            <span>Direct messages</span>
                        </span>
                        <span class="bg-slate-100 text-slate-500 font-bold text-[10px] px-1.5 py-0.5 rounded-full">1</span>
                    </a>
                </div>

                <!-- Section 2: Favorites -->
                <div>
                    <div class="flex items-center justify-between text-[11px] font-bold text-slate-400 px-2.5 py-1 uppercase tracking-wider cursor-pointer" onclick="toggleCollapse('favoritesList', 'favArrow')">
                        <span>Favorites</span>
                        <i class="fas fa-chevron-down text-[9px] transition-transform duration-200" id="favArrow"></i>
                    </div>

                    <div class="space-y-0.5 mt-1" id="favoritesList">
                        <!-- Sophia Wilson (Favorite DM) -->
                        @if($favoriteUser)
                            <a href="{{ route('netchat.index', ['dm' => $favoriteUser->id, 'mode' => $isPopup ? 'standalone' : null]) }}" class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs font-semibold text-decoration-none {{ $activeDmUser && $activeDmUser->id === $favoriteUser->id ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                                <span class="flex items-center gap-2.5 truncate">
                                    <span class="relative shrink-0">
                                        <span class="w-4 h-4 rounded-full bg-pink-500 text-white text-[9px] font-bold flex items-center justify-center">S</span>
                                        <span class="absolute -bottom-0.5 -right-0.5 w-1.5 h-1.5 bg-emerald-500 rounded-full border border-white"></span>
                                    </span>
                                    <span class="truncate">Sophia Wilson</span>
                                </span>
                                <span class="bg-slate-100 text-slate-500 font-bold text-[10px] px-1.5 py-0.5 rounded-full">2</span>
                            </a>
                        @endif

                        <!-- # front-end (Favorite Channel) -->
                        @if($favoriteChannel)
                            <a href="{{ route('netchat.index', ['channel' => 'front-end', 'mode' => $isPopup ? 'standalone' : null]) }}" class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs font-semibold text-decoration-none {{ $activeChannel && $activeChannel->slug === 'front-end' ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                                <span class="flex items-center gap-2 truncate">
                                    <span class="text-slate-400 font-bold">#</span>
                                    <span class="truncate">front-end</span>
                                </span>
                                <span class="bg-slate-100 text-slate-500 font-bold text-[10px] px-1.5 py-0.5 rounded-full">4</span>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Section 3: Channels (with nested tree: Website -> v3.0 -> Wireframe, Design, UI-kit design) -->
                <div>
                    <div class="flex items-center justify-between text-[11px] font-bold text-slate-400 px-2.5 py-1 uppercase tracking-wider">
                        <div class="flex items-center gap-1.5 cursor-pointer" onclick="toggleCollapse('channelsList', 'channelArrow')">
                            <span>Channels</span>
                            <i class="fas fa-chevron-down text-[9px] transition-transform duration-200" id="channelArrow"></i>
                        </div>
                        <button type="button" onclick="openCreateChannelModal()" class="w-5 h-5 rounded hover:bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center text-xs" title="Add Channel">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>

                    <div class="space-y-0.5 mt-1" id="channelsList">
                        <!-- 🔥 General -->
                        <a href="{{ route('netchat.index', ['channel' => 'general', 'mode' => $isPopup ? 'standalone' : null]) }}" class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs font-semibold text-decoration-none {{ $activeChannel && $activeChannel->slug === 'general' ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                            <span class="flex items-center gap-2 truncate">
                                <span>🔥</span>
                                <span class="truncate">General</span>
                            </span>
                            <span class="bg-slate-100 text-slate-500 font-bold text-[10px] px-1.5 py-0.5 rounded-full">1</span>
                        </a>

                        <!-- # front-end -->
                        <a href="{{ route('netchat.index', ['channel' => 'front-end', 'mode' => $isPopup ? 'standalone' : null]) }}" class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs font-semibold text-decoration-none {{ $activeChannel && $activeChannel->slug === 'front-end' ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                            <span class="flex items-center gap-2 truncate">
                                <span class="text-slate-400 font-bold">#</span>
                                <span class="truncate">front-end</span>
                            </span>
                            <span class="bg-slate-100 text-slate-500 font-bold text-[10px] px-1.5 py-0.5 rounded-full">4</span>
                        </a>

                        <!-- # Website (Parent Group with Nested Tree) -->
                        <div class="pt-0.5">
                            <div class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs font-bold text-slate-800 hover:bg-slate-50 cursor-pointer" onclick="toggleCollapse('websiteSubchannels', 'websiteArrow')">
                                <span class="flex items-center gap-2 truncate">
                                    <span class="text-slate-400 font-bold">#</span>
                                    <span>Website</span>
                                </span>
                                <i class="fas fa-chevron-down text-[9px] text-slate-400 transition-transform duration-200" id="websiteArrow"></i>
                            </div>

                            <!-- Nested Level 1: v3.0 and v2.0 -->
                            <div class="ml-4 pl-2 border-l border-slate-200/80 space-y-0.5 my-0.5" id="websiteSubchannels">
                                <!-- v3.0 parent -->
                                <div>
                                    <div class="flex items-center justify-between px-2 py-1 rounded-md text-xs font-semibold text-slate-600 hover:bg-slate-50 cursor-pointer" onclick="toggleCollapse('v3Subchannels', 'v3Arrow')">
                                        <span class="flex items-center gap-1.5 truncate">
                                            <span class="text-slate-400 font-mono text-[10px]">↳</span>
                                            <span>v3.0</span>
                                        </span>
                                        <i class="fas fa-chevron-down text-[8px] text-slate-400" id="v3Arrow"></i>
                                    </div>

                                    <!-- Nested Level 2: Wireframe, Design, UI-kit design -->
                                    <div class="ml-3 pl-2 border-l border-slate-200/80 space-y-0.5 mt-0.5" id="v3Subchannels">
                                        <!-- Wireframe -->
                                        <a href="{{ route('netchat.index', ['channel' => 'website-v3-wireframe', 'mode' => $isPopup ? 'standalone' : null]) }}" class="flex items-center gap-1.5 px-2 py-1 rounded-md text-xs font-medium text-decoration-none {{ $activeChannel && $activeChannel->slug === 'website-v3-wireframe' ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                                            <span class="text-slate-400 font-mono text-[10px]">↳</span>
                                            <span>Wireframe</span>
                                        </a>

                                        <!-- Design -->
                                        <a href="{{ route('netchat.index', ['channel' => 'website-v3-design', 'mode' => $isPopup ? 'standalone' : null]) }}" class="flex items-center gap-1.5 px-2 py-1 rounded-md text-xs font-medium text-decoration-none {{ $activeChannel && $activeChannel->slug === 'website-v3-design' ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                                            <span class="text-slate-400 font-mono text-[10px]">↳</span>
                                            <span>Design</span>
                                        </a>

                                        <!-- UI-kit design (ACTIVE SELECTED ITEM IN PHOTO) -->
                                        <a href="{{ route('netchat.index', ['channel' => 'website-v3-ui-kit-design', 'mode' => $isPopup ? 'standalone' : null]) }}" class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs text-decoration-none transition-all {{ $activeChannel && $activeChannel->slug === 'website-v3-ui-kit-design' ? 'bg-slate-200/70 text-slate-950 font-bold shadow-2xs' : 'text-slate-600 hover:bg-slate-50 font-medium' }}">
                                            <span class="text-slate-500 font-mono text-[10px]">↳</span>
                                            <span>UI-kit design</span>
                                        </a>
                                    </div>
                                </div>

                                <!-- v2.0 - actual version -->
                                <a href="{{ route('netchat.index', ['channel' => 'website-v2-0-actual', 'mode' => $isPopup ? 'standalone' : null]) }}" class="flex items-center gap-1.5 px-2 py-1 rounded-md text-xs font-medium text-decoration-none {{ $activeChannel && $activeChannel->slug === 'website-v2-0-actual' ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                                    <span class="text-slate-400 font-mono text-[10px]">↳</span>
                                    <span class="truncate">v2.0 - actual version</span>
                                </a>
                            </div>
                        </div>

                        <!-- 🎯 Strategy -->
                        <a href="{{ route('netchat.index', ['channel' => 'strategy', 'mode' => $isPopup ? 'standalone' : null]) }}" class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-semibold text-decoration-none {{ $activeChannel && $activeChannel->slug === 'strategy' ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                            <span>🎯</span>
                            <span>Strategy</span>
                        </a>

                        <!-- 📍 Events -->
                        <a href="{{ route('netchat.index', ['channel' => 'events', 'mode' => $isPopup ? 'standalone' : null]) }}" class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-semibold text-decoration-none {{ $activeChannel && $activeChannel->slug === 'events' ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                            <span>📍</span>
                            <span>Events</span>
                        </a>

                        <!-- 📢 Announcements -->
                        <a href="{{ route('netchat.index', ['channel' => 'announcements', 'mode' => $isPopup ? 'standalone' : null]) }}" class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-semibold text-decoration-none {{ $activeChannel && $activeChannel->slug === 'announcements' ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                            <span>📢</span>
                            <span>Announcements</span>
                        </a>

                        <!-- # UI/UX -->
                        <a href="{{ route('netchat.index', ['channel' => 'ui-ux', 'mode' => $isPopup ? 'standalone' : null]) }}" class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs font-semibold text-decoration-none {{ $activeChannel && $activeChannel->slug === 'ui-ux' ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                            <span class="flex items-center gap-2 truncate">
                                <span class="text-slate-400 font-bold">#</span>
                                <span class="truncate">UI/UX</span>
                            </span>
                            <span class="bg-slate-100 text-slate-500 font-bold text-[10px] px-1.5 py-0.5 rounded-full">2</span>
                        </a>
                    </div>
                </div>

                <!-- Section 4: Direct Messages -->
                <div id="direct-messages-section">
                    <div class="flex items-center justify-between text-[11px] font-bold text-slate-400 px-2.5 py-1 uppercase tracking-wider cursor-pointer" onclick="toggleCollapse('dmUsersList', 'dmArrow')">
                        <span>Direct Messages</span>
                        <i class="fas fa-chevron-down text-[9px] transition-transform duration-200" id="dmArrow"></i>
                    </div>

                    <div class="space-y-0.5 mt-1" id="dmUsersList">
                        @foreach($workspaceUsers->take(8) as $usr)
                            @php
                                $isCurrentDm = $activeDmUser && $activeDmUser->id === $usr->id;
                            @endphp
                            <a href="{{ route('netchat.index', ['dm' => $usr->id, 'mode' => $isPopup ? 'standalone' : null]) }}" class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs font-semibold text-decoration-none {{ $isCurrentDm ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                                <span class="flex items-center gap-2 truncate">
                                    <span class="relative shrink-0">
                                        <span class="w-4 h-4 rounded-full bg-slate-200 text-slate-700 text-[9px] font-bold flex items-center justify-center">
                                            {{ strtoupper(substr($usr->name, 0, 1)) }}
                                        </span>
                                        <span class="absolute -bottom-0.5 -right-0.5 w-1.5 h-1.5 bg-emerald-500 rounded-full border border-white"></span>
                                    </span>
                                    <span class="truncate">{{ $usr->name }}</span>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>

        <!-- COLUMN 3: Center Chat Canvas -->
        <div class="flex-grow flex flex-col bg-white overflow-hidden relative">

            <!-- Chat Header Bar -->
            <div class="h-14 border-b border-slate-200/80 px-6 flex items-center justify-between shrink-0 bg-white z-10">
                <!-- Breadcrumbs Path -->
                <div class="flex items-center gap-2 min-w-0">
                    <span class="text-slate-400 font-bold text-sm">#</span>
                    <div class="flex items-center gap-1.5 text-sm font-bold text-slate-900 truncate">
                        @if($activeChannel)
                            @php
                                $crumbs = $activeChannel->breadcrumbs;
                            @endphp
                            @foreach($crumbs as $idx => $crumb)
                                <span>{{ $crumb['name'] }}</span>
                                @if(!$loop->last)
                                    <span class="text-slate-300 font-normal">/</span>
                                @endif
                            @endforeach
                            <!-- Canvas tab icon -->
                            <span class="text-slate-400 text-xs ms-1.5 cursor-pointer hover:text-slate-700" title="Open canvas">
                                <i class="far fa-window-maximize"></i>
                            </span>
                        @elseif($activeDmUser)
                            <span>Direct Chat with {{ $activeDmUser->name }}</span>
                            <span class="w-2 h-2 rounded-full bg-emerald-500 ms-1"></span>
                        @endif
                    </div>
                </div>

                <!-- Right Action Buttons -->
                <div class="flex items-center gap-1.5 shrink-0">
                    <!-- Huddle Start / Join Button (Voice & Screen Sharing) -->
                    <button type="button" onclick="toggleHuddle()" id="huddleTopBtn" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border transition-all {{ $activeHuddle ? 'bg-emerald-50 border-emerald-300 text-emerald-700 animate-pulse font-bold' : 'border-slate-200 bg-white hover:bg-slate-50 text-slate-700' }}" title="Start Slack Huddle (Voice Call & Screen Sharing)">
                        <i class="fas fa-headphones text-xs text-indigo-600"></i>
                        <span id="huddleTopBtnText">{{ $activeHuddle ? 'Join Huddle (' . $activeHuddle->participants->count() . ')' : 'Huddle' }}</span>
                    </button>

                    <!-- Open in New Window Button (Explicitly requested by user) -->
                    @if(!$isPopup)
                        <button type="button" onclick="openNetChatWindow()" class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-semibold border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 transition-colors shadow-2xs" title="Open NetChat in Dedicated Window">
                            <i class="fas fa-arrow-up-right-from-square text-xs text-slate-500"></i>
                            <span class="hidden md:inline">Open in Window</span>
                        </button>
                    @endif

                    <!-- More options ... -->
                    <button type="button" class="w-8 h-8 rounded-lg hover:bg-slate-100 text-slate-500 hover:text-slate-800 flex items-center justify-center text-sm transition-colors" title="More options">
                        <i class="fas fa-ellipsis"></i>
                    </button>

                    <!-- AI Sparkle Assistant ✨ -->
                    <button type="button" onclick="suggestAiResponse()" class="w-8 h-8 rounded-lg hover:bg-pink-50 text-pink-600 flex items-center justify-center text-xs transition-colors" title="AI Assistant Prompt">
                        <i class="fas fa-wand-magic-sparkles"></i>
                    </button>

                    <!-- Info Toggle ⓘ -->
                    <button type="button" onclick="toggleRightSidebar()" id="infoToggleBtn" class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 flex items-center justify-center text-xs transition-colors" title="Toggle Channel Details">
                        <i class="fas fa-circle-info"></i>
                    </button>
                </div>
            </div>

            <!-- Messages Stream Area -->
            <div class="flex-grow overflow-y-auto custom-scrollbar relative py-4" id="chatMessageStream">
                <div class="space-y-1" id="messagesListContainer">
                    @forelse($messages as $msg)
                        @include('messages.partials.single_message', ['msg' => $msg, 'currentUser' => $currentUser])
                    @empty
                        <div class="flex flex-col items-center justify-center py-20 text-slate-400">
                            <i class="fas fa-comments text-4xl mb-3 opacity-30"></i>
                            <h4 class="text-sm font-bold text-slate-700">No messages in this channel yet</h4>
                            <p class="text-xs text-slate-400">Be the first to share an update, start a huddle or post a Figma file!</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- FLOATING SLACK HUDDLE BAR (Voice Call & Screen Sharing) -->
            <div id="floatingHuddleBar" class="hidden absolute bottom-28 left-6 right-6 md:left-auto md:right-8 md:w-[480px] bg-slate-900/95 backdrop-blur-md text-white rounded-2xl p-3.5 shadow-2xl border border-slate-700/80 z-30 transition-all duration-300">
                <div class="flex items-center justify-between gap-3">
                    <!-- Left: Wave & Channel Name -->
                    <div class="flex items-center gap-3 min-w-0">
                        <!-- Audio Wave Visualizer -->
                        <div class="flex items-end gap-0.5 h-6 px-1.5 py-1 bg-emerald-500/20 rounded-lg shrink-0">
                            <div class="w-1 bg-emerald-400 rounded-full waveform-bar" style="animation-delay: 0.1s;"></div>
                            <div class="w-1 bg-emerald-400 rounded-full waveform-bar" style="animation-delay: 0.3s;"></div>
                            <div class="w-1 bg-emerald-400 rounded-full waveform-bar" style="animation-delay: 0.2s;"></div>
                            <div class="w-1 bg-emerald-400 rounded-full waveform-bar" style="animation-delay: 0.4s;"></div>
                        </div>

                        <div class="min-w-0">
                            <div class="text-xs font-bold text-white flex items-center gap-1.5 truncate">
                                <span>Huddle in</span>
                                <span class="text-emerald-400 font-semibold truncate">{{ $activeChannel ? '#' . $activeChannel->name : 'Call' }}</span>
                            </div>
                            <div class="text-[10px] text-slate-400" id="huddleStatusText">Active • 1 participant</div>
                        </div>
                    </div>

                    <!-- Center: Participant Avatars -->
                    <div class="flex items-center -space-x-1.5 overflow-hidden shrink-0" id="huddleParticipantsBubbles">
                        <div class="w-7 h-7 rounded-full bg-indigo-600 border-2 border-slate-900 text-white text-[10px] font-bold flex items-center justify-center">
                            {{ strtoupper(substr($currentUser->name, 0, 1)) }}
                        </div>
                    </div>

                    <!-- Right Controls: Mute, Screen Share, Leave -->
                    <div class="flex items-center gap-1.5 shrink-0">
                        <!-- Mic Mute Button -->
                        <button type="button" onclick="toggleHuddleMic()" id="huddleMicBtn" class="w-8 h-8 rounded-full bg-slate-800 hover:bg-slate-700 text-white flex items-center justify-center text-xs transition-colors" title="Mute/Unmute Mic">
                            <i class="fas fa-microphone" id="huddleMicIcon"></i>
                        </button>

                        <!-- Screen Share Button -->
                        <button type="button" onclick="toggleScreenShare()" id="huddleScreenBtn" class="w-8 h-8 rounded-full bg-slate-800 hover:bg-slate-700 text-white flex items-center justify-center text-xs transition-colors" title="Share Screen">
                            <i class="fas fa-desktop" id="huddleScreenIcon"></i>
                        </button>

                        <!-- Leave Button -->
                        <button type="button" onclick="leaveHuddle()" class="w-8 h-8 rounded-full bg-rose-600 hover:bg-rose-700 text-white flex items-center justify-center text-xs transition-colors" title="Leave Huddle">
                            <i class="fas fa-phone-slash"></i>
                        </button>
                    </div>
                </div>

                <!-- Screen Sharing Live Preview Container (Expands when screenshare is active) -->
                <div id="screenShareContainer" class="hidden mt-3 pt-3 border-t border-slate-800">
                    <div class="relative bg-black rounded-xl overflow-hidden aspect-video border border-slate-700 flex items-center justify-center">
                        <video id="screenVideoElement" autoplay playsinline muted class="w-full h-full object-contain"></video>
                        <div class="absolute top-2 left-2 bg-black/60 backdrop-blur-xs text-white text-[10px] font-semibold px-2 py-0.5 rounded flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 bg-rose-500 rounded-full animate-ping"></span>
                            <span>Live Screen Share</span>
                        </div>
                        <button type="button" onclick="toggleScreenFullscreen()" class="absolute bottom-2 right-2 bg-black/60 hover:bg-black/90 text-white p-1.5 rounded text-xs transition-colors">
                            <i class="fas fa-expand"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Bottom Floating Message Composer (Matches Photo Pixel-for-Pixel) -->
            <div class="p-6 pt-2 shrink-0 bg-white">
                <form id="slackComposerForm" onsubmit="handleSendMessage(event)" class="relative bg-white rounded-2xl border border-slate-200/90 shadow-sm focus-within:border-slate-400 focus-within:shadow-md transition-all duration-200 p-3.5">
                    @csrf
                    <input type="hidden" name="channel_id" value="{{ $activeChannel ? $activeChannel->id : '' }}">
                    <input type="hidden" name="receiver_id" value="{{ $activeDmUser ? $activeDmUser->id : '' }}">

                    <!-- Expand modal icon top-right -->
                    <button type="button" class="absolute top-3.5 right-3.5 text-slate-400 hover:text-slate-700 text-xs" title="Expand composer">
                        <i class="fas fa-up-right-and-down-left-from-center text-[11px]"></i>
                    </button>

                    <!-- Textarea -->
                    <textarea 
                        name="message" 
                        id="messageInputText" 
                        rows="2" 
                        class="w-full bg-transparent border-0 focus:ring-0 focus:outline-hidden text-[13.5px] leading-relaxed text-slate-800 placeholder-slate-400 resize-none pr-8 custom-scrollbar" 
                        placeholder="{{ $activeChannel ? 'Everything looks pretty good. Finish the states and we\'ll start development...' : 'Message ' . ($activeDmUser ? $activeDmUser->name : 'team') . '...' }}"
                        onkeydown="handleComposerKeydown(event)"
                    ></textarea>

                    <!-- Selected Attachment Preview Pill (if file selected) -->
                    <div id="attachmentPreviewBar" class="hidden mb-2 inline-flex items-center gap-2 px-2.5 py-1 bg-slate-100 rounded-lg text-xs text-slate-700">
                        <i class="fas fa-paperclip text-slate-400"></i>
                        <span id="attachmentFileName" class="truncate max-w-[200px]">file.png</span>
                        <button type="button" onclick="clearAttachment()" class="text-slate-400 hover:text-rose-500 ms-1">
                            <i class="fas fa-xmark text-xs"></i>
                        </button>
                    </div>

                    <!-- Hidden file inputs -->
                    <input type="file" name="attachment" id="chatFileInput" class="hidden" onchange="handleFileSelected(event)">

                    <!-- Bottom Toolbar -->
                    <div class="flex items-center justify-between pt-2 border-t border-slate-100 mt-1">
                        <!-- Left Tools: ✨ @ Aa 😊 📎 🎙️ ⚡ -->
                        <div class="flex items-center gap-1 text-slate-400">
                            <!-- ✨ AI Assistant Rewrite -->
                            <button type="button" onclick="suggestAiResponse()" class="w-7 h-7 rounded-lg hover:bg-slate-100 hover:text-slate-700 flex items-center justify-center text-xs transition-colors" title="AI Assistant">
                                <i class="fas fa-sparkles"></i>
                            </button>

                            <!-- @ Mention -->
                            <button type="button" onclick="insertMentionTag()" class="w-7 h-7 rounded-lg hover:bg-slate-100 hover:text-slate-700 flex items-center justify-center text-xs transition-colors" title="Mention someone">
                                <i class="fas fa-at"></i>
                            </button>

                            <!-- Aa Formatting -->
                            <button type="button" onclick="applyBoldFormat()" class="w-7 h-7 rounded-lg hover:bg-slate-100 hover:text-slate-700 flex items-center justify-center text-xs transition-colors font-bold" title="Formatting">
                                Aa
                            </button>

                            <!-- 😊 Emoji Picker -->
                            <button type="button" onclick="toggleEmojiPicker(event)" class="w-7 h-7 rounded-lg hover:bg-slate-100 hover:text-slate-700 flex items-center justify-center text-xs transition-colors" title="Add emoji">
                                <i class="far fa-face-smile"></i>
                            </button>

                            <!-- 📎 File Attachment -->
                            <button type="button" onclick="document.getElementById('chatFileInput').click()" class="w-7 h-7 rounded-lg hover:bg-slate-100 hover:text-slate-700 flex items-center justify-center text-xs transition-colors" title="Attach file or Figma preview">
                                <i class="fas fa-paperclip"></i>
                            </button>

                            <!-- 🎙️ Voice Message Recording -->
                            <button type="button" onclick="toggleVoiceRecording()" id="voiceRecordBtn" class="w-7 h-7 rounded-lg hover:bg-slate-100 hover:text-slate-700 flex items-center justify-center text-xs transition-colors" title="Record Voice Note">
                                <i class="fas fa-microphone" id="voiceMicIcon"></i>
                            </button>

                            <!-- ⚡ Slash Commands -->
                            <button type="button" onclick="insertSlashCommand()" class="w-7 h-7 rounded-lg hover:bg-slate-100 hover:text-slate-700 flex items-center justify-center text-xs transition-colors" title="Shortcuts">
                                <i class="fas fa-bolt"></i>
                            </button>
                        </div>

                        <!-- Right Actions: Discard & Send -->
                        <div class="flex items-center gap-2">
                            <!-- Discard -->
                            <button type="button" onclick="clearComposer()" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors">
                                Discard
                            </button>

                            <!-- Send (Solid Black Button matching photo) -->
                            <button type="submit" id="sendMsgBtn" class="px-4 py-1.5 rounded-lg text-xs font-bold bg-black text-white hover:bg-slate-800 active:scale-95 transition-all shadow-xs flex items-center gap-1.5">
                                <span>Send</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        </div>

        <!-- COLUMN 4: Right Info Sidebar (Matches Photo Pixel-for-Pixel) -->
        <div class="w-[280px] bg-white border-l border-slate-200/80 flex flex-col shrink-0 select-none overflow-hidden transition-all duration-300" id="rightInfoSidebar">

            <!-- Top Tabs: [ Info ] Pins Files Links -->
            <div class="h-14 border-b border-slate-200/80 px-4 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-1 bg-slate-100/80 p-1 rounded-xl text-xs font-bold text-slate-600">
                    <button type="button" onclick="switchRightTab('info')" id="tabBtnInfo" class="px-3 py-1 rounded-lg bg-white text-slate-900 shadow-2xs">Info</button>
                    <button type="button" onclick="switchRightTab('pins')" id="tabBtnPins" class="px-3 py-1 rounded-lg hover:text-slate-900 transition-colors">Pins</button>
                    <button type="button" onclick="switchRightTab('files')" id="tabBtnFiles" class="px-3 py-1 rounded-lg hover:text-slate-900 transition-colors">Files</button>
                    <button type="button" onclick="switchRightTab('links')" id="tabBtnLinks" class="px-3 py-1 rounded-lg hover:text-slate-900 transition-colors">Links</button>
                </div>
            </div>

            <!-- Tab Content: Scrollable -->
            <div class="flex-grow overflow-y-auto custom-scrollbar p-5 space-y-6 text-xs" id="rightSidebarContent">

                <!-- 1. Main Info Card -->
                <div>
                    <h4 class="font-bold text-slate-900 text-sm mb-3">Main info</h4>
                    <div class="space-y-3">
                        <!-- Creator -->
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400 flex items-center gap-2">
                                <i class="far fa-user text-xs"></i> Creator
                            </span>
                            <div class="flex items-center gap-1.5 font-semibold text-slate-800">
                                <div class="w-5 h-5 rounded-full bg-slate-900 text-white text-[9px] font-bold flex items-center justify-center">
                                    {{ $creator ? strtoupper(substr($creator->name, 0, 1)) : 'A' }}
                                </div>
                                <span>{{ $creator ? $creator->name : 'Andrew M.' }}</span>
                            </div>
                        </div>

                        <!-- Date of creation -->
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400 flex items-center gap-2">
                                <i class="far fa-calendar text-xs"></i> Date of creation
                            </span>
                            <span class="font-semibold text-slate-800">28 May</span>
                        </div>

                        <!-- Status: • Active -->
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400 flex items-center gap-2">
                                <i class="fas fa-asterisk text-xs"></i> Status
                            </span>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                            </span>
                        </div>

                        <!-- Tags: 13 > -->
                        <div class="flex items-center justify-between cursor-pointer hover:text-slate-900">
                            <span class="text-slate-400 flex items-center gap-2">
                                <i class="fas fa-tag text-xs"></i> Tags
                            </span>
                            <span class="font-semibold text-slate-600 flex items-center gap-1">
                                <span>13</span> <i class="fas fa-chevron-right text-[9px]"></i>
                            </span>
                        </div>

                        <!-- Tasks: 4 > -->
                        <div class="flex items-center justify-between cursor-pointer hover:text-slate-900">
                            <span class="text-slate-400 flex items-center gap-2">
                                <i class="far fa-circle-check text-xs"></i> Tasks
                            </span>
                            <span class="font-semibold text-slate-600 flex items-center gap-1">
                                <span>4</span> <i class="fas fa-chevron-right text-[9px]"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="h-px bg-slate-100"></div>

                <!-- 2. Linked Threads -->
                <div>
                    <h4 class="font-bold text-slate-900 text-sm mb-3">Linked threads</h4>
                    <div class="space-y-1.5 font-medium">
                        <div class="flex items-center justify-between p-2 rounded-lg hover:bg-slate-50 cursor-pointer">
                            <span class="flex items-center gap-1.5 text-slate-700">
                                <span class="text-slate-400">#</span>
                                <span>front-end</span>
                            </span>
                            <span class="bg-slate-100 text-slate-500 font-bold text-[10px] px-1.5 py-0.5 rounded-full">4</span>
                        </div>
                        <div class="flex items-center justify-between p-2 rounded-lg hover:bg-slate-50 cursor-pointer">
                            <span class="flex items-center gap-1.5 text-slate-700">
                                <span class="text-slate-400">#</span>
                                <span>UI-kit design standards</span>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="h-px bg-slate-100"></div>

                <!-- 3. Thread Activity (Commit Heat Map Grid like GitHub / Photo) -->
                <div>
                    <h4 class="font-bold text-slate-900 text-sm mb-2.5">Thread activity</h4>
                    <!-- Heat map calendar squares -->
                    <div class="grid grid-cols-12 gap-1 py-1">
                        @php
                            $colors = ['bg-emerald-200', 'bg-emerald-300', 'bg-emerald-400', 'bg-emerald-500', 'bg-emerald-400', 'bg-emerald-300'];
                        @endphp
                        @for($i = 0; $i < 24; $i++)
                            <div class="w-3.5 h-3.5 rounded-xs {{ $colors[$i % count($colors)] }} opacity-90 hover:opacity-100 transition-opacity cursor-pointer" title="Activity score: {{ ($i * 3) % 10 + 1 }}"></div>
                        @endfor
                    </div>
                </div>

                <div class="h-px bg-slate-100"></div>

                <!-- 4. Members (9) -->
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-bold text-slate-900 text-sm">Members <span class="text-slate-400 font-normal">9</span></h4>
                        <div class="flex items-center gap-1.5 text-slate-400">
                            <button type="button" onclick="inviteMemberModal()" class="w-6 h-6 rounded hover:bg-slate-100 hover:text-slate-700 flex items-center justify-center text-xs" title="Invite">
                                <i class="fas fa-plus"></i>
                            </button>
                            <button type="button" class="w-6 h-6 rounded hover:bg-slate-100 hover:text-slate-700 flex items-center justify-center text-xs" title="Filter">
                                <i class="fas fa-sliders text-xs"></i>
                            </button>
                            <button type="button" class="w-6 h-6 rounded hover:bg-slate-100 hover:text-slate-700 flex items-center justify-center text-xs" title="Sort">
                                <i class="fas fa-arrow-down-wide-short text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Members List matching the photo -->
                    <div class="space-y-3">
                        <!-- Daniel Anderson -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-8 h-8 rounded-full bg-slate-800 text-white font-bold text-xs flex items-center justify-center shrink-0">
                                    DA
                                </div>
                                <div class="min-w-0">
                                    <h5 class="text-xs font-bold text-slate-900 truncate">Daniel Anderson</h5>
                                    <p class="text-[10px] text-slate-400 truncate">Art director</p>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700">Design</span>
                        </div>

                        <!-- Andrew Miller -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-8 h-8 rounded-full bg-orange-600 text-white font-bold text-xs flex items-center justify-center shrink-0">
                                    AM
                                </div>
                                <div class="min-w-0">
                                    <h5 class="text-xs font-bold text-slate-900 truncate">Andrew Miller</h5>
                                    <p class="text-[10px] text-slate-400 truncate">Product owner</p>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-rose-50 text-rose-700">Management</span>
                        </div>

                        <!-- William Johnson -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-8 h-8 rounded-full bg-blue-600 text-white font-bold text-xs flex items-center justify-center shrink-0">
                                    WJ
                                </div>
                                <div class="min-w-0">
                                    <h5 class="text-xs font-bold text-slate-900 truncate">William Johnson</h5>
                                    <p class="text-[10px] text-slate-400 truncate">UX/UI designer</p>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700">Design</span>
                        </div>

                        <!-- Emily Davis -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-8 h-8 rounded-full bg-slate-900 text-white font-bold text-xs flex items-center justify-center shrink-0">
                                    ED
                                </div>
                                <div class="min-w-0">
                                    <h5 class="text-xs font-bold text-slate-900 truncate">Emily Davis</h5>
                                    <p class="text-[10px] text-slate-400 truncate">Front-end dev</p>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-sky-50 text-sky-700">Development</span>
                        </div>
                    </div>

                    <!-- Offline Members Divider -->
                    <div class="mt-4 pt-3 border-t border-slate-100 text-[11px] font-semibold text-slate-400">
                        Offline
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<!-- Modal: Create New Channel / Subchannel -->
<div id="createChannelModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-slate-900 text-base">Create Channel or Sub-Channel</h3>
            <button onclick="closeCreateChannelModal()" class="text-slate-400 hover:text-slate-700">
                <i class="fas fa-xmark text-lg"></i>
            </button>
        </div>
        <form onsubmit="handleCreateChannelSubmit(event)" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Channel Name</label>
                <div class="flex items-center gap-2 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50 focus-within:bg-white focus-within:border-indigo-600">
                    <span class="text-slate-400 font-bold">#</span>
                    <input type="text" id="newChannelName" required placeholder="e.g. mobile-redesign or sprint-3" class="w-full bg-transparent border-0 focus:outline-hidden text-sm">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Parent Channel (Optional for nested tree)</label>
                <select id="newChannelParent" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm bg-white focus:outline-hidden">
                    <option value="">None (Top-Level Channel)</option>
                    @foreach($allChannels as $c)
                        <option value="{{ $c->id }}"># {{ $c->name }}</option>
                        @foreach($c->children as $sub)
                            <option value="{{ $sub->id }}">&nbsp;&nbsp;↳ {{ $sub->name }}</option>
                        @endforeach
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Topic / Description</label>
                <input type="text" id="newChannelTopic" placeholder="What is this channel about?" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-hidden">
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeCreateChannelModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100">Cancel</button>
                <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold bg-black text-white hover:bg-slate-800 transition-colors">Create Channel</button>
            </div>
        </form>
    </div>
</div>

<!-- Floating Emoji Picker Popover -->
<div id="emojiPickerPopover" class="hidden absolute bg-white border border-slate-200 rounded-2xl shadow-xl p-3 z-50 w-64 text-lg grid grid-cols-6 gap-2">
    @php
        $quickEmojis = ['👍', '❤️', '😊', '🔥', '🚀', '👀', '🎉', '🙌', '💯', '👏', '✨', '💡', '✅', '💪', '🤝', '😍', '🤩', '🎯'];
    @endphp
    @foreach($quickEmojis as $em)
        <button type="button" class="hover:scale-125 transition-transform p-1.5 flex items-center justify-center rounded-lg hover:bg-slate-100 cursor-pointer" onclick="selectEmoji('{{ $em }}')">
            {{ $em }}
        </button>
    @endforeach
</div>

<!-- Audio Chimes Synthesizer (Slack join/leave/message tones) -->
<script>
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
    const CURRENT_USER_ID = {{ $currentUser->id }};
    const CURRENT_USER_NAME = "{{ addslashes($currentUser->name) }}";
    const ACTIVE_CHANNEL_ID = {{ $activeChannel ? $activeChannel->id : 'null' }};
    const ACTIVE_DM_USER_ID = {{ $activeDmUser ? $activeDmUser->id : 'null' }};

    // Synthesized Web Audio Chimes
    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    function playChime(type) {
        if (audioCtx.state === 'suspended') {
            audioCtx.resume();
        }
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.connect(gain);
        gain.connect(audioCtx.destination);

        const now = audioCtx.currentTime;
        if (type === 'huddle_join') {
            osc.frequency.setValueAtTime(329.63, now); // E4
            osc.frequency.exponentialRampToValueAtTime(659.25, now + 0.18); // E5
            gain.gain.setValueAtTime(0.12, now);
            gain.gain.exponentialRampToValueAtTime(0.001, now + 0.4);
            osc.start(now);
            osc.stop(now + 0.4);
        } else if (type === 'huddle_leave') {
            osc.frequency.setValueAtTime(523.25, now); // C5
            osc.frequency.exponentialRampToValueAtTime(261.63, now + 0.2); // C4
            gain.gain.setValueAtTime(0.12, now);
            gain.gain.exponentialRampToValueAtTime(0.001, now + 0.35);
            osc.start(now);
            osc.stop(now + 0.35);
        } else if (type === 'message_sent') {
            osc.frequency.setValueAtTime(440, now);
            osc.frequency.exponentialRampToValueAtTime(880, now + 0.08);
            gain.gain.setValueAtTime(0.08, now);
            gain.gain.exponentialRampToValueAtTime(0.001, now + 0.2);
            osc.start(now);
            osc.stop(now + 0.2);
        }
    }

    // Auto-scroll chat to bottom
    function scrollChatToBottom() {
        const stream = document.getElementById('chatMessageStream');
        if (stream) {
            stream.scrollTop = stream.scrollHeight;
        }
    }
    window.addEventListener('load', scrollChatToBottom);

    // Collapsible sections
    function toggleCollapse(contentId, arrowId) {
        const el = document.getElementById(contentId);
        const arrow = document.getElementById(arrowId);
        if (el) {
            el.classList.toggle('hidden');
        }
        if (arrow) {
            arrow.classList.toggle('-rotate-90');
        }
    }

    // Toggle Right Info Sidebar
    function toggleRightSidebar() {
        const sidebar = document.getElementById('rightInfoSidebar');
        const btn = document.getElementById('infoToggleBtn');
        if (sidebar.classList.contains('hidden')) {
            sidebar.classList.remove('hidden');
            btn.classList.add('bg-slate-100');
        } else {
            sidebar.classList.add('hidden');
            btn.classList.remove('bg-slate-100');
        }
    }

    // Open dedicated window (Slack Desktop mode requested by user)
    function openNetChatWindow() {
        const channelSlug = "{{ $activeChannel ? $activeChannel->slug : '' }}";
        const dmId = "{{ $activeDmUser ? $activeDmUser->id : '' }}";
        let url = "{{ route('netchat.index') }}?mode=standalone";
        if (channelSlug) url += "&channel=" + channelSlug;
        if (dmId) url += "&dm=" + dmId;

        const w = 1420;
        const h = 920;
        const left = (screen.width - w) / 2;
        const top = (screen.height - h) / 2;
        window.open(url, 'NetChatDesktopWindow', `width=${w},height=${h},top=${top},left=${left},menubar=no,toolbar=no,location=no,status=no,resizable=yes`);
    }

    // Send Message AJAX Handler
    function handleSendMessage(e) {
        e.preventDefault();
        const form = document.getElementById('slackComposerForm');
        const textInput = document.getElementById('messageInputText');
        const textVal = textInput.value.trim();
        const fileInput = document.getElementById('chatFileInput');

        if (!textVal && (!fileInput.files || fileInput.files.length === 0)) {
            return;
        }

        const formData = new FormData(form);
        const sendBtn = document.getElementById('sendMsgBtn');
        sendBtn.disabled = true;
        sendBtn.classList.add('opacity-50');

        fetch("{{ route('netchat.send') }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            sendBtn.disabled = false;
            sendBtn.classList.remove('opacity-50');
            if (data.success && data.html) {
                const container = document.getElementById('messagesListContainer');
                container.insertAdjacentHTML('beforeend', data.html);
                textInput.value = '';
                clearAttachment();
                playChime('message_sent');
                scrollChatToBottom();
            }
        })
        .catch(err => {
            console.error('Error sending message:', err);
            sendBtn.disabled = false;
            sendBtn.classList.remove('opacity-50');
        });
    }

    function handleComposerKeydown(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            handleSendMessage(e);
        }
    }

    function clearComposer() {
        document.getElementById('messageInputText').value = '';
        clearAttachment();
    }

    function handleFileSelected(e) {
        const file = e.target.files[0];
        if (file) {
            document.getElementById('attachmentPreviewBar').classList.remove('hidden');
            document.getElementById('attachmentFileName').textContent = file.name + ' (' + (file.size/1024).toFixed(1) + ' KB)';
        }
    }

    function clearAttachment() {
        const fileInput = document.getElementById('chatFileInput');
        fileInput.value = '';
        document.getElementById('attachmentPreviewBar').classList.add('hidden');
    }

    // Toggle Reaction
    function toggleReaction(msgId, emoji) {
        fetch(`/netchat/reaction/${msgId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ emoji: emoji })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                renderReactionsRow(msgId, data.reactions);
            }
        });
    }

    function renderReactionsRow(msgId, reactions) {
        const row = document.getElementById(`reactions-${msgId}`);
        if (!row) return;

        let html = '';
        for (const [em, uids] of Object.entries(reactions)) {
            if (uids.length > 0) {
                const hasMine = uids.includes(CURRENT_USER_ID);
                html += `<button type="button" class="reaction-pill inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium border transition-colors ${hasMine ? 'bg-indigo-50 border-indigo-200 text-indigo-700 font-bold' : 'bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100'}" onclick="toggleReaction(${msgId}, '${em}')">
                    <span>${em}</span>
                    ${uids.length > 1 ? `<span class="text-[11px]">${uids.length}</span>` : ''}
                </button>`;
            }
        }
        html += `<button type="button" class="add-reaction-btn opacity-0 group-hover:opacity-100 inline-flex items-center justify-center w-6 h-6 rounded-full border border-transparent hover:border-slate-200 hover:bg-slate-100 text-slate-400 hover:text-slate-600 text-xs transition-all" onclick="showReactionPicker(event, ${msgId})" title="Add reaction">
            <i class="far fa-smile"></i>
        </button>`;

        row.innerHTML = html;
    }

    let activeReactionMsgId = null;
    function showReactionPicker(e, msgId) {
        e.stopPropagation();
        activeReactionMsgId = msgId;
        const pop = document.getElementById('emojiPickerPopover');
        const rect = e.target.getBoundingClientRect();
        pop.style.top = (rect.bottom + window.scrollY - 180) + 'px';
        pop.style.left = (rect.left + window.scrollX - 80) + 'px';
        pop.classList.remove('hidden');
    }

    function toggleEmojiPicker(e) {
        e.stopPropagation();
        activeReactionMsgId = null;
        const pop = document.getElementById('emojiPickerPopover');
        const rect = e.target.getBoundingClientRect();
        pop.style.top = (rect.top + window.scrollY - 160) + 'px';
        pop.style.left = (rect.left + window.scrollX) + 'px';
        pop.classList.toggle('hidden');
    }

    function selectEmoji(em) {
        const pop = document.getElementById('emojiPickerPopover');
        pop.classList.add('hidden');
        if (activeReactionMsgId) {
            toggleReaction(activeReactionMsgId, em);
        } else {
            const input = document.getElementById('messageInputText');
            input.value += ' ' + em + ' ';
            input.focus();
        }
    }

    document.addEventListener('click', () => {
        document.getElementById('emojiPickerPopover')?.classList.add('hidden');
    });

    // Formatting shortcuts
    function applyBoldFormat() {
        const input = document.getElementById('messageInputText');
        const start = input.selectionStart;
        const end = input.selectionEnd;
        const val = input.value;
        const selected = val.substring(start, end) || 'text';
        input.value = val.substring(0, start) + '*' + selected + '*' + val.substring(end);
        input.focus();
    }

    function insertMentionTag() {
        const input = document.getElementById('messageInputText');
        input.value += ' @Diana T. ';
        input.focus();
    }

    function suggestAiResponse() {
        const input = document.getElementById('messageInputText');
        const suggestions = [
            "Everything looks pretty good. Finish the states and we'll start development. This time it will be even faster than the last time. We're breaking all records 💪 @Diana T.",
            "Looks awesome! Checked the Lottie animations and typography tokens. Ready for client review.",
            "Could we verify the dark mode contrast tokens for button states before merging?"
        ];
        input.value = suggestions[Math.floor(Math.random() * suggestions.length)];
        input.focus();
    }

    function insertSlashCommand() {
        const input = document.getElementById('messageInputText');
        input.value = '/huddle ';
        input.focus();
    }

    function copyMessageText(msgId) {
        const item = document.getElementById(`msg-item-${msgId}`);
        if (item) {
            const text = item.querySelector('.break-words')?.innerText;
            if (text) {
                navigator.clipboard.writeText(text);
                alert('Message copied to clipboard!');
            }
        }
    }

    function replyInThread(msgId, senderName) {
        const input = document.getElementById('messageInputText');
        input.value = `@${senderName} `;
        input.focus();
    }

    // Voice Message Recording with MediaRecorder
    let mediaRecorder = null;
    let audioChunks = [];
    let isRecordingVoice = false;

    async function toggleVoiceRecording() {
        const btn = document.getElementById('voiceRecordBtn');
        const icon = document.getElementById('voiceMicIcon');

        if (!isRecordingVoice) {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                mediaRecorder = new MediaRecorder(stream);
                audioChunks = [];

                mediaRecorder.ondataavailable = e => {
                    if (e.data.size > 0) audioChunks.push(e.data);
                };

                mediaRecorder.onstop = () => {
                    const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                    sendVoiceBlob(audioBlob);
                    stream.getTracks().forEach(t => t.stop());
                };

                mediaRecorder.start();
                isRecordingVoice = true;
                btn.classList.add('text-rose-600', 'animate-pulse');
                icon.className = 'fas fa-stop';
            } catch (err) {
                alert('Microphone access is required to record voice notes: ' + err.message);
            }
        } else {
            mediaRecorder.stop();
            isRecordingVoice = false;
            btn.classList.remove('text-rose-600', 'animate-pulse');
            icon.className = 'fas fa-microphone';
        }
    }

    function sendVoiceBlob(blob) {
        const formData = new FormData();
        formData.append('audio', blob, 'voice.webm');
        if (ACTIVE_CHANNEL_ID) formData.append('channel_id', ACTIVE_CHANNEL_ID);
        if (ACTIVE_DM_USER_ID) formData.append('receiver_id', ACTIVE_DM_USER_ID);

        fetch("{{ route('netchat.voice') }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.html) {
                document.getElementById('messagesListContainer').insertAdjacentHTML('beforeend', data.html);
                playChime('message_sent');
                scrollChatToBottom();
            }
        });
    }

    function togglePlayVoice(btn, path) {
        let audio = btn._audio;
        if (!audio) {
            audio = new Audio(path);
            btn._audio = audio;
            audio.onended = () => {
                btn.innerHTML = '<i class="fas fa-play text-xs"></i>';
            };
        }
        if (audio.paused) {
            audio.play();
            btn.innerHTML = '<i class="fas fa-pause text-xs"></i>';
        } else {
            audio.pause();
            btn.innerHTML = '<i class="fas fa-play text-xs"></i>';
        }
    }

    // ==========================================
    // SLACK HUDDLE (VOICE CALL & SCREEN SHARING)
    // ==========================================
    let isHuddleActive = false;
    let huddleRoomId = null;
    let localStream = null;
    let screenStream = null;
    let isMicMuted = false;
    let isScreenSharing = false;
    let huddlePollInterval = null;

    async function toggleHuddle() {
        if (!isHuddleActive) {
            startHuddle();
        } else {
            leaveHuddle();
        }
    }

    async function startHuddle() {
        try {
            // Get user audio
            try {
                localStream = await navigator.mediaDevices.getUserMedia({ audio: true, video: false });
            } catch (audioErr) {
                console.warn('Microphone not available, joining listen-only:', audioErr);
            }

            const res = await fetch("{{ route('netchat.huddle.start') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    channel_id: ACTIVE_CHANNEL_ID
                })
            });
            const data = await res.json();

            if (data.success) {
                isHuddleActive = true;
                huddleRoomId = data.room_id;
                playChime('huddle_join');

                // Show floating bar
                const bar = document.getElementById('floatingHuddleBar');
                bar.classList.remove('hidden');

                // Update top header button
                const topBtn = document.getElementById('huddleTopBtn');
                topBtn.className = 'flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold border border-emerald-400 bg-emerald-50 text-emerald-800 animate-pulse';
                document.getElementById('huddleTopBtnText').textContent = 'In Huddle';

                renderHuddleParticipants(data.participants);

                // Start polling signals and participant heartbeats
                if (huddlePollInterval) clearInterval(huddlePollInterval);
                huddlePollInterval = setInterval(pollHuddleState, 3000);
            }
        } catch (err) {
            alert('Could not start huddle: ' + err.message);
        }
    }

    async function leaveHuddle() {
        if (!huddleRoomId) return;

        if (localStream) {
            localStream.getTracks().forEach(t => t.stop());
            localStream = null;
        }
        if (screenStream) {
            screenStream.getTracks().forEach(t => t.stop());
            screenStream = null;
        }

        playChime('huddle_leave');

        try {
            await fetch("{{ route('netchat.huddle.leave') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ room_id: huddleRoomId })
            });
        } catch (e) {
            console.error(e);
        }

        isHuddleActive = false;
        huddleRoomId = null;
        isScreenSharing = false;
        if (huddlePollInterval) clearInterval(huddlePollInterval);

        // Hide floating bar
        document.getElementById('floatingHuddleBar').classList.add('hidden');
        document.getElementById('screenShareContainer').classList.add('hidden');

        // Reset top button
        const topBtn = document.getElementById('huddleTopBtn');
        topBtn.className = 'flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border border-slate-200 bg-white hover:bg-slate-50 text-slate-700';
        document.getElementById('huddleTopBtnText').textContent = 'Huddle';
    }

    function toggleHuddleMic() {
        if (!localStream) return;
        const audioTrack = localStream.getAudioTracks()[0];
        if (audioTrack) {
            audioTrack.enabled = !audioTrack.enabled;
            isMicMuted = !audioTrack.enabled;

            const icon = document.getElementById('huddleMicIcon');
            const btn = document.getElementById('huddleMicBtn');
            if (isMicMuted) {
                icon.className = 'fas fa-microphone-slash text-rose-400';
                btn.classList.add('bg-rose-900/60');
            } else {
                icon.className = 'fas fa-microphone';
                btn.classList.remove('bg-rose-900/60');
            }

            // Post signal update
            fetch("{{ route('netchat.huddle.signal') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                },
                body: JSON.stringify({
                    room_id: huddleRoomId,
                    type: 'mute_state',
                    is_muted: isMicMuted
                })
            });
        }
    }

    // Real Screen Sharing with navigator.mediaDevices.getDisplayMedia
    async function toggleScreenShare() {
        if (!isScreenSharing) {
            try {
                screenStream = await navigator.mediaDevices.getDisplayMedia({
                    video: { cursor: 'always' },
                    audio: false
                });

                const videoEl = document.getElementById('screenVideoElement');
                videoEl.srcObject = screenStream;
                document.getElementById('screenShareContainer').classList.remove('hidden');

                isScreenSharing = true;
                document.getElementById('huddleScreenBtn').classList.add('bg-emerald-600');

                // Handle screen share stop via browser UI
                screenStream.getVideoTracks()[0].onended = () => {
                    stopScreenShare();
                };

                // Notify peers
                fetch("{{ route('netchat.huddle.signal') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                    body: JSON.stringify({
                        room_id: huddleRoomId,
                        type: 'screen_share_state',
                        is_screensharing: true
                    })
                });
            } catch (err) {
                console.warn('Screen share canceled or not supported:', err);
            }
        } else {
            stopScreenShare();
        }
    }

    function stopScreenShare() {
        if (screenStream) {
            screenStream.getTracks().forEach(t => t.stop());
            screenStream = null;
        }
        isScreenSharing = false;
        document.getElementById('screenShareContainer').classList.add('hidden');
        document.getElementById('huddleScreenBtn').classList.remove('bg-emerald-600');

        if (huddleRoomId) {
            fetch("{{ route('netchat.huddle.signal') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                body: JSON.stringify({
                    room_id: huddleRoomId,
                    type: 'screen_share_state',
                    is_screensharing: false
                })
            });
        }
    }

    function toggleScreenFullscreen() {
        const videoEl = document.getElementById('screenVideoElement');
        if (videoEl.requestFullscreen) {
            videoEl.requestFullscreen();
        }
    }

    let lastSignalId = 0;
    async function pollHuddleState() {
        if (!isHuddleActive || !huddleRoomId) return;

        try {
            const res = await fetch(`{{ route('netchat.huddle.poll') }}?room_id=${huddleRoomId}&last_signal_id=${lastSignalId}`);
            const data = await res.json();

            if (data.participants) {
                renderHuddleParticipants(data.participants);
            }

            if (data.signals && data.signals.length > 0) {
                data.signals.forEach(sig => {
                    if (sig.id > lastSignalId) lastSignalId = sig.id;
                    if (sig.type === 'user_joined') {
                        playChime('huddle_join');
                    } else if (sig.type === 'user_left') {
                        playChime('huddle_leave');
                    }
                });
            }
        } catch (err) {
            console.error('Error polling huddle:', err);
        }
    }

    function renderHuddleParticipants(participants) {
        const count = participants.length;
        document.getElementById('huddleStatusText').textContent = `Active • ${count} participant${count > 1 ? 's' : ''}`;

        const bubbles = document.getElementById('huddleParticipantsBubbles');
        bubbles.innerHTML = participants.map(p => `
            <div class="w-7 h-7 rounded-full bg-slate-800 border-2 ${p.is_speaking ? 'border-emerald-400' : 'border-slate-900'} text-white text-[10px] font-bold flex items-center justify-center shrink-0 shadow-xs" title="${p.user_name} ${p.is_muted ? '(Muted)' : ''}">
                ${p.user_name.charAt(0).toUpperCase()}
            </div>
        `).join('');
    }

    // Modal Create Channel
    function openCreateChannelModal() {
        document.getElementById('createChannelModal').classList.remove('hidden');
        document.getElementById('createChannelModal').classList.add('flex');
    }

    function closeCreateChannelModal() {
        document.getElementById('createChannelModal').classList.add('hidden');
        document.getElementById('createChannelModal').classList.remove('flex');
    }

    async function handleCreateChannelSubmit(e) {
        e.preventDefault();
        const name = document.getElementById('newChannelName').value;
        const parentId = document.getElementById('newChannelParent').value;
        const topic = document.getElementById('newChannelTopic').value;

        const res = await fetch("{{ route('netchat.channel.create') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                name: name,
                parent_id: parentId || null,
                topic: topic
            })
        });
        const data = await res.json();
        if (data.success && data.redirect) {
            window.location.href = data.redirect;
        }
    }

    // Right Sidebar Tab Switcher
    function switchRightTab(tab) {
        const tabs = ['info', 'pins', 'files', 'links'];
        tabs.forEach(t => {
            const btn = document.getElementById('tabBtn' + t.charAt(0).toUpperCase() + t.slice(1));
            if (t === tab) {
                btn.className = 'px-3 py-1 rounded-lg bg-white text-slate-900 shadow-2xs font-bold';
            } else {
                btn.className = 'px-3 py-1 rounded-lg hover:text-slate-900 transition-colors font-medium';
            }
        });
    }

    // Periodic chat message polling for live real-time sync
    let lastPollMsgId = {{ $messages->last() ? $messages->last()->id : 0 }};
    setInterval(async () => {
        const channelParam = ACTIVE_CHANNEL_ID ? `&channel_id=${ACTIVE_CHANNEL_ID}` : '';
        const dmParam = ACTIVE_DM_USER_ID ? `&dm_user_id=${ACTIVE_DM_USER_ID}` : '';
        if (!channelParam && !dmParam) return;

        try {
            const res = await fetch(`{{ route('netchat.poll') }}?last_id=${lastPollMsgId}${channelParam}${dmParam}`);
            const data = await res.json();
            if (data.messages && data.messages.length > 0) {
                const container = document.getElementById('messagesListContainer');
                data.messages.forEach(m => {
                    if (m.id > lastPollMsgId) {
                        lastPollMsgId = m.id;
                        if (m.sender_id !== CURRENT_USER_ID) {
                            container.insertAdjacentHTML('beforeend', m.html);
                            playChime('message_sent');
                            scrollChatToBottom();
                        }
                    }
                });
            }
        } catch (e) {
            // silent poll
        }
    }, 4000);
</script>

@if(!$isPopup)
    @endsection
@else
</body>
</html>
@endif
