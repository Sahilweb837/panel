@extends('layouts.app')

@section('title', 'Welcome Back - Netcoder LMS')
@section('page-title', 'Student Dashboard')

@section('content')
<style>
    :root {
        --nc-bg-dark: #0f0f12;
        --nc-surface-card: #18181c;
        --nc-surface-elevated: #202026;
        --nc-border: rgba(255, 255, 255, 0.08);
        --nc-coral: #ff5733;
        --nc-coral-gradient: linear-gradient(135deg, #ff5733, #f0441d);
        --nc-coral-hover: #ff6a47;
        --nc-text-muted: #9494a0;
        --nc-text-light: #f5f5f8;
    }

    body {
        background-color: var(--nc-bg-dark) !important;
        color: var(--nc-text-light) !important;
        font-family: 'Inter', -apple-system, sans-serif;
    }

    .lms-dashboard {
        max-width: 1360px;
        margin: 0 auto;
        padding: 20px 24px 60px;
    }

    .breadcrumb-dark {
        color: var(--nc-text-muted);
        font-size: 0.82rem;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .breadcrumb-dark a {
        color: var(--nc-text-muted);
        text-decoration: none;
    }

    .breadcrumb-dark a:hover {
        color: #fff;
    }

    .welcome-title {
        font-size: 2.2rem;
        font-weight: 800;
        color: #ffffff;
        letter-spacing: -0.5px;
        margin-bottom: 6px;
    }

    .welcome-subtitle {
        color: var(--nc-text-muted);
        font-size: 0.95rem;
        margin-bottom: 24px;
    }

    .section-label {
        font-size: 0.88rem;
        font-weight: 700;
        color: var(--nc-coral);
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-heading-dark {
        font-size: 1.25rem;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .card-dark {
        background: var(--nc-surface-card);
        border: 1px solid var(--nc-border);
        border-radius: 18px;
        padding: 22px;
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.2s;
        position: relative;
        overflow: hidden;
    }

    .card-dark:hover {
        border-color: rgba(255, 87, 51, 0.3);
        transform: translateY(-2px);
    }

    .course-progress-bar {
        height: 6px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 999px;
        overflow: hidden;
        margin: 14px 0 16px;
    }

    .course-progress-fill {
        height: 100%;
        background: var(--nc-coral);
        border-radius: 999px;
        transition: width 0.8s ease-in-out;
    }

    .btn-resume {
        background: var(--nc-coral-gradient);
        color: #fff !important;
        font-weight: 700;
        font-size: 0.9rem;
        padding: 12px;
        border-radius: 12px;
        border: none;
        width: 100%;
        display: block;
        text-align: center;
        text-decoration: none;
        box-shadow: 0 4px 14px rgba(255, 87, 51, 0.3);
        transition: all 0.2s;
    }

    .btn-resume:hover {
        background: linear-gradient(135deg, var(--nc-coral-hover), #ff5733);
        box-shadow: 0 6px 20px rgba(255, 87, 51, 0.5);
        transform: translateY(-1px);
    }

    /* Metric Box */
    .metric-card {
        background: var(--nc-surface-card);
        border: 1px solid var(--nc-border);
        border-radius: 16px;
        padding: 20px;
    }

    .metric-card .metric-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        color: var(--nc-text-muted);
        font-size: 0.82rem;
        font-weight: 600;
        margin-bottom: 12px;
    }

    .metric-card .metric-value {
        font-size: 1.85rem;
        font-weight: 800;
        color: #ffffff;
        line-height: 1;
        margin-bottom: 6px;
    }

    .metric-card .metric-desc {
        color: var(--nc-text-muted);
        font-size: 0.78rem;
    }

    /* Course Grid in Recommended */
    .rec-course-card {
        background: var(--nc-surface-card);
        border: 1px solid var(--nc-border);
        border-radius: 18px;
        padding: 16px;
        display: flex;
        gap: 16px;
        align-items: center;
        transition: all 0.25s;
    }

    .rec-course-card:hover {
        background: var(--nc-surface-elevated);
        border-color: rgba(255, 87, 51, 0.35);
    }

    .rec-course-thumb {
        width: 120px;
        height: 120px;
        border-radius: 14px;
        background: linear-gradient(135deg, #ff5733, #431407);
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    .rec-course-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .rec-course-info {
        flex-grow: 1;
    }

    /* Skeleton Loading System */
    .skeleton-mode .skeleton-elem {
        background: linear-gradient(90deg, #18181c 25%, #25252c 50%, #18181c 75%) !important;
        background-size: 200% 100% !important;
        animation: shimmer 1.5s infinite !important;
        color: transparent !important;
        border-color: transparent !important;
        pointer-events: none !important;
    }

    .skeleton-mode .skeleton-elem * {
        visibility: hidden !important;
    }

    @keyframes shimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
</style>

<div class="lms-dashboard" id="lmsDashboard">

    {{-- Breadcrumb --}}
    <div class="breadcrumb-dark">
        <a href="{{ route('dashboard') }}">Home</a>
        <span>/</span>
        <span class="text-white">Dashboard</span>
    </div>

    {{-- Welcome Title & Toggle Skeleton --}}
    <div class="d-flex justify-content-between align-items-center mb-1">
        <h1 class="welcome-title">Welcome Back, {{ $student->first_name }}</h1>
        <button type="button" class="btn btn-sm btn-outline-secondary text-secondary border-secondary px-3 py-1 rounded-pill" onclick="toggleSkeletonView()">
            <i class="fas fa-layer-group me-1"></i> Skeleton Loading
        </button>
    </div>
    <p class="welcome-subtitle">Pick up where you left off and keep making progress.</p>

    {{-- Section: Continue Learning (from Screenshot) --}}
    <div class="section-label">
        <i class="fas fa-play-circle"></i> Continue Learning
    </div>

    <div class="row g-4 mb-5">
        {{-- Card 1: Active Student Course --}}
        @php
            $activeCourseName = $student->course ? $student->course->name : 'Web Development';
            $activeCourseDuration = $student->course ? $student->course->duration : '3 Months';
        @endphp
        <div class="col-12 col-lg-6">
            <div class="card-dark skeleton-elem">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(255,87,51,0.12); color: var(--nc-coral); display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                            <i class="fas fa-paint-brush"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold text-white mb-0">{{ $activeCourseName }}</h4>
                            <small style="color: var(--nc-text-muted);">Development | Core Track</small>
                        </div>
                    </div>
                    <small style="color: var(--nc-text-muted); font-size: 0.75rem;">Last Accessed: Yesterday</small>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <small style="color: var(--nc-text-muted); font-size: 0.78rem;">Course Progress</small>
                    <small class="fw-bold" style="color: var(--nc-coral);">{{ $attendancePercentage > 0 ? $attendancePercentage : 50 }}%</small>
                </div>

                <div class="course-progress-bar">
                    <div class="course-progress-fill" style="width: {{ $attendancePercentage > 0 ? $attendancePercentage : 50 }}%;"></div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <small style="color: var(--nc-text-muted); font-size: 0.78rem;"><i class="fas fa-book-open me-1"></i> 25/50 Lessons</small>
                    <small style="color: var(--nc-text-muted); font-size: 0.78rem;"><i class="fas fa-clock me-1"></i> {{ $activeCourseDuration }}</small>
                </div>

                <a href="{{ route('student.attendance.capture') }}" class="btn-resume">
                    Resume Course
                </a>
            </div>
        </div>

        {{-- Card 2: Secondary / Training Course Track --}}
        <div class="col-12 col-lg-6">
            <div class="card-dark skeleton-elem">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(59,130,246,0.12); color: #3b82f6; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                            <i class="fas fa-code"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold text-white mb-0">Practical Coding & Projects</h4>
                            <small style="color: var(--nc-text-muted);">Full Stack | Lab Work</small>
                        </div>
                    </div>
                    <small style="color: var(--nc-text-muted); font-size: 0.75rem;">Last Accessed: 01 Nov, 2026</small>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <small style="color: var(--nc-text-muted); font-size: 0.78rem;">Course Progress</small>
                    <small class="fw-bold" style="color: var(--nc-coral);">60%</small>
                </div>

                <div class="course-progress-bar">
                    <div class="course-progress-fill" style="width: 60%;"></div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <small style="color: var(--nc-text-muted); font-size: 0.78rem;"><i class="fas fa-tasks me-1"></i> 18/30 Assignments</small>
                    <small style="color: var(--nc-text-muted); font-size: 0.78rem;"><i class="fas fa-calendar-check me-1"></i> Live Schedule</small>
                </div>

                <a href="{{ route('messages.index') }}" class="btn-resume" style="background: linear-gradient(135deg, #ff5733, #e0441d);">
                    Resume Project Lab
                </a>
            </div>
        </div>
    </div>

    {{-- Section: Learning Overview (from Screenshot) --}}
    <h3 class="section-heading-dark">
        <span>Learning Overview</span>
    </h3>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="metric-card skeleton-elem">
                <div class="metric-header">
                    <span>Enrolled Courses</span>
                    <i class="fas fa-book-bookmark" style="color: #ff5733;"></i>
                </div>
                <div class="metric-value">02</div>
                <div class="metric-desc">Active Courses</div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="metric-card skeleton-elem">
                <div class="metric-header">
                    <span>Completed Lessons</span>
                    <i class="fas fa-check-square" style="color: #10b981;"></i>
                </div>
                <div class="metric-value">{{ $presentDays > 0 ? $presentDays * 2 : 50 }}</div>
                <div class="metric-desc">Across All Tracks</div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="metric-card skeleton-elem">
                <div class="metric-header">
                    <span>Pending Tasks</span>
                    <i class="fas fa-clipboard-list" style="color: #f59e0b;"></i>
                </div>
                <div class="metric-value">05</div>
                <div class="metric-desc">Assignments To Submit</div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="metric-card skeleton-elem">
                <div class="metric-header">
                    <span>Certificates</span>
                    <i class="fas fa-award" style="color: #6366f1;"></i>
                </div>
                <div class="metric-value">02</div>
                <div class="metric-desc">Earned Certificates</div>
            </div>
        </div>
    </div>

    {{-- Upcoming Class & Assignments (from Screenshot) --}}
    <div class="row g-4 mb-5">
        <div class="col-12 col-md-6">
            <div class="card-dark skeleton-elem">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge" style="background: rgba(255, 87, 51, 0.15); color: var(--nc-coral); font-size: 0.72rem; font-weight: 700;">
                        Upcoming Class
                    </span>
                </div>
                <h4 class="fw-bold text-white mb-1">{{ $activeCourseName }}</h4>
                <p style="color: var(--nc-text-muted); font-size: 0.88rem;" class="mb-3">HTML Forms, Backend API & Validation</p>
                <div class="d-flex align-items-center justify-content-between pt-2 border-top border-secondary border-opacity-25">
                    <small style="color: var(--nc-text-muted);"><i class="fas fa-clock me-1"></i> Monday, 12:00 PM</small>
                    <a href="{{ route('student.attendance.capture') }}" style="color: var(--nc-coral); font-weight: 700; text-decoration: none; font-size: 0.85rem;">
                        View Schedule &rarr;
                    </a>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="card-dark skeleton-elem">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge" style="background: rgba(59, 130, 246, 0.15); color: #3b82f6; font-size: 0.72rem; font-weight: 700;">
                        Assignments
                    </span>
                </div>
                <h4 class="fw-bold text-white mb-1">Full-Stack Poster & UI Project</h4>
                <p style="color: var(--nc-text-muted); font-size: 0.88rem;" class="mb-3">Practical Design & Code Validation Task</p>
                <div class="d-flex align-items-center justify-content-between pt-2 border-top border-secondary border-opacity-25">
                    <small style="color: var(--nc-text-muted);"><i class="fas fa-calendar-alt me-1"></i> Due: Wednesday</small>
                    <a href="{{ route('messages.index') }}" style="color: var(--nc-coral); font-weight: 700; text-decoration: none; font-size: 0.85rem;">
                        View Assignment &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Automated Fee Ledger Status --}}
    <div class="card-dark p-4 mb-5 skeleton-elem">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <div>
                <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981; font-weight: 700;">
                    <i class="fas fa-file-invoice-dollar me-1"></i> Auto-Generated Fee Ledger
                </span>
                <h4 class="fw-bold text-white mb-0 mt-1">Course Invoicing & Payment Receipts</h4>
            </div>
            <div>
                <span class="badge bg-dark border border-secondary text-white px-3 py-2">
                    Admission ID: {{ $student->admission_no }}
                </span>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.03); border: 1px solid var(--nc-border);">
                    <small style="color: var(--nc-text-muted);">Total Course Fee</small>
                    <div class="fs-4 fw-bold text-white">₹{{ number_format($totalFees) }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 rounded-3" style="background: rgba(16,185,129,0.06); border: 1px solid rgba(16,185,129,0.2);">
                    <small class="text-success">Paid to Date</small>
                    <div class="fs-4 fw-bold text-success">₹{{ number_format($coursePaid) }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 rounded-3" style="background: rgba(239,68,68,0.06); border: 1px solid rgba(239,68,68,0.2);">
                    <small class="text-danger">Balance Due</small>
                    <div class="fs-4 fw-bold text-danger">₹{{ number_format($remainingCourseFee) }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section: Recommended Courses (from Screenshot) --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="section-heading-dark mb-0">Recommended Courses</h3>
        <a href="{{ route('student.select-course') }}" style="color: var(--nc-coral); font-weight: 700; font-size: 0.85rem; text-decoration: none;">
            View All &rarr;
        </a>
    </div>

    <div class="row g-4">
        @php
            $recList = [
                ['title' => 'Advanced Graphic Design', 'cat' => 'Design | Advanced', 'lessons' => '60 Lessons', 'dur' => '10h 30m'],
                ['title' => 'Digital Marketing', 'cat' => 'Marketing | Beginner', 'lessons' => '30 Lessons', 'dur' => '4h 30m'],
                ['title' => 'UI/UX Design Masterclass', 'cat' => 'Design | Intermediate', 'lessons' => '40 Lessons', 'dur' => '6h 30m'],
                ['title' => 'Full-Stack Web Development', 'cat' => 'Development | Advanced', 'lessons' => '68 Lessons', 'dur' => '12h 30m'],
            ];
        @endphp

        @foreach($recList as $rec)
            <div class="col-12 col-md-6">
                <div class="rec-course-card skeleton-elem">
                    <div class="rec-course-thumb">
                        <i class="fas fa-graduation-cap text-white fs-1 opacity-75"></i>
                    </div>
                    <div class="rec-course-info">
                        <h5 class="fw-bold text-white mb-1">{{ $rec['title'] }}</h5>
                        <small style="color: var(--nc-text-muted); display: block;" class="mb-2">{{ $rec['cat'] }}</small>
                        <div class="d-flex align-items-center gap-3 text-secondary small mb-3">
                            <span><i class="fas fa-book-open me-1"></i> {{ $rec['lessons'] }}</span>
                            <span><i class="fas fa-clock me-1"></i> {{ $rec['dur'] }}</span>
                        </div>
                        <a href="{{ route('student.select-course') }}" class="btn btn-sm btn-resume py-2 px-3 d-inline-block w-auto" style="font-size: 0.82rem;">
                            View Course
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>

<script>
    function toggleSkeletonView() {
        const root = document.getElementById('lmsDashboard');
        root.classList.toggle('skeleton-mode');
    }
</script>
@endsection
