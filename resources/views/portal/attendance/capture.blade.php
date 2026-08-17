@extends('layouts.app')

@section('title', $actionType === 'check_out' ? 'Punch Out - AI Camera' : 'Punch In - AI Camera')
@section('page-title', 'Face Recognition Attendance')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-7">
        @if($alreadyMarked)
            <div class="card shadow-sm border-0 rounded-4 p-5 text-center bg-white">
                <div class="mb-3">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10" style="width: 80px; height: 80px;">
                        <i class="fas fa-check-double fa-2x text-success"></i>
                    </div>
                </div>
                <h4 class="fw-black text-dark mb-1">Today's Attendance Completed!</h4>
                <p class="text-muted small mb-4">Both Check-In and Check-Out punches have been safely recorded for today.</p>
                
                <div class="row g-3 justify-content-center mb-4">
                    <div class="col-6 col-sm-5">
                        <div class="p-3 bg-light rounded-3 border">
                            <span class="text-muted small text-uppercase d-block" style="font-size: 0.7rem; font-weight: 700;">Check-In Time</span>
                            <span class="fs-5 fw-bold text-success font-monospace">{{ $checkInTimeFormatted }}</span>
                        </div>
                    </div>
                    <div class="col-6 col-sm-5">
                        <div class="p-3 bg-light rounded-3 border">
                            <span class="text-muted small text-uppercase d-block" style="font-size: 0.7rem; font-weight: 700;">Check-Out Time</span>
                            <span class="fs-5 fw-bold text-danger font-monospace">{{ $checkOutTimeFormatted }}</span>
                        </div>
                    </div>
                </div>

                <div>
                    <a href="{{ $type === 'student' ? route('student.dashboard') : route('staff.dashboard') }}" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm">
                        <i class="fas fa-arrow-left me-1"></i> Return to Dashboard
                    </a>
                </div>
            </div>
        @else
            <!-- Active Scanner Card -->
            <div class="card shadow border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-dark text-white border-bottom-0 py-3 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge {{ $actionType === 'check_out' ? 'bg-danger' : 'bg-success' }} rounded-pill px-3 py-1 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                            <i class="fas {{ $actionType === 'check_out' ? 'fa-sign-out-alt' : 'fa-sign-in-alt' }} me-1"></i>
                            {{ $actionType === 'check_out' ? 'Check-Out (Punch Out)' : 'Check-In (Punch In)' }}
                        </span>
                        @if($actionType === 'check_out' && $checkInTimeFormatted)
                            <small class="text-white-50" style="font-size: 0.75rem;">(In: {{ $checkInTimeFormatted }})</small>
                        @endif
                    </div>
                    <div id="live-clock" class="fw-bold fs-5 font-monospace text-warning"></div>
                </div>
                
                <div class="card-body p-0 position-relative bg-black" style="min-height: 480px; display: flex; align-items: center; justify-content: center;">
                    
                    <!-- Hidden Reference Image -->
                    <img id="reference-image" src="{{ $referencePhoto }}" crossorigin="anonymous" style="display: none;">

                    <video id="webcam" autoplay playsinline style="width: 100%; height: 480px; object-fit: cover;" class="opacity-50"></video>
                    <canvas id="overlay-canvas" class="position-absolute top-0 start-0 w-100 h-100" style="pointer-events: none; z-index: 10;"></canvas>
                    <canvas id="capture-canvas" class="d-none"></canvas>
                    
                    <!-- Loading overlay -->
                    <div id="loading-overlay" class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-75 d-flex flex-column align-items-center justify-content-center text-white" style="z-index: 20;">
                        <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <h5 id="status-text" class="fw-bold">Initializing AI Face Models...</h5>
                        <p class="small text-muted" id="status-subtext">Please wait, loading neural networks.</p>
                    </div>
                </div>
                
                <div class="card-footer bg-white border-top-0 p-4 text-center">
                    <h5 class="fw-bold mb-1 text-dark">{{ $user->first_name ?? $user->user?->name ?? $user->employee_code }} {{ $user->last_name ?? '' }}</h5>
                    <p class="text-muted mb-0 small" id="instruction-text">
                        {{ $actionType === 'check_out' ? 'Please look at the camera to verify face and Punch Out.' : 'Please look directly at the camera. Make sure lighting is good.' }}
                    </p>
                </div>
            </div>
        @endif
    </div>
