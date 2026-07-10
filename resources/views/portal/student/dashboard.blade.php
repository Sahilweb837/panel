@extends('layouts.app')

@section('title', 'Student Portal Dashboard')
@section('page-title', 'Student Portal')

@section('content')
<style>
    :root {
        --bg-primary: #121212;
        --surface-card: #1E1E1E;
        --accent-primary: #00E5FF;
        --accent-alert: #FF453A;
        --accent-status: #32D74B;
        --text-primary: #FFFFFF;
        --text-secondary: #98989D;
        --border-sutil: #2C2C2E;
    }

    body {
        background-color: var(--bg-primary) !important;
        color: var(--text-primary) !important;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }

    .card {
        background-color: var(--surface-card) !important;
        border: 1px solid var(--border-sutil) !important;
        border-radius: 16px !important;
        color: var(--text-primary) !important;
    }

    .card-header, .card-footer {
        border-color: var(--border-sutil) !important;
        background-color: var(--surface-card) !important;
        color: var(--text-primary) !important;
    }

    .text-muted {
        color: var(--text-secondary) !important;
    }

    .text-dark {
        color: var(--text-primary) !important;
    }

    .fw-bold, .fw-black, .fw-semibold, .badge, td, .fs-5, h3, h4 {
        font-family: 'JetBrains Mono', 'Fira Code', monospace;
    }

    .bg-light {
        background-color: var(--surface-card) !important;
        border-color: var(--border-sutil) !important;
    }

    .nav-pills .nav-link {
        color: var(--text-secondary) !important;
        font-family: 'Inter', sans-serif !important;
    }

    .nav-pills .nav-link.active {
        background-color: var(--accent-primary) !important;
        color: #121212 !important;
    }

    .table {
        color: var(--text-primary) !important;
    }

    .table-light, .table-light-head, .table th, .table-responsive thead th {
        background-color: var(--surface-card) !important;
        color: var(--text-secondary) !important;
        border-bottom: 1px solid var(--border-sutil) !important;
    }

    .table tbody tr {
        border-bottom: 1px solid var(--border-sutil) !important;
    }

    .table tbody tr:hover {
        background-color: #252525 !important;
    }

    .alert-danger, .alert-warning, .border-danger {
        background-color: #3A1C1C !important;
        color: var(--accent-alert) !important;
        border: none !important;
        border-left: 4px solid var(--accent-alert) !important;
        border-radius: 0 8px 8px 0 !important;
    }

    .alert-success {
        background-color: rgba(50, 215, 75, 0.15) !important;
        color: var(--accent-status) !important;
        border: none !important;
        border-left: 4px solid var(--accent-status) !important;
        border-radius: 0 8px 8px 0 !important;
    }

    .btn-outline-primary {
        color: var(--accent-primary) !important;
        border-color: var(--accent-primary) !important;
    }

    .btn-outline-primary:hover {
        background-color: var(--accent-primary) !important;
        color: #121212 !important;
    }

    .btn-primary {
        background-color: var(--accent-primary) !important;
        color: #121212 !important;
        border: none !important;
    }

    .btn-primary:hover {
        background-color: #00b8cc !important;
    }

    .text-success {
        color: var(--accent-status) !important;
    }

    .text-danger {
        color: var(--accent-alert) !important;
    }

    .bg-primary {
        background-color: var(--accent-primary) !important;
        color: #121212 !important;
    }

    .badge.bg-success {
        background-color: rgba(50, 215, 75, 0.15) !important;
        color: var(--accent-status) !important;
    }

    .badge.bg-danger {
        background-color: rgba(255, 69, 58, 0.15) !important;
        color: var(--accent-alert) !important;
    }

    .badge.bg-warning {
        background-color: rgba(255, 159, 11, 0.15) !important;
        color: #f59e0b !important;
    }

    .badge.bg-info {
        background-color: rgba(0, 229, 255, 0.15) !important;
        color: var(--accent-primary) !important;
    }

    .badge.bg-secondary {
        background-color: rgba(255, 255, 255, 0.05) !important;
        color: var(--text-secondary) !important;
    }

    .login-info-card {
        background: linear-gradient(135deg, rgba(0, 229, 255, 0.1), rgba(0, 229, 255, 0.05));
        border: 1px dashed var(--accent-primary);
    }

    .milestone-card {
        transition: all 0.3s ease;
    }
    .milestone-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    .milestone-progress {
        height: 8px;
        border-radius: 4px;
        background: var(--border-sutil);
        overflow: hidden;
    }
    .milestone-progress-bar {
        height: 100%;
        border-radius: 4px;
        background: var(--accent-primary);
        transition: width 0.6s ease;
    }
