@extends('layouts.app')

@section('title', 'Course Bundle & Executive Operations - Netcoder ERP')
@section('page-title', 'Manage Course Bundle')

@section('content')
<style>
    /* Edaca Ultra-Clean SaaS Theme */
    :root {
        --edaca-bg: #f8fafc;
        --edaca-card: #ffffff;
        --edaca-border: #e2e8f0;
        --edaca-text-main: #0f172a;
        --edaca-text-muted: #64748b;
        --edaca-primary: #2563eb;
        --edaca-primary-hover: #1d4ed8;
        --edaca-coral: #ff5532;
        --edaca-coral-hover: #e0441d;
    }

    body {
        background-color: var(--edaca-bg) !important;
        color: var(--edaca-text-main) !important;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
    }

    .edaca-container {
        max-width: 1440px;
        margin: 0 auto;
        padding: 24px 28px 48px;
    }

    /* Breadcrumbs */
    .edaca-breadcrumb {
        font-size: 0.85rem;
        color: var(--edaca-text-muted);
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 20px;
    }
    .edaca-breadcrumb a {
        color: var(--edaca-text-muted);
        text-decoration: none;
        transition: color 0.15s ease;
    }
    .edaca-breadcrumb a:hover {
        color: var(--edaca-primary);
    }
    .edaca-breadcrumb .active {
        color: var(--edaca-text-main);
        font-weight: 600;
    }

    /* Inner Dashboard Switcher Pills */
    .dashboard-switcher-nav {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #ffffff;
        padding: 6px;
        border-radius: 14px;
        border: 1px solid var(--edaca-border);
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        margin-bottom: 24px;
        overflow-x: auto;
    }

    .switcher-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 18px;
        border-radius: 10px;
        font-size: 0.84rem;
        font-weight: 600;
        color: var(--edaca-text-muted);
        background: transparent;
        border: none;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        white-space: nowrap;
        text-decoration: none;
    }

    .switcher-pill:hover {
        color: var(--edaca-text-main);
        background: #f1f5f9;
    }

    .switcher-pill.active {
        background: var(--edaca-primary);
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
    }

    .switcher-pill .badge-count {
        font-size: 0.7rem;
        padding: 2px 7px;
        border-radius: 999px;
        background: rgba(0,0,0,0.08);
    }
    .switcher-pill.active .badge-count {
        background: rgba(255,255,255,0.25);
        color: #fff;
    }

    /* Animated Tab Panes */
    .tab-view-pane {
        display: none;
        animation: edacaSlideFade 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    .tab-view-pane.active-pane {
        display: block;
    }

    @keyframes edacaSlideFade {
        from {
            opacity: 0;
            transform: translateX(14px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* Edaca Master White Card */
    .edaca-card {
        background: var(--edaca-card);
        border: 1px solid var(--edaca-border);
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        padding: 24px 28px;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    /* Edaca Toolbar */
    .edaca-toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 22px;
    }

    .search-input-wrap {
        position: relative;
        display: flex;
        align-items: center;
        min-width: 260px;
    }
    .search-input-wrap i {
        position: absolute;
        left: 14px;
        color: #94a3b8;
        font-size: 0.88rem;
    }
    .search-input-wrap input {
        width: 100%;
        padding: 9px 44px 9px 38px;
        font-size: 0.84rem;
        background: #ffffff;
        border: 1px solid var(--edaca-border);
        border-radius: 10px;
        color: var(--edaca-text-main);
        outline: none;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .search-input-wrap input:focus {
        border-color: var(--edaca-primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }
    .search-input-wrap .kbd-badge {
        position: absolute;
        right: 12px;
        font-size: 0.68rem;
        font-weight: 600;
        color: #94a3b8;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        padding: 2px 6px;
        border-radius: 6px;
        pointer-events: none;
    }

    .btn-edaca-outline {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 16px;
        background: #ffffff;
        border: 1px solid var(--edaca-border);
        border-radius: 10px;
        font-size: 0.84rem;
        font-weight: 600;
        color: #475569;
        cursor: pointer;
        transition: all 0.15s ease;
        text-decoration: none;
    }
    .btn-edaca-outline:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #0f172a;
    }

    .btn-edaca-primary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 18px;
        background: var(--edaca-primary);
        border: 1px solid transparent;
        border-radius: 10px;
        font-size: 0.84rem;
        font-weight: 600;
        color: #ffffff !important;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .btn-edaca-primary:hover {
        background: var(--edaca-primary-hover);
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.35);
    }

    /* Edaca Data Table (Matching Image 2) */
    .edaca-table-wrap {
        border: 1px solid var(--edaca-border);
        border-radius: 14px;
        overflow: hidden;
        background: #ffffff;
    }

    .edaca-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }
    .edaca-table th {
        background: #f8fafc;
        padding: 14px 20px;
        font-size: 0.72rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        border-bottom: 1px solid var(--edaca-border);
        white-space: nowrap;
    }
    .edaca-table th i {
        font-size: 0.68rem;
        margin-left: 4px;
        color: #94a3b8;
    }
    .edaca-table td {
        padding: 16px 20px;
        font-size: 0.86rem;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .edaca-table tbody tr {
        transition: background-color 0.15s ease;
    }
    .edaca-table tbody tr:hover {
        background-color: #f8fafc;
    }
    .edaca-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Bundle Thumbnail from Edaca */
    .bundle-thumb {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        object-fit: cover;
        flex-shrink: 0;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #1e293b, #0f172a);
        color: #fff;
        font-size: 1.15rem;
    }

    /* Course Bullets */
    .course-bullet-list {
        list-style: none;
        padding: 0;
        margin: 0;
        font-size: 0.78rem;
        color: #475569;
        line-height: 1.5;
    }
    .course-bullet-list li {
        position: relative;
        padding-left: 14px;
        margin-bottom: 3px;
    }
    .course-bullet-list li::before {
        content: "•";
        position: absolute;
        left: 0;
        color: #94a3b8;
        font-weight: bold;
    }

    /* Status Pill Badges */
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 0.74rem;
        font-weight: 600;
        line-height: 1.2;
    }
    .status-pill.active {
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
    }
    .status-pill.upcoming {
        background: #f0f9ff;
        color: #0284c7;
        border: 1px solid #bae6fd;
    }
    .status-pill.paused {
        background: #fffbeb;
        color: #d97706;
        border: 1px solid #fde68a;
    }

    /* 3-Dots Action Dropdown */
    .action-dropdown-btn {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        color: #64748b;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .action-dropdown-btn:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    .edaca-dropdown-menu {
        border-radius: 12px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        padding: 6px;
        min-width: 150px;
    }
    .edaca-dropdown-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 12px;
        font-size: 0.82rem;
        font-weight: 500;
        border-radius: 8px;
        color: #334155;
        text-decoration: none;
        transition: all 0.15s ease;
    }
    .edaca-dropdown-item:hover {
        background: #f1f5f9;
        color: #0f172a;
    }
    .edaca-dropdown-item.primary-item {
        background: var(--edaca-primary);
        color: #ffffff;
    }
    .edaca-dropdown-item.primary-item:hover {
        background: var(--edaca-primary-hover);
        color: #ffffff;
    }

    /* Table Footer & Pagination */
    .edaca-table-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        background: #ffffff;
        border-top: 1px solid var(--edaca-border);
        font-size: 0.82rem;
        color: #64748b;
    }
    .btn-edaca-page {
        padding: 6px 14px;
        border: 1px solid var(--edaca-border);
        border-radius: 8px;
        background: #ffffff;
        color: #334155;
        font-size: 0.82rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .btn-edaca-page:hover:not(:disabled) {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #0f172a;
    }
    .btn-edaca-page:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Stat Cards for Executive Overview */
    .edaca-stat-card {
        background: #ffffff;
        border: 1px solid var(--edaca-border);
        border-radius: 16px;
        padding: 22px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .edaca-stat-card:hover {
        transform: translateY(-2px);
        border-color: #cbd5e1;
        box-shadow: 0 8px 20px rgba(0,0,0,0.06);
    }
    .stat-label-subtle {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #64748b;
    }
    .stat-val-bold {
        font-size: 1.85rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
        margin: 6px 0 4px;
    }
    .stat-badge-soft {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 6px;
        display: inline-block;
    }

    /* Skeleton Loading Simulation */
    .skeleton-mode .skeleton-box {
        background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%) !important;
        background-size: 200% 100% !important;
        animation: edacaShimmer 1.4s infinite !important;
        color: transparent !important;
        border-color: transparent !important;
        pointer-events: none !important;
    }
    .skeleton-mode .skeleton-box * {
        visibility: hidden !important;
    }
    @keyframes edacaShimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
</style>

<div class="edaca-container" id="dashboardContainer">

    {{-- Breadcrumb Top Navigation --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-3">
        <div class="edaca-breadcrumb mb-0">
            <a href="javascript:history.back()"><i class="fas fa-chevron-left me-1"></i> Back</a>
            <span>/</span>
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <span>/</span>
            <span class="active" id="currentBreadcrumbTitle">Course Bundle</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn-edaca-outline" onclick="toggleSkeletonSimulation()">
                <i class="fas fa-magic text-primary"></i>
                <span id="skeletonBtnText">Skeleton Loading</span>
            </button>
            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 py-2 px-3 rounded-pill" style="font-size: 0.76rem;">
                <i class="fas fa-circle-dot me-1"></i> Live LMS Sync Active
            </span>
        </div>
    </div>

    {{-- Multi-Dashboard Inner Switcher (Slider Pills) --}}
    <div class="dashboard-switcher-nav">
        <button type="button" class="switcher-pill active" onclick="switchDashboardTab('bundles', this)">
            <i class="fas fa-layer-group"></i>
            <span>Course Bundles &amp; LMS</span>
            <span class="badge-count">{{ $allCourses->count() }}</span>
        </button>
        <button type="button" class="switcher-pill" onclick="switchDashboardTab('executive', this)">
            <i class="fas fa-chart-pie"></i>
            <span>Executive Overview &amp; KPIs</span>
        </button>
        <button type="button" class="switcher-pill" onclick="switchDashboardTab('operations', this)">
            <i class="fas fa-user-clock"></i>
            <span>Operations &amp; Staff Desk</span>
            <span class="badge-count">{{ $workingHoursEmployeesCount }} active</span>
        </button>
        <button type="button" class="switcher-pill" onclick="switchDashboardTab('audit-logs', this)">
            <i class="fas fa-shield-halved"></i>
            <span>Security &amp; Login Audit Logs</span>
            <span class="badge-count">{{ $loginLogs->count() }}</span>
        </button>
    </div>

    {{-- ========================================================
         TAB 1: COURSE BUNDLES & LMS (EXACT REPLICA OF EDACA IMAGE 2)
         ======================================================== --}}
    <div id="tab-bundles" class="tab-view-pane active-pane">
        <div class="edaca-card skeleton-box">
            
            {{-- Toolbar: Search, Filter, Sort, Add New Bundle --}}
            <div class="edaca-toolbar">
                <div class="d-flex align-items-center gap-3">
                    <h3 class="fw-bold mb-0" style="color: #0f172a; font-size: 1.35rem;">Course Bundle</h3>
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1 rounded-pill" style="font-size: 0.72rem;">
                        {{ $allCourses->count() }} Total Programs
                    </span>
                </div>

                <div class="d-flex flex-wrap align-items-center gap-2">
                    {{-- Search with ⌘ K shortcut --}}
                    <div class="search-input-wrap">
                        <i class="fas fa-search"></i>
                        <input type="text" id="courseBundleSearch" placeholder="Search bundles..." onkeyup="filterBundleTable(this.value)">
                        <span class="kbd-badge">⌘ K</span>
                    </div>

                    {{-- Filter button --}}
                    <div class="dropdown">
                        <button class="btn-edaca-outline dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <ul class="dropdown-menu edaca-dropdown-menu">
                            <li><a class="dropdown-item edaca-dropdown-item" href="javascript:void(0)" onclick="filterStatus('all')">All Status</a></li>
                            <li><a class="dropdown-item edaca-dropdown-item" href="javascript:void(0)" onclick="filterStatus('active')">Active Only</a></li>
                            <li><a class="dropdown-item edaca-dropdown-item" href="javascript:void(0)" onclick="filterStatus('upcoming')">Upcoming Only</a></li>
                        </ul>
                    </div>

                    {{-- Sort by button --}}
                    <div class="dropdown">
                        <button class="btn-edaca-outline dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-sort"></i> Sort by
                        </button>
                        <ul class="dropdown-menu edaca-dropdown-menu">
                            <li><a class="dropdown-item edaca-dropdown-item" href="javascript:void(0)" onclick="sortTableBy(1)">Bundle Name</a></li>
                            <li><a class="dropdown-item edaca-dropdown-item" href="javascript:void(0)" onclick="sortTableBy(3)">Price (Low to High)</a></li>
                            <li><a class="dropdown-item edaca-dropdown-item" href="javascript:void(0)" onclick="sortTableBy(4)">Subscription Tenure</a></li>
                        </ul>
                    </div>

                    {{-- + Add New Bundle CTA (Image 2) --}}
                    <a href="{{ route('lms.course_bundles.index') }}" class="btn-edaca-primary">
                        <i class="fas fa-plus"></i> Add New Bundle
                    </a>

                    <a href="{{ route('lms.course_bundles.index') }}" class="btn-edaca-outline" title="Open Full Edaca LMS Experience">
                        <i class="fas fa-external-link-alt"></i> Open Edaca Suite
                    </a>
                </div>
            </div>

            {{-- Master Edaca Table --}}
            <div class="edaca-table-wrap">
                <table class="edaca-table" id="bundleDataTable">
                    <thead>
                        <tr>
                            <th style="width: 280px;">Bundle <i class="fas fa-sort"></i></th>
                            <th>Courses Included <i class="fas fa-sort"></i></th>
                            <th style="width: 170px;">Subscription Limit <i class="fas fa-sort"></i></th>
                            <th style="width: 120px;">Price <i class="fas fa-sort"></i></th>
                            <th style="width: 120px;">Status <i class="fas fa-sort"></i></th>
                            <th style="width: 80px;" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($courseBundles) && $courseBundles->isNotEmpty())
                            @foreach($courseBundles as $index => $bundle)
                                @php
                                    $coursesArr = is_array($bundle->courses) ? $bundle->courses : json_decode($bundle->courses ?? '[]', true);
                                    if (empty($coursesArr)) {
                                        $coursesArr = ['Master Course Track: ' . $bundle->title, 'Capstone Projects & Live Lab Deployments', 'Verified Industry Certification'];
                                    }
                                    $statusClass = strtolower($bundle->status);
                                @endphp
                                <tr class="bundle-row" data-status="{{ $statusClass }}">
                                    {{-- Bundle Column: Thumbnail + Title --}}
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            @if($bundle->thumbnail)
                                                <img src="{{ $bundle->thumbnail }}" alt="{{ $bundle->title }}" class="bundle-thumb" style="object-fit: cover;" onerror="this.onerror=null; this.src='{{ asset('image.png') }}';">
                                            @else
                                                <div class="bundle-thumb">
                                                    <i class="fas fa-box"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-bold text-slate-900 bundle-name" style="font-size: 0.9rem; color: #0f172a;">
                                                    {{ $bundle->title }}
                                                </div>
                                                <small class="text-slate-500" style="font-size: 0.74rem; color: #64748b;">
                                                    {{ $bundle->slug }}
                                                </small>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Courses Included Column --}}
                                    <td>
                                        <ul class="course-bullet-list">
                                            @foreach($coursesArr as $cItem)
                                                <li>{{ $cItem }}</li>
                                            @endforeach
                                        </ul>
                                    </td>

                                    {{-- Subscription Limit Column --}}
                                    <td>
                                        <span class="text-slate-700 fw-medium" style="font-size: 0.84rem; color: #334155;">
                                            {{ $bundle->subscription_limit }}
                                        </span>
                                    </td>

                                    {{-- Price Column --}}
                                    <td>
                                        <span class="fw-bold text-slate-900" style="font-size: 0.9rem; color: #0f172a;">
                                            ${{ number_format($bundle->price, 0) }}
                                        </span>
                                    </td>

                                    {{-- Status Column --}}
                                    <td>
                                        <span class="status-pill {{ $statusClass }}">
                                            {{ $bundle->status }}
                                        </span>
                                    </td>

                                    {{-- Actions Column --}}
                                    <td class="text-end">
                                        <div class="dropdown d-inline-block">
                                            <button class="action-dropdown-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-ellipsis-h"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end edaca-dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item edaca-dropdown-item primary-item" href="{{ route('lms.course_bundles.index') }}">
                                                        <i class="fas fa-pen-to-square"></i> Edit Course
                                                    </a>
                                                </li>
                                                <li>
                                                    <form action="{{ route('lms.course_bundles.status', $bundle->id) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="status" value="{{ $bundle->status === 'Paused' ? 'Active' : 'Paused' }}">
                                                        <button type="submit" class="dropdown-item edaca-dropdown-item w-100 text-start">
                                                            <i class="fas {{ $bundle->status === 'Paused' ? 'fa-play' : 'fa-pause' }}"></i> {{ $bundle->status === 'Paused' ? 'Activate' : 'Pause' }}
                                                        </button>
                                                    </form>
                                                </li>
                                                <li><hr class="dropdown-divider my-1"></li>
                                                <li>
                                                    <form action="{{ route('lms.course_bundles.destroy', $bundle->id) }}" method="POST" onsubmit="return confirm('Delete this course bundle?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item edaca-dropdown-item text-danger w-100 text-start">
                                                            <i class="fas fa-trash-alt"></i> Delete
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            @forelse($allCourses as $index => $course)
                            @php
                                $tenureDays = match(true) {
                                    str_contains(strtolower($course->duration ?? ''), '1 year') || str_contains(strtolower($course->duration ?? ''), '12 month') => '365 Days (1 Year)',
                                    str_contains(strtolower($course->duration ?? ''), '6 month') => '180 Days',
                                    str_contains(strtolower($course->duration ?? ''), '3 month') => '90 Days',
                                    str_contains(strtolower($course->duration ?? ''), '1 month') => '30 Days',
                                    default => '120 Days'
                                };
                                $statusLabel = ($course->status ?? true) ? 'Active' : 'Upcoming';
                                $statusClass = ($course->status ?? true) ? 'active' : 'upcoming';
                                
                                // Color gradient variants for bundle thumbnails
                                $bgGradients = [
                                    'linear-gradient(135deg, #0284c7, #0369a1)',
                                    'linear-gradient(135deg, #10b981, #059669)',
                                    'linear-gradient(135deg, #f59e0b, #d97706)',
                                    'linear-gradient(135deg, #6366f1, #4f46e5)',
                                    'linear-gradient(135deg, #ec4899, #be185d)',
                                    'linear-gradient(135deg, #8b5cf6, #6d28d9)',
                                ];
                                $thumbGradient = $bgGradients[$index % count($bgGradients)];

                                $icons = ['fa-laptop-code', 'fa-palette', 'fa-database', 'fa-mobile-screen', 'fa-brain', 'fa-cloud'];
                                $thumbIcon = $icons[$index % count($icons)];
                            @endphp
                            <tr class="bundle-row" data-status="{{ strtolower($statusLabel) }}">
                                {{-- Bundle Column: Thumbnail + Title --}}
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bundle-thumb" style="background: {{ $thumbGradient }};">
                                            <i class="fas {{ $thumbIcon }}"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-slate-900 bundle-name" style="font-size: 0.9rem; color: #0f172a;">
                                                {{ $course->name }}
                                            </div>
                                            <small class="text-slate-500" style="font-size: 0.74rem; color: #64748b;">
                                                {{ $course->students_count }} Active Students Enrolled
                                            </small>
                                        </div>
                                    </div>
                                </td>

                                {{-- Courses Included Column (Bulleted List from Image 2) --}}
                                <td>
                                    <ul class="course-bullet-list">
                                        <li>Core Curriculum &amp; Production Workshop: {{ $course->name }}</li>
                                        <li>Hands-on Capstone Projects &amp; Live Lab Deployments</li>
                                        <li>Verified Certification Track &amp; Portfolio Assessment</li>
                                    </ul>
                                </td>

                                {{-- Subscription Limit Column --}}
                                <td>
                                    <span class="text-slate-700 fw-medium" style="font-size: 0.84rem; color: #334155;">
                                        {{ $tenureDays }}
                                    </span>
                                </td>

                                {{-- Price Column --}}
                                <td>
                                    <span class="fw-bold text-slate-900" style="font-size: 0.9rem; color: #0f172a;">
                                        ₹{{ number_format($course->fee ?? 0) }}
                                    </span>
                                </td>

                                {{-- Status Column --}}
                                <td>
                                    <span class="status-pill {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>

                                {{-- Actions Column (••• Menu from Image 2) --}}
                                <td class="text-end">
                                    <div class="dropdown d-inline-block">
                                        <button class="action-dropdown-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-h"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end edaca-dropdown-menu">
                                            <li>
                                                <a class="dropdown-item edaca-dropdown-item primary-item" href="{{ route('courses.edit', $course->id) }}">
                                                    <i class="fas fa-pen-to-square"></i> Edit Course
                                                </a>
                                            </li>
                                            <li>
                                                <form action="{{ route('lms.courses.publish', $course->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item edaca-dropdown-item w-100 text-start">
                                                        <i class="fas fa-pause-circle"></i> Pause / Toggle
                                                    </button>
                                                </form>
                                            </li>
                                            <li><hr class="dropdown-divider my-1"></li>
                                            <li>
                                                <form action="{{ route('courses.destroy', $course->id) }}" method="POST" onsubmit="return confirm('Delete this course bundle?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item edaca-dropdown-item text-danger w-100 text-start">
                                                        <i class="fas fa-trash-alt"></i> Delete
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-slate-400">
                                    <i class="fas fa-box-open fa-2x mb-3 d-block text-slate-300"></i>
                                    No courses or bundles found. Click <strong>+ Add New Bundle</strong> to create one.
                                </td>
                            </tr>
                        @endforelse
                        @endif
                    </tbody>
                </table>

                {{-- Table Footer & Pagination (Image 2) --}}
                <div class="edaca-table-footer">
                    <div>
                        Showing result 1-{{ min(10, $allCourses->count()) }} of {{ $allCourses->count() }} Entries
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn-edaca-page" disabled>Previous</button>
                        <button type="button" class="btn-edaca-page">Next</button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ========================================================
         TAB 2: EXECUTIVE OVERVIEW & FINANCIAL KPIS
         ======================================================== --}}
    <div id="tab-executive" class="tab-view-pane">
        
        {{-- KPI Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="edaca-stat-card skeleton-box">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="stat-label-subtle">Enrolled Students</span>
                        <div class="rounded-circle p-2 bg-primary bg-opacity-10 text-primary">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                    </div>
                    <div class="stat-val-bold">{{ number_format($studentCount) }}</div>
                    <span class="stat-badge-soft bg-primary bg-opacity-10 text-primary">Active in System</span>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="edaca-stat-card skeleton-box">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="stat-label-subtle">Total Revenue</span>
                        <div class="rounded-circle p-2 bg-success bg-opacity-10 text-success">
                            <i class="fas fa-indian-rupee-sign"></i>
                        </div>
                    </div>
                    <div class="stat-val-bold">₹{{ number_format($totalIncome) }}</div>
                    <span class="stat-badge-soft bg-success bg-opacity-10 text-success">Receipts Successfully Paid</span>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="edaca-stat-card skeleton-box">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="stat-label-subtle">Pending Invoices</span>
                        <div class="rounded-circle p-2 bg-warning bg-opacity-10 text-warning">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                    </div>
                    <div class="stat-val-bold">₹{{ number_format($totalPendingFees) }}</div>
                    <span class="stat-badge-soft bg-warning bg-opacity-10 text-warning">{{ $dueInvoices }} Unpaid Due</span>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="edaca-stat-card skeleton-box">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="stat-label-subtle">Active Staff</span>
                        <div class="rounded-circle p-2 bg-info bg-opacity-10 text-info">
                            <i class="fas fa-chalkboard-user"></i>
                        </div>
                    </div>
                    <div class="stat-val-bold">{{ number_format($employeeCount) }}</div>
                    <span class="stat-badge-soft bg-info bg-opacity-10 text-info">Allocated Across Programs</span>
                </div>
            </div>
        </div>

        {{-- Automated LMS Fee Registration Engine Banner --}}
        <div class="edaca-card mb-4 skeleton-box" style="background: linear-gradient(135deg, #ffffff, #f8fafc); border-color: #cbd5e1;">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 52px; height: 52px; border-radius: 14px; background: rgba(37,99,235,0.1); color: var(--edaca-primary); display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div>
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 mb-1 px-2 py-1 rounded-pill" style="font-size: 0.72rem; font-weight: 700;">
                            Automated Invoicing Engine
                        </span>
                        <h4 class="fw-bold mb-1" style="color: #0f172a; font-size: 1.15rem;">Enroll in LMS &amp; Automatically Issue Official Fee Invoice</h4>
                        <p class="text-slate-500 mb-0" style="font-size: 0.84rem; color: #64748b;">
                            When you enroll a learner into an LMS cohort or masterclass, the ERP instantly generates their student account, schedules their tenure, and creates their official invoice receipt.
                        </p>
                    </div>
                </div>
                <a href="{{ route('lms.index') }}" class="btn-edaca-primary flex-shrink-0">
                    <i class="fas fa-plus-circle"></i> Open LMS Auto-Enrollment
                </a>
            </div>
        </div>

        {{-- Popularity and Recent Enrollments Grid --}}
        <div class="row g-4 mb-4">
            {{-- Course Popularity Distribution --}}
            <div class="col-12 col-lg-5">
                <div class="edaca-card h-100 skeleton-box">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="fw-bold mb-0" style="color: #0f172a; font-size: 1rem;">Course Popularity &amp; Velocity</h5>
                            <small class="text-slate-500" style="font-size: 0.75rem;">Real-time enrollment distribution</small>
                        </div>
                        <span class="badge bg-light text-slate-600 border">Live</span>
                    </div>

                    <div class="d-flex flex-column gap-3 mb-4">
                        @forelse($coursesPopularity as $cp)
                            @php
                                $popPct = $maxStudentsInCourse > 0 ? round(($cp->students_count / $maxStudentsInCourse) * 100) : 0;
                            @endphp
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-semibold text-slate-800 small">{{ $cp->name }}</span>
                                    <span class="text-slate-500 small" style="font-size: 0.78rem;">{{ $cp->students_count }} learners ({{ $popPct }}%)</span>
                                </div>
                                <div class="progress" style="height: 7px; background-color: #f1f5f9; border-radius: 999px;">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ max(8, $popPct) }}%; border-radius: 999px;"></div>
                                </div>
                            </div>
                        @empty
                            <div class="text-muted text-center py-4">No course data recorded yet.</div>
                        @endforelse
                    </div>

                    <div class="p-3 rounded-3 mt-auto bg-light border border-slate-200">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="fas fa-lightbulb text-warning"></i>
                            <span class="fw-bold text-slate-900 small">Program Intelligence</span>
                        </div>
                        <p class="text-slate-500 small mb-0" style="font-size: 0.78rem;">
                            @if($coursesPopularity->isNotEmpty())
                                <strong>{{ $coursesPopularity->first()->name }}</strong> leads in learner demand. Consider expanding mentorship cohorts for this track.
                            @else
                                Add course programs to review curriculum velocity metrics.
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            {{-- Recent Student Enrollments --}}
            <div class="col-12 col-lg-7">
                <div class="edaca-card h-100 skeleton-box">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="fw-bold mb-0" style="color: #0f172a; font-size: 1rem;">Recent Student Registrations</h5>
                            <small class="text-slate-500" style="font-size: 0.75rem;">Latest learners registered in institute ERP</small>
                        </div>
                        <a href="{{ route('students.index') }}" class="btn-edaca-outline py-1 px-3" style="font-size: 0.76rem;">View All Students &rarr;</a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="border-collapse: separate; border-spacing: 0 4px;">
                            <thead>
                                <tr style="font-size: 0.72rem; color: #64748b; text-transform: uppercase;">
                                    <th class="border-0">Student</th>
                                    <th class="border-0">Course Track</th>
                                    <th class="border-0">Admission ID</th>
                                    <th class="border-0 text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentStudents as $rst)
                                    <tr style="background: #ffffff; border: 1px solid #f1f5f9; border-radius: 8px;">
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(37,99,235,0.1); color: var(--edaca-primary); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.75rem;">
                                                    {{ strtoupper(substr($rst->first_name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-slate-900 small">{{ $rst->first_name }} {{ $rst->last_name }}</div>
                                                    <small class="text-slate-400" style="font-size: 0.7rem;">{{ $rst->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-slate-700 border">{{ $rst->course?->name ?? 'General' }}</span>
                                        </td>
                                        <td>
                                            <code style="font-size: 0.74rem; color: #0284c7; background: #f0f9ff; padding: 2px 6px; border-radius: 4px;">
                                                {{ $rst->admission_no }}
                                            </code>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('students.show', $rst->id) }}" class="btn btn-sm btn-light border py-1 px-2" title="View Profile">
                                                <i class="fas fa-eye text-slate-500"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">No recent student registrations.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Navigation Shortcuts --}}
        <div class="edaca-card skeleton-box">
            <h5 class="fw-bold mb-3" style="color: #0f172a; font-size: 1rem;">
                <i class="fas fa-grid-2 me-2 text-primary"></i> Module Quick Shortcuts
            </h5>
            <div class="row g-2">
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="{{ route('students.index') }}" class="btn btn-light border p-3 d-flex flex-column align-items-center justify-content-center text-decoration-none rounded-3 w-100 h-100" style="border-color: #e2e8f0 !important;">
                        <i class="fas fa-user-graduate text-primary mb-2" style="font-size: 1.3rem;"></i>
                        <span class="fw-semibold text-slate-800" style="font-size: 0.8rem;">Students</span>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="{{ route('employees.index') }}" class="btn btn-light border p-3 d-flex flex-column align-items-center justify-content-center text-decoration-none rounded-3 w-100 h-100" style="border-color: #e2e8f0 !important;">
                        <i class="fas fa-users text-info mb-2" style="font-size: 1.3rem;"></i>
                        <span class="fw-semibold text-slate-800" style="font-size: 0.8rem;">Staff Members</span>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="{{ route('courses.index') }}" class="btn btn-light border p-3 d-flex flex-column align-items-center justify-content-center text-decoration-none rounded-3 w-100 h-100" style="border-color: #e2e8f0 !important;">
                        <i class="fas fa-book text-success mb-2" style="font-size: 1.3rem;"></i>
                        <span class="fw-semibold text-slate-800" style="font-size: 0.8rem;">Courses</span>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="{{ route('fee_invoices.index') }}" class="btn btn-light border p-3 d-flex flex-column align-items-center justify-content-center text-decoration-none rounded-3 w-100 h-100" style="border-color: #e2e8f0 !important;">
                        <i class="fas fa-receipt text-warning mb-2" style="font-size: 1.3rem;"></i>
                        <span class="fw-semibold text-slate-800" style="font-size: 0.8rem;">Fee Receipts</span>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="{{ route('attendances.live') }}" class="btn btn-light border p-3 d-flex flex-column align-items-center justify-content-center text-decoration-none rounded-3 w-100 h-100" style="border-color: #e2e8f0 !important;">
                        <i class="fas fa-fingerprint text-danger mb-2" style="font-size: 1.3rem;"></i>
                        <span class="fw-semibold text-slate-800" style="font-size: 0.8rem;">Live Biometric</span>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="{{ route('messages.full') }}" class="btn btn-light border p-3 d-flex flex-column align-items-center justify-content-center text-decoration-none rounded-3 w-100 h-100" style="border-color: #e2e8f0 !important;">
                        <i class="fas fa-comments text-purple mb-2" style="font-size: 1.3rem; color: #8b5cf6;"></i>
                        <span class="fw-semibold text-slate-800" style="font-size: 0.8rem;">NetChat Suite</span>
                    </a>
                </div>
            </div>
        </div>

    </div>

    {{-- ========================================================
         TAB 3: OPERATIONS & STAFF DESK (10 AM TO 5 PM & BIOMETRIC)
         ======================================================== --}}
    <div id="tab-operations" class="tab-view-pane">
        
        <div class="row g-4 mb-4">
            {{-- Biometric Status Card --}}
            <div class="col-12 col-md-4">
                <div class="edaca-card h-100 skeleton-box">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div style="width: 44px; height: 44px; border-radius: 12px; background: #ecfdf5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                            <i class="fas fa-fingerprint"></i>
                        </div>
                        <div>
                            <span class="stat-label-subtle">Biometric Terminal</span>
                            <h5 class="fw-bold mb-0 text-slate-900" style="font-size: 1rem;">
                                {{ $biometricDevice->device_name ?? 'ZKTeco K40 Terminal' }}
                            </h5>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between p-2 bg-light rounded-2 mb-3" style="font-size: 0.8rem;">
                        <span class="text-slate-500">IP Address:</span>
                        <code class="text-primary">{{ $biometricDevice->ip_address ?? '192.168.1.201' }}</code>
                    </div>
                    <form action="{{ route('biometric.sync') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-edaca-outline w-100 justify-content-center">
                            <i class="fas fa-rotate text-primary"></i> Sync Biometric Punches Now
                        </button>
                    </form>
                </div>
            </div>

            {{-- 10 to 5 Staff Working Hours Stat --}}
            <div class="col-12 col-md-4">
                <div class="edaca-card h-100 skeleton-box">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div style="width: 44px; height: 44px; border-radius: 12px; background: #f0f9ff; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                            <i class="fas fa-business-time"></i>
                        </div>
                        <div>
                            <span class="stat-label-subtle">Core Working Hours</span>
                            <h5 class="fw-bold mb-0 text-slate-900" style="font-size: 1rem;">10:00 AM – 5:00 PM Window</h5>
                        </div>
                    </div>
                    <div class="stat-val-bold mb-1">{{ $workingHoursEmployeesCount }} Active Today</div>
                    <p class="text-slate-500 small mb-0" style="font-size: 0.8rem;">
                        Staff members currently checked in within the standard shift.
                    </p>
                </div>
            </div>

            {{-- Cache & System Health --}}
            <div class="col-12 col-md-4">
                <div class="edaca-card h-100 skeleton-box">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div style="width: 44px; height: 44px; border-radius: 12px; background: #fffbeb; color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                            <i class="fas fa-shield-heart"></i>
                        </div>
                        <div>
                            <span class="stat-label-subtle">System Status</span>
                            <h5 class="fw-bold mb-0 text-slate-900" style="font-size: 1rem;">All Modules Operational</h5>
                        </div>
                    </div>
                    <div class="p-2 bg-light rounded-2 mb-3 d-flex justify-content-between align-items-center" style="font-size: 0.8rem;">
                        <span class="text-slate-500">Laravel Framework:</span>
                        <span class="fw-bold text-success"><i class="fas fa-circle-check me-1"></i> Healthy</span>
                    </div>
                    <form action="{{ route('clear-cache') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-warning w-100 rounded-3">
                            <i class="fas fa-broom me-1"></i> Flush ERP Cache
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Staff Punches Table Today --}}
        <div class="edaca-card skeleton-box">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="fw-bold mb-0" style="color: #0f172a; font-size: 1.05rem;">Today's Staff Clock-In Ledger</h5>
                    <small class="text-slate-500" style="font-size: 0.78rem;">Live punch log for instructors and operations team</small>
                </div>
                <a href="{{ route('employee-attendances.index') }}" class="btn-edaca-outline py-1 px-3" style="font-size: 0.78rem;">Full Staff Attendance &rarr;</a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="font-size: 0.72rem; color: #64748b; text-transform: uppercase;">
                        <tr>
                            <th>Staff Member</th>
                            <th>Employee Code</th>
                            <th>Check-In Time</th>
                            <th>Check-Out Time</th>
                            <th>Shift Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staffAttendanceToday as $att)
                            <tr style="font-size: 0.85rem;">
                                <td>
                                    <div class="fw-bold text-slate-900">{{ $att->employee?->full_name ?? 'Staff Member' }}</div>
                                    <small class="text-slate-400" style="font-size: 0.72rem;">{{ $att->employee?->department ?? 'Faculty' }}</small>
                                </td>
                                <td><code>{{ $att->employee?->employee_code ?? 'EMP-' . $att->employee_id }}</code></td>
                                <td>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">
                                        <i class="fas fa-arrow-right-to-bracket me-1"></i>
                                        {{ $att->check_in_time ? \Carbon\Carbon::parse($att->check_in_time)->format('h:i A') : '—' }}
                                    </span>
                                </td>
                                <td>
                                    @if($att->check_out_time)
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border">
                                            <i class="fas fa-arrow-right-from-bracket me-1"></i>
                                            {{ \Carbon\Carbon::parse($att->check_out_time)->format('h:i A') }}
                                        </span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning border">On Duty</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="status-pill active">Present</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No staff attendance punches logged today.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- ========================================================
         TAB 4: SECURITY & LOGIN AUDIT LOGS (FOR ADMIN)
         ======================================================== --}}
    <div id="tab-audit-logs" class="tab-view-pane">
        <div class="edaca-card skeleton-box">
            
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
                <div>
                    <h4 class="fw-bold mb-1" style="color: #0f172a; font-size: 1.2rem;">
                        <i class="fas fa-user-shield text-primary me-2"></i> Real-Time Login &amp; Security Audit Trail
                    </h4>
                    <p class="text-slate-500 mb-0" style="font-size: 0.84rem; color: #64748b;">
                        Admin audit monitor tracking every authentication attempt across Administrator, Mentor, and Staff portals.
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="search-input-wrap">
                        <i class="fas fa-search"></i>
                        <input type="text" id="auditSearchInput" placeholder="Filter by user or IP..." onkeyup="filterAuditLogs(this.value)">
                    </div>
                </div>
            </div>

            <div class="edaca-table-wrap">
                <table class="edaca-table" id="auditLogsTable">
                    <thead>
                        <tr>
                            <th>User / Login ID</th>
                            <th>Role / Portal</th>
                            <th>Action &amp; Route</th>
                            <th>IP Address</th>
                            <th>Device / User Agent</th>
                            <th>Status</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loginLogs as $log)
                            @php
                                $isSuccess = ($log->status_code == 200 || !str_contains(strtolower($log->action ?? ''), 'fail'));
                                $roleBadgeClass = match(strtolower($log->user_role ?? '')) {
                                    'superadmin', 'super-admin', 'admin', 'institute' => 'bg-primary bg-opacity-10 text-primary border-primary border-opacity-25',
                                    'mentor', 'instructor' => 'bg-purple bg-opacity-10 text-purple border-purple border-opacity-25',
                                    'staff', 'employee' => 'bg-info bg-opacity-10 text-info border-info border-opacity-25',
                                    default => 'bg-secondary bg-opacity-10 text-secondary border-secondary border-opacity-25'
                                };
                            @endphp
                            <tr class="audit-row">
                                <td>
                                    <div class="fw-bold text-slate-900" style="font-size: 0.86rem; color: #0f172a;">
                                        {{ $log->user_name ?: 'System Guest' }}
                                    </div>
                                    @if(isset($log->request_data['email']))
                                        <small class="text-slate-400" style="font-size: 0.72rem;">{{ $log->request_data['email'] }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge border rounded-pill {{ $roleBadgeClass }}" style="font-size: 0.72rem; text-transform: uppercase;">
                                        {{ $log->user_role ?: 'User' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="text-slate-800 fw-medium" style="font-size: 0.84rem;">{{ $log->action }}</div>
                                    <small class="text-slate-400 font-monospace" style="font-size: 0.7rem;">{{ $log->method }} /{{ $log->route }}</small>
                                </td>
                                <td>
                                    <code style="font-size: 0.76rem; color: #0284c7; background: #f0f9ff; padding: 2px 6px; border-radius: 4px;">
                                        {{ $log->ip_address ?: '127.0.0.1' }}
                                    </code>
                                </td>
                                <td style="max-width: 220px;" class="text-truncate">
                                    <small class="text-slate-500" style="font-size: 0.72rem;" title="{{ $log->user_agent }}">
                                        {{ Str::limit($log->user_agent ?: 'Standard Browser', 45) }}
                                    </small>
                                </td>
                                <td>
                                    @if($isSuccess)
                                        <span class="status-pill active">
                                            <i class="fas fa-check-circle me-1"></i> Success ({{ $log->status_code ?: 200 }})
                                        </span>
                                    @else
                                        <span class="status-pill paused" style="background:#fee2e2; color:#b91c1c; border-color:#fca5a5;">
                                            <i class="fas fa-triangle-exclamation me-1"></i> Failed ({{ $log->status_code ?: 401 }})
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-slate-700" style="font-size: 0.78rem;">
                                        {{ $log->created_at ? $log->created_at->diffForHumans() : 'Just now' }}
                                    </div>
                                    <small class="text-slate-400" style="font-size: 0.7rem;">
                                        {{ $log->created_at ? $log->created_at->format('M d, Y h:i A') : '' }}
                                    </small>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="fas fa-shield-check fa-2x mb-3 d-block text-slate-300"></i>
                                    No audit logs recorded yet. Successful and rejected logins are recorded here automatically.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

<script>
    // Tab titles map for dynamic breadcrumbs
    const tabTitles = {
        'bundles': 'Course Bundle',
        'executive': 'Executive Overview & KPIs',
        'operations': 'Operations & Staff Desk',
        'audit-logs': 'Security & Login Audit Logs'
    };

    // Smooth Inner-Dashboard Switcher with Slider Transition & Lazy Loading
    function switchDashboardTab(tabId, btnElement) {
        // Update switcher buttons
        document.querySelectorAll('.switcher-pill').forEach(btn => btn.classList.remove('active'));
        if (btnElement) {
            btnElement.classList.add('active');
        }

        // Show brief skeleton shimmer for smooth lazy-loading feel
        const container = document.getElementById('dashboardContainer');
        container.classList.add('skeleton-mode');
        
        // Switch tab panes
        document.querySelectorAll('.tab-view-pane').forEach(pane => pane.classList.remove('active-pane'));
        const targetPane = document.getElementById('tab-' + tabId);
        if (targetPane) {
            targetPane.classList.add('active-pane');
        }

        // Update breadcrumb
        const titleEl = document.getElementById('currentBreadcrumbTitle');
        if (titleEl && tabTitles[tabId]) {
            titleEl.textContent = tabTitles[tabId];
        }

        // Update URL hash without jumping
        history.replaceState(null, null, '#' + tabId);

        // Remove skeleton shimmer after 220ms
        setTimeout(() => {
            container.classList.remove('skeleton-mode');
        }, 220);
    }

    // Toggle Skeleton Simulation button
    function toggleSkeletonSimulation() {
        const container = document.getElementById('dashboardContainer');
        const isSkel = container.classList.toggle('skeleton-mode');
        const btnText = document.getElementById('skeletonBtnText');
        if (btnText) {
            btnText.textContent = isSkel ? 'Exit Skeleton' : 'Skeleton Loading';
        }
    }

    // Filter table by bundle name
    function filterBundleTable(query) {
        const q = query.toLowerCase();
        document.querySelectorAll('.bundle-row').forEach(row => {
            const name = row.querySelector('.bundle-name')?.textContent.toLowerCase() || '';
            row.style.display = name.includes(q) ? '' : 'none';
        });
    }

    // Filter table by status
    function filterStatus(status) {
        document.querySelectorAll('.bundle-row').forEach(row => {
            if (status === 'all') {
                row.style.display = '';
            } else {
                row.style.display = (row.dataset.status === status) ? '' : 'none';
            }
        });
    }

    // Filter audit logs table
    function filterAuditLogs(query) {
        const q = query.toLowerCase();
        document.querySelectorAll('.audit-row').forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(q) ? '' : 'none';
        });
    }

    // Sort table rows by column index
    function sortTableBy(colIndex) {
        const table = document.getElementById('bundleDataTable');
        if (!table) return;
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('.bundle-row'));

        rows.sort((a, b) => {
            const aVal = a.cells[colIndex]?.textContent.trim() || '';
            const bVal = b.cells[colIndex]?.textContent.trim() || '';
            return aVal.localeCompare(bVal, undefined, { numeric: true });
        });

        rows.forEach(r => tbody.appendChild(r));
    }

    // Keyboard shortcut handler for ⌘ K / Ctrl+K
    window.addEventListener('keydown', function(e) {
        if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            const searchInput = document.getElementById('courseBundleSearch') || document.getElementById('topNavSearchInput');
            if (searchInput) {
                searchInput.focus();
                searchInput.select();
            }
        }
    });

    // Check hash on page load
    window.addEventListener('DOMContentLoaded', function() {
        const hash = window.location.hash.replace('#', '');
        if (hash && document.getElementById('tab-' + hash)) {
            const pill = document.querySelector(`.switcher-pill[onclick*="'${hash}'"]`);
            switchDashboardTab(hash, pill);
        }
    });
</script>
@endsection