</div>

@if(!$alreadyMarked)
<!-- Face API library -->
<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<!-- SweetAlert2 for nice popups -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
#webcam {
    transition: opacity 0.5s ease;
}
.fw-black {
    font-weight: 900;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', async function() {
    // Live Clock
    function updateClock() {
        const now = new Date();
        document.getElementById('live-clock').innerText = now.toLocaleTimeString('en-US', { hour12: true });
    }
    setInterval(updateClock, 1000);
    updateClock();

    const video = document.getElementById('webcam');
    const overlayCanvas = document.getElementById('overlay-canvas');
    const captureCanvas = document.getElementById('capture-canvas');
    const loadingOverlay = document.getElementById('loading-overlay');
    const statusText = document.getElementById('status-text');
    const statusSubtext = document.getElementById('status-subtext');
    const referenceImage = document.getElementById('reference-image');
    const instructionText = document.getElementById('instruction-text');
    
    const userId = {{ $user->id }};
    const userType = '{{ $type }}';
    const actionType = '{{ $actionType }}';
    
    let stream = null;
    let referenceDescriptor = null;
    let isProcessing = false;

    // 1. Load Models
    async function loadModels() {
        try {
            statusSubtext.innerText = "Loading SSD MobileNet V1...";
            const MODEL_URL = 'https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights';
            
            await faceapi.nets.ssdMobilenetv1.loadFromUri(MODEL_URL);
            
            statusSubtext.innerText = "Loading Face Landmark Model...";
            await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
            
            statusSubtext.innerText = "Loading Face Recognition Model...";
            await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
            
            statusText.innerText = 'Analyzing Profile Photo...';
            statusSubtext.innerText = 'Computing facial descriptor from your profile photo.';
            
            // 2. Compute reference descriptor
            const refDetection = await faceapi.detectSingleFace(referenceImage).withFaceLandmarks().withFaceDescriptor();
            
            if (!refDetection) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Clear Face Not Detected',
                    text: 'We could not detect a clear reference face from your profile photo. You can still use the 1-click punch on your dashboard.',
                    confirmButtonText: 'Return to Dashboard',
                    allowOutsideClick: false
                }).then(() => {
                    window.location.href = userType === 'student' ? '{{ route("student.dashboard") }}' : '{{ route("staff.dashboard") }}';
                });
                return;
            }
            
            referenceDescriptor = refDetection.descriptor;
            
            // 3. Start Webcam
            statusText.innerText = 'Accessing Camera...';
            statusSubtext.innerText = 'Please allow camera access in your browser.';
            startCamera();
            
        } catch (err) {
            console.error(err);
            Swal.fire('Error', 'Failed to load AI models. Please check your internet connection or use 1-click punch on your dashboard.', 'error');
        }
    }

    async function startCamera() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({ 
                video: { facingMode: 'user', width: { ideal: 1280 }, height: { ideal: 720 } } 
            });
            video.srcObject = stream;
        } catch (err) {
            console.error('Error accessing camera:', err);
            Swal.fire('Camera Error', 'Camera access denied or not available. Please allow permissions in browser.', 'error');
        }
    }

    // When video starts playing, hide loading and start face detection loop
    video.addEventListener('play', () => {
        loadingOverlay.classList.add('d-none');
        video.classList.remove('opacity-50');
        instructionText.innerText = actionType === 'check_out' ? "Scanning face for Punch Out... Hold still." : "Scanning face for Punch In... Hold still.";
        
        const displaySize = { width: video.clientWidth, height: video.clientHeight };
        faceapi.matchDimensions(overlayCanvas, displaySize);

        // Run detection loop
        const detectionInterval = setInterval(async () => {
            if (isProcessing) return;
            isProcessing = true;

            const detections = await faceapi.detectAllFaces(video).withFaceLandmarks().withFaceDescriptors();
            const resizedDetections = faceapi.resizeResults(detections, displaySize);
            
            overlayCanvas.getContext('2d').clearRect(0, 0, overlayCanvas.width, overlayCanvas.height);
            faceapi.draw.drawDetections(overlayCanvas, resizedDetections);

            if (detections.length > 1) {
                instructionText.innerHTML = "<span class='text-danger fw-bold'><i class='fas fa-exclamation-triangle'></i> Multiple faces detected! Only one person allowed in frame.</span>";
                overlayCanvas.getContext('2d').clearRect(0, 0, overlayCanvas.width, overlayCanvas.height);
                isProcessing = false;
                return;
            }

            if (detections.length === 0) {
                instructionText.innerText = "No face detected. Please look directly at the camera.";
                isProcessing = false;
                return;
            }

            // Exactly 1 face detected. Compare descriptor.
            instructionText.innerText = "Face detected. Verifying identity...";
            
            const distance = faceapi.euclideanDistance(detections[0].descriptor, referenceDescriptor);
            
            // 0.6 is the standard threshold. Lower is stricter.
            if (distance < 0.6) {
                // MATCH!
                clearInterval(detectionInterval);
                const actionLabel = actionType === 'check_out' ? 'Punch Out Verified!' : 'Punch In Verified!';
                instructionText.innerHTML = "<span class='text-success fw-bold'><i class='fas fa-check-circle'></i> " + actionLabel + " Capturing...</span>";
                
                const box = resizedDetections[0].detection.box;
                const drawBox = new faceapi.draw.DrawBox(box, { label: 'Verified Match!', boxColor: 'green' });
                drawBox.draw(overlayCanvas);
                
                setTimeout(() => {
                    executeCapture();
                }, 800);
                
            } else {
                // MISMATCH
                instructionText.innerHTML = `<span class='text-danger fw-bold'><i class='fas fa-times-circle'></i> Face mismatch! (${distance.toFixed(2)})</span>`;
                const box = resizedDetections[0].detection.box;
                const drawBox = new faceapi.draw.DrawBox(box, { label: 'Unknown Person', boxColor: 'red' });
                drawBox.draw(overlayCanvas);
            }

            isProcessing = false;
        }, 300);
    });

    function executeCapture() {
        loadingOverlay.classList.remove('d-none');
        statusText.innerText = actionType === 'check_out' ? 'Saving Punch Out...' : 'Saving Punch In...';
        statusSubtext.innerText = 'Securely uploading verified punch...';
        document.querySelector('.spinner-border').style.display = 'inline-block';
        
        captureCanvas.width = video.videoWidth;
        captureCanvas.height = video.videoHeight;
        const ctx = captureCanvas.getContext('2d');
        ctx.drawImage(video, 0, 0, captureCanvas.width, captureCanvas.height);
        
        // Add Live Timestamp Watermark
        const now = new Date();
        const timestamp = now.toLocaleString('en-US', { dateStyle: 'medium', timeStyle: 'medium' });
        const punchTypeLabel = actionType === 'check_out' ? 'PUNCH OUT' : 'PUNCH IN';
        
        ctx.fillStyle = 'rgba(0, 0, 0, 0.65)';
        ctx.fillRect(10, captureCanvas.height - 40, captureCanvas.width - 20, 30);
        
        ctx.font = 'bold 18px monospace';
        ctx.fillStyle = actionType === 'check_out' ? '#ff4d4d' : '#00ff00';
        ctx.fillText(punchTypeLabel + " CAPTURE: " + timestamp, 20, captureCanvas.height - 18);

        const imageData = captureCanvas.toDataURL('image/jpeg', 0.55);
        
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
        
        fetch('{{ route("attendance.face.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ image: imageData, type: userType, id: userId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: data.action === 'check_out' ? 'Punch Out Successful!' : 'Punch In Successful!',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 2000
                }).then(() => {
                    window.location.href = userType === 'student' ? '{{ route("student.dashboard") }}' : '{{ route("staff.dashboard") }}';
                });
            } else {
                throw new Error(data.message || 'Unknown error');
            }
        })
        .catch(err => {
            Swal.fire({
                icon: 'error',
                title: 'Punch Recording Failed',
                text: err.message,
                confirmButtonText: 'Try Again'
            }).then(() => {
                location.reload();
            });
        });
    }

    // Start loading AI models
    loadModels();
});
</script>
@endif
@endsection

