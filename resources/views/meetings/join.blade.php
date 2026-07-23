<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Meeting | {{ config('app.name') }}</title>
    <!-- Fonts and Bootstrap -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background-color: #111827;
            color: white;
            font-family: 'Outfit', sans-serif;
            height: 100vh;
            margin: 0;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        
        .header {
            padding: 15px 20px;
            background: rgba(17, 24, 39, 0.9);
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 10;
        }

        .video-container {
            flex-grow: 1;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            gap: 20px;
        }

        .video-wrapper {
            position: relative;
            background: #1f2937;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
            flex: 1;
            max-width: 800px;
            aspect-ratio: 16/9;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .local-video-wrapper {
            position: absolute;
            bottom: 20px;
            right: 20px;
            width: 200px;
            aspect-ratio: 16/9;
            background: #374151;
            border-radius: 12px;
            overflow: hidden;
            border: 2px solid rgba(255,255,255,0.2);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            z-index: 20;
        }

        .video-label {
            position: absolute;
            bottom: 10px;
            left: 10px;
            background: rgba(0,0,0,0.6);
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.85rem;
            backdrop-filter: blur(4px);
        }

        .controls {
            padding: 20px;
            background: rgba(17, 24, 39, 0.9);
            border-top: 1px solid rgba(255,255,255,0.1);
            display: flex;
            justify-content: center;
            gap: 15px;
            z-index: 10;
        }

        .control-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: none;
            background: rgba(255,255,255,0.1);
            color: white;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .control-btn:hover {
            background: rgba(255,255,255,0.2);
        }

        .control-btn.active {
            background: rgba(255,255,255,0.9);
            color: #111827;
        }

        .control-btn.danger {
            background: #ef4444;
        }
        
        .control-btn.danger:hover {
            background: #dc2626;
        }

        .join-panel {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: #111827;
            z-index: 100;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .status-msg {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: rgba(255,255,255,0.5);
            font-size: 1.2rem;
        }
    </style>
</head>
<body>

    <!-- Join Panel -->
    <div class="join-panel" id="joinPanel">
        <h2 class="mb-4">Meeting Room: <span class="text-primary">{{ $id }}</span></h2>
        <p class="text-muted mb-4 text-center" style="max-width: 400px;">Allow camera and microphone access to join the meeting. This is a secure, peer-to-peer connection.</p>
        <button class="btn btn-primary btn-lg rounded-pill px-5 fw-bold" onclick="startMeeting()">
            <i class="fas fa-video me-2"></i> Join Meeting
        </button>
    </div>

    <!-- Header -->
    <div class="header">
        <div class="d-flex align-items-center gap-3">
            <h5 class="m-0"><i class="fas fa-shield-alt text-success me-2"></i> Secure Meeting</h5>
        </div>
        <div>
            <span class="badge bg-secondary" id="connectionStatus">Connecting...</span>
        </div>
    </div>

    <!-- Video Area -->
    <div class="video-container" id="videoContainer">
        <!-- Remote Video -->
        <div class="video-wrapper">
            <div class="status-msg" id="waitingMsg">Waiting for others to join...</div>
            <video id="remoteVideo" autoplay playsinline></video>
            <div class="video-label" id="remoteLabel" style="display:none;"><i class="fas fa-user me-1"></i> Remote Participant</div>
        </div>

        <!-- Local Video -->
        <div class="local-video-wrapper">
            <video id="localVideo" autoplay playsinline muted></video>
            <div class="video-label">You</div>
        </div>
    </div>

    <!-- Controls -->
    <div class="controls">
        <button class="control-btn" id="btnAudio" onclick="toggleAudio()" title="Mute/Unmute Audio">
            <i class="fas fa-microphone"></i>
        </button>
        <button class="control-btn" id="btnVideo" onclick="toggleVideo()" title="Turn Off/On Camera">
            <i class="fas fa-video"></i>
        </button>
        <button class="control-btn" onclick="copyInviteLink()" title="Copy Meeting Link">
            <i class="fas fa-link"></i>
        </button>
        <button class="control-btn" id="btnScreen" onclick="toggleScreenShare()" title="Share Screen">
            <i class="fas fa-desktop"></i>
        </button>
        <button class="control-btn danger ms-3" onclick="endCall()" title="End Call">
            <i class="fas fa-phone-slash"></i>
        </button>
    </div>

    <!-- PeerJS -->
    <script src="https://unpkg.com/peerjs@1.5.1/dist/peerjs.min.js"></script>
    <script>
        const ROOM_ID = "{{ $id }}";
        const PEER_ID_PREFIX = "feesmanager-peer-";
        
        // We will generate a specific ID based on user_id to ensure determinism for 1-on-1s if needed,
        // but since this is a general link, we just connect to a known room host.
        // Actually, to make a simple mesh network or 1-on-1, the first person to join is the "host" of the room.
        const myPeerId = PEER_ID_PREFIX + Math.random().toString(36).substr(2, 9);
        const hostId = PEER_ID_PREFIX + ROOM_ID;

        let peer = null;
        let localStream = null;
        let currentCall = null;

        const localVideo = document.getElementById('localVideo');
        const remoteVideo = document.getElementById('remoteVideo');
        const statusBadge = document.getElementById('connectionStatus');
        const waitingMsg = document.getElementById('waitingMsg');
        const remoteLabel = document.getElementById('remoteLabel');

        let isAudioMuted = false;
        let isVideoMuted = false;
        let isScreenSharing = false;
        let screenStream = null;

        async function startMeeting() {
            document.getElementById('joinPanel').style.display = 'none';

            try {
                localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
                localVideo.srcObject = localStream;
                initPeer();
            } catch (err) {
                alert("Failed to access camera and microphone. Please allow permissions.");
                console.error(err);
                document.getElementById('joinPanel').style.display = 'flex';
            }
        }

        function initPeer() {
            // First try to connect as the host (using the exact room ID)
            peer = new Peer(hostId);

            peer.on('open', (id) => {
                // I am the host!
                statusBadge.textContent = "Host - Waiting for peers";
                statusBadge.className = "badge bg-primary";
            });

            peer.on('error', (err) => {
                if (err.type === 'unavailable-id') {
                    // Host already exists, I am a participant
                    joinAsParticipant();
                } else {
                    console.error("PeerJS Error:", err);
                }
            });

            // Handle incoming calls (Host receives this)
            peer.on('call', (call) => {
                call.answer(localStream);
                setupCallEvents(call);
            });
        }

        function joinAsParticipant() {
            peer = new Peer(myPeerId);
            
            peer.on('open', (id) => {
                statusBadge.textContent = "Participant - Connecting...";
                statusBadge.className = "badge bg-warning text-dark";
                
                // Call the host
                const call = peer.call(hostId, localStream);
                setupCallEvents(call);
            });
        }

        function setupCallEvents(call) {
            currentCall = call;
            
            call.on('stream', (remoteStream) => {
                remoteVideo.srcObject = remoteStream;
                waitingMsg.style.display = 'none';
                remoteLabel.style.display = 'block';
                statusBadge.textContent = "Connected";
                statusBadge.className = "badge bg-success";
            });

            call.on('close', () => {
                remoteVideo.srcObject = null;
                waitingMsg.style.display = 'block';
                remoteLabel.style.display = 'none';
                statusBadge.textContent = "Peer Disconnected";
                statusBadge.className = "badge bg-danger";
            });
        }

        function toggleAudio() {
            if(!localStream) return;
            isAudioMuted = !isAudioMuted;
            localStream.getAudioTracks()[0].enabled = !isAudioMuted;
            
            const btn = document.getElementById('btnAudio');
            if(isAudioMuted) {
                btn.classList.add('active');
                btn.innerHTML = '<i class="fas fa-microphone-slash text-danger"></i>';
            } else {
                btn.classList.remove('active');
                btn.innerHTML = '<i class="fas fa-microphone"></i>';
            }
        }

        function toggleVideo() {
            if(!localStream) return;
            isVideoMuted = !isVideoMuted;
            localStream.getVideoTracks()[0].enabled = !isVideoMuted;
            
            const btn = document.getElementById('btnVideo');
            if(isVideoMuted) {
                btn.classList.add('active');
                btn.innerHTML = '<i class="fas fa-video-slash text-danger"></i>';
            } else {
                btn.classList.remove('active');
                btn.innerHTML = '<i class="fas fa-video"></i>';
            }
        }

        function copyInviteLink() {
            navigator.clipboard.writeText(window.location.href);
            alert("Meeting link copied to clipboard!");
        }

        async function toggleScreenShare() {
            if (!isScreenSharing) {
                try {
                    screenStream = await navigator.mediaDevices.getDisplayMedia({ video: true, audio: false });
                    const screenTrack = screenStream.getVideoTracks()[0];
                    
                    // Show screen on local video
                    localVideo.srcObject = screenStream;

                    // Send screen track to peers
                    if (currentCall) {
                        const sender = currentCall.peerConnection.getSenders().find(s => s.track.kind === 'video');
                        if (sender) sender.replaceTrack(screenTrack);
                    }

                    // Handle native browser 'stop sharing' button
                    screenTrack.onended = () => {
                        stopScreenShare();
                    };
                    
                    isScreenSharing = true;
                    document.getElementById('btnScreen').classList.add('active');
                } catch (err) {
                    console.error("Error sharing screen:", err);
                }
            } else {
                stopScreenShare();
            }
        }

        function stopScreenShare() {
            if (!isScreenSharing) return;
            
            if (screenStream) {
                screenStream.getTracks().forEach(track => track.stop());
            }

            // Restore camera to local video
            localVideo.srcObject = localStream;

            // Restore camera track to peers
            const videoTrack = localStream.getVideoTracks()[0];
            if (currentCall && videoTrack) {
                const sender = currentCall.peerConnection.getSenders().find(s => s.track.kind === 'video');
                if (sender) sender.replaceTrack(videoTrack);
            }
            
            isScreenSharing = false;
            document.getElementById('btnScreen').classList.remove('active');
        }

        function endCall() {
            if(currentCall) currentCall.close();
            if(peer) peer.destroy();
            if(localStream) {
                localStream.getTracks().forEach(track => track.stop());
            }
            window.close(); // Attempt to close window
            window.location.href = "/dashboard"; // Fallback redirect
        }
    </script>
</body>
</html>
