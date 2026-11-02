@extends('layouts.app')

@section('title', 'Executive Operations & LMS Dashboard - Netcoder')
@section('page-title', 'Executive Overview')

@section('content')
<style>
    :root {
        --nc-bg-dark: #0f0f12;
        --nc-card-dark: #18181c;
        --nc-card-hover: #1f1f26;
        --nc-border-dark: rgba(255, 255, 255, 0.08);
        --nc-coral: #ff5733;
        --nc-coral-gradient: linear-gradient(135deg, #ff5733, #ea3e16);
        --nc-coral-glow: rgba(255, 87, 51, 0.28);
        --nc-text-muted: #8f8f9d;
        --nc-text-light: #f5f5f9;
    }

    body {
        background-color: var(--nc-bg-dark) !important;
        color: var(--nc-text-light) !important;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    .dashboard-container {
        max-width: 1440px;
        margin: 0 auto;
        padding: 24px;
    }

    .dark-panel-card {
        background: var(--nc-card-dark);
        border: 1px solid var(--nc-border-dark);
        border-radius: 18px;
        transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.25s, box-shadow 0.25s;
    }

    .dark-panel-card:hover {
        border-color: rgba(255, 87, 51, 0.32);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
        transform: translateY(-2px);
    }

    .btn-coral-primary {
        background: var(--nc-coral-gradient);
        color: #fff !important;
        font-weight: 700;
        font-size: 0.88rem;
        padding: 10px 20px;
        border-radius: 10px;
        border: none;
        box-shadow: 0 4px 14px var(--nc-coral-glow);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .btn-coral-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 22px rgba(255, 87, 51, 0.5);
        background: linear-gradient(135deg, #ff6b47, #ff5733);
    }

    .badge-coral-soft {
        background: rgba(255, 87, 51, 0.12);
        color: var(--nc-coral);
        border: 1px solid rgba(255, 87, 51, 0.22);
        font-size: 0.75rem;
        font-weight: 700;
        border-radius: 8px;
        padding: 4px 10px;
    }

    /* Skeleton Loading Shimmer Animation */
    .skeleton-active .skeleton-item {
        background: linear-gradient(90deg, #18181c 25%, #25252c 50%, #18181c 75%) !important;
        background-size: 200% 100% !important;
        animation: ncShimmer 1.5s infinite !important;
        color: transparent !important;
        border-color: transparent !important;
        pointer-events: none !important;
    }

    .skeleton-active .skeleton-item * {
        visibility: hidden !important;
    }

    @keyframes ncShimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    .progress-track-coral {
        height: 6px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 999px;
        overflow: hidden;
    }

    .progress-fill-coral {
        height: 100%;
        background: var(--nc-coral);
        border-radius: 999px;
        transition: width 0.7s ease;
    }

    .custom-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 6px;
    }

    .custom-table thead th {
        color: var(--nc-text-muted);
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        padding: 10px 14px;
        border: none;
    }

    .custom-table tbody tr {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--nc-border-dark);
        border-radius: 12px;
        transition: background 0.2s, transform 0.2s;
    }

    .custom-table tbody tr:hover {
        background: rgba(255, 255, 255, 0.04);
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
</style>

<div class="dashboard-container" id="executiveDashboard">

    {{-- Breadcrumb --}}
    <div class="d-flex align-items-center gap-2 mb-2 text-secondary small">
        <span style="color: var(--nc-text-muted);">Home</span>
        <span style="color: var(--nc-text-muted);">/</span>
        <span class="text-white fw-bold">Executive Dashboard</span>
    </div>

    {{-- Header Banner & Action Buttons --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge-coral-soft">
                    <i class="fas fa-bolt me-1"></i> Executive Pulse
                </span>
                <span style="color: var(--nc-text-muted); font-size: 0.8rem;">System Logged &amp; Real-time Connected</span>
            </div>
            <h1 class="h2 fw-bold text-white mb-1">Welcome Back</h1>
            <p style="color: var(--nc-text-muted); font-size: 0.92rem;" class="mb-0">
                Pick up where you left off and keep making progress across academy operations and online LMS tracks.
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button type="button" class="btn btn-sm btn-outline-secondary text-secondary border-secondary px-3 py-2 rounded-3" onclick="toggleDashboardSkeleton()">
                <i class="fas fa-magic me-1"></i> Skeleton Loading
            </button>
            <a href="{{ route('lms.index') }}" class="btn-coral-primary">
                <i class="fas fa-graduation-cap"></i>
                <span>LMS Hub &amp; Auto-Fee</span>
            </a>
            <a href="{{ route('reports.index') }}" class="btn btn-outline-light px-3 py-2 rounded-3 text-decoration-none">
                <i class="fas fa-download me-1"></i> Export Report
            </a>
        </div>
    </div>

    {{-- Section: Continue Learning & Active Operations (Matching Screenshot Pattern) --}}
    <div class="mb-2 d-flex justify-content-between align-items-center">
        <span style="color: var(--nc-coral); font-weight: 700; font-size: 0.88rem;">
            <i class="fas fa-play-circle me-1"></i> Active Learning &amp; Course Tracks
        </span>
        <a href="{{ route('courses.index') }}" style="color: var(--nc-coral); font-size: 0.82rem; font-weight: 600; text-decoration: none;">
            View All Courses &rarr;
        </a>
    </div>

    <div class="row g-4 mb-5">
        {{-- Card 1: Top Popular Course --}}
        @php
            $firstCourse = $coursesPopularity->first();
            $secondCourse = $coursesPopularity->skip(1)->first();
            $firstPct = ($firstCourse && $maxStudentsInCourse > 0) ? round(($firstCourse->students_count / $maxStudentsInCourse) * 100) : 50;
            $secondPct = ($secondCourse && $maxStudentsInCourse > 0) ? round(($secondCourse->students_count / $maxStudentsInCourse) * 100) : 60;
        @endphp
        <div class="col-12 col-lg-6">
            <div class="dark-panel-card p-4 skeleton-item">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(255,87,51,0.12); color: var(--nc-coral); display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                            <i class="fas fa-paint-brush"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold text-white mb-0">{{ $firstCourse?->name ?? 'Graphic Design' }}</h4>
                            <small style="color: var(--nc-text-muted);">Design &amp; Creative | Primary Track</small>
                        </div>
                    </div>
                    <small style="color: var(--nc-text-muted); font-size: 0.75rem;">Active Students: {{ $firstCourse?->students_count ?? 25 }}</small>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <small style="color: var(--nc-text-muted); font-size: 0.78rem;">Course Enrollment Velocity</small>
                    <small class="fw-bold" style="color: var(--nc-coral);">{{ $firstPct }}% Capacity</small>
                </div>

                <div class="progress-track-coral my-2">
                    <div class="progress-fill-coral" style="width: {{ $firstPct }}%;"></div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <small style="color: var(--nc-text-muted); font-size: 0.78rem;"><i class="fas fa-user-graduate me-1"></i> {{ $firstCourse?->students_count ?? 25 }} Enrolled</small>
                    <small style="color: var(--nc-text-muted); font-size: 0.78rem;"><i class="fas fa-tag me-1"></i> ₹{{ number_format($firstCourse?->fee ?? 15000) }}</small>
                </div>

                <a href="{{ route('lms.index') }}" class="btn-coral-primary w-100 justify-content-center text-center">
                    Manage Course in LMS
                </a>
            </div>
        </div>

        {{-- Card 2: Web Development / Tech Track --}}
        <div class="col-12 col-lg-6">
            <div class="dark-panel-card p-4 skeleton-item">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(59,130,246,0.12); color: #3b82f6; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                            <i class="fas fa-code"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold text-white mb-0">{{ $secondCourse?->name ?? 'Web Development' }}</h4>
                            <small style="color: var(--nc-text-muted);">Engineering | Full-Stack Track</small>
                        </div>
                    </div>
                    <small style="color: var(--nc-text-muted); font-size: 0.75rem;">Active Students: {{ $secondCourse?->students_count ?? 18 }}</small>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <small style="color: var(--nc-text-muted); font-size: 0.78rem;">Course Enrollment Velocity</small>
                    <small class="fw-bold" style="color: var(--nc-coral);">{{ $secondPct }}% Capacity</small>
                </div>

                <div class="progress-track-coral my-2">
                    <div class="progress-fill-coral" style="width: {{ $secondPct }}%;"></div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <small style="color: var(--nc-text-muted); font-size: 0.78rem;"><i class="fas fa-user-graduate me-1"></i> {{ $secondCourse?->students_count ?? 18 }} Enrolled</small>
                    <small style="color: var(--nc-text-muted); font-size: 0.78rem;"><i class="fas fa-tag me-1"></i> ₹{{ number_format($secondCourse?->fee ?? 25000) }}</small>
                </div>

                <a href="{{ route('lms.index') }}" class="btn-coral-primary w-100 justify-content-center text-center">
                    Manage Course in LMS
                </a>
            </div>
        </div>
    </div>

    {{-- Section: Learning & Operations Overview (4 Metric Cards matching Screenshot) --}}
    <h3 class="fw-bold text-white mb-3" style="font-size: 1.2rem;">Learning &amp; Operational Overview</h3>

    <div class="row g-3 mb-5">
        <div class="col-6 col-lg-3">
            <div class="dark-panel-card p-4 skeleton-item">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span style="color: var(--nc-text-muted); font-size: 0.78rem; text-transform: uppercase; font-weight: 700;">Enrolled Students</span>
                    <i class="fas fa-book-bookmark text-coral" style="color: var(--nc-coral);"></i>
                </div>
                <div class="h2 fw-bold text-white mb-1">{{ number_format($studentCount) }}</div>
                <div style="color: var(--nc-text-muted); font-size: 0.78rem;">Active Students in System</div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="dark-panel-card p-4 skeleton-item">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span style="color: var(--nc-text-muted); font-size: 0.78rem; text-transform: uppercase; font-weight: 700;">Total Revenue Collected</span>
                    <i class="fas fa-check-square text-success"></i>
                </div>
                <div class="h2 fw-bold text-white mb-1">₹{{ number_format($totalIncome) }}</div>
                <div style="color: #10b981; font-size: 0.78rem;">Receipts Successfully Paid</div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="dark-panel-card p-4 skeleton-item">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span style="color: var(--nc-text-muted); font-size: 0.78rem; text-transform: uppercase; font-weight: 700;">Pending Invoices</span>
                    <i class="fas fa-clipboard-list text-warning"></i>
                </div>
                <div class="h2 fw-bold text-white mb-1">₹{{ number_format($totalPendingFees) }}</div>
                <div style="color: #f59e0b; font-size: 0.78rem;">{{ $dueInvoices }} Unpaid Invoices Due</div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="dark-panel-card p-4 skeleton-item">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span style="color: var(--nc-text-muted); font-size: 0.78rem; text-transform: uppercase; font-weight: 700;">Active Staff &amp; Faculty</span>
                    <i class="fas fa-award text-info"></i>
                </div>
                <div class="h2 fw-bold text-white mb-1">{{ number_format($employeeCount) }}</div>
                <div style="color: #38bdf8; font-size: 0.78rem;">Allocated Across Programs</div>
            </div>
        </div>
    </div>

    {{-- Automated LMS Enrollment & Quick Fee Register Banner --}}
    <div class="dark-panel-card p-4 mb-5 skeleton-item" style="background: linear-gradient(135deg, #18181c, #22222b); border-color: rgba(255,87,51,0.25);">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <div style="width: 52px; height: 52px; border-radius: 14px; background: rgba(255,87,51,0.15); color: var(--nc-coral); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0;">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <div>
                    <span class="badge-coral-soft mb-1 d-inline-block">Automated Invoicing Engine</span>
                    <h4 class="fw-bold text-white mb-0">Enroll in LMS &amp; Automatically Register Fee Invoice</h4>
                    <p style="color: var(--nc-text-muted); font-size: 0.85rem;" class="mb-0">
                        When a student is enrolled into an LMS course, the system automatically creates their portal account and issues their official fee receipt into the ledger.
                    </p>
                </div>
            </div>
            <a href="{{ route('lms.index') }}" class="btn-coral-primary flex-shrink-0">
                <i class="fas fa-plus-circle"></i>
                <span>Open LMS Enrollment</span>
            </a>
        </div>
    </div>

    {{-- Two Column Grid: Popularity vs Recent Enrollments --}}
    <div class="row g-4 mb-5">
        {{-- Left: Course Popularity --}}
        <div class="col-12 col-lg-5">
            <div class="dark-panel-card p-4 h-100 skeleton-item">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="fw-bold text-white mb-0">Course Popularity &amp; Velocity</h5>
                        <small style="color: var(--nc-text-muted);">Real-time enrollment distribution</small>
                    </div>
                    <span class="badge bg-dark border border-secondary text-secondary">Active Stats</span>
                </div>

                <div class="d-flex flex-column gap-3 mb-4">
                    @forelse($coursesPopularity as $c)
                        @php
                            $pct = $maxStudentsInCourse > 0 ? round(($c->students_count / $maxStudentsInCourse) * 100) : 0;
                        @endphp
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-1 text-sm">
                                <span class="fw-semibold text-white">{{ $c->name }}</span>
                                <span style="color: var(--nc-text-muted); font-size: 0.8rem;">{{ $c->students_count }} Students ({{ $pct }}%)</span>
                            </div>
                            <div class="progress-track-coral">
                                <div class="progress-fill-coral" style="width: {{ max(10, $pct) }}%;"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-secondary text-center py-4">No course popularity data available.</div>
                    @endforelse
                </div>

                <div class="p-3 rounded-3 mt-auto" style="background: rgba(255,255,255,0.03); border: 1px solid var(--nc-border-dark);">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="fas fa-lightbulb text-warning"></i>
                        <span class="fw-bold text-white small">Enrollment Intelligence</span>
                    </div>
                    <p style="color: var(--nc-text-muted); font-size: 0.8rem;" class="mb-0">
                        @if($coursesPopularity->isNotEmpty())
                            <strong>{{ $coursesPopularity->first()->name }}</strong> is the highest performing program with {{ $coursesPopularity->first()->students_count }} active learners.
                        @else
                            Regularly review course enrollment metrics to optimize faculty allocation.
                        @endif
                    </p>
                </div>
            </div>
        </div>

        {{-- Right: Recent Students --}}
        <div class="col-12 col-lg-7">
            <div class="dark-panel-card p-4 h-100 skeleton-item">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="fw-bold text-white mb-0">Recent Student Enrollments</h5>
                        <small style="color: var(--nc-text-muted);">Latest registered learners in system</small>
                    </div>
                    <a href="{{ route('students.index') }}" style="color: var(--nc-coral); font-weight: 700; font-size: 0.82rem; text-decoration: none;">
                        View All Students &rarr;
                    </a>
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
                                            <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(255,87,51,0.15); color: var(--nc-coral); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.75rem;">
                                                {{ strtoupper(substr($st->first_name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="text-white fw-bold small">{{ $st->first_name }} {{ $st->last_name }}</div>
                                                <small style="color: var(--nc-text-muted); font-size: 0.72rem;">{{ $st->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge-coral-soft">{{ $st->course?->name ?? 'Unassigned' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-dark border border-secondary text-secondary font-monospace" style="font-size: 0.72rem;">
                                            {{ $st->admission_no }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success bg-opacity-25 text-success font-semibold" style="font-size: 0.72rem;">
                                            Active
                                        </span>
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

    {{-- Operational Row: Attendance Today, Staff Working Hours, Clear Cache --}}
    <div class="row g-4 mb-5">
        <div class="col-12 col-md-4">
            <div class="dark-panel-card p-4 skeleton-item d-flex justify-content-between align-items-center">
                <div>
                    <span style="color: var(--nc-text-muted); font-size: 0.78rem; text-transform: uppercase; font-weight: 700;">Attendance Logs Today</span>
                    <h3 class="fw-bold text-white mb-1 mt-1">{{ number_format($attendanceCount) }} Logs</h3>
                    <a href="{{ route('attendances.index') }}" style="color: var(--nc-coral); font-size: 0.8rem; font-weight: 600; text-decoration: none;">
                        View Attendance Ledger &rarr;
                    </a>
                </div>
                <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(16,185,129,0.12); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                    <i class="fas fa-calendar-check"></i>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="dark-panel-card p-4 skeleton-item d-flex justify-content-between align-items-center">
                <div>
                    <span style="color: var(--nc-text-muted); font-size: 0.78rem; text-transform: uppercase; font-weight: 700;">Staff Hours (10 to 5)</span>
                    <h3 class="fw-bold text-white mb-1 mt-1">{{ number_format($workingHoursEmployeesCount) }} Active</h3>
                    <a href="{{ route('employee-attendances.index') }}" style="color: #3b82f6; font-size: 0.8rem; font-weight: 600; text-decoration: none;">
                        Staff Attendance &rarr;
                    </a>
                </div>
                <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(59,130,246,0.12); color: #3b82f6; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                    <i class="fas fa-id-badge"></i>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="dark-panel-card p-4 skeleton-item d-flex justify-content-between align-items-center">
                <div>
                    <span style="color: var(--nc-text-muted); font-size: 0.78rem; text-transform: uppercase; font-weight: 700;">System Cache &amp; Health</span>
                    <h3 class="fw-bold text-white mb-1 mt-1">Operational</h3>
                    <form action="{{ route('clear-cache') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-warning py-1 px-2 border-0 p-0 text-warning text-decoration-underline" style="background: transparent;">
                            <i class="fas fa-broom me-1"></i> Clear Cache Now
                        </button>
                    </form>
                </div>
                <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(245,158,11,0.12); color: #f59e0b; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                    <i class="fas fa-shield-virus"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Shortcuts Row to All ERP Modules --}}
    <div class="dark-panel-card p-4 mb-4 skeleton-item">
        <h5 class="fw-bold text-white mb-3"><i class="fas fa-th-large me-2 text-coral" style="color: var(--nc-coral);"></i> Quick Navigation Shortcuts</h5>
        <div class="row g-2">
            <div class="col-6 col-md-3 col-lg-2">
                <a href="{{ route('students.index') }}" class="btn btn-dark w-100 p-3 text-start border-secondary border-opacity-25 rounded-3 d-flex flex-column gap-2 text-decoration-none">
                    <i class="fas fa-user-graduate fs-5 text-coral" style="color: var(--nc-coral);"></i>
                    <span class="text-white small fw-bold">Students</span>
                </a>
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <a href="{{ route('employees.index') }}" class="btn btn-dark w-100 p-3 text-start border-secondary border-opacity-25 rounded-3 d-flex flex-column gap-2 text-decoration-none">
                    <i class="fas fa-users-cog fs-5 text-info"></i>
                    <span class="text-white small fw-bold">Staff Members</span>
                </a>
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <a href="{{ route('courses.index') }}" class="btn btn-dark w-100 p-3 text-start border-secondary border-opacity-25 rounded-3 d-flex flex-column gap-2 text-decoration-none">
                    <i class="fas fa-book-open fs-5 text-success"></i>
                    <span class="text-white small fw-bold">Courses</span>
                </a>
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <a href="{{ route('fee_invoices.index') }}" class="btn btn-dark w-100 p-3 text-start border-secondary border-opacity-25 rounded-3 d-flex flex-column gap-2 text-decoration-none">
                    <i class="fas fa-receipt fs-5 text-warning"></i>
                    <span class="text-white small fw-bold">Fee Receipts</span>
                </a>
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <a href="{{ route('attendances.live') }}" class="btn btn-dark w-100 p-3 text-start border-secondary border-opacity-25 rounded-3 d-flex flex-column gap-2 text-decoration-none">
                    <i class="fas fa-fingerprint fs-5 text-danger"></i>
                    <span class="text-white small fw-bold">Live Biometric</span>
                </a>
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <a href="{{ route('messages.full') }}" class="btn btn-dark w-100 p-3 text-start border-secondary border-opacity-25 rounded-3 d-flex flex-column gap-2 text-decoration-none">
                    <i class="fas fa-comments fs-5 text-primary"></i>
                    <span class="text-white small fw-bold">NetChat Suite</span>
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
        const db = document.getElementById('executiveDashboard');
        db.classList.toggle('skeleton-active');
    }
</script>
@endsection
