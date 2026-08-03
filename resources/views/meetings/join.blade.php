<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Collaboration Hub - Nexus Institute</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600;700;800;900&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
    <!-- Tailwind Config -->
    <script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            "colors": {
                    "secondary": "#5f5e5c",
                    "on-error": "#ffffff",
                    "surface-bright": "#f9f9f9",
                    "surface-tint": "#b02e00",
                    "surface-container-lowest": "#ffffff",
                    "primary": "#b02e00",
                    "surface": "#f9f9f9",
                    "on-primary-fixed-variant": "#872100",
                    "on-secondary-fixed": "#1c1c1a",
                    "tertiary": "#5f5e5e",
                    "on-surface": "#1a1c1c",
                    "primary-container": "#ff5c2b",
                    "inverse-surface": "#2f3131",
                    "on-secondary-container": "#656461",
                    "border-subtle": "#E2E8F0",
                    "surface-container": "#eeeeee",
                    "on-tertiary-fixed": "#1c1b1b",
                    "on-secondary": "#ffffff",
                    "outline-variant": "#e3beb4",
                    "success-green": "#10B981",
                    "on-tertiary": "#ffffff",
                    "on-surface-variant": "#5b4139",
                    "secondary-fixed-dim": "#c8c6c3",
                    "on-secondary-fixed-variant": "#474744",
                    "on-primary-fixed": "#3b0900",
                    "primary-fixed-dim": "#ffb5a0",
                    "on-tertiary-container": "#2c2b2b",
                    "surface-container-high": "#e8e8e8",
                    "tertiary-container": "#949292",
                    "on-error-container": "#93000a",
                    "secondary-container": "#e5e2de",
                    "tertiary-fixed": "#e5e2e1",
                    "surface-dim": "#dadada",
                    "surface-container-highest": "#e2e2e2",
                    "surface-variant": "#e2e2e2",
                    "info-blue": "#3B82F6",
                    "on-tertiary-fixed-variant": "#474646",
                    "error-container": "#ffdad6",
                    "on-primary-container": "#571200",
                    "tertiary-fixed-dim": "#c9c6c5",
                    "primary-fixed": "#ffdbd1",
                    "inverse-primary": "#ffb5a0",
                    "secondary-fixed": "#e5e2de",
                    "surface-slate": "#F8FAFC",
                    "surface-container-low": "#f4f3f3",
                    "inverse-on-surface": "#f1f1f1",
                    "outline": "#8f7068",
                    "on-primary": "#ffffff",
                    "background": "#f9f9f9",
                    "on-background": "#1a1c1c",
                    "error": "#ba1a1a"
            },
            "borderRadius": {
                    "DEFAULT": "0.125rem",
                    "lg": "0.25rem",
                    "xl": "0.5rem",
                    "full": "0.75rem"
            },
            "spacing": {
                    "stack-sm": "12px",
                    "stack-md": "24px",
                    "container-max-width": "1440px",
                    "stack-lg": "48px",
                    "margin-desktop": "40px",
                    "base": "8px",
                    "margin-mobile": "16px",
                    "gutter": "24px"
            },
            "fontFamily": {
                    "label-sm": ["JetBrains Mono"],
                    "display-lg": ["Hanken Grotesk"],
                    "body-lg": ["Inter"],
                    "button": ["Inter"],
                    "title-md": ["Hanken Grotesk"],
                    "body-md": ["Inter"],
                    "headline-lg-mobile": ["Hanken Grotesk"],
                    "headline-lg": ["Hanken Grotesk"]
            },
          },
        },
      }
    </script>
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e2e2e2;
            border-radius: 10px;
        }
        .active-speaker-ring {
            box-shadow: 0 0 0 3px #ff5c2b;
        }
        .video-container {
            aspect-ratio: 16/9;
            background: #1e293b;
        }
        video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
</head>
<body class="bg-background text-on-background font-body-md overflow-hidden h-screen">

