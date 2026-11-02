@extends('layouts.app')

@section('title', 'LMS Suite: Courses, Cohorts, Masterclasses & Performance - Netcoder')
@section('page-title', 'LMS Management Suite')

@section('content')
<style>
    :root {
        --nc-bg-dark: #0f0f12;
        --nc-card-dark: #18181c;
        --nc-card-hover: #1f1f26;
        --nc-border-dark: rgba(255, 255, 255, 0.08);
        --nc-coral: #ff5733;
        --nc-coral-gradient: linear-gradient(135deg, #ff5733, #e0441d);
        --nc-coral-glow: rgba(255, 87, 51, 0.28);
        --nc-text-muted: #8e8e9c;
    }

    body {
        background-color: var(--nc-bg-dark) !important;
        color: #f3f3f8 !important;
        font-family: 'Inter', -apple-system, sans-serif;
    }

    .lms-suite-container {
        max-width: 1440px;
        margin: 0 auto;
        padding: 24px;
    }

    .lms-panel-card {
        background: var(--nc-card-dark);
        border: 1px solid var(--nc-border-dark);
        border-radius: 18px;
        transition: all 0.25s ease;
    }

    .lms-panel-card:hover {
        border-color: rgba(255, 87, 51, 0.28);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
    }

    .btn-coral {
        background: var(--nc-coral-gradient);
        color: #fff !important;
        border: none;
        font-weight: 700;
        border-radius: 10px;
        padding: 9px 18px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 14px var(--nc-coral-glow);
        transition: all 0.2s ease;
        text-decoration: none;
        cursor: pointer;
        font-size: 0.88rem;
    }

    .btn-coral:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(255, 87, 51, 0.45);
        background: linear-gradient(135deg, #ff6b48, #ff5733);
    }

    .badge-coral {
        background: rgba(255, 87, 51, 0.12);
        color: var(--nc-coral);
        border: 1px solid rgba(255, 87, 51, 0.25);
        font-size: 0.75rem;
        font-weight: 700;
        border-radius: 8px;
        padding: 4px 10px;
    }

    /* Tab Navigation Styles */
    .nav-tabs-dark {
        border-bottom: 1px solid var(--nc-border-dark);
        gap: 8px;
    }

    .nav-tabs-dark .nav-link {
        color: var(--nc-text-muted);
        border: 1px solid transparent;
        border-radius: 10px 10px 0 0;
        padding: 12px 20px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.2s;
    }

    .nav-tabs-dark .nav-link:hover {
        color: #fff;
        background: rgba(255, 255, 255, 0.04);
        border-color: var(--nc-border-dark) var(--nc-border-dark) transparent;
    }

    .nav-tabs-dark .nav-link.active {
        color: #fff !important;
        background: var(--nc-card-dark) !important;
        border-color: var(--nc-coral) var(--nc-border-dark) transparent !important;
        border-top: 3px solid var(--nc-coral) !important;
    }

    .lms-custom-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 8px;
    }

    .lms-custom-table thead th {
        color: var(--nc-text-muted);
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        padding: 12px 16px;
        border: none;
    }

    .lms-custom-table tbody tr {
        background: #18181c;
        border: 1px solid var(--nc-border-dark);
        border-radius: 12px;
        transition: transform 0.2s, background 0.2s;
    }

    .lms-custom-table tbody tr:hover {
        background: #202028;
        transform: translateY(-1px);
    }

    .lms-custom-table td {
        padding: 15px 16px;
        border: none;
        vertical-align: middle;
    }

    .lms-custom-table td:first-child {
        border-top-left-radius: 12px;
        border-bottom-left-radius: 12px;
    }

    .lms-custom-table td:last-child {
        border-top-right-radius: 12px;
        border-bottom-right-radius: 12px;
    }

    .modal-dark .modal-content {
        background: #18181c;
        border: 1px solid rgba(255, 255, 255, 0.12);
        color: #f1f1f5;
        border-radius: 18px;
    }

    .modal-dark .form-control, .modal-dark .form-select {
        background: #121215;
        border: 1px solid rgba(255, 255, 255, 0.12);
        color: #fff;
        border-radius: 10px;
        padding: 10px 14px;
    }

    .modal-dark .form-control:focus, .modal-dark .form-select:focus {
        border-color: var(--nc-coral);
        box-shadow: 0 0 0 2px var(--nc-coral-glow);
        background: #15151a;
        color: #fff;
    }

    .skeleton-mode .skeleton-elem {
        background: linear-gradient(90deg, #18181c 25%, #25252c 50%, #18181c 75%) !important;
        background-size: 200% 100% !important;
        animation: lmsShimmer 1.5s infinite !important;
        color: transparent !important;
        border-color: transparent !important;
        pointer-events: none !important;
    }

    .skeleton-mode .skeleton-elem * {
        visibility: hidden !important;
    }

    @keyframes lmsShimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
</style>

<div class="lms-suite-container" id="lmsSuiteRoot">

    {{-- Breadcrumb & Title --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge-coral">
                    <i class="fas fa-graduation-cap me-1"></i> LMS Complete Suite
                </span>
                <span style="color: var(--nc-text-muted); font-size: 0.82rem;">Admin &amp; Mentor Management Console</span>
            </div>
            <h1 class="h2 fw-bold text-white mb-1">Academy LMS &amp; Course Publishing Hub</h1>
            <p style="color: var(--nc-text-muted); font-size: 0.92rem;" class="mb-0">
                Manage Courses, Batches/Cohorts, Live Masterclasses, Track Student Performance, and Auto-Register Invoices.
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button type="button" class="btn btn-outline-secondary text-secondary border-secondary px-3 py-2 rounded-3" onclick="toggleLmsSkeleton()">
                <i class="fas fa-magic me-1"></i> Skeleton Loading
            </button>
            <button type="button" class="btn-coral" data-bs-toggle="modal" data-bs-target="#quickEnrollModal">
                <i class="fas fa-user-plus"></i>
                <span>Enroll &amp; Auto-Fee</span>
            </button>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm d-flex align-items-center gap-3 mb-4" style="background: rgba(16, 185, 129, 0.15); color: #10b981; border-radius: 12px;">
            <i class="fas fa-check-circle fs-5"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    {{-- Top Operational Metrics (4 Bento Cards) --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="lms-panel-card p-4 skeleton-elem">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span style="color: var(--nc-text-muted); font-size: 0.78rem; text-transform: uppercase; font-weight: 700;">Enrolled Students</span>
                    <i class="fas fa-user-graduate" style="color: var(--nc-coral);"></i>
                </div>
                <div class="h2 fw-bold text-white mb-1">{{ number_format($totalEnrolled) }}</div>
                <div style="color: #10b981; font-size: 0.78rem;"><i class="fas fa-check me-1"></i> {{ $activePortalUsers }} Portal Active</div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="lms-panel-card p-4 skeleton-elem">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span style="color: var(--nc-text-muted); font-size: 0.78rem; text-transform: uppercase; font-weight: 700;">Active Cohorts</span>
                    <i class="fas fa-users-class text-info" style="color: #38bdf8;"></i>
                </div>
                <div class="h2 fw-bold text-white mb-1">{{ number_format($totalCohorts) }}</div>
                <div style="color: #38bdf8; font-size: 0.78rem;">Live Batches Running</div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="lms-panel-card p-4 skeleton-elem">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span style="color: var(--nc-text-muted); font-size: 0.78rem; text-transform: uppercase; font-weight: 700;">Live Masterclasses</span>
                    <i class="fas fa-video text-warning" style="color: #f59e0b;"></i>
                </div>
                <div class="h2 fw-bold text-white mb-1">{{ number_format($totalMasterclasses) }}</div>
                <div style="color: #f59e0b; font-size: 0.78rem;">Scheduled Interactive Labs</div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="lms-panel-card p-4 skeleton-elem">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span style="color: var(--nc-text-muted); font-size: 0.78rem; text-transform: uppercase; font-weight: 700;">LMS Fees Collected</span>
                    <i class="fas fa-file-invoice-dollar text-success" style="color: #10b981;"></i>
                </div>
                <div class="h2 fw-bold text-white mb-1">₹{{ number_format($totalLmsRevenue) }}</div>
                <div style="color: #ef4444; font-size: 0.78rem;">Pending: ₹{{ number_format($pendingLmsFees) }}</div>
            </div>
        </div>
    </div>

    {{-- Tabs Navigation --}}
    <ul class="nav nav-tabs nav-tabs-dark mb-4" id="lmsTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="students-tab" data-bs-toggle="tab" data-bs-target="#tab-students" type="button" role="tab">
                <i class="fas fa-user-graduate me-2"></i> Enrolled Students &amp; Auto-Fees
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="cohorts-tab" data-bs-toggle="tab" data-bs-target="#tab-cohorts" type="button" role="tab">
                <i class="fas fa-layer-group me-2"></i> Cohorts &amp; Batches
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="masterclasses-tab" data-bs-toggle="tab" data-bs-target="#tab-masterclasses" type="button" role="tab">
                <i class="fas fa-video me-2"></i> Live Masterclasses
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="progress-tab" data-bs-toggle="tab" data-bs-target="#tab-progress" type="button" role="tab">
                <i class="fas fa-chart-line me-2"></i> Track Performance
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="courses-tab" data-bs-toggle="tab" data-bs-target="#tab-courses" type="button" role="tab">
                <i class="fas fa-book-open me-2"></i> Courses &amp; Publishing
            </button>
        </li>
    </ul>

    {{-- Tabs Content --}}
    <div class="tab-content" id="lmsTabsContent">

        {{-- TAB 1: Enrolled Students & Auto-Fee Ledger --}}
        <div class="tab-pane fade show active" id="tab-students" role="tabpanel">
            <div class="lms-panel-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div>
                        <h4 class="fw-bold text-white mb-0">Enrolled Students &amp; Automated Fee Ledger</h4>
                        <small style="color: var(--nc-text-muted);">View all enrolled learners, auto-issued receipts, and portal states</small>
                    </div>
                    <form method="GET" action="{{ route('lms.index') }}" class="d-flex gap-2">
                        <input type="text" name="search" class="form-control form-control-sm bg-dark border-secondary text-white" placeholder="Search student or ID..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-sm btn-outline-light"><i class="fas fa-search"></i></button>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="lms-custom-table">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Admission ID</th>
                                <th>Course Enrolled</th>
                                <th>Fee Status</th>
                                <th>Total / Due</th>
                                <th>Portal Access</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $st)
                                @php
                                    $inv = $st->feeInvoices->first();
                                    $status = $inv ? $inv->status : ($st->course ? 'Unpaid' : 'No Course');
                                    $badgeClass = match($status) {
                                        'Paid' => 'bg-success',
                                        'Partial' => 'bg-warning text-dark',
                                        'Unpaid' => 'bg-danger',
                                        default => 'bg-secondary'
                                    };
                                    $tot = $inv ? $inv->total_amount : ($st->course?->fee ?? 0);
                                    $due = $inv ? $inv->due_amount : $tot;
                                @endphp
                                <tr class="skeleton-elem">
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div style="width: 38px; height: 38px; border-radius: 10px; background: linear-gradient(135deg, #ff5733, #7928ca); display: flex; align-items: center; justify-content: center; font-weight: 700; color: #fff;">
                                                {{ strtoupper(substr($st->first_name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-white">{{ $st->first_name }} {{ $st->last_name }}</div>
                                                <small style="color: var(--nc-text-muted);">{{ $st->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-dark border border-secondary text-light font-monospace">{{ $st->admission_no }}</span>
                                    </td>
                                    <td>
                                        <span class="badge-coral">{{ $st->course?->name ?? 'Unassigned' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $badgeClass }} px-2 py-1">{{ $status }}</span>
                                    </td>
                                    <td>
                                        <div class="text-white fw-bold">₹{{ number_format($tot) }}</div>
                                        @if($due > 0)
                                            <small class="text-danger">Due: ₹{{ number_format($due) }}</small>
                                        @else
                                            <small class="text-success">Cleared</small>
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('lms.toggle-portal', $st->id) }}" method="POST">
                                            @csrf
                                            @if($st->portal_active)
                                                <button type="submit" class="btn btn-sm btn-outline-success px-2 py-1 rounded-pill" title="Click to Suspend">
                                                    <i class="fas fa-check-circle me-1"></i> Active
                                                </button>
                                            @else
                                                <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1 rounded-pill" title="Click to Enable">
                                                    <i class="fas fa-ban me-1"></i> Disabled
                                                </button>
                                            @endif
                                        </form>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-1">
                                            @if($inv)
                                                <a href="{{ route('fee_invoices.show', $inv->id) }}" class="btn btn-sm btn-outline-light" title="Fee Receipt">
                                                    <i class="fas fa-receipt"></i>
                                                </a>
                                            @endif
                                            <a href="{{ route('students.show', $st->id) }}" class="btn btn-sm btn-outline-light" title="Profile">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-secondary">No enrolled students found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $students->links() }}
                </div>
            </div>
        </div>

        {{-- TAB 2: Cohorts & Batches Management --}}
        <div class="tab-pane fade" id="tab-cohorts" role="tabpanel">
            <div class="lms-panel-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div>
                        <h4 class="fw-bold text-white mb-0">Cohorts &amp; Batches Management</h4>
                        <small style="color: var(--nc-text-muted);">Group students into cohorts, allocate mentors, and control enrollment status</small>
                    </div>
                    <button type="button" class="btn-coral" data-bs-toggle="modal" data-bs-target="#createCohortModal">
                        <i class="fas fa-plus-circle"></i>
                        <span>Create New Cohort</span>
                    </button>
                </div>

                <div class="row g-4">
                    @forelse($cohorts as $cohort)
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="p-4 rounded-3 h-100 d-flex flex-column" style="background: rgba(255,255,255,0.02); border: 1px solid var(--nc-border-dark);">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge {{ $cohort->is_published ? 'bg-success' : 'bg-secondary' }} px-2.5 py-1 rounded-pill">
                                        {{ $cohort->is_published ? 'Published' : 'Draft' }}
                                    </span>
                                    <span style="color: var(--nc-text-muted); font-size: 0.75rem;">Cap: {{ $cohort->capacity }} Students</span>
                                </div>
                                <h5 class="fw-bold text-white mb-1">{{ $cohort->name }}</h5>
                                <div class="badge-coral mb-3 d-inline-block">{{ $cohort->course?->name ?? 'Course' }}</div>
                                
                                <div class="small text-secondary mb-2">
                                    <div><i class="fas fa-chalkboard-teacher me-1 text-info"></i> Mentor: <strong>{{ $cohort->mentor?->name ?? 'Lead Faculty' }}</strong></div>
                                    <div><i class="fas fa-calendar-alt me-1 text-warning"></i> Starts: {{ $cohort->start_date ? $cohort->start_date->format('M d, Y') : 'TBA' }}</div>
                                    <div><i class="fas fa-clock me-1 text-success"></i> Schedule: {{ $cohort->schedule ?? 'Regular Track' }}</div>
                                </div>

                                <div class="mt-auto pt-3 border-top border-secondary border-opacity-25 d-flex justify-content-between align-items-center">
                                    <span class="small text-muted">Status: <strong class="text-white">{{ ucfirst($cohort->status) }}</strong></span>
                                    <form action="{{ route('lms.cohorts.publish', $cohort->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $cohort->is_published ? 'btn-outline-warning' : 'btn-outline-success' }} py-1 px-3">
                                            {{ $cohort->is_published ? 'Unpublish' : 'Publish' }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5 text-secondary">
                            <i class="fas fa-layer-group fs-1 mb-3 d-block text-muted"></i>
                            No cohorts created yet. Click "Create New Cohort" to organize students into live batches.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- TAB 3: Live Masterclasses Management --}}
        <div class="tab-pane fade" id="tab-masterclasses" role="tabpanel">
            <div class="lms-panel-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div>
                        <h4 class="fw-bold text-white mb-0">Live Masterclasses &amp; Webinar Schedule</h4>
                        <small style="color: var(--nc-text-muted);">Publish interactive live masterclasses with mentors, Google Meet / Zoom links, and recordings</small>
                    </div>
                    <button type="button" class="btn-coral" data-bs-toggle="modal" data-bs-target="#scheduleMasterclassModal">
                        <i class="fas fa-calendar-plus"></i>
                        <span>Schedule Live Masterclass</span>
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="lms-custom-table">
                        <thead>
                            <tr>
                                <th>Masterclass Title</th>
                                <th>Mentor / Host</th>
                                <th>Course Topic</th>
                                <th>Date &amp; Time</th>
                                <th>Meeting Link</th>
                                <th>Publish Status</th>
                                <th class="text-end">Toggle Publish</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($masterclasses as $mc)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-white">{{ $mc->title }}</div>
                                        <small style="color: var(--nc-text-muted);">{{ Str::limit($mc->description, 60) }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fas fa-user-circle text-coral"></i>
                                            <span>{{ $mc->mentor_name ?? ($mc->instructor?->name ?? 'Guest Speaker') }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge-coral">{{ $mc->course?->name ?? 'General Industry' }}</span>
                                    </td>
                                    <td>
                                        <div class="text-white">{{ $mc->scheduled_at ? $mc->scheduled_at->format('M d, Y') : 'TBA' }}</div>
                                        <small style="color: var(--nc-text-muted);">{{ $mc->scheduled_at ? $mc->scheduled_at->format('h:i A') : '' }} ({{ $mc->duration_minutes }}m)</small>
                                    </td>
                                    <td>
                                        @if($mc->meeting_link)
                                            <a href="{{ $mc->meeting_link }}" target="_blank" class="btn btn-sm btn-outline-info py-0 px-2 text-decoration-none">
                                                <i class="fas fa-external-link-alt me-1"></i> Join Room
                                            </a>
                                        @else
                                            <span class="text-secondary small">Not set</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $mc->status === 'published' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ ucfirst($mc->status) }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <form action="{{ route('lms.masterclasses.publish', $mc->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $mc->status === 'published' ? 'btn-outline-warning' : 'btn-outline-success' }} py-1 px-3">
                                                {{ $mc->status === 'published' ? 'Move to Draft' : 'Publish Live' }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-secondary">
                                        No live masterclasses scheduled. Click "Schedule Live Masterclass" to create one.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- TAB 4: Student Performance & Progress Tracking --}}
        <div class="tab-pane fade" id="tab-progress" role="tabpanel">
            <div class="lms-panel-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div>
                        <h4 class="fw-bold text-white mb-0">Track Student Performance &amp; Course Progress</h4>
                        <small style="color: var(--nc-text-muted);">Monitor completion velocity, syllabus progress percentage, grades, and certificate issuance</small>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="lms-custom-table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Enrolled Course</th>
                                <th>Progress Bar</th>
                                <th>Completed Lessons</th>
                                <th>Grade</th>
                                <th>Certificate</th>
                                <th class="text-end">Update Progress</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($progressRecords as $prog)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-white">{{ $prog->student?->first_name }} {{ $prog->student?->last_name }}</div>
                                        <small style="color: var(--nc-text-muted);">ID: {{ $prog->student?->admission_no }}</small>
                                    </td>
                                    <td>
                                        <span class="badge-coral">{{ $prog->course?->name ?? 'Course' }}</span>
                                    </td>
                                    <td style="min-width: 180px;">
                                        <div class="d-flex justify-content-between small mb-1">
                                            <span>Progress</span>
                                            <span class="fw-bold" style="color: var(--nc-coral);">{{ $prog->progress_percentage }}%</span>
                                        </div>
                                        <div class="progress" style="height: 6px; background: rgba(255,255,255,0.08);">
                                            <div class="progress-bar" style="width: {{ $prog->progress_percentage }}%; background: var(--nc-coral);"></div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-white fw-bold">{{ $prog->completed_lessons }}</span>
                                        <span style="color: var(--nc-text-muted);">/ {{ $prog->total_lessons }} Lessons</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-dark border border-secondary text-warning fw-bold px-2 py-1">
                                            {{ $prog->grade ?? 'In Progress' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($prog->is_certified)
                                            <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Certified</span>
                                        @else
                                            <span class="badge bg-secondary">Incomplete</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-outline-light py-1 px-3" data-bs-toggle="modal" data-bs-target="#updateProgressModal{{ $prog->id }}">
                                            <i class="fas fa-edit me-1"></i> Grade &amp; Progress
                                        </button>
                                    </td>
                                </tr>

                                {{-- Progress Update Modal --}}
                                <div class="modal fade modal-dark" id="updateProgressModal{{ $prog->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <form action="{{ route('lms.progress.update', $prog->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-header border-secondary">
                                                    <h5 class="modal-title fw-bold text-white">Update Progress: {{ $prog->student?->first_name }}</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <div class="mb-3">
                                                        <label class="form-label text-white small fw-bold">Completed Lessons</label>
                                                        <input type="number" name="completed_lessons" class="form-control" value="{{ $prog->completed_lessons }}" min="0" max="{{ $prog->total_lessons }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label text-white small fw-bold">Total Lessons in Syllabus</label>
                                                        <input type="number" name="total_lessons" class="form-control" value="{{ $prog->total_lessons }}" min="1">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label text-white small fw-bold">Grade Awarded</label>
                                                        <select name="grade" class="form-select">
                                                            <option value="A+" {{ $prog->grade === 'A+' ? 'selected' : '' }}>A+ (Outstanding)</option>
                                                            <option value="A" {{ $prog->grade === 'A' ? 'selected' : '' }}>A (Excellent)</option>
                                                            <option value="B" {{ $prog->grade === 'B' ? 'selected' : '' }}>B (Good)</option>
                                                            <option value="In Progress" {{ $prog->grade === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-check mt-3">
                                                        <input class="form-check-input" type="checkbox" name="is_certified" value="1" id="certCheck{{ $prog->id }}" {{ $prog->is_certified ? 'checked' : '' }}>
                                                        <label class="form-check-label text-white" for="certCheck{{ $prog->id }}">
                                                            Award Official LMS Completion Certificate
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-secondary">
                                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn-coral">Save Performance</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-secondary">
                                        No active progress records yet. Progress automatically tracks when students enroll.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- TAB 5: Courses & Publishing Control --}}
        <div class="tab-pane fade" id="tab-courses" role="tabpanel">
            <div class="lms-panel-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div>
                        <h4 class="fw-bold text-white mb-0">Master Courses Catalog &amp; Publishing</h4>
                        <small style="color: var(--nc-text-muted);">Admin &amp; Mentors can publish, unpublish, and manage fee structures across all programs</small>
                    </div>
                    <a href="{{ route('courses.create') }}" class="btn-coral">
                        <i class="fas fa-plus-circle"></i>
                        <span>Add New Course</span>
                    </a>
                </div>

                <div class="row g-4">
                    @forelse($courses as $c)
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="p-4 rounded-3 h-100 d-flex flex-column" style="background: rgba(255,255,255,0.02); border: 1px solid var(--nc-border-dark);">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge {{ $c->status ? 'bg-success' : 'bg-secondary' }} px-2.5 py-1 rounded-pill">
                                        {{ $c->status ? 'Published' : 'Draft' }}
                                    </span>
                                    <span class="text-white fw-bold">₹{{ number_format($c->fee) }}</span>
                                </div>
                                <h5 class="fw-bold text-white mb-1">{{ $c->name }}</h5>
                                <div class="font-monospace text-secondary small mb-3">Code: {{ $c->code }} | Duration: {{ $c->duration }}</div>
                                <div class="small text-muted mb-3">
                                    <i class="fas fa-user-graduate me-1 text-coral"></i> Active Students: <strong>{{ $c->students_count }}</strong>
                                </div>
                                <div class="mt-auto pt-3 border-top border-secondary border-opacity-25 d-flex justify-content-between align-items-center">
                                    <a href="{{ route('courses.edit', $c->id) }}" class="btn btn-sm btn-outline-light py-1 px-3">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </a>
                                    <form action="{{ route('lms.courses.publish', $c->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $c->status ? 'btn-outline-warning' : 'btn-outline-success' }} py-1 px-3">
                                            {{ $c->status ? 'Unpublish' : 'Publish Course' }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5 text-secondary">
                            No master courses created. Click "Add New Course" to add to catalog.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Modal 1: Quick Enroll & Auto-Fee --}}
<div class="modal fade modal-dark" id="quickEnrollModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form action="{{ route('lms.enroll') }}" method="POST">
                @csrf
                <div class="modal-header border-secondary">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge-coral"><i class="fas fa-bolt"></i> One-Click</span>
                        <h5 class="modal-title fw-bold text-white mb-0">Enroll in LMS &amp; Auto-Generate Fee</h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="form-label text-secondary fw-semibold">Enrollment Target</label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="student_type" id="typeNew" value="new" checked onchange="toggleStudentType('new')">
                                <label class="form-check-label text-white" for="typeNew">
                                    Register New Student &amp; Account
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="student_type" id="typeExisting" value="existing" onchange="toggleStudentType('existing')">
                                <label class="form-check-label text-white" for="typeExisting">
                                    Existing Student
                                </label>
                            </div>
                        </div>
                    </div>

                    <div id="existingStudentSection" class="mb-3" style="display: none;">
                        <label class="form-label text-white fw-semibold">Select Student</label>
                        <select name="student_id" class="form-select">
                            <option value="">-- Choose Existing Student --</option>
                            @foreach($students as $st)
                                <option value="{{ $st->id }}">{{ $st->first_name }} {{ $st->last_name }} ({{ $st->admission_no }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="newStudentSection">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-white fw-semibold">First Name *</label>
                                <input type="text" name="first_name" class="form-control" placeholder="e.g. Rahul">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white fw-semibold">Last Name</label>
                                <input type="text" name="last_name" class="form-control" placeholder="e.g. Sharma">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white fw-semibold">Email Address *</label>
                                <input type="email" name="email" class="form-control" placeholder="rahul@example.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white fw-semibold">Phone Number</label>
                                <input type="text" name="phone" class="form-control" placeholder="+91 9876543210">
                            </div>
                        </div>
                    </div>

                    <hr class="border-secondary my-4">

                    <h6 class="text-coral fw-bold mb-3" style="color: var(--nc-coral);"><i class="fas fa-book-reader me-1"></i> Course &amp; Automated Fee Details</h6>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-white fw-semibold">Target LMS Course *</label>
                            <select name="course_id" id="courseSelector" class="form-select" required onchange="updateCourseFee(this)">
                                <option value="">-- Select Course --</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" data-fee="{{ $course->fee }}">
                                        {{ $course->name }} (₹{{ number_format($course->fee) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white fw-semibold">Total Course Fee (₹) *</label>
                            <input type="number" step="0.01" name="course_fee" id="courseFeeInput" class="form-control" required placeholder="0.00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white fw-semibold">Initial Payment (₹)</label>
                            <input type="number" step="0.01" name="paid_amount" class="form-control" placeholder="0.00 (leave 0 if due)">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white fw-semibold">Payment Mode</label>
                            <select name="payment_method" class="form-select">
                                <option value="Online LMS">Online LMS Portal</option>
                                <option value="UPI / QR">UPI / QR Code</option>
                                <option value="Cash">Cash at Campus</option>
                                <option value="Bank Transfer">Bank Transfer / NEFT</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-coral">
                        <i class="fas fa-check-double"></i>
                        <span>Confirm Enrollment &amp; Generate Invoice</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal 2: Create Cohort --}}
<div class="modal fade modal-dark" id="createCohortModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('lms.cohorts.store') }}" method="POST">
                @csrf
                <div class="modal-header border-secondary">
                    <h5 class="modal-title fw-bold text-white"><i class="fas fa-layer-group text-coral me-2"></i> Create New Cohort</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-white small fw-bold">Cohort Name *</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. MERN Full-Stack Batch #12" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white small fw-bold">Associated Course *</label>
                        <select name="course_id" class="form-select" required>
                            @foreach($courses as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white small fw-bold">Assigned Mentor / Instructor</label>
                        <select name="mentor_id" class="form-select">
                            <option value="">-- Choose Mentor --</option>
                            @foreach($mentors as $m)
                                <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->role?->role_name ?? 'Faculty' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label text-white small fw-bold">Start Date</label>
                            <input type="date" name="start_date" class="form-control">
                        </div>
                        <div class="col-6">
                            <label class="form-label text-white small fw-bold">Max Capacity</label>
                            <input type="number" name="capacity" class="form-control" value="30">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white small fw-bold">Schedule Track</label>
                        <input type="text" name="schedule" class="form-control" placeholder="e.g. Mon-Fri 10:00 AM - 1:00 PM">
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-coral">Publish Cohort</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal 3: Schedule Live Masterclass --}}
<div class="modal fade modal-dark" id="scheduleMasterclassModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('lms.masterclasses.store') }}" method="POST">
                @csrf
                <div class="modal-header border-secondary">
                    <h5 class="modal-title fw-bold text-white"><i class="fas fa-video text-warning me-2"></i> Schedule Live Masterclass</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-white small fw-bold">Masterclass Title *</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. System Design & Cloud Deployment" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white small fw-bold">Course / Track (Optional)</label>
                        <select name="course_id" class="form-select">
                            <option value="">-- General / All Courses --</option>
                            @foreach($courses as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white small fw-bold">Lead Mentor / Guest Speaker</label>
                        <select name="instructor_id" class="form-select">
                            <option value="">-- Choose Instructor --</option>
                            @foreach($mentors as $m)
                                <option value="{{ $m->id }}">{{ $m->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-8">
                            <label class="form-label text-white small fw-bold">Date &amp; Time *</label>
                            <input type="datetime-local" name="scheduled_at" class="form-control" required>
                        </div>
                        <div class="col-4">
                            <label class="form-label text-white small fw-bold">Duration (Min)</label>
                            <input type="number" name="duration_minutes" class="form-control" value="60">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white small fw-bold">Meeting Room Link (Google Meet / Zoom)</label>
                        <input type="text" name="meeting_link" class="form-control" placeholder="https://meet.google.com/xyz-abc-def">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white small fw-bold">Description &amp; Highlights</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Brief outline of the live interactive session..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-coral">Schedule &amp; Publish</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleStudentType(type) {
        if (type === 'existing') {
            document.getElementById('existingStudentSection').style.display = 'block';
            document.getElementById('newStudentSection').style.display = 'none';
        } else {
            document.getElementById('existingStudentSection').style.display = 'none';
            document.getElementById('newStudentSection').style.display = 'block';
        }
    }

    function updateCourseFee(select) {
        const option = select.options[select.selectedIndex];
        const fee = option.getAttribute('data-fee');
        if (fee) {
            document.getElementById('courseFeeInput').value = fee;
        }
    }

    function toggleLmsSkeleton() {
        const root = document.getElementById('lmsSuiteRoot');
        root.classList.toggle('skeleton-mode');
    }
</script>
@endsection
