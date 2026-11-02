@extends('layouts.app')

@section('title', 'Executive Operations & LMS Dashboard - Netcoder')
@section('page-title', 'Executive Overview')

@section('content')
<style>
    :root {
        --nc-bg-dark: #121214;
        --nc-sidebar-dark: #18181b;
        --nc-card-dark: #1c1c20;
        --nc-card-hover: #232329;
        --nc-border-dark: rgba(255, 255, 255, 0.07);
        --nc-coral: #ff5a3c;
        --nc-coral-soft: rgba(255, 90, 60, 0.14);
        --nc-coral-gradient: linear-gradient(135deg, #ff5a3c, #f0421f);
        --nc-text-muted: #8a8a96;
        --nc-text-light: #f2f2f5;
        --nc-green: #22c55e;
        --nc-blue: #3b82f6;
        --nc-amber: #f59e0b;
    }

    body {
        background-color: var(--nc-bg-dark) !important;
        color: var(--nc-text-light) !important;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    .dashboard-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 28px 28px 48px;
    }

    /* ---------- Panels ---------- */
    .dark-panel-card {
        background: var(--nc-card-dark);
        border: 1px solid var(--nc-border-dark);
        border-radius: 16px;
        transition: transform .22s cubic-bezier(.16,1,.3,1), border-color .22s, box-shadow .22s;
    }

    .dark-panel-card:hover {
        border-color: rgba(255, 90, 60, 0.28);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.35);
        transform: translateY(-2px);
    }

    /* ---------- Buttons ---------- */
    .btn-coral-primary {
        background: var(--nc-coral-gradient);
        color: #fff !important;
        font-weight: 700;
        font-size: .85rem;
        padding: 11px 18px;
        border-radius: 10px;
        border: none;
        box-shadow: 0 4px 14px rgba(255, 90, 60, 0.28);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
        transition: all .2s ease;
        cursor: pointer;
    }

    .btn-coral-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(255, 90, 60, 0.45);
        background: linear-gradient(135deg, #ff6a4d, #ff5a3c);
    }

    .badge-coral-soft {
        background: var(--nc-coral-soft);
        color: var(--nc-coral);
        border: 1px solid rgba(255, 90, 60, 0.22);
        font-size: .72rem;
        font-weight: 700;
        border-radius: 8px;
        padding: 5px 10px;
    }

    /* ---------- Skeleton ---------- */
    .skeleton-active .skeleton-item {
        background: linear-gradient(90deg, #1c1c20 25%, #26262d 50%, #1c1c20 75%) !important;
        background-size: 200% 100% !important;
        animation: ncShimmer 1.5s infinite !important;
        color: transparent !important;
        border-color: transparent !important;
        pointer-events: none !important;
    }
    .skeleton-active .skeleton-item * { visibility: hidden !important; }
    @keyframes ncShimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    /* ---------- Progress ---------- */
    .progress-track-coral {
        height: 8px;
        background: rgba(255, 255, 255, 0.06);
        border-radius: 999px;
        overflow: hidden;
    }
    .progress-fill-coral {
        height: 100%;
        background: var(--nc-coral);
        border-radius: 999px;
        transition: width .7s ease;
    }

    /* ---------- Tables ---------- */
    .custom-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 8px;
    }
    .custom-table thead th {
        color: var(--nc-text-muted);
        font-size: .72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .8px;
        padding: 8px 14px;
        border: none;
    }
    .custom-table tbody tr {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--nc-border-dark);
        border-radius: 12px;
        transition: background .2s, transform .2s;
    }
    .custom-table tbody tr:hover {
        background: rgba(255, 255, 255, 0.045);
        transform: translateY(-1px);
    }
    .custom-table td {
        padding: 14px;
        border: none;
        vertical-align: middle;
    }
    .custom-table td:first-child {
        border-top-left-radius: 12px;
        border-bottom-left-radius: 12px;
    }
    .custom-table td:last-child {
        border-top-right-radius: 12px;
        border-bottom-right-radius: 12px;
    }

    /* ---------- Stat cards ---------- */
    .stat-card .stat-label {
        color: var(--nc-text-muted);
        font-size: .72rem;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: .6px;
    }
    .stat-card .stat-value {
        font-size: 1.75rem;
        font-weight: 800;
        color: #fff;
        line-height: 1.15;
    }
    .stat-card .stat-sub {
        font-size: .76rem;
        color: var(--nc-text-muted);
    }

    /* ---------- Quick nav ---------- */
    .quick-nav-btn {
        background: #1a1a1e;
        border: 1px solid var(--nc-border-dark);
        border-radius: 12px;
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        text-decoration: none;
        transition: all .2s ease;
    }
    .quick-nav-btn:hover {
        background: #222228;
        border-color: rgba(255,90,60,.3);
        transform: translateY(-2px);
    }
    .quick-nav-btn i { font-size: 1.15rem; }
    .quick-nav-btn span { color: #fff; font-size: .8rem; font-weight: 600; }

    /* ---------- Section headers ---------- */
    .section-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: #fff;
        margin-bottom: 14px;
    }
    .section-link {
        color: var(--nc-coral);
        font-size: .8rem;
        font-weight: 600;
        text-decoration: none;
    }
    .section-link:hover { color: #ff7a5f; }

    /* ---------- Continue card ---------- */
    .continue-card {
        background: var(--nc-card-dark);
        border: 1px solid var(--nc-border-dark);
        border-radius: 16px;
        padding: 22px;
    }
    .continue-card:hover { border-color: rgba(255,90,60,.3); }
    .continue-icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    /* ---------- Recommended course tile ---------- */
    .course-tile {
        background: var(--nc-card-dark);
        border: 1px solid var(--nc-border-dark);
        border-radius: 16px;
        overflow: hidden;
        transition: all .22s ease;
    }
    .course-tile:hover {
        border-color: rgba(255,90,60,.28);
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(0,0,0,.35);
    }
    .course-thumb {
        height: 130px;
        background: linear-gradient(135deg, #ff5a3c 0%, #b32400 100%);
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .course-thumb::after {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 70% 40%, rgba(255,255,255,.15), transparent 60%);
    }
    .course-thumb i {
        font-size: 2.5rem;
        color: rgba(255,255,255,.55);
        z-index: 1;
    }

    /* ---------- Breadcrumb ---------- */
    .breadcrumb-mini { font-size: .78rem; color: var(--nc-text-muted); }
    .breadcrumb-mini .active { color: #fff; font-weight: 600; }
</style>

<div class="dashboard-container" id="executiveDashboard">

    {{-- Breadcrumb --}}
    <div class="breadcrumb-mini d-flex align-items-center gap-2 mb-3">
        <span>Home</span>
        <span>/</span>
        <span class="active">Executive Dashboard</span>
    </div>

    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="fw-bold text-white mb-1" style="font-size: 2rem;">Welcome Back</h1>
            <p style="color: var(--nc-text-muted); font-size: .92rem;" class="mb-2">
                Pick up where you left off and keep making progress across academy operations and online LMS tracks.
            </p>
            <span class="badge-coral-soft">
                <i class="fas fa-bolt me-1"></i> Continue Learning
            </span>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-2 rounded-3"
                    onclick="toggleDashboardSkeleton()">
                <i class="fas fa-magic me-1"></i> Skeleton Loading
            </button>
            <a href="{{ route('lms.index') }}" class="btn-coral-primary">
                <i class="fas fa-graduation-cap"></i> LMS Hub &amp; Auto-Fee
            </a>
            <a href="{{ route('reports.index') }}" class="btn btn-outline-light px-3 py-2 rounded-3 text-decoration-none">
                <i class="fas fa-download me-1"></i> Export Report
            </a>
        </div>
    </div>

    {{-- Continue Learning Cards (matches screenshot) --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="section-title mb-0"><i class="fas fa-play-circle me-1 text-coral" style="color: var(--nc-coral);"></i> Active Learning &amp; Course Tracks</span>
        <a href="{{ route('courses.index') }}" class="section-link">View All Courses &rarr;</a>
    </div>

    <div class="row g-4 mb-5">
        @php
            $firstCourse  = $coursesPopularity->first();
            $secondCourse = $coursesPopularity->skip(1)->first();
            $firstPct  = ($firstCourse  && $maxStudentsInCourse > 0) ? round(($firstCourse->students_count  / $maxStudentsInCourse) * 100) : 50;
            $secondPct = ($secondCourse && $maxStudentsInCourse > 0) ? round(($secondCourse->students_count / $maxStudentsInCourse) * 100) : 60;
        @endphp

        {{-- Card 1 --}}
        <div class="col-12 col-lg-6">
            <div class="continue-card skeleton-item">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="continue-icon" style="background: rgba(255,90,60,.14); color: var(--nc-coral);">
                            <i class="fas fa-paint-brush"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold text-white mb-0">{{ $firstCourse?->name ?? 'Graphic Design' }}</h4>
                            <small style="color: var(--nc-text-muted);">Design | Beginner</small>
                        </div>
                    </div>
                    <small style="color: var(--nc-text-muted); font-size: .72rem;">Active Students: {{ $firstCourse?->students_count ?? 25 }}</small>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <small style="color: var(--nc-text-muted); font-size: .78rem;">Course Progress</small>
                    <small class="fw-bold" style="color: var(--nc-coral);">{{ $firstPct }}%</small>
                </div>
                <div class="progress-track-coral my-2">
                    <div class="progress-fill-coral" style="width: {{ $firstPct }}%;"></div>
                </div>

                <div class="d-flex justify-content-between mb-3">
                    <small style="color: var(--nc-text-muted); font-size: .76rem;">
                        <i class="fas fa-user-graduate me-1"></i> {{ $firstCourse?->students_count ?? 25 }} Enrolled
                    </small>
                    <small style="color: var(--nc-text-muted); font-size: .76rem;">
                        <i class="fas fa-tag me-1"></i> ₹{{ number_format($firstCourse?->fee ?? 15000) }}
                    </small>
                </div>

                <a href="{{ route('lms.index') }}" class="btn-coral-primary w-100">
                    Resume Course
                </a>
            </div>
        </div>

        {{-- Card 2 --}}
        <div class="col-12 col-lg-6">
            <div class="continue-card skeleton-item">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="continue-icon" style="background: rgba(59,130,246,.14); color: var(--nc-blue);">
                            <i class="fas fa-code"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold text-white mb-0">{{ $secondCourse?->name ?? 'Web Development' }}</h4>
                            <small style="color: var(--nc-text-muted);">Web | Beginner</small>
                        </div>
                    </div>
                    <small style="color: var(--nc-text-muted); font-size: .72rem;">Active Students: {{ $secondCourse?->students_count ?? 18 }}</small>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <small style="color: var(--nc-text-muted); font-size: .78rem;">Course Progress</small>
                    <small class="fw-bold" style="color: var(--nc-coral);">{{ $secondPct }}%</small>
                </div>
                <div class="progress-track-coral my-2">
                    <div class="progress-fill-coral" style="width: {{ $secondPct }}%;"></div>
                </div>

                <div class="d-flex justify-content-between mb-3">
                    <small style="color: var(--nc-text-muted); font-size: .76rem;">
                        <i class="fas fa-user-graduate me-1"></i> {{ $secondCourse?->students_count ?? 18 }} Enrolled
                    </small>
                    <small style="color: var(--nc-text-muted); font-size: .76rem;">
                        <i class="fas fa-tag me-1"></i> ₹{{ number_format($secondCourse?->fee ?? 25000) }}
                    </small>
                </div>

                <a href="{{ route('lms.index') }}" class="btn-coral-primary w-100">
                    Resume Course
                </a>
            </div>
        </div>
    </div>

    {{-- Learning & Operational Overview --}}
    <h3 class="section-title">Learning &amp; Operational Overview</h3>

    <div class="row g-3 mb-5">
        <div class="col-6 col-lg-3">
            <div class="dark-panel-card p-4 skeleton-item stat-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="stat-label">Enrolled Students</span>
                    <i class="fas fa-book-bookmark" style="color: var(--nc-amber);"></i>
                </div>
                <div class="stat-value">{{ number_format($studentCount) }}</div>
                <div class="stat-sub">Active Students in System</div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="dark-panel-card p-4 skeleton-item stat-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="stat-label">Total Revenue</span>
                    <i class="fas fa-check-square" style="color: var(--nc-green);"></i>
                </div>
                <div class="stat-value">₹{{ number_format($totalIncome) }}</div>
                <div class="stat-sub" style="color: var(--nc-green);">Receipts Successfully Paid</div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="dark-panel-card p-4 skeleton-item stat-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="stat-label">Pending Invoices</span>
                    <i class="fas fa-clipboard-list" style="color: var(--nc-amber);"></i>
                </div>
                <div class="stat-value">₹{{ number_format($totalPendingFees) }}</div>
                <div class="stat-sub" style="color: var(--nc-amber);">{{ $dueInvoices }} Unpaid Due</div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="dark-panel-card p-4 skeleton-item stat-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="stat-label">Active Staff</span>
                    <i class="fas fa-award" style="color: var(--nc-blue);"></i>
                </div>
                <div class="stat-value">{{ number_format($employeeCount) }}</div>
                <div class="stat-sub" style="color: var(--nc-blue);">Allocated Across Programs</div>
            </div>
        </div>
    </div>

    {{-- Automated LMS Banner --}}
    <div class="dark-panel-card p-4 mb-5 skeleton-item"
         style="background: linear-gradient(135deg, #1c1c20, #26262d); border-color: rgba(255,90,60,.28);">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <div style="width: 52px; height: 52px; border-radius: 14px; background: var(--nc-coral-soft); color: var(--nc-coral); display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <div>
                    <span class="badge-coral-soft mb-1 d-inline-block">Automated Invoicing Engine</span>
                    <h4 class="fw-bold text-white mb-0">Enroll in LMS &amp; Automatically Register Fee Invoice</h4>
                    <p style="color: var(--nc-text-muted); font-size: .84rem;" class="mb-0">
                        When a student is enrolled into an LMS course, the system automatically creates their portal account and issues their official fee receipt.
                    </p>
                </div>
            </div>
            <a href="{{ route('lms.index') }}" class="btn-coral-primary flex-shrink-0">
                <i class="fas fa-plus-circle"></i> Open LMS Enrollment
            </a>
        </div>
    </div>

    {{-- Popularity vs Recent Enrollments --}}
    <div class="row g-4 mb-5">
        <div class="col-12 col-lg-5">
            <div class="dark-panel-card p-4 h-100 skeleton-item">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="fw-bold text-white mb-0">Course Popularity &amp; Velocity</h5>
                        <small style="color: var(--nc-text-muted);">Real-time enrollment distribution</small>
                    </div>
                    <span class="badge bg-dark border border-secondary text-secondary">Active</span>
                </div>

                <div class="d-flex flex-column gap-3 mb-4">
                    @forelse($coursesPopularity as $c)
                        @php
                            $pct = $maxStudentsInCourse > 0 ? round(($c->students_count / $maxStudentsInCourse) * 100) : 0;
                        @endphp
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-semibold text-white small">{{ $c->name }}</span>
                                <span style="color: var(--nc-text-muted); font-size: .78rem;">{{ $c->students_count }} ({{ $pct }}%)</span>
                            </div>
                            <div class="progress-track-coral">
                                <div class="progress-fill-coral" style="width: {{ max(10, $pct) }}%;"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-secondary text-center py-4">No course popularity data available.</div>
                    @endforelse
                </div>

                <div class="p-3 rounded-3 mt-auto" style="background: rgba(255,255,255,.03); border: 1px solid var(--nc-border-dark);">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="fas fa-lightbulb" style="color: var(--nc-amber);"></i>
                        <span class="fw-bold text-white small">Enrollment Intelligence</span>
                    </div>
                    <p style="color: var(--nc-text-muted); font-size: .78rem;" class="mb-0">
                        @if($coursesPopularity->isNotEmpty())
                            <strong class="text-white">{{ $coursesPopularity->first()->name }}</strong> is the highest performing program with {{ $coursesPopularity->first()->students_count }} active learners.
                        @else
                            Regularly review enrollment metrics to optimize faculty allocation.
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-7">
            <div class="dark-panel-card p-4 h-100 skeleton-item">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="fw-bold text-white mb-0">Recent Student Enrollments</h5>
                        <small style="color: var(--nc-text-muted);">Latest registered learners in system</small>
                    </div>
                    <a href="{{ route('students.index') }}" class="section-link">View All &rarr;</a>
                </div>

                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Course</th>
                                <th>Admission ID</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentStudents as $st)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div style="width: 32px; height: 32px; border-radius: 8px; background: var(--nc-coral-soft); color: var(--nc-coral); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: .72rem;">
                                                {{ strtoupper(substr($st->first_name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="text-white fw-bold small">{{ $st->first_name }} {{ $st->last_name }}</div>
                                                <small style="color: var(--nc-text-muted); font-size: .7rem;">{{ $st->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge-coral-soft">{{ $st->course?->name ?? 'Unassigned' }}</span></td>
                                    <td>
                                        <span class="badge bg-dark border border-secondary text-secondary font-monospace" style="font-size: .7rem;">
                                            {{ $st->admission_no }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success bg-opacity-25 text-success" style="font-size: .7rem;">Active</span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('students.show', $st->id) }}" class="btn btn-sm btn-outline-light py-1 px-2">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-secondary py-4">No recent student registrations.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Operational Row --}}
    <div class="row g-4 mb-5">
        <div class="col-12 col-md-4">
            <div class="dark-panel-card p-4 skeleton-item d-flex justify-content-between align-items-center">
                <div>
                    <span class="stat-label">Attendance Logs Today</span>
                    <h3 class="fw-bold text-white mb-1 mt-1">{{ number_format($attendanceCount) }} Logs</h3>
                    <a href="{{ route('attendances.index') }}" class="section-link" style="font-size: .78rem;">View Attendance Ledger &rarr;</a>
                </div>
                <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(34,197,94,.12); color: var(--nc-green); display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                    <i class="fas fa-calendar-check"></i>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="dark-panel-card p-4 skeleton-item d-flex justify-content-between align-items-center">
                <div>
                    <span class="stat-label">Staff Hours (10 to 5)</span>
                    <h3 class="fw-bold text-white mb-1 mt-1">{{ number_format($workingHoursEmployeesCount) }} Active</h3>
                    <a href="{{ route('employee-attendances.index') }}" class="section-link" style="font-size: .78rem; color: var(--nc-blue);">Staff Attendance &rarr;</a>
                </div>
                <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(59,130,246,.12); color: var(--nc-blue); display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                    <i class="fas fa-id-badge"></i>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="dark-panel-card p-4 skeleton-item d-flex justify-content-between align-items-center">
                <div>
                    <span class="stat-label">System Cache &amp; Health</span>
                    <h3 class="fw-bold text-white mb-1 mt-1">Operational</h3>
                    <form action="{{ route('clear-cache') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm p-0 border-0 text-warning text-decoration-underline" style="background: transparent; font-size: .78rem;">
                            <i class="fas fa-broom me-1"></i> Clear Cache Now
                        </button>
                    </form>
                </div>
                <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(245,158,11,.12); color: var(--nc-amber); display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                    <i class="fas fa-shield-virus"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Navigation --}}
    <div class="dark-panel-card p-4 mb-4 skeleton-item">
        <h5 class="fw-bold text-white mb-3">
            <i class="fas fa-th-large me-2" style="color: var(--nc-coral);"></i> Quick Navigation Shortcuts
        </h5>
        <div class="row g-2">
            <div class="col-6 col-md-3 col-lg-2">
                <a href="{{ route('students.index') }}" class="quick-nav-btn">
                    <i class="fas fa-user-graduate" style="color: var(--nc-coral);"></i>
                    <span>Students</span>
                </a>
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <a href="{{ route('employees.index') }}" class="quick-nav-btn">
                    <i class="fas fa-users-cog" style="color: var(--nc-blue);"></i>
                    <span>Staff Members</span>
                </a>
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <a href="{{ route('courses.index') }}" class="quick-nav-btn">
                    <i class="fas fa-book-open" style="color: var(--nc-green);"></i>
                    <span>Courses</span>
                </a>
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <a href="{{ route('fee_invoices.index') }}" class="quick-nav-btn">
                    <i class="fas fa-receipt" style="color: var(--nc-amber);"></i>
                    <span>Fee Receipts</span>
                </a>
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <a href="{{ route('attendances.live') }}" class="quick-nav-btn">
                    <i class="fas fa-fingerprint" style="color: #ef4444;"></i>
                    <span>Live Biometric</span>
                </a>
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <a href="{{ route('messages.full') }}" class="quick-nav-btn">
                    <i class="fas fa-comments" style="color: #8b5cf6;"></i>
                    <span>NetChat Suite</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Embedded Messages Widget --}}
    <div class="w-full mt-4">
        @include('messages.widget')
    </div>

</div>

<script>
    function toggleDashboardSkeleton() {
        document.getElementById('executiveDashboard').classList.toggle('skeleton-active');
    }
</script>
@endsection