<!-- Global Layout Shell -->
<div class="flex h-screen overflow-hidden">
    <!-- Side Navigation Shell -->
    <aside class="fixed left-0 top-0 h-screen w-64 bg-surface border-r border-border-subtle flex flex-col py-base z-50">
        <div class="px-6 mb-stack-lg py-4 flex flex-col gap-2">
            <img src="{{ \App\Models\Setting::getLogoUrl() }}" alt="{{ \App\Models\Setting::get('institute_name', 'Nexus Institute') }} Logo" class="h-10 w-auto object-contain self-start">
            <div>
                <h1 class="font-display-lg text-[18px] font-bold text-primary leading-tight">{{ \App\Models\Setting::get('institute_name', 'Nexus Institute') }}</h1>
                <p class="text-secondary text-xs font-medium m-0">Collaboration Suite</p>
            </div>
        </div>
        <nav class="flex-1 px-3 space-y-1">
            @php
                $dashRoute = route('dashboard');
                if (session('user_role_slug') === 'student') $dashRoute = route('student.dashboard');
                elseif (session('user_role_slug') === 'staff') $dashRoute = route('staff.dashboard');
            @endphp
            <a class="flex items-center gap-3 px-3 py-3 rounded-lg text-secondary font-medium hover:bg-surface-container transition-colors duration-150 group text-decoration-none" href="{{ $dashRoute }}">
                <span class="material-symbols-outlined group-hover:scale-110 transition-transform">dashboard</span>
                Dashboard
            </a>
            <a class="flex items-center gap-3 px-3 py-3 rounded-lg text-secondary font-medium hover:bg-surface-container transition-colors duration-150 group text-decoration-none" href="{{ route('messages.full') }}">
                <span class="material-symbols-outlined group-hover:scale-110 transition-transform">chat</span>
                Messages
            </a>
            <a class="flex items-center gap-3 px-3 py-3 rounded-lg text-primary font-bold border-r-4 border-primary bg-surface-container-low transition-colors duration-150 group text-decoration-none" href="{{ route('meetings.index') }}">
                <span class="material-symbols-outlined group-hover:scale-110 transition-transform" style="font-variation-settings: 'FILL' 1;">video_call</span>
                Meetings
            </a>
        </nav>
        <div class="px-3 mt-auto space-y-1 border-t border-border-subtle pt-4">
            <a class="flex items-center gap-3 px-3 py-3 rounded-lg text-secondary font-medium hover:bg-surface-container transition-colors text-decoration-none" href="{{ route('settings.index') }}">
                <span class="material-symbols-outlined">settings</span>
                Settings
            </a>
            <div class="flex items-center gap-3 px-3 py-4 mt-2">
                <div class="w-10 h-10 rounded-full bg-primary-container text-white flex items-center justify-center font-bold">
                    {{ strtoupper(substr(session('user_name', 'U'), 0, 1)) }}
                </div>
                <div class="overflow-hidden">
                    <p class="text-sm font-bold truncate mb-0">{{ session('user_name', 'Participant') }}</p>
                    <p class="text-xs text-secondary truncate mb-0">{{ session('user_role', 'Member') }}</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content Canvas -->
    <div class="flex-grow ml-64 flex flex-col h-screen overflow-hidden">
        
        <!-- Join Room Overlay -->
        <div class="absolute inset-0 bg-slate-950 z-50 flex flex-col items-center justify-center p-6 ml-64" id="joinPanel">
            <div class="max-w-md w-full bg-slate-900 border border-slate-800 p-8 rounded-2xl text-center shadow-2xl">
                <div class="mb-6 flex justify-center">
                    <img src="{{ \App\Models\Setting::getLogoUrl() }}" alt="{{ \App\Models\Setting::get('institute_name', 'Nexus Institute') }} Logo" class="h-16 w-auto object-contain bg-slate-850 p-2 rounded-2xl">
                </div>
                <h2 class="text-2xl font-bold text-white mb-2">Join Meeting Room</h2>
                <p class="text-primary-container font-mono text-sm mb-4">ID: {{ $id }}</p>
                <p class="text-slate-400 text-sm mb-6">Please grant camera and microphone permissions when prompted to join the secure peer connection.</p>
                <button class="w-full bg-primary-container text-white py-3 px-6 rounded-xl font-bold hover:brightness-110 active:scale-95 transition-all border-0 cursor-pointer flex items-center justify-center gap-2" onclick="startMeeting()">
                    <span class="material-symbols-outlined">sensors</span> Join Conference
                </button>
            </div>
        </div>

        <!-- Top App Bar -->
        <header class="h-16 bg-surface-bright border-b border-border-subtle flex justify-between items-center px-margin-desktop shrink-0">
            <div class="flex items-center gap-6">
                <img src="{{ \App\Models\Setting::getLogoUrl() }}" alt="Logo" class="h-8 w-auto object-contain">
                <span class="text-title-md font-black text-primary font-title-md">{{ \App\Models\Setting::get('institute_name', 'Nexus Institute') }} Collaboration Hub</span>
                <div class="h-6 w-px bg-border-subtle"></div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 bg-primary/10 text-primary rounded-full text-xs font-mono font-bold">Room: {{ $id }}</span>
                    <span class="badge bg-secondary" id="connectionStatus">Idle</span>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2 px-3 py-1.5 bg-error-container text-on-error-container rounded-full text-xs font-bold animate-pulse">
                    <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">fiber_manual_record</span>
                    LIVE CALL
                </div>
            </div>
        </header>

        <!-- Messaging Viewport / Main Conference Area -->
        <div class="flex-grow flex overflow-hidden">
            <!-- Video Conferencing Area -->
            <section class="flex-1 flex flex-col bg-slate-950 relative overflow-hidden">
                <!-- Main Speaker View -->
                <div class="flex-1 relative flex items-center justify-center p-6 overflow-hidden">
                    <div class="w-full h-full rounded-2xl overflow-hidden shadow-2xl relative bg-slate-900 flex items-center justify-center border border-slate-800">
                        <video id="remoteVideo" autoplay playsinline class="w-full h-full object-cover"></video>
                        <div class="status-msg text-slate-500 absolute font-medium text-center" id="waitingMsg">
                            <span class="material-symbols-outlined text-4xl mb-2 d-block animate-pulse">hourglass_empty</span>
                            Waiting for others to join...
                        </div>
                        <!-- Overlay Info -->
                        <div class="absolute bottom-6 left-6 flex items-center gap-3 bg-black/40 backdrop-blur-md px-4 py-2 rounded-lg border border-white/10" id="remoteLabel" style="display:none;">
                            <div class="w-2 h-2 bg-success-green rounded-full animate-ping"></div>
                            <span class="text-white font-medium text-xs">Remote Participant (Speaker)</span>
                            <span class="material-symbols-outlined text-white text-sm" style="font-variation-settings: 'FILL' 1;">mic</span>
                        </div>
                    </div>
                </div>

                <!-- Participant Grid / Thumbnail Bar -->
                <div id="participantGrid" class="px-6 pb-6 flex gap-4 overflow-x-auto custom-scrollbar shrink-0">
                    <!-- Local Camera Feed -->
                    <div class="flex-shrink-0 w-44 aspect-video rounded-xl bg-slate-900 border border-white/10 overflow-hidden relative shadow-md">
                        <video id="localVideo" autoplay playsinline muted class="w-full h-full object-cover"></video>
                        <div class="absolute bottom-2 left-2 text-[10px] text-white bg-black/50 px-1.5 py-0.5 rounded">You (Local Camera)</div>
                    </div>
                </div>

                <!-- Conference Controls -->
                <div class="h-20 bg-slate-900/95 backdrop-blur-xl border-t border-slate-800 flex items-center justify-between px-6 shrink-0 z-30">
                    <div class="flex items-center gap-4">
                        <span class="font-bold text-sm text-slate-400" id="callDuration">00:00:00</span>
                        <div class="h-4 w-px bg-slate-800"></div>
                        <div class="flex items-center gap-2 text-slate-400">
                            <span class="material-symbols-outlined text-success-green text-sm">shield</span>
                            <span class="text-xs font-medium">Secure Peer Network</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <button class="w-12 h-12 flex items-center justify-center rounded-full bg-slate-800 border border-slate-700 hover:bg-slate-700 transition-all text-white cursor-pointer" id="btnAudio" onclick="toggleAudio()" title="Mute/Unmute Audio">
                            <span class="material-symbols-outlined">mic</span>
                        </button>
                        <button class="w-12 h-12 flex items-center justify-center rounded-full bg-slate-800 border border-slate-700 hover:bg-slate-700 transition-all text-white cursor-pointer" id="btnVideo" onclick="toggleVideo()" title="Turn Off/On Camera">
                            <span class="material-symbols-outlined">videocam</span>
                        </button>
                        <button class="w-12 h-12 flex items-center justify-center rounded-full bg-slate-800 border border-slate-700 hover:bg-slate-700 transition-all text-white cursor-pointer" id="btnScreen" onclick="toggleScreenShare()" title="Share Screen">
                            <span class="material-symbols-outlined">screen_share</span>
                        </button>
                        <button class="w-12 h-12 flex items-center justify-center rounded-full bg-slate-800 border border-slate-700 hover:bg-slate-700 transition-all text-white cursor-pointer" onclick="copyInviteLink()" title="Copy Meeting Link">
                            <span class="material-symbols-outlined">link</span>
                        </button>
                        <button class="px-6 h-12 flex items-center justify-center rounded-full bg-error text-white font-bold hover:bg-error/90 transition-all border-0 cursor-pointer" onclick="endCall()">
                            Leave Call
                        </button>
                    </div>
                    <div class="flex items-center gap-3">
                        <button class="w-10 h-10 flex items-center justify-center rounded-lg bg-primary/10 text-primary hover:bg-primary/20 transition-all cursor-pointer" id="chat-toggle">
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">chat_bubble</span>
                        </button>
                    </div>
                </div>
            </section>

            <!-- Chat Sidebar Panel -->
            <aside class="w-80 bg-surface border-l border-border-subtle flex flex-col transition-all duration-300" id="meeting-sidebar">
                <div class="flex border-b border-border-subtle p-3 bg-surface-slate align-items-center justify-between">
                    <span class="text-sm font-bold text-primary flex items-center gap-2">
                        <span class="material-symbols-outlined">chat</span> Live Chat Feed
                    </span>
                </div>
                <!-- Chat Content -->
                <div class="flex-1 overflow-y-auto p-4 space-y-4 custom-scrollbar bg-slate-50" id="meeting-chat-body">
                    <div class="space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-on-surface">System Host</span>
                            <span class="text-[10px] text-secondary">Now</span>
                        </div>
                        <div class="bg-white p-3 rounded-lg rounded-tl-none border border-border-subtle">
                            <p class="text-xs text-secondary m-0">Welcome to Nexus Institute Collaboration Suite. Share the link to invite participants.</p>
                        </div>
                    </div>
                </div>
                <!-- Chat Input -->
                <div class="p-4 border-t border-border-subtle bg-surface">
                    <div class="relative">
                        <textarea class="w-full bg-white border border-border-subtle rounded-xl text-xs py-3 px-3 pr-10 focus:ring-primary/20 focus:border-primary resize-none h-16 transition-all" placeholder="Send a message..." rows="1" id="meeting-chat-input" onkeydown="handleChatKeyPress(event)"></textarea>
                        <button class="absolute bottom-2.5 right-2 bg-transparent border-0 text-primary hover:scale-110 transition-transform cursor-pointer" onclick="sendMeetingChatMessage()">
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">send</span>
                        </button>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>

