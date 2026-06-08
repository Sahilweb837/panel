@extends('layouts.app')

@section('title', 'Mark Attendance')
@section('page-title', 'Face Recognition Attendance')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-7">
        @if($alreadyMarked)
            <div class="alert alert-warning shadow-sm border-0 rounded-4 p-4 text-center">
                <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                <h4 class="fw-bold">Attendance Already Marked!</h4>
                <p class="mb-0">You have already successfully marked your attendance for today.</p>
                <a href="{{ $type === 'student' ? route('student.dashboard') : route('staff.dashboard') }}" class="btn btn-primary mt-3 rounded-pill px-4">Return to Dashboard</a>
            </div>
        @else
            <div class="card shadow border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-dark text-white border-bottom-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-camera me-2"></i> Face Scanning System</h5>
                    <div id="live-clock" class="fw-bold fs-5 font-monospace text-info"></div>
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
                        <h5 id="status-text" class="fw-bold">Initializing AI Models...</h5>
                        <p class="small text-muted" id="status-subtext">Please wait, loading neural networks.</p>
                    </div>
                </div>
                
                <div class="card-footer bg-white border-top-0 p-4 text-center">
                    <h5 class="fw-bold mb-1">{{ $user->first_name ?? $user->employee_code }} {{ $user->last_name ?? '' }}</h5>
                    <p class="text-muted mb-0 small" id="instruction-text">Please look directly at the camera. Make sure lighting is good.</p>
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
    
    let stream = null;
    let referenceDescriptor = null;
    let isProcessing = false;

    // 1. Load Models
    async function loadModels() {
        try {
            statusSubtext.innerText = "Loading SSD MobileNet V1...";
            // Use the public raw github url to fetch the models reliably
            const MODEL_URL = 'https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights';
            
            await faceapi.nets.ssdMobilenetv1.loadFromUri(MODEL_URL);
            
            statusSubtext.innerText = "Loading Face Landmark Model...";
            await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
            
            statusSubtext.innerText = "Loading Face Recognition Model...";
            await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
            
            statusText.innerText = 'Analyzing Profile Photo...';
            statusSubtext.innerText = 'Computing facial descriptor from your registered profile picture.';
            
            // 2. Compute reference descriptor
            const refDetection = await faceapi.detectSingleFace(referenceImage).withFaceLandmarks().withFaceDescriptor();
            
            if (!refDetection) {
                Swal.fire({
                    icon: 'error',
                    title: 'Profile Photo Error',
                    text: 'We could not detect a clear face in your saved profile photo. Please contact Admin to update your photo.',
                    confirmButtonText: 'Go Back',
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
            Swal.fire('Error', 'Failed to load AI models. Check your internet connection.', 'error');
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
            Swal.fire('Camera Error', 'Camera access denied or not available. Please allow permissions.', 'error');
        }
    }

    // When video starts playing, hide loading and start face detection loop
    video.addEventListener('play', () => {
        loadingOverlay.classList.add('d-none');
        video.classList.remove('opacity-50');
        instructionText.innerText = "Scanning face... Hold still.";
        
        const displaySize = { width: video.clientWidth, height: video.clientHeight };
        faceapi.matchDimensions(overlayCanvas, displaySize);

        // Run detection every 100ms
        const detectionInterval = setInterval(async () => {
            if (isProcessing) return; // Prevent overlapping checks
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
                instructionText.innerText = "No face detected. Please look at the camera.";
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
                instructionText.innerHTML = "<span class='text-success fw-bold'><i class='fas fa-check-circle'></i> Identity Verified! Capturing...</span>";
                
                // Pause video slightly to show success box
                const box = resizedDetections[0].detection.box;
                const drawBox = new faceapi.draw.DrawBox(box, { label: 'Verified Match!', boxColor: 'green' });
                drawBox.draw(overlayCanvas);
                
                setTimeout(() => {
                    executeCapture();
                }, 800);
                
            } else {
                // MISMATCH
                instructionText.innerHTML = `<span class='text-danger fw-bold'><i class='fas fa-times-circle'></i> Face mismatch! Identity could not be verified. (${distance.toFixed(2)})</span>`;
                const box = resizedDetections[0].detection.box;
                const drawBox = new faceapi.draw.DrawBox(box, { label: 'Unknown Person', boxColor: 'red' });
                drawBox.draw(overlayCanvas);
            }

            isProcessing = false;
        }, 300);
    });

    function executeCapture() {
        loadingOverlay.classList.remove('d-none');
        statusText.innerText = 'Saving Attendance...';
        statusSubtext.innerText = 'Uploading encrypted photo securely.';
        document.querySelector('.spinner-border').style.display = 'inline-block';
        
        captureCanvas.width = video.videoWidth;
        captureCanvas.height = video.videoHeight;
        captureCanvas.getContext('2d').drawImage(video, 0, 0, captureCanvas.width, captureCanvas.height);
        const imageData = captureCanvas.toDataURL('image/jpeg', 0.8);
        
        stream.getTracks().forEach(track => track.stop());
        
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
                    title: 'Attendance Marked!',
                    text: 'Your attendance has been recorded successfully.',
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
                title: 'Submission Failed',
                text: err.message,
                confirmButtonText: 'Try Again'
            }).then(() => {
                location.reload();
            });
        });
    }

    // Start everything
    loadModels();
});
</script>
@endif
@endsection
