@extends('layouts.app')

@section('title', 'Live Attendance Stream')
@section('page-title', 'Live Attendance Stream')

@section('content')
<div class="container-fluid px-0">
    <!-- Stat Counter Cards -->
    <div class="row g-3 mb-4">
        <!-- Present Today -->
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: linear-gradient(135deg, #10b981, #059669); color: #fff;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="small text-white-50 text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Present Today</span>
                        <h3 class="fw-black mb-0 mt-1" id="total-present">{{ $presentToday }}</h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: rgba(255,255,255,0.2);">
                        <i class="fas fa-user-check fa-lg text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Late Today -->
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: #fff;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="small text-white-50 text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Late Today</span>
                        <h3 class="fw-black mb-0 mt-1" id="total-late">{{ $lateToday }}</h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: rgba(255,255,255,0.2);">
                        <i class="fas fa-clock fa-lg text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Absent Today -->
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: linear-gradient(135deg, #ef4444, #dc2626); color: #fff;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="small text-white-50 text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Absent Today</span>
                        <h3 class="fw-black mb-0 mt-1" id="total-absent">{{ $absentToday }}</h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: rgba(255,255,255,0.2);">
                        <i class="fas fa-user-times fa-lg text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Staff Active -->
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: linear-gradient(135deg, #6366f1, #4f46e5); color: #fff;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="small text-white-50 text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Staff Punches</span>
                        <h3 class="fw-black mb-0 mt-1" id="total-staff">{{ $staffCount ?? 0 }}</h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: rgba(255,255,255,0.2);">
                        <i class="fas fa-chalkboard-teacher fa-lg text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Students Active -->
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); color: #fff;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="small text-white-50 text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Student Punches</span>
                        <h3 class="fw-black mb-0 mt-1" id="total-students">{{ $studentCount ?? 0 }}</h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: rgba(255,255,255,0.2);">
                        <i class="fas fa-user-graduate fa-lg text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Face / Web Captures -->
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: linear-gradient(135deg, #ff5532, #ea580c); color: #fff;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="small text-white-50 text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Photo Captures</span>
                        <h3 class="fw-black mb-0 mt-1" id="face-captures">{{ $faceCaptures }}</h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: rgba(255,255,255,0.2);">
                        <i class="fas fa-camera fa-lg text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Live Feed Table Card -->
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-white border-bottom p-3.5 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="d-flex align-items-center gap-2">
                    <span class="position-relative d-flex" style="width: 12px; height: 12px;">
                        <span class="animate-ping position-absolute w-100 h-100 rounded-circle bg-danger opacity-75"></span>
                        <span class="position-relative rounded-circle bg-danger" style="width: 12px; height: 12px;"></span>
                    </span>
                    <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-broadcast-tower text-danger me-1.5"></i> Live Attendance Feed</h5>
                </div>
                <span class="badge bg-light text-muted border px-2.5 py-1" style="font-size: 0.75rem;">
                    <i class="fas fa-sync-alt fa-spin me-1 text-primary"></i> Streaming Every 3s
                </span>
                <span class="text-muted small" id="last-sync-time" style="font-size: 0.75rem;">Last synced: Just now</span>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <!-- Role Filter Tabs -->
                <div class="btn-group p-1 bg-light rounded-pill border" role="group" id="role-filter-group">
                    <button type="button" onclick="setRoleFilter('all')" class="btn btn-sm rounded-pill px-3 py-1 fw-bold btn-primary" id="filter-btn-all">
                        All Records (<span id="btn-count-all">{{ count($allAttendances) }}</span>)
                    </button>
                    <button type="button" onclick="setRoleFilter('staff')" class="btn btn-sm rounded-pill px-3 py-1 fw-bold text-dark btn-light" id="filter-btn-staff">
                        <i class="fas fa-chalkboard-teacher me-1 text-indigo"></i> Staff (<span id="btn-count-staff">{{ $staffCount ?? 0 }}</span>)
                    </button>
                    <button type="button" onclick="setRoleFilter('student')" class="btn btn-sm rounded-pill px-3 py-1 fw-bold text-dark btn-light" id="filter-btn-student">
                        <i class="fas fa-user-graduate me-1 text-info"></i> Students (<span id="btn-count-student">{{ $studentCount ?? 0 }}</span>)
                    </button>
                </div>

                <!-- Instant Search Input -->
                <div class="position-relative" style="width: 220px;">
                    <input type="text" id="liveSearchInput" placeholder="Search name or ID..." class="form-control form-control-sm rounded-pill ps-4" onkeyup="filterTableRows()">
                    <i class="fas fa-search text-muted position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); font-size: 0.75rem;"></i>
                </div>

                <!-- Manual Refresh Button -->
                <button onclick="fetchLiveAttendance(true)" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5" title="Force Refresh">
                    <i class="fas fa-sync-alt" id="refresh-icon"></i>
                </button>
            </div>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="liveAttendanceTable">
                    <thead class="table-light">
                        <tr style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">
                            <th class="ps-4">Photo</th>
                            <th>Person & Designation</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th class="text-center"><i class="fas fa-clock me-1 text-primary"></i>Check-In / Out Punch</th>
                            <th>Device / Method</th>
                            <th class="text-end pe-4">Activity Time</th>
                        </tr>
                    </thead>
                    <tbody id="live-table-body">
                        @include('attendances.partials.live_table')
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Image View Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white border-bottom-0 py-3">
                <h6 class="modal-title fw-bold" id="imageModalTitle"><i class="fas fa-camera me-2"></i> Attendance Capture</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4 bg-light">
                <img id="modalImage" src="" class="img-fluid rounded-3 shadow" style="max-height: 420px; width: auto; object-fit: contain;">
            </div>
            <div class="modal-footer bg-white border-top-0 py-2 justify-content-center">
                <button type="button" class="btn btn-sm btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
.animate-ping {
    animation: ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;
}
@keyframes ping {
    75%, 100% {
        transform: scale(2);
        opacity: 0;
    }
}
.fw-black {
    font-weight: 900;
}
</style>

<script>
let currentRoleFilter = '{{ $currentRole ?? "all" }}';

function showImageModal(src, name) {
    document.getElementById('modalImage').src = src;
    document.getElementById('imageModalTitle').innerHTML = '<i class="fas fa-camera me-2"></i> ' + name + "'s Punch Photo";
    new bootstrap.Modal(document.getElementById('imageModal')).show();
}

function setRoleFilter(role) {
    currentRoleFilter = role;
    
    // Update button styles
    ['all', 'staff', 'student'].forEach(r => {
        const btn = document.getElementById('filter-btn-' + r);
        if (btn) {
            if (r === role) {
                btn.className = 'btn btn-sm rounded-pill px-3 py-1 fw-bold btn-primary';
            } else {
                btn.className = 'btn btn-sm rounded-pill px-3 py-1 fw-bold text-dark btn-light';
            }
        }
    });

    fetchLiveAttendance(false);
}

function filterTableRows() {
    const q = document.getElementById('liveSearchInput').value.toLowerCase().trim();
    const rows = document.querySelectorAll('#live-table-body tr.attendance-row');
    
    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        if (text.includes(q)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function fetchLiveAttendance(isManual = false) {
    const refreshIcon = document.getElementById('refresh-icon');
    if (isManual && refreshIcon) {
        refreshIcon.classList.add('fa-spin');
    }

    const url = '{{ route("attendances.live") }}?role=' + currentRoleFilter;

    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('live-table-body').innerHTML = data.html;
        
        // Re-apply client search filter if active
        filterTableRows();

        // Update stats
        if (data.stats) {
            animateValue(document.getElementById('total-present'), data.stats.present);
            animateValue(document.getElementById('total-absent'), data.stats.absent);
            animateValue(document.getElementById('total-late'), data.stats.late);
            animateValue(document.getElementById('face-captures'), data.stats.face);
            
            if (document.getElementById('total-staff')) {
                animateValue(document.getElementById('total-staff'), data.stats.staff);
            }
            if (document.getElementById('total-students')) {
                animateValue(document.getElementById('total-students'), data.stats.students);
            }

            // Update badge counts in filter buttons
            const countAll = (data.stats.staff || 0) + (data.stats.students || 0);
            if (document.getElementById('btn-count-all')) document.getElementById('btn-count-all').innerText = countAll;
            if (document.getElementById('btn-count-staff')) document.getElementById('btn-count-staff').innerText = data.stats.staff || 0;
            if (document.getElementById('btn-count-student')) document.getElementById('btn-count-student').innerText = data.stats.students || 0;
        }

        const now = new Date();
        document.getElementById('last-sync-time').innerText = 'Last synced: ' + now.toLocaleTimeString();

        if (isManual && refreshIcon) {
            setTimeout(() => refreshIcon.classList.remove('fa-spin'), 600);
        }
    })
    .catch(error => {
        console.error('Error fetching live attendance:', error);
        if (isManual && refreshIcon) {
            refreshIcon.classList.remove('fa-spin');
        }
    });
}

function animateValue(obj, val) {
    if (!obj) return;
    const current = parseInt(obj.innerText) || 0;
    if(current !== val) {
        obj.innerText = val;
        obj.classList.add('text-warning');
        setTimeout(() => obj.classList.remove('text-warning'), 1000);
    }
}

// Auto refresh every 3 seconds via AJAX
setInterval(() => fetchLiveAttendance(false), 3000);
</script>
@endsection