<!-- Toast Notification Container -->
<div id="toastContainer" class="fixed top-4 right-4 z-[9999] flex flex-col gap-2 pointer-events-none"></div>

<!-- PeerJS & Conference Logic -->
<script src="https://unpkg.com/peerjs@1.5.1/dist/peerjs.min.js"></script>
<script>
    const ROOM_ID = "{{ $id }}";
    const PEER_ID_PREFIX = "feesmanager-peer-";
    
    const myPeerId = PEER_ID_PREFIX + Math.random().toString(36).substr(2, 9);

    let peer = null;
    let localStream = null;
    const activeCalls = {};

    const localVideo = document.getElementById('localVideo');
    const remoteVideo = document.getElementById('remoteVideo');
    const statusBadge = document.getElementById('connectionStatus');
    const waitingMsg = document.getElementById('waitingMsg');
    const remoteLabel = document.getElementById('remoteLabel');

    let isAudioMuted = false;
    let isVideoMuted = false;
    let isScreenSharing = false;
    let screenStream = null;

    // Start timer for duration
    let durationSeconds = 0;
    setInterval(() => {
        if (localStream) {
            durationSeconds++;
            const hrs = String(Math.floor(durationSeconds / 3600)).padStart(2, '0');
            const mins = String(Math.floor((durationSeconds % 3600) / 60)).padStart(2, '0');
            const secs = String(durationSeconds % 60).padStart(2, '0');
            document.getElementById('callDuration').textContent = `${hrs}:${mins}:${secs}`;
        }
    }, 1000);

    // Collapsible Sidebar logic
    const chatToggle = document.getElementById('chat-toggle');
    const sidebar = document.getElementById('meeting-sidebar');
    let sidebarVisible = true;
    chatToggle.addEventListener('click', () => {
        sidebarVisible = !sidebarVisible;
        if(sidebarVisible) {
            sidebar.style.display = 'flex';
        } else {
            sidebar.style.display = 'none';
        }
    });

    const MEETING_ID = "{{ $meeting ? $meeting->id : '' }}";
    const MY_USER_ID = "{{ session('user_id') }}";

    // Chat sending functions
    function handleChatKeyPress(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMeetingChatMessage();
        }
    }

    function sendMeetingChatMessage() {
        const textarea = document.getElementById('meeting-chat-input');
        const text = textarea.value.trim();
        if (text === '' || !MEETING_ID) return;

        fetch(`/meetings/${MEETING_ID}/chat`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ message: text })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                textarea.value = '';
                fetchChatMessages();
            }
        })
        .catch(err => console.error("Error sending chat message:", err));
    }

    let isFirstLoad = true;
    const displayedMessageIds = new Set();

    function fetchChatMessages() {
        if (!MEETING_ID) return;

        fetch(`/meetings/${MEETING_ID}/chat`)
        .then(res => res.json())
        .then(data => {
            if (data.success && data.messages) {
                const chatBody = document.getElementById('meeting-chat-body');
                let newMessagesAppended = false;

                data.messages.forEach(msg => {
                    if (!displayedMessageIds.has(msg.id)) {
                        displayedMessageIds.add(msg.id);

                        const isMe = String(msg.sender_id) === String(MY_USER_ID);
                        const senderName = isMe ? 'You' : msg.sender;
                        const messageHtml = `
                            <div class="space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold ${isMe ? 'text-primary' : 'text-on-surface'}">${senderName}</span>
                                    <span class="text-[10px] text-secondary">${msg.time}</span>
                                </div>
                                <div class="${isMe ? 'bg-primary/5 border-primary/20' : 'bg-white border-border-subtle'} p-3 rounded-lg rounded-tl-none border">
                                    <p class="text-xs text-on-surface m-0">${msg.message}</p>
                                </div>
                            </div>
                        `;
                        chatBody.insertAdjacentHTML('beforeend', messageHtml);
                        newMessagesAppended = true;

                        if (!isFirstLoad && !isMe) {
                            showToast(msg.sender, msg.message);
                        }
                    }
                });

                if (newMessagesAppended) {
                    chatBody.scrollTop = chatBody.scrollHeight;
                }
                isFirstLoad = false;
            }
        })
        .catch(err => console.error("Error fetching messages:", err));
    }

    function showToast(senderName, messageText) {
        const container = document.getElementById('toastContainer');
        if (!container) return;
        const toast = document.createElement('div');
        toast.className = "bg-slate-900 border border-slate-800 text-white px-4 py-3 rounded-xl shadow-2xl flex items-center gap-3 transition-all duration-300 transform translate-x-full pointer-events-auto max-w-sm";
        toast.innerHTML = `
            <span class="material-symbols-outlined text-primary-container">chat_bubble</span>
            <div class="flex-grow min-w-0">
                <p class="text-xs font-bold text-slate-200 truncate">${senderName}</p>
                <p class="text-xs text-slate-400 truncate m-0">${messageText}</p>
            </div>
            <button class="bg-transparent border-0 text-slate-400 hover:text-white cursor-pointer" onclick="this.parentElement.remove()">
                <span class="material-symbols-outlined text-sm">close</span>
            </button>
        `;
        container.appendChild(toast);
        
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 10);

        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }


    // Helper to create a dummy stream (silent audio and black video) if permissions/devices are missing
    function createDummyStream() {
        let audioTrack = null;
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const dest = ctx.createMediaStreamDestination();
            audioTrack = dest.stream.getAudioTracks()[0];
        } catch (e) {
            console.error("Web Audio API not supported or failed to initialize", e);
        }
        
        let videoTrack = null;
        try {
            const canvas = document.createElement('canvas');
            canvas.width = 640;
            canvas.height = 480;
            const context = canvas.getContext('2d');
            context.fillStyle = '#0f172a';
            context.fillRect(0, 0, canvas.width, canvas.height);
            
            context.fillStyle = '#94a3b8';
            context.font = '20px Inter, sans-serif';
            context.textAlign = 'center';
            context.textBaseline = 'middle';
            context.fillText('No Camera Connected', canvas.width / 2, canvas.height / 2);
            
            const videoStream = canvas.captureStream(10);
            videoTrack = videoStream.getVideoTracks()[0];
        } catch (e) {
            console.error("Failed to create dummy video track", e);
        }
        
        const tracks = [];
        if (videoTrack) tracks.push(videoTrack);
        if (audioTrack) tracks.push(audioTrack);
        
        return new MediaStream(tracks);
    }

    // Meeting connection start
    async function startMeeting() {
        document.getElementById('joinPanel').style.display = 'none';

        try {
            // Attempt to get both camera and audio
            localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
        } catch (err) {
            console.warn("Could not get both video and audio, trying audio only...", err);
            try {
                localStream = await navigator.mediaDevices.getUserMedia({ video: false, audio: true });
                isVideoMuted = true;
                const btnVideo = document.getElementById('btnVideo');
                if (btnVideo) {
                    btnVideo.classList.add('bg-red-500/20', 'border-red-500', 'text-red-500');
                    btnVideo.querySelector('.material-symbols-outlined').textContent = 'videocam_off';
                }
            } catch (err2) {
                console.warn("Could not get audio, trying video only...", err2);
                try {
                    localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                    isAudioMuted = true;
                    const btnAudio = document.getElementById('btnAudio');
                    if (btnAudio) {
                        btnAudio.classList.add('bg-red-500/20', 'border-red-500', 'text-red-500');
                        btnAudio.querySelector('.material-symbols-outlined').textContent = 'mic_off';
                    }
                } catch (err3) {
                    console.warn("No camera/microphone available or permission denied. Joining with dummy stream...", err3);
                    localStream = createDummyStream();
                    isAudioMuted = true;
                    isVideoMuted = true;
                    
                    const btnAudio = document.getElementById('btnAudio');
                    if (btnAudio) {
                        btnAudio.classList.add('bg-red-500/20', 'border-red-500', 'text-red-500');
                        btnAudio.querySelector('.material-symbols-outlined').textContent = 'mic_off';
                    }
                    const btnVideo = document.getElementById('btnVideo');
                    if (btnVideo) {
                        btnVideo.classList.add('bg-red-500/20', 'border-red-500', 'text-red-500');
                        btnVideo.querySelector('.material-symbols-outlined').textContent = 'videocam_off';
                    }
                }
            }
        }

        if (localStream) {
            localVideo.srcObject = localStream;
        }
        initPeer();
    }

    function initPeer() {
        peer = new Peer(myPeerId);

        peer.on('open', (id) => {
            statusBadge.textContent = "Connected";
            statusBadge.className = "badge bg-success px-3 py-1";
            
            // Start heartbeat polling
            handleHeartbeat();
            setInterval(handleHeartbeat, 3000);

            // Start chat polling
            fetchChatMessages();
            setInterval(fetchChatMessages, 3000);
        });

        peer.on('error', (err) => {
            console.error("PeerJS Error:", err);
        });

        peer.on('call', (call) => {
            console.log("Incoming call from:", call.peer);
            call.answer(localStream);
            activeCalls[call.peer] = call;
            setupCallEvents(call, call.peer, "Participant");
        });
    }

    function handleHeartbeat() {
        if (!peer || peer.destroyed || !localStream) return;

        fetch("{{ route('meetings.join.heartbeat', $id) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ peer_id: peer.id })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.peers) {
                const activePeerIds = data.peers.map(p => p.peer_id);
                
                // 1. Clean up calls/thumbnails for peers who left
                for (const peerId in activeCalls) {
                    if (!activePeerIds.includes(peerId)) {
                        console.log("Peer left, closing call:", peerId);
                        activeCalls[peerId].close();
                        delete activeCalls[peerId];
                        removeParticipantElement(peerId);
                    }
                }

                // 2. Connect to new peers and update display names
                data.peers.forEach(p => {
                    const peerId = p.peer_id;
                    
                    // Update label if the element exists
                    const label = document.getElementById('label-' + peerId);
                    if (label && p.user_name) {
                        label.textContent = p.user_name;
                    }

                    if (peerId !== peer.id && !activeCalls[peerId]) {
                        // Lexicographical check: only call if my peer.id is greater
                        // This prevents duplicate calling
                        if (peer.id > peerId) {
                            console.log("Initiating call to:", peerId);
                            const call = peer.call(peerId, localStream);
                            activeCalls[peerId] = call;
                            setupCallEvents(call, peerId, p.user_name);
                        } else {
                            console.log("Waiting for incoming call from:", peerId);
                        }
                    }
                });
            }
        })
        .catch(err => console.error("Heartbeat error:", err));
    }

    function setupCallEvents(call, peerId, displayName) {
        call.on('stream', (remoteStream) => {
            console.log("Received stream from:", peerId);
            addOrUpdateParticipant(peerId, displayName, remoteStream);
        });

        call.on('close', () => {
            console.log("Call closed:", peerId);
            removeParticipantElement(peerId);
            if (activeCalls[peerId]) {
                delete activeCalls[peerId];
            }
        });

        call.on('error', (err) => {
            console.error("Call error for peer:", peerId, err);
            removeParticipantElement(peerId);
            if (activeCalls[peerId]) {
                delete activeCalls[peerId];
            }
        });
    }

    function addOrUpdateParticipant(peerId, displayName, stream) {
        let container = document.getElementById('participant-' + peerId);
        let video = null;
        if (!container) {
            container = document.createElement('div');
            container.id = 'participant-' + peerId;
            container.className = "flex-shrink-0 w-44 aspect-video rounded-xl bg-slate-900 border border-white/10 overflow-hidden relative shadow-md cursor-pointer";
            
            container.onclick = () => {
                setMainSpeaker(stream, displayName);
            };

            video = document.createElement('video');
            video.autoplay = true;
            video.playsInline = true;
            video.className = "w-full h-full object-cover";
            
            const label = document.createElement('div');
            label.className = "absolute bottom-2 left-2 text-[10px] text-white bg-black/50 px-1.5 py-0.5 rounded";
            label.id = 'label-' + peerId;
            label.textContent = displayName;

            container.appendChild(video);
            container.appendChild(label);
            document.getElementById('participantGrid').appendChild(container);
        } else {
            video = container.querySelector('video');
            const label = document.getElementById('label-' + peerId);
            if (label && displayName !== "Participant") {
                label.textContent = displayName;
            }
        }

        if (video && video.srcObject !== stream) {
            video.srcObject = stream;
        }

        // Set as main speaker if no main speaker stream is set
        const remoteVideo = document.getElementById('remoteVideo');
        if (!remoteVideo.srcObject) {
            setMainSpeaker(stream, displayName);
        }
    }

    function setMainSpeaker(stream, displayName) {
        const remoteVideo = document.getElementById('remoteVideo');
        const waitingMsg = document.getElementById('waitingMsg');
        const remoteLabel = document.getElementById('remoteLabel');

        remoteVideo.srcObject = stream;
        waitingMsg.style.display = 'none';
        
        const labelText = remoteLabel.querySelector('span.text-white');
        if (labelText) {
            labelText.textContent = displayName;
        }
        remoteLabel.style.display = 'flex';
    }

    function removeParticipantElement(peerId) {
        const container = document.getElementById('participant-' + peerId);
        if (container) {
            container.remove();
        }

        // If the main speaker was this participant, clear it or switch to another active stream
        const remoteVideo = document.getElementById('remoteVideo');
        const activePeerIds = Object.keys(activeCalls);
        if (activePeerIds.length === 0) {
            remoteVideo.srcObject = null;
            document.getElementById('waitingMsg').style.display = 'block';
            document.getElementById('remoteLabel').style.display = 'none';
        } else {
            const nextPeerId = activePeerIds[0];
            const nextContainer = document.getElementById('participant-' + nextPeerId);
            if (nextContainer) {
                const nextVideo = nextContainer.querySelector('video');
                const nextLabel = document.getElementById('label-' + nextPeerId);
                if (nextVideo && nextVideo.srcObject) {
                    setMainSpeaker(nextVideo.srcObject, nextLabel ? nextLabel.textContent : "Participant");
                }
            }
        }
    }

    function toggleAudio() {
        if(!localStream) return;
        isAudioMuted = !isAudioMuted;
        const tracks = localStream.getAudioTracks();
        if (tracks.length > 0) {
            tracks[0].enabled = !isAudioMuted;
        }
        
        const btn = document.getElementById('btnAudio');
        const icon = btn.querySelector('.material-symbols-outlined');
        if(isAudioMuted) {
            btn.classList.add('bg-red-500/20', 'border-red-500', 'text-red-500');
            icon.textContent = 'mic_off';
        } else {
            btn.classList.remove('bg-red-500/20', 'border-red-500', 'text-red-500');
            icon.textContent = 'mic';
        }
    }

    function toggleVideo() {
        if(!localStream) return;
        isVideoMuted = !isVideoMuted;
        const tracks = localStream.getVideoTracks();
        if (tracks.length > 0) {
            tracks[0].enabled = !isVideoMuted;
        }
        
        const btn = document.getElementById('btnVideo');
        const icon = btn.querySelector('.material-symbols-outlined');
        if(isVideoMuted) {
            btn.classList.add('bg-red-500/20', 'border-red-500', 'text-red-500');
            icon.textContent = 'videocam_off';
        } else {
            btn.classList.remove('bg-red-500/20', 'border-red-500', 'text-red-500');
            icon.textContent = 'videocam';
        }
    }

    function copyInviteLink() {
        navigator.clipboard.writeText(window.location.href);
        alert("Invite link copied to clipboard!");
    }

    async function toggleScreenShare() {
        const btn = document.getElementById('btnScreen');
        if (!isScreenSharing) {
            try {
                screenStream = await navigator.mediaDevices.getDisplayMedia({ video: true, audio: false });
                const screenTrack = screenStream.getVideoTracks()[0];
                
                localVideo.srcObject = screenStream;

                for (const peerId in activeCalls) {
                    const sender = activeCalls[peerId].peerConnection.getSenders().find(s => s.track.kind === 'video');
                    if (sender) sender.replaceTrack(screenTrack);
                }

                screenTrack.onended = () => {
                    stopScreenShare();
                };
                
                isScreenSharing = true;
                btn.classList.add('bg-primary-container/20', 'border-primary-container', 'text-primary-container');
            } catch (err) {
                console.error("Error sharing screen:", err);
            }
        } else {
            stopScreenShare();
        }
    }

    function stopScreenShare() {
        if (!isScreenSharing) return;
        const btn = document.getElementById('btnScreen');
        
        if (screenStream) {
            screenStream.getTracks().forEach(track => track.stop());
        }

        localVideo.srcObject = localStream;

        const videoTrack = localStream.getVideoTracks()[0];
        if (videoTrack) {
            for (const peerId in activeCalls) {
                const sender = activeCalls[peerId].peerConnection.getSenders().find(s => s.track.kind === 'video');
                if (sender) sender.replaceTrack(videoTrack);
            }
        }
        
        isScreenSharing = false;
        btn.classList.remove('bg-primary-container/20', 'border-primary-container', 'text-primary-container');
    }

    function endCall() {
        if (peer && peer.id) {
            const leaveUrl = "{{ route('meetings.join.leave', $id) }}";
            const data = JSON.stringify({ peer_id: peer.id, _token: "{{ csrf_token() }}" });
            if (navigator.sendBeacon) {
                navigator.sendBeacon(leaveUrl, new Blob([data], {type: 'application/json'}));
            } else {
                fetch(leaveUrl, { method: 'POST', body: data, headers: { 'Content-Type': 'application/json' }, keepalive: true });
            }
        }
        for (const peerId in activeCalls) {
            if (activeCalls[peerId]) {
                activeCalls[peerId].close();
            }
        }
        if(peer) peer.destroy();
        if(localStream) {
            localStream.getTracks().forEach(track => track.stop());
        }
        window.location.href = "{{ $dashRoute }}";
    }

    window.addEventListener('beforeunload', () => {
        if (peer && peer.id) {
            const leaveUrl = "{{ route('meetings.join.leave', $id) }}";
            const data = JSON.stringify({ peer_id: peer.id, _token: "{{ csrf_token() }}" });
            if (navigator.sendBeacon) {
                navigator.sendBeacon(leaveUrl, new Blob([data], {type: 'application/json'}));
            }
        }
    });
</script>

</body>
</html>