</style>

<div class="container-fluid px-0">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm border-0 mb-4 p-3 d-flex align-items-center gap-2" role="alert">
            <i class="fas fa-check-circle fa-lg"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm border-0 mb-4 p-3 d-flex align-items-center gap-2" role="alert">
            <i class="fas fa-exclamation-circle fa-lg"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
                <div class="card-body text-center p-4">
                    <div class="mb-3 position-relative d-inline-block">
                        @if($student->photo)
                            <img src="{{ Storage::url($student->photo) }}" class="rounded-circle shadow border border-3 border-white" width="120" height="120" style="object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto shadow border border-3 border-white" style="width: 120px; height: 120px;">
                                <i class="fas fa-user-graduate text-muted fa-3x"></i>
                            </div>
                        @endif
                    </div>
                    <h4 class="fw-bold mb-1">{{ $student->first_name }} {{ $student->last_name }}</h4>
                    <p class="text-muted mb-3"><i class="fas fa-book-reader me-2"></i>{{ $student->course->name ?? 'No Course Assigned' }}</p>

                    <div class="d-flex justify-content-center gap-2 mb-4">
                        <span class="badge bg-primary px-3 py-2 rounded-pill"><i class="fas fa-id-card me-1"></i> {{ $student->admission_no }}</span>
                        <span class="badge bg-info px-3 py-2 rounded-pill"><i class="fas fa-layer-group me-1"></i> {{ $student->course_duration ?? 'N/A' }}</span>
                    </div>

                    <div class="card login-info-card rounded-3 p-3 mb-3 text-start">
                        <h6 class="fw-bold mb-3 text-first"><i class="fas fa-key me-2"></i>Login Credentials</h6>
                        <div class="d-flex flex-column gap-2 small">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted"><i class="fas fa-user me-1"></i>Username:</span>
                                <span class="fw-semibold">{{ $student->user->username ?? 'N/A' }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted"><i class="fas fa-envelope me-1"></i>Email:</span>
                                <span class="fw-semibold">{{ $student->user->email ?? 'N/A' }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted"><i class="fas fa-lock me-1"></i>Password:</span>
                                <span class="fw-semibold">
                                    @if($student->dob)
                                        {{ \Carbon\Carbon::parse($student->dob)->format('dmY') }}
                                    @else
                                        {{ $student->admission_no }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="border-top pt-3 text-start">
                        <h6 class="fw-bold mb-3"><i class="fas fa-info-circle text-primary me-2"></i>Personal Details</h6>
                        <div class="d-flex flex-column gap-2 small">
                            <div class="d-flex justify-content-between"><span class="text-muted">Phone:</span> <span class="fw-semibold">{{ $student->phone ?? 'N/A' }}</span></div>
                            <div class="d-flex justify-content-between"><span class="text-muted">Verified Phone:</span> <span class="fw-semibold {{ $student->user->is_phone_verified ? 'text-success' : 'text-danger' }}">{{ $student->user->is_phone_verified ? $student->user->phone_number : 'Not Verified' }}</span></div>
                            <div class="d-flex justify-content-between"><span class="text-muted">Aadhar No:</span> <span class="fw-semibold">{{ $student->aadhar_number ? implode(' ', str_split($student->aadhar_number, 4)) : 'N/A' }}</span></div>
                            <div class="d-flex justify-content-between"><span class="text-muted">Address:</span> <span class="fw-semibold text-end" style="max-width: 180px;">{{ $student->current_address ?? $student->address ?? 'N/A' }}</span></div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 p-4 pt-0">
                    <a href="{{ route('student.attendance.capture') }}" class="btn btn-primary w-100 py-3 rounded-3 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                        <i class="fas fa-camera fa-lg"></i> Mark Face Attendance
                    </a>
                </div>
            </div>

            <!-- Fee Status Summary Card -->
            @php
                $computedDueFees = $invoices->sum('due_amount');
                $computedPaidFees = $invoices->sum('paid_amount');
                $computedTotalFees = $invoices->sum('total_amount');
            @endphp
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-transparent py-3 border-bottom-0">
                    <h6 class="fw-bold mb-0 text-white"><i class="fas fa-wallet text-primary me-2"></i>Fee Status Summary</h6>
                </div>
                <div class="card-body pt-0">
                    <div class="d-flex justify-content-between align-items-center mb-2.5">
                        <span class="text-muted small">Total Invoiced:</span>
                        <span class="fw-semibold text-white">₹{{ number_format($computedTotalFees, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2.5">
                        <span class="text-muted small">Total Paid:</span>
                        <span class="fw-semibold text-success">₹{{ number_format($computedPaidFees, 2) }}</span>
                    </div>
                    <hr class="my-2" style="border-color: var(--border-sutil) !important;">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small fw-bold">Outstanding Balance:</span>
                        <span class="fw-bold fs-5 {{ $computedDueFees > 0 ? 'text-danger' : 'text-success' }}">
                            ₹{{ number_format($computedDueFees, 2) }}
                        </span>
                    </div>
                    @if($computedDueFees > 0)
                        <div class="alert alert-danger mt-3 mb-0 py-2 px-3 small border-0 rounded-3 text-center" style="background-color: #3A1C1C !important; color: var(--accent-alert) !important;">
                            <i class="fas fa-exclamation-circle me-1"></i> Dues are pending. Please pay soon.
                        </div>
                    @else
                        <div class="alert alert-success mt-3 mb-0 py-2 px-3 small border-0 rounded-3 text-center" style="background-color: rgba(50, 215, 75, 0.15) !important; color: var(--accent-status) !important;">
                            <i class="fas fa-check-circle me-1"></i> All dues are clear.
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-transparent border-top-0 p-3 pt-0">
                    <button class="btn btn-outline-primary btn-sm w-100 rounded-3 fw-bold" onclick="document.getElementById('fees-tab').click();">
                        <i class="fas fa-file-invoice-dollar me-1"></i> View Invoices & Receipts
                    </button>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <ul class="nav nav-pills mb-4 p-1 bg-light rounded-4 shadow-sm border" id="studentTab" role="tablist">
                <li class="nav-item flex-grow-1 text-center" role="presentation">
                    <button class="nav-link active fw-bold py-2 rounded-3 w-100" id="attendance-tab" data-bs-toggle="tab" data-bs-target="#attendance-panel" type="button" role="tab" aria-controls="attendance-panel" aria-selected="true">
                        <i class="fas fa-calendar-check me-2"></i>Attendance Log
                    </button>
                </li>
                <li class="nav-item flex-grow-1 text-center" role="presentation">
                    <button class="nav-link fw-bold py-2 rounded-3 w-100" id="milestone-tab" data-bs-toggle="tab" data-bs-target="#milestone-panel" type="button" role="tab" aria-controls="milestone-panel" aria-selected="false">
                        <i class="fas fa-flag-checkered me-2"></i>Milestones
                    </button>
                </li>
                <li class="nav-item flex-grow-1 text-center" role="presentation">
                    <button class="nav-link fw-bold py-2 rounded-3 w-100" id="course-tab" data-bs-toggle="tab" data-bs-target="#course-panel" type="button" role="tab" aria-controls="course-panel" aria-selected="false">
                        <i class="fas fa-book-open me-2"></i>Course &amp; Syllabus
                    </button>
                </li>
                <li class="nav-item flex-grow-1 text-center" role="presentation">
                    <button class="nav-link fw-bold py-2 rounded-3 w-100" id="fees-tab" data-bs-toggle="tab" data-bs-target="#fees-panel" type="button" role="tab" aria-controls="fees-panel" aria-selected="false">
                        <i class="fas fa-file-invoice-dollar me-2"></i>Fees &amp; Payments
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="studentTabContent">
                <!-- Tab 1: Attendance Log -->
                <div class="tab-pane fade show active" id="attendance-panel" role="tabpanel" aria-labelledby="attendance-tab">
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-3">
                            <div class="card bg-primary text-white border-0 shadow-sm rounded-4 h-100">
                                <div class="card-body text-center p-3">
                                    <i class="fas fa-calendar-check fa-2x mb-2 opacity-75"></i>
                                    <h3 class="fw-bold mb-0">{{ $presentDays }}</h3>
                                    <span class="small">Present</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card bg-danger text-white border-0 shadow-sm rounded-4 h-100">
                                <div class="card-body text-center p-3">
                                    <i class="fas fa-calendar-times fa-2x mb-2 opacity-75"></i>
                                    <h3 class="fw-bold mb-0">{{ $absentDays }}</h3>
                                    <span class="small">Absent</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card bg-warning text-white border-0 shadow-sm rounded-4 h-100">
                                <div class="card-body text-center p-3">
                                    <i class="fas fa-clock fa-2x mb-2 opacity-75"></i>
                                    <h3 class="fw-bold mb-0">{{ $lateDays }}</h3>
                                    <span class="small">Late</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card bg-success text-white border-0 shadow-sm rounded-4 h-100">
                                <div class="card-body text-center p-3">
                                    <i class="fas fa-chart-pie fa-2x mb-2 opacity-75"></i>
                                    <h3 class="fw-bold mb-0">{{ $attendancePercentage }}%</h3>
                                    <span class="small">Overall</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-header bg-transparent border-bottom-0 pt-4 pb-0 px-4">
                            <h5 class="fw-bold mb-0"><i class="fas fa-list text-primary me-2"></i>Recent Attendance</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Device</th>
                                            <th>Photo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($attendances as $attendance)
                                            <tr>
                                                <td class="fw-medium">{{ \Carbon\Carbon::parse($attendance->attendance_date)->format('M d, Y') }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $attendance->status === 'Present' ? 'success' : ($attendance->status === 'Absent' ? 'danger' : 'warning') }} rounded-pill">
                                                        {{ $attendance->status }}
                                                    </span>
                                                </td>
                                                <td class="text-muted small"><i class="fas fa-laptop me-1"></i>{{ $attendance->device_name ?? 'Face Camera' }}</td>
                                                <td>
                                                    @if($attendance->photo_path)
                                                        <a href="{{ Storage::url($attendance->photo_path) }}" target="_blank">
                                                            <img src="{{ Storage::url($attendance->photo_path) }}" width="40" height="40" class="rounded shadow-sm" style="object-fit: cover;">
                                                        </a>
                                                    @else
                                                        <span class="text-muted small">No photo</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-4">No attendance records found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab 2: Milestones -->
                <div class="tab-pane fade" id="milestone-panel" role="tabpanel" aria-labelledby="milestone-tab">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card shadow-sm border-0 rounded-4 h-100">
                                <div class="card-body text-center p-4">
                                    <i class="fas fa-flag-checkered fa-2x text-primary mb-2"></i>
                                    <h3 class="fw-bold mb-0">{{ $totalMilestones }}</h3>
                                    <span class="small text-muted">Total Milestones</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card shadow-sm border-0 rounded-4 h-100">
                                <div class="card-body text-center p-4">
                                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                    <h3 class="fw-bold mb-0">{{ $completedMilestones }}</h3>
                                    <span class="small text-muted">Completed</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card shadow-sm border-0 rounded-4 h-100">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="small text-muted">Progress</span>
                                        <span class="fw-bold">{{ $milestoneProgress }}%</span>
                                    </div>
                                    <div class="milestone-progress">
                                        <div class="milestone-progress-bar" style="width: {{ $milestoneProgress }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-header bg-transparent border-bottom pt-4 pb-3 px-4">
                            <h5 class="fw-bold mb-0"><i class="fas fa-list-check text-primary me-2"></i>Syllabus Milestones (Read-Only)</h5>
                            <p class="text-muted small mb-0 mt-1">These milestones are derived from your course syllabus. View-only, no editing allowed.</p>
                        </div>
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Milestone</th>
                                            <th>Type</th>
                                            <th>Priority</th>
                                            <th>Target Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($milestones as $milestone)
                                            <tr>
                                                <td class="fw-bold">{{ $milestone->title }}</td>
                                                <td class="small">{{ $milestone->milestone_type }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $milestone->priority === 'High' ? 'danger' : ($milestone->priority === 'Medium' ? 'warning' : 'info') }} px-2 py-1">
                                                        {{ $milestone->priority }}
                                                    </span>
                                                </td>
                                                <td class="small">{{ $milestone->target_date ? \Carbon\Carbon::parse($milestone->target_date)->format('M d, Y') : 'N/A' }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $milestone->status === 'Completed' ? 'success' : ($milestone->status === 'In Progress' ? 'primary' : 'secondary') }} rounded-pill">
                                                        {{ $milestone->status }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4">No milestones found for your course.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab 3: Course & Syllabus -->
                <div class="tab-pane fade" id="course-panel" role="tabpanel" aria-labelledby="course-tab">
                    <div class="card shadow-sm border-0 rounded-4 mb-4 overflow-hidden">
                        <div class="card-header bg-primary text-white p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-uppercase small opacity-75">Your Current Enrolled Course</span>
                                    <h3 class="fw-bold mb-0 mt-1">{{ $student->course->name ?? 'No Course Assigned' }}</h3>
                                </div>
                                @if($student->course && $student->course->syllabus_path)
                                    <a href="{{ Storage::url($student->course->syllabus_path) }}" target="_blank" class="btn btn-light btn-lg fw-bold rounded-3 shadow-sm text-primary">
                                        <i class="fas fa-file-pdf me-2"></i> Download Syllabus
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="card-body p-4 bg-light">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="p-3 bg-white rounded-3 border">
                                        <span class="text-muted d-block small mb-1">COURSE CODE</span>
                                        <span class="fw-bold text-dark">{{ $student->course->code ?? 'N/A' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 bg-white rounded-3 border">
                                        <span class="text-muted d-block small mb-1">DURATION</span>
                                        <span class="fw-bold text-dark">{{ $student->course_duration ?? '1 Year' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 bg-white rounded-3 border">
                                        <span class="text-muted d-block small mb-1">TOTAL COURSE FEE</span>
                                        <span class="fw-bold text-success">\u20B9{{ number_format($student->course->fee ?? 0, 2) }}</span>
                                    </div>
                                </div>
                            </div>

                            @if($student->course && !$student->course->syllabus_path)
                                <div class="alert alert-warning mt-3 mb-0 border-0 rounded-3">
                                    <i class="fas fa-exclamation-circle me-2"></i> The syllabus document for this course has not been uploaded by the admin yet.
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-header bg-transparent border-bottom pt-4 pb-3 px-4">
                            <h5 class="fw-bold mb-0"><i class="fas fa-exchange-alt text-primary me-2"></i>Enroll or Change Course</h5>
                            <p class="text-muted small mb-0 mt-1">Select a course according to the syllabus details and duration that fits your schedule.</p>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('student.select-course') }}" method="POST">
                                @csrf
                                <div class="row g-3">
                                    @forelse($courses as $crs)
                                        <div class="col-12 col-md-6">
                                            <div class="p-3 rounded-4 border bg-white h-100 d-flex flex-column justify-content-between align-items-start card-hover-pill">
                                                <div class="w-100">
                                                    <div class="d-flex justify-content-between mb-2">
                                                        <span class="badge bg-secondary rounded-pill">{{ $crs->code ?? 'N/A' }}</span>
                                                        <span class="text-success fw-bold">\u20B9{{ number_format($crs->fee, 2) }}</span>
                                                    </div>
                                                    <h6 class="fw-bold text-dark mb-1">{{ $crs->name }}</h6>
                                                    <p class="text-muted small mb-2"><i class="fas fa-clock me-1"></i> {{ $crs->duration ?? 'Flexible' }}</p>

                                                    @if($crs->syllabus_path)
                                                        <a href="{{ Storage::url($crs->syllabus_path) }}" target="_blank" class="text-primary text-decoration-underline small d-inline-flex align-items-center gap-1 mb-3">
                                                            <i class="fas fa-file-pdf"></i> Read Syllabus
                                                        </a>
                                                    @else
                                                        <span class="text-muted small d-block mb-3"><i class="fas fa-ban me-1"></i> No syllabus available</span>
                                                    @endif
                                                </div>

                                                <div class="form-check w-100 pt-2 border-top">
                                                    <input class="form-check-input" type="radio" name="course_id" id="course_radio_{{ $crs->id }}" value="{{ $crs->id }}" {{ ($student->course_id == $crs->id) ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-semibold text-dark cursor-pointer" for="course_radio_{{ $crs->id }}">
                                                        Select This Course
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12 py-3 text-center text-muted">No courses are available for selection.</div>
                                    @endforelse
                                </div>

                                @if($courses->count() > 0)
                                    <div class="mt-4 text-end">
                                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-bold shadow-sm">
                                            <i class="fas fa-save me-2"></i>Confirm Course Enrollment
                                        </button>
                                    </div>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Tab 4: Fees & Payments -->
                <div class="tab-pane fade" id="fees-panel" role="tabpanel" aria-labelledby="fees-tab">
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="card shadow-sm border-0 rounded-4 h-100">
                                <div class="card-header bg-transparent border-bottom pt-4 px-4">
                                    <h5 class="fw-bold mb-0"><i class="fas fa-receipt text-primary me-2"></i>Fee Structure</h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="d-flex flex-column gap-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted">Tenure Cycle:</span>
                                            <span class="badge bg-secondary px-3 py-1.5 rounded-pill">{{ $student->fee_tenure ?? '1 Year' }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted">Calculated Monthly Share:</span>
                                            <span class="fw-semibold">\u20B9{{ number_format($monthlyCourseFee, 2) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center text-danger">
                                            <span>Active Discount:</span>
                                            <span>-\u20B9{{ number_format($monthlyDiscount, 2) }}</span>
                                        </div>
                                        <hr class="my-1">
                                        <div class="d-flex justify-content-between align-items-center fw-bold fs-5">
                                            <span>Net Monthly Fee:</span>
                                            <span class="text-success">\u20B9{{ number_format($netMonthlyFee, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card shadow-sm border-0 rounded-4 h-100">
                                <div class="card-header bg-transparent border-bottom pt-4 px-4">
                                    <h5 class="fw-bold mb-0"><i class="fas fa-wallet text-primary me-2"></i>Fee Status</h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="d-flex flex-column gap-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted">Total Fees:</span>
                                            <span class="fw-semibold">\u20B9{{ number_format($totalFees, 2) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center text-success">
                                            <span>Total Paid:</span>
                                            <span>+\u20B9{{ number_format($paidFees, 2) }}</span>
                                        </div>
                                        <hr class="my-1">
                                        <div class="d-flex justify-content-between align-items-center fw-bold fs-5">
                                            <span>Due Balance:</span>
                                            <span class="{{ $dueFees > 0 ? 'text-danger' : 'text-success' }}">\u20B9{{ number_format($dueFees, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-header bg-transparent border-bottom pt-4 pb-3 px-4">
                            <h5 class="fw-bold mb-0"><i class="fas fa-file-invoice-dollar text-primary me-2"></i>Your Fee Invoices &amp; Receipts</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Receipt No</th>
                                            <th>Month/Period</th>
                                            <th>Fee Type</th>
                                            <th>Total Due</th>
                                            <th>Paid</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($invoices as $invoice)
                                            <tr>
                                                <td class="fw-bold text-dark">{{ $invoice->invoice_no }}</td>
                                                <td class="small">{{ $invoice->billing_month ? \Carbon\Carbon::create()->month($invoice->billing_month)->format('F') : '' }} {{ $invoice->billing_year }}</td>
                                                <td class="small">{{ $invoice->fee_category }}</td>
                                                <td class="fw-semibold">\u20B9{{ number_format($invoice->total_amount, 2) }}</td>
                                                <td class="text-success fw-semibold">\u20B9{{ number_format($invoice->paid_amount, 2) }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $invoice->status === 'Paid' ? 'success' : ($invoice->status === 'Partial' ? 'warning' : 'danger') }} rounded-pill">
                                                        {{ $invoice->status }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('fee_invoices.show', $invoice) }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1">
                                                        <i class="fas fa-print me-1"></i> Receipt
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-4">No invoice records found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
