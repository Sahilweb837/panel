@extends('layouts.app')

@section('title', 'Mark Attendance')
@section('page-title', 'Face Attendance')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
            <div class="card-header bg-primary text-white border-bottom-0 py-3">
                <h5 class="mb-0 text-center fw-bold"><i class="fas fa-camera me-2"></i> Face Attendance Capture</h5>
            </div>
            
            <div class="card-body p-0 position-relative bg-dark" style="min-height: 400px; display: flex; align-items: center; justify-content: center;">
                <video id="webcam" autoplay playsinline style="width: 100%; height: auto; max-height: 500px; object-fit: cover;"></video>
                <canvas id="canvas" class="d-none"></canvas>
                
                <!-- Loading overlay -->
                <div id="loading-overlay" class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-75 d-flex flex-column align-items-center justify-content-center text-white" style="z-index: 10;">
                    <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h5 id="status-text">Accessing camera...</h5>
                </div>
                
                <!-- Scanning animation overlay -->
                <div id="scanning-overlay" class="position-absolute top-0 start-0 w-100 h-100 d-none" style="z-index: 5; pointer-events: none;">
                    <div class="w-100 h-100 position-relative">
                        <div class="border border-success border-2 rounded-3 position-absolute" style="top: 15%; left: 15%; right: 15%; bottom: 15%; box-shadow: 0 0 20px rgba(25, 135, 84, 0.5);"></div>
                        <div class="position-absolute w-100 border-top border-success border-2" style="height: 2px; box-shadow: 0 0 10px rgba(25, 135, 84, 0.8); animation: scan 2s linear infinite;"></div>
                    </div>
                </div>
            </div>
            
            <div class="card-footer bg-white border-top-0 p-4 text-center">
                <h5 class="fw-bold mb-1">{{ $user->first_name ?? $user->employee_code }} {{ $user->last_name ?? '' }}</h5>
                <p class="text-muted mb-3 small">Please look directly at the camera. The system will auto-capture when ready.</p>
                
                <button id="manual-capture-btn" class="btn btn-outline-primary rounded-pill px-4" style="display: none;">
                    <i class="fas fa-camera me-1"></i> Capture Manually
                </button>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes scan {
    0% { top: 15%; opacity: 0; }
    10% { opacity: 1; }
    90% { opacity: 1; }
    100% { top: 85%; opacity: 0; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const video = document.getElementById('webcam');
    const canvas = document.getElementById('canvas');
    const loadingOverlay = document.getElementById('loading-overlay');
    const scanningOverlay = document.getElementById('scanning-overlay');
    const statusText = document.getElementById('status-text');
    const manualCaptureBtn = document.getElementById('manual-capture-btn');
    
    const userId = {{ $user->id }};
    const userType = '{{ $type }}';
    
    let stream = null;
    let autoCaptureTimeout = null;
    
    // Start webcam
    async function startCamera() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({ 
                video: { facingMode: 'user', width: { ideal: 1280 }, height: { ideal: 720 } } 
            });
            
            video.srcObject = stream;
            
            video.onloadedmetadata = () => {
                loadingOverlay.classList.add('d-none');
                scanningOverlay.classList.remove('d-none');
                manualCaptureBtn.style.display = 'inline-block';
                
                // Simulate face detection processing delay, then auto-capture
                autoCaptureTimeout = setTimeout(() => {
                    captureImage();
                }, 3000); // Wait 3 seconds to let user position themselves
            };
        } catch (err) {
            console.error('Error accessing camera:', err);
            statusText.innerHTML = '<i class="fas fa-exclamation-triangle text-danger mb-2 fa-2x"></i><br>Camera access denied.<br><small class="text-muted">Please allow camera permissions in your browser.</small>';
            loadingOverlay.classList.remove('bg-opacity-75');
            loadingOverlay.classList.add('bg-dark');
            document.querySelector('.spinner-border').style.display = 'none';
        }
    }
    
    // Capture and upload
    function captureImage() {
        if (!stream) return;
        
        clearTimeout(autoCaptureTimeout);
        
        // Show capturing state
        scanningOverlay.classList.add('d-none');
        loadingOverlay.classList.remove('d-none');
        statusText.innerText = 'Processing attendance...';
        document.querySelector('.spinner-border').style.display = 'inline-block';
        manualCaptureBtn.disabled = true;
        
        // Draw frame to canvas
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
        
        // Get base64 image
        const imageData = canvas.toDataURL('image/jpeg', 0.8);
        
        // Stop camera
        stream.getTracks().forEach(track => track.stop());
        
        // Upload to server
        fetch('{{ route("attendance.face.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                image: imageData,
                type: userType,
                id: userId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                statusText.innerHTML = '<i class="fas fa-check-circle text-success mb-2 fa-3x"></i><br>Attendance Marked!';
                document.querySelector('.spinner-border').style.display = 'none';
                
                // Redirect back to dashboard after 2 seconds
                setTimeout(() => {
                    window.location.href = userType === 'student' ? '{{ route("student.dashboard") }}' : '{{ route("staff.dashboard") }}';
                }, 2000);
            } else {
                throw new Error(data.message || 'Unknown error');
            }
        })
        .catch(err => {
            console.error('Upload error:', err);
            statusText.innerHTML = '<i class="fas fa-times-circle text-danger mb-2 fa-3x"></i><br>Failed to save attendance.<br><button onclick="location.reload()" class="btn btn-sm btn-light mt-3">Try Again</button>';
            document.querySelector('.spinner-border').style.display = 'none';
        });
    }
    
    manualCaptureBtn.addEventListener('click', captureImage);
    
    // Start process
    startCamera();
});
</script>
@endsection
