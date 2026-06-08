@extends('layouts.app')

@section('title', 'Live Attendance Feed')
@section('page-title', 'Live Attendance Feed')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-6 col-md-3">
        <div class="card bg-primary text-white border-0 shadow-sm rounded-4 h-100">
            <div class="card-body text-center p-3">
                <i class="fas fa-users fa-2x mb-2 opacity-75"></i>
                <h3 class="fw-bold mb-0" id="total-present">{{ $presentToday }}</h3>
                <span class="small">Present Today</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card bg-danger text-white border-0 shadow-sm rounded-4 h-100">
            <div class="card-body text-center p-3">
                <i class="fas fa-user-times fa-2x mb-2 opacity-75"></i>
                <h3 class="fw-bold mb-0" id="total-absent">{{ $absentToday }}</h3>
                <span class="small">Absent Today</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card bg-warning text-white border-0 shadow-sm rounded-4 h-100">
            <div class="card-body text-center p-3">
                <i class="fas fa-clock fa-2x mb-2 opacity-75"></i>
                <h3 class="fw-bold mb-0" id="total-late">{{ $lateToday }}</h3>
                <span class="small">Late Today</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card bg-success text-white border-0 shadow-sm rounded-4 h-100">
            <div class="card-body text-center p-3">
                <i class="fas fa-camera fa-2x mb-2 opacity-75"></i>
                <h3 class="fw-bold mb-0" id="face-captures">{{ $faceCaptures }}</h3>
                <span class="small">Face Captures</span>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold"><i class="fas fa-broadcast-tower text-danger me-2 blink-animation"></i> Real-time Feed</h5>
        <div>
            <button onclick="location.reload()" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                <i class="fas fa-sync-alt me-1"></i> Refresh
            </button>
        </div>
    </div>
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Time</th>
                        <th>Photo</th>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Device</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allAttendances as $attendance)
                        <tr>
                            <td class="ps-4 fw-medium text-muted">
                                <i class="far fa-clock me-1"></i> {{ $attendance->time }}
                            </td>
                            <td>
                                @if($attendance->photo)
                                    <img src="{{ Storage::url($attendance->photo) }}" width="45" height="45" class="rounded-circle shadow-sm" style="object-fit: cover; cursor: pointer;" onclick="showImageModal('{{ Storage::url($attendance->photo) }}', '{{ $attendance->name }}')">
                                @else
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-muted border" style="width: 45px; height: 45px;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="fw-bold">{{ $attendance->name }}</td>
                            <td>
                                <span class="badge bg-{{ $attendance->role === 'Student' ? 'info' : 'secondary' }} rounded-pill text-white">
                                    {{ $attendance->role }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $attendance->status === 'Present' ? 'success' : ($attendance->status === 'Absent' ? 'danger' : 'warning') }} rounded-pill px-3">
                                    {{ $attendance->status }}
                                </span>
                            </td>
                            <td class="text-muted small">
                                <i class="fas {{ str_contains(strtolower($attendance->device), 'web') ? 'fa-laptop' : 'fa-mobile-alt' }} me-1"></i> 
                                {{ $attendance->device ?? 'System' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                                <h5>No attendance records today</h5>
                                <p>Records will appear here automatically when students/staff check in.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Image View Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold" id="imageModalTitle">Captured Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4 pt-0">
                <img id="modalImage" src="" class="img-fluid rounded shadow" style="max-height: 400px; width: auto;">
            </div>
        </div>
    </div>
</div>

<style>
.blink-animation {
    animation: blinker 1.5s linear infinite;
}
@keyframes blinker {
    50% { opacity: 0.3; }
}
</style>

<script>
function showImageModal(src, name) {
    document.getElementById('modalImage').src = src;
    document.getElementById('imageModalTitle').innerText = name + "'s Attendance Photo";
    new bootstrap.Modal(document.getElementById('imageModal')).show();
}

// Auto refresh every 30 seconds
setTimeout(function() {
    location.reload();
}, 30000);
</script>
@endsection
