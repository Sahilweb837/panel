@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <!-- Dashboard Styling -->
    <style>
        /* Design.md Dark Theme Variable Overrides */
        html[data-theme="dark"] {
            --main-bg: #121212 !important;
            --surface: #1E1E1E !important;
            --surface-soft: #252525 !important;
            --border: #2C2C2E !important;
            --first-color: #00E5FF !important;
            --text-main: #FFFFFF !important;
            --text-muted: #98989D !important;
        }
        html[data-theme="dark"] .stat-card {
            background: #1E1E1E !important;
            border: 1px solid #2C2C2E !important;
        }
        html[data-theme="dark"] .stat-card:hover {
            border-color: #00E5FF !important;
            box-shadow: 0 10px 30px rgba(0, 229, 255, 0.2) !important;
        }
        html[data-theme="dark"] .sk-card {
            background: linear-gradient(90deg, #1E1E1E 25%, #2C2C2E 50%, #1E1E1E 75%) !important;
        }
        /* JetBrains Mono typography for numerical values */
        html[data-theme="dark"] .metric-number,
        html[data-theme="dark"] .stat-card h3,
        html[data-theme="dark"] .table td,
        html[data-theme="dark"] .value-accent {
            font-family: 'JetBrains Mono', 'Fira Code', monospace !important;
        }

        .dashboard-container {
            position: relative;
            z-index: 1;
        }

        .skeleton-loader-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--main-bg);
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 20px;
            pointer-events: none;
            opacity: 1;
            transition: opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .skeleton-loader-overlay.fade-out {
            opacity: 0;
            display: none !important;
        }

        .sk-card {
            background: linear-gradient(90deg, var(--surface-soft) 25%, var(--border) 50%, var(--surface-soft) 75%);
            background-size: 200% 100%;
            animation: loadingSkeleton 1.5s infinite linear;
            border-radius: 16px;
            border: 1px solid var(--border);
        }

        @keyframes loadingSkeleton {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* Premium Glassmorphic Cards */
        .stat-card {
            border: 1px solid var(--border);
            border-radius: 16px;
            background: var(--surface);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            position: relative;
        }

        html[data-theme="dark"] .stat-card {
            background: rgba(31, 41, 55, 0.45);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            border-color: var(--first-color);
        }

        html[data-theme="dark"] .stat-card:hover {
            box-shadow: 0 20px 40px rgba(255, 85, 50, 0.15);
            border-color: rgba(255, 85, 50, 0.4);
        }

        /* Gradient Accents for Stat Cards */
        .stat-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: transparent;
            transition: background 0.3s ease;
        }

        .stat-card.card-students::after {
            background: linear-gradient(90deg, #ff5532, #ff8a00);
        }
        .stat-card.card-staff::after {
            background: linear-gradient(90deg, #10b981, #059669);
        }
        .stat-card.card-attendance::after {
            background: linear-gradient(90deg, #f59e0b, #d97706);
        }
        .stat-card.card-receipts::after {
            background: linear-gradient(90deg, #ef4444, #dc2626);
        }

        /* Glowing Background Highlights */
        .glow-dot {
            position: absolute;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.15;
            top: -20px;
            right: -20px;
            z-index: 0;
            transition: opacity 0.3s ease;
        }
        html[data-theme="dark"] .glow-dot {
            opacity: 0.25;
        }
        .stat-card:hover .glow-dot {
            opacity: 0.35;
        }

        .bg-glow-students { background: #ff5532; }
        .bg-glow-staff { background: #10b981; }
        .bg-glow-attendance { background: #f59e0b; }
        .bg-glow-receipts { background: #ef4444; }

        /* Icon Wrapper styling */
        .stat-icon-wrapper {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
            transition: transform 0.3s ease;
        }
        .stat-card:hover .stat-icon-wrapper {
            transform: scale(1.1) rotate(5deg);
        }

        .stat-icon-students { background: rgba(255, 85, 50, 0.1); color: #ff5532; }
        .stat-icon-staff { background: rgba(16, 185, 129, 0.1); color: #10b981; }
        .stat-icon-attendance { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        .stat-icon-receipts { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

        .stat-card h3 {
            position: relative;
            z-index: 1;
            font-size: 2.2rem;
            letter-spacing: -0.5px;
        }

        .card-header-clean {
            border-bottom: 1px solid var(--border);
            padding: 20px 24px;
            background: var(--surface-soft);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        html[data-theme="dark"] .card-header-clean {
            background: rgba(55, 65, 81, 0.2);
            border-color: rgba(255, 255, 255, 0.05);
        }

        .progress-bar-clean {
            height: 6px;
            background-color: var(--border);
            border-radius: 999px;
            overflow: hidden;
            position: relative;
            z-index: 1;
        }
        html[data-theme="dark"] .progress-bar-clean {
            background-color: rgba(255, 255, 255, 0.05);
        }

        .progress-fill {
            height: 100%;
            border-radius: 999px;
            transition: width 0.6s ease;
        }

        .table-clean th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.72rem;
            color: var(--muted);
            background-color: var(--surface-soft);
            border-bottom: 2px solid var(--border) !important;
            padding: 14px 20px;
            letter-spacing: 0.5px;
        }
        html[data-theme="dark"] .table-clean th {
            background-color: rgba(55, 65, 81, 0.3);
            border-color: rgba(255, 255, 255, 0.05) !important;
        }

        .table-clean td {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
        }
        html[data-theme="dark"] .table-clean td {
            border-color: rgba(255, 255, 255, 0.05);
        }

        .shortcut-btn {
            border: 1px solid var(--border);
            background: var(--surface);
            border-radius: 12px;
            padding: 20px 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-align: center;
            color: var(--text);
            font-weight: 600;
            font-size: 0.9rem;
            position: relative;
            overflow: hidden;
        }
        html[data-theme="dark"] .shortcut-btn {
            background: rgba(31, 41, 55, 0.3);
            border-color: rgba(255, 255, 255, 0.05);
        }

        .shortcut-btn:hover {
            border-color: var(--first-color);
            color: var(--first-color);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(255, 85, 50, 0.08);
            background: rgba(255, 85, 50, 0.02);
        }

        .shortcut-btn i {
            font-size: 1.4rem;
            color: var(--muted);
            transition: all 0.3s ease;
        }

        .shortcut-btn:hover i {
            color: var(--first-color);
            transform: scale(1.1);
        }

        /* ADMS live pulse animation */
        .blink-animation {
            animation: pulse 1.8s infinite;
        }
        @keyframes pulse {
            0% { opacity: 0.4; }
            50% { opacity: 1; }
            100% { opacity: 0.4; }
        }

        /* Cashflow overview metrics styling */
        .financial-stat-box {
            padding: 15px;
            transition: all 0.3s ease;
            border-radius: 12px;
        }
        .financial-stat-box:hover {
            background: var(--surface-soft);
        }
        html[data-theme="dark"] .financial-stat-box:hover {
            background: rgba(255, 255, 255, 0.02);
        }

        /* Color Picker Styling */
        .color-dot {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 3px solid var(--surface);
            box-shadow: 0 0 0 1px var(--border);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }
        .color-dot:hover {
            transform: scale(1.15);
        }
        .color-dot.active-accent {
            box-shadow: 0 0 0 2px var(--first-color);
            transform: scale(1.1);
        }
    </style>

    <div class="dashboard-container">
        <!-- Lazy Loading Skeleton screens overlay -->
        <div class="skeleton-loader-overlay" id="dashboard-skeleton">
            <div class="row g-4">
                <div class="col-12 col-sm-6 col-xl-3"><div class="sk-card" style="height: 140px;"></div></div>
                <div class="col-12 col-sm-6 col-xl-3"><div class="sk-card" style="height: 140px;"></div></div>
                <div class="col-12 col-sm-6 col-xl-3"><div class="sk-card" style="height: 140px;"></div></div>
                <div class="col-12 col-sm-6 col-xl-3"><div class="sk-card" style="height: 140px;"></div></div>
            </div>
            <div class="row g-4 mt-1">
                <div class="col-12 col-lg-8"><div class="sk-card" style="height: 380px;"></div></div>
                <div class="col-12 col-lg-4"><div class="sk-card" style="height: 380px;"></div></div>
            </div>
        </div>

        <!-- Main Real Dashboard Content -->
        <div id="dashboard-content" style="opacity: 0; transition: opacity 0.5s ease;">
            <!-- Stat Cards Deck -->
            <div class="row g-4 mb-4">
                <!-- Students Stat -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card stat-card card-students h-100">
                        <div class="glow-dot bg-glow-students"></div>
                        <div class="card-body p-4 position-relative" style="z-index: 1;">
                            <div class="stat-icon-wrapper stat-icon-students">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <span class="text-muted small fw-bold d-block mb-1 text-uppercase">Students Enrolled</span>
                            <h3 class="fw-bold mb-3 text-dark-title">{{ $studentCount }}</h3>
                            <div class="w-100 mb-2">
                                <div class="progress-bar-clean">
                                    <div class="progress-fill" style="width: 82%; background: linear-gradient(90deg, #ff5532, #ff8a00);"></div>
                                </div>
                            </div>
                            <span class="text-success small fw-semibold"><i class="fas fa-arrow-up me-1"></i>Active accounts</span>
                        </div>
                    </div>
                </div>

                <!-- Staff Stat -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card stat-card card-staff h-100">
                        <div class="glow-dot bg-glow-staff"></div>
                        <div class="card-body p-4 position-relative" style="z-index: 1;">
                            <div class="stat-icon-wrapper stat-icon-staff">
                                <i class="fas fa-users-cog"></i>
                            </div>
                            <span class="text-muted small fw-bold d-block mb-1 text-uppercase">Active Staff</span>
                            <h3 class="fw-bold mb-3 text-dark-title">{{ $employeeCount }}</h3>
                            <div class="w-100 mb-2">
                                <div class="progress-bar-clean">
                                    <div class="progress-fill" style="width: 95%; background: linear-gradient(90deg, #10b981, #059669);"></div>
                                </div>
                            </div>
                            <span class="text-success small fw-semibold"><i class="fas fa-check-circle me-1"></i>Fully Verified</span>
                        </div>
                    </div>
                </div>

                <!-- Attendance Stat -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card stat-card card-attendance h-100">
                        <div class="glow-dot bg-glow-attendance"></div>
                        <div class="card-body p-4 position-relative" style="z-index: 1;">
                            <div class="stat-icon-wrapper stat-icon-attendance">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <span class="text-muted small fw-bold d-block mb-1 text-uppercase">Attendance Today</span>
                            <h3 class="fw-bold mb-3 text-dark-title">{{ $attendanceCount }}</h3>
                            <div class="w-100 mb-2">
                                <div class="progress-bar-clean">
                                    <div class="progress-fill" style="width: 74%; background: linear-gradient(90deg, #f59e0b, #d97706);"></div>
                                </div>
                            </div>
                            <span class="text-warning small fw-semibold"><i class="fas fa-clock me-1"></i>Daily Logged</span>
                        </div>
                    </div>
                </div>

                <!-- Pending Receipts Stat -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card stat-card card-receipts h-100">
                        <div class="glow-dot bg-glow-receipts"></div>
                        <div class="card-body p-4 position-relative" style="z-index: 1;">
                            <div class="stat-icon-wrapper stat-icon-receipts">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                            <span class="text-muted small fw-bold d-block mb-1 text-uppercase">Pending Receipts</span>
                            <h3 class="fw-bold mb-3 text-dark-title">{{ $dueInvoices }}</h3>
                            <div class="w-100 mb-2">
                                <div class="progress-bar-clean">
                                    <div class="progress-fill" style="width: 38%; background: linear-gradient(90deg, #ef4444, #dc2626);"></div>
                                </div>
                            </div>
                            <span class="text-danger small fw-semibold"><i class="fas fa-exclamation-circle me-1"></i>Needs Attention</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Biometric Connection Status -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card stat-card" style="border-radius: 16px;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    @php
                                        $isAdmsConnected = isset($biometricDevice) && $biometricDevice->last_sync && \Carbon\Carbon::parse($biometricDevice->last_sync)->diffInMinutes(now()) < 5;
                                    @endphp
                                    <div style="width: 48px; height: 48px; background: {{ $isAdmsConnected ? 'rgba(16, 185, 129, 0.1)' : 'rgba(108, 117, 125, 0.1)' }}; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-satellite-dish {{ $isAdmsConnected ? 'text-success' : 'text-secondary' }} fs-5"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-1">ZKTeco Biometric Connection</h5>
                                        @if($isAdmsConnected)
                                            <span class="badge bg-success"><i class="fas fa-circle blink-animation me-1"></i> Online & Live</span>
                                            <small class="text-muted ms-2">Syncing punches automatically. Last ping: {{ \Carbon\Carbon::parse($biometricDevice->last_sync)->diffForHumans() }}</small>
                                        @else
                                            <span class="badge bg-secondary"><i class="fas fa-circle me-1"></i> Offline</span>
                                            <small class="text-muted ms-2">
                                                @if(isset($biometricDevice) && $biometricDevice->last_sync)
                                                    Last seen: {{ \Carbon\Carbon::parse($biometricDevice->last_sync)->diffForHumans() }}
                                                @else
                                                    Device has never connected to ADMS.
                                                @endif
                                            </small>
                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('biometric.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2">
                                        <i class="fas fa-cog me-2"></i>Hardware Setup
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Summary Cashflow Deck -->
            <div class="row g-4 mb-4">
                <div class="col-12 col-lg-8">
                    <div class="card stat-card h-100" style="border-radius: 16px;">
                        <div class="card-header-clean">
                            <h5 class="fw-bold mb-0 text-dark-title"><i class="fas fa-chart-pie text-first me-2"></i>Financial Overview</h5>
                            <span class="badge bg-light border text-dark">System Calculated</span>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-12 col-md-4 border-end financial-stat-box">
                                    <span class="text-muted d-block small fw-bold mb-1 text-uppercase">Total Income Received</span>
                                    <h3 class="fw-bold text-success mb-2">₹{{ number_format($totalIncome, 2) }}</h3>
                                    <small class="text-muted">Paid student fee receipts</small>
                                </div>
                                <div class="col-12 col-md-4 border-end financial-stat-box">
                                    <span class="text-muted d-block small fw-bold mb-1 text-uppercase">Total Expenditure</span>
                                    <h3 class="fw-bold text-danger mb-2">₹{{ number_format($totalExpense, 2) }}</h3>
                                    <small class="text-muted">Recorded corporate expenses</small>
                                </div>
                                <div class="col-12 col-md-4 financial-stat-box">
                                    <span class="text-muted d-block small fw-bold mb-1 text-uppercase">Outstanding Fees</span>
                                    <h3 class="fw-bold text-warning mb-2">₹{{ number_format($totalPendingFees, 2) }}</h3>
                                    <small class="text-muted">Outstanding student receipts</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Core Shortcuts Panel & Theme settings -->
                <div class="col-12 col-lg-4">
                    <div class="d-flex flex-column gap-4 h-100">
                        <!-- Quick Actions -->
                        <div class="card stat-card" style="border-radius: 16px; flex: 1;">
                            <div class="card-header-clean">
                                <h5 class="fw-bold mb-0 text-dark-title"><i class="fas fa-bolt text-first me-2"></i>Quick Actions</h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <a href="{{ route('students.create') }}" class="shortcut-btn w-100 h-100">
                                            <i class="fas fa-user-plus"></i>
                                            <span>Add Student</span>
                                        </a>
                                    </div>
                                    <div class="col-6">
                                        <a href="{{ route('attendances.create') }}" class="shortcut-btn w-100 h-100">
                                            <i class="fas fa-calendar-check"></i>
                                            <span>Attendance</span>
                                        </a>
                                    </div>
                                    <div class="col-6">
                                        <a href="{{ route('expenses.create') }}" class="shortcut-btn w-100 h-100">
                                            <i class="fas fa-receipt"></i>
                                            <span>Expense</span>
                                        </a>
                                    </div>
                                    <div class="col-6">
                                        <a href="{{ route('courses.create') }}" class="shortcut-btn w-100 h-100">
                                            <i class="fas fa-book"></i>
                                            <span>Add Course</span>
                                        </a>
                                    </div>
                                    <div class="col-12 mt-2">
                                        <form action="{{ route('clear-cache') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="shortcut-btn w-100 flex-row p-2" style="background: rgba(255, 85, 50, 0.05);">
                                                <i class="fas fa-broom text-first"></i>
                                                <span class="text-first">Clear Application Cache</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Theme Settings -->
                        <div class="card stat-card" style="border-radius: 16px;">
                            <div class="card-header-clean">
                                <h5 class="fw-bold mb-0 text-dark-title"><i class="fas fa-palette text-first me-2"></i>Theme Settings</h5>
                            </div>
                            <div class="card-body p-4">
                                <p class="text-muted small mb-3">Personalize your system workspace color accent and light/dark appearance.</p>
                                
                                <div class="mb-4">
                                    <label class="fw-bold small text-muted text-uppercase d-block mb-2">Accent Color</label>
                                    <div class="d-flex flex-wrap gap-2" id="accent-picker">
                                        <!-- Orange -->
                                        <button type="button" class="color-dot" style="background: #ff5532;" 
                                                onclick="selectAccentColor(this, '#ff5532', '#d63a1f', 'rgba(255, 85, 50, 0.12)', 'rgba(255, 85, 50, 0.22)')" title="Sunset Orange"></button>
                                        <!-- Blue -->
                                        <button type="button" class="color-dot" style="background: #3b82f6;" 
                                                onclick="selectAccentColor(this, '#3b82f6', '#1d4ed8', 'rgba(59, 130, 246, 0.12)', 'rgba(59, 130, 246, 0.22)')" title="Ocean Blue"></button>
                                        <!-- Green -->
                                        <button type="button" class="color-dot" style="background: #10b981;" 
                                                onclick="selectAccentColor(this, '#10b981', '#059669', 'rgba(16, 185, 129, 0.12)', 'rgba(16, 185, 129, 0.22)')" title="Emerald Green"></button>
                                        <!-- Purple -->
                                        <button type="button" class="color-dot" style="background: #8b5cf6;" 
                                                onclick="selectAccentColor(this, '#8b5cf6', '#6d28d9', 'rgba(139, 92, 246, 0.12)', 'rgba(139, 92, 246, 0.22)')" title="Royal Purple"></button>
                                        <!-- Pink -->
                                        <button type="button" class="color-dot" style="background: #ec4899;" 
                                                onclick="selectAccentColor(this, '#ec4899', '#be185d', 'rgba(236, 72, 153, 0.12)', 'rgba(236, 72, 153, 0.22)')" title="Deep Pink"></button>
                                        <!-- Crimson -->
                                        <button type="button" class="color-dot" style="background: #e11d48;" 
                                                onclick="selectAccentColor(this, '#e11d48', '#9f1239', 'rgba(225, 29, 72, 0.12)', 'rgba(225, 29, 72, 0.22)')" title="Classic Crimson"></button>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="fw-bold small text-muted text-uppercase d-block mb-2">Display Theme</label>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <button type="button" class="btn btn-outline-secondary w-100 btn-sm py-2 px-3 d-flex align-items-center justify-content-center gap-2" onclick="setThemeMode('light')">
                                                <i class="fas fa-sun"></i> Light Mode
                                            </button>
                                        </div>
                                        <div class="col-6">
                                            <button type="button" class="btn btn-outline-secondary w-100 btn-sm py-2 px-3 d-flex align-items-center justify-content-center gap-2" onclick="setThemeMode('dark')">
                                                <i class="fas fa-moon"></i> Dark Mode
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                    <button type="button" class="btn btn-link text-danger text-decoration-none p-0 small fw-bold" onclick="window.resetTheme()">
                                        <i class="fas fa-undo me-1"></i> Reset Settings
                                    </button>
                                    <span class="text-muted small" style="font-size: 0.75rem;"><i class="fas fa-cloud-upload-alt me-1"></i> Autosaved</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Tables Grid -->
            <div class="row g-4">
                <!-- Recent Attendance -->
                <div class="col-12 col-lg-6">
                    <div class="card stat-card h-100" style="border-radius: 16px; padding: 0;">
                        <div class="card-header-clean">
                            <h5 class="mb-0 fw-bold text-dark-title"><i class="fas fa-clock text-first me-2"></i>Recent Attendance Logs</h5>
                            <a href="{{ route('attendances.index') }}" class="btn btn-xs btn-outline-secondary rounded-pill px-3">View All</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-clean table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th class="ps-4">Student</th>
                                            <th>Date</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-end pe-4">Fine</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentAttendances as $attendance)
                                            <tr>
                                                <td class="fw-medium ps-4 text-dark-title">{{ $attendance->student?->first_name ?? 'Unknown' }} {{ $attendance->student?->last_name }}</td>
                                                <td class="text-muted small">{{ $attendance->attendance_date }}</td>
                                                <td class="text-center">
                                                    <span class="badge bg-{{ strtolower($attendance->status) === 'present' ? 'success' : (strtolower($attendance->status) === 'absent' ? 'danger' : 'warning') }} rounded-pill">
                                                        {{ $attendance->status }}
                                                    </span>
                                                </td>
                                                <td class="text-end fw-bold pe-4 text-dark-title">
                                                    @if($attendance->fine > 0)
                                                        ₹{{ number_format($attendance->fine, 2) }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-4">No recent attendance records.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Latest Fee Receipts -->
                <div class="col-12 col-lg-6">
                    <div class="card stat-card h-100" style="border-radius: 16px; padding: 0;">
                        <div class="card-header-clean">
                            <h5 class="mb-0 fw-bold text-dark-title"><i class="fas fa-receipt text-first me-2"></i>Latest Fee Receipts</h5>
                            <a href="{{ route('fee_invoices.index') }}" class="btn btn-xs btn-outline-secondary rounded-pill px-3">View All</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-clean table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th class="ps-4">Receipt</th>
                                            <th>Student</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-end pe-4">Total Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentInvoices as $invoice)
                                            <tr>
                                                <td class="fw-bold text-first ps-4">{{ $invoice->invoice_no }}</td>
                                                <td class="fw-medium text-dark-title">{{ $invoice->student?->first_name ?? 'Unknown' }} {{ $invoice->student?->last_name }}</td>
                                                <td class="text-center">
                                                    <span class="badge bg-{{ strtolower($invoice->status) === 'paid' ? 'success' : (strtolower($invoice->status) === 'unpaid' ? 'danger' : 'warning') }} rounded-pill">
                                                        {{ $invoice->status }}
                                                    </span>
                                                </td>
                                                <td class="text-end fw-bold pe-4 text-dark-title">
                                                    ₹{{ number_format($invoice->due_amount, 2) }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-4">No recent fee receipts.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student Portal Cards Row -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-end mb-3">
                        <h4 class="fw-bold mb-0 text-dark-title"><i class="fas fa-user-graduate text-first me-2"></i>Student Portal</h4>
                        <a href="{{ route('students.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Manage Students</a>
                    </div>
                    <div class="row g-3">
                        @forelse($recentStudents as $student)
                            <div class="col-12 col-md-6 col-xl-3">
                                <a href="{{ route('students.show', $student) }}" class="text-decoration-none">
                                    <div class="card stat-card h-100 p-3 shortcut-btn" style="border-radius: 12px;">
                                        <div class="d-flex align-items-center gap-3 w-100 mb-2">
                                            <div class="flex-shrink-0" style="width: 42px; height: 42px; border-radius: 50%; background: var(--primary-glow); color: var(--first-color); display: grid; place-items: center; font-weight: bold;">
                                                {{ strtoupper(substr($student->first_name, 0, 1)) }}
                                            </div>
                                            <div class="text-start overflow-hidden">
                                                <h6 class="mb-0 fw-bold text-dark-title text-truncate">{{ $student->first_name }} {{ $student->last_name }}</h6>
                                                <small class="text-muted text-truncate d-block">{{ $student->admission_no }}</small>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between w-100 text-muted small mt-auto pt-2 border-top">
                                            <span class="text-truncate" style="max-width: 65%;"><i class="fas fa-book me-1"></i>{{ $student->course?->name ?? 'N/A' }}</span>
                                            <span class="badge bg-{{ $student->status ? 'success' : 'danger' }}">{{ $student->status ? 'Active' : 'Inactive' }}</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @empty
                            <div class="col-12 text-center text-muted py-3">No students found.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Staff Portal Cards Row -->
            <div class="row mt-4 mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-end mb-3">
                        <h4 class="fw-bold mb-0 text-dark-title"><i class="fas fa-users-cog text-success me-2"></i>Staff Portal</h4>
                        <a href="{{ route('employees.index') }}" class="btn btn-sm btn-outline-success rounded-pill px-3">Manage Staff</a>
                    </div>
                    <div class="row g-3">
                        @forelse($recentStaff as $staff)
                            <div class="col-12 col-md-6 col-xl-3">
                                <a href="{{ route('employees.show', $staff) }}" class="text-decoration-none">
                                    <div class="card stat-card h-100 p-3 shortcut-btn" style="border-radius: 12px; border-color: rgba(16, 185, 129, 0.2);">
                                        <div class="d-flex align-items-center gap-3 w-100 mb-2">
                                            <div class="flex-shrink-0" style="width: 42px; height: 42px; border-radius: 50%; background: rgba(16, 185, 129, 0.1); color: #10b981; display: grid; place-items: center; font-weight: bold;">
                                                {{ strtoupper(substr($staff->user?->name ?? 'S', 0, 1)) }}
                                            </div>
                                            <div class="text-start overflow-hidden">
                                                <h6 class="mb-0 fw-bold text-dark-title text-truncate">{{ $staff->user?->name ?? 'Unknown' }}</h6>
                                                <small class="text-muted text-truncate d-block">{{ $staff->employee_code }}</small>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between w-100 text-muted small mt-auto pt-2 border-top">
                                            <span class="text-truncate" style="max-width: 65%;"><i class="fas fa-briefcase me-1"></i>{{ $staff->designation ?? 'Staff' }}</span>
                                            <span class="badge bg-{{ $staff->status ? 'success' : 'danger' }}">{{ $staff->status ? 'Active' : 'Inactive' }}</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @empty
                            <div class="col-12 text-center text-muted py-3">No staff found.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script to simulate dynamic lazy loading and skeleton fading, and theme management -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const skeleton = document.getElementById('dashboard-skeleton');
            const content = document.getElementById('dashboard-content');
            
            // Instantly render skeleton, trigger fading out in 600ms
            setTimeout(() => {
                if (skeleton) skeleton.classList.add('fade-out');
                if (content) content.style.opacity = '1';
            }, 600);
        });

        // Theme settings color picker and synchronization logic
        function selectAccentColor(btn, primary, dark, light, focus) {
            document.querySelectorAll('#accent-picker .color-dot').forEach(el => {
                el.classList.remove('active-accent');
            });
            btn.classList.add('active-accent');
            window.applyPrimaryColor(primary, dark, light, focus);
        }
        
        function setThemeMode(mode) {
            document.documentElement.dataset.theme = mode;
            localStorage.setItem('fees-theme', mode);
            
            // Sync side navbar icons / top buttons
            document.querySelectorAll('[data-theme-toggle]').forEach(t => {
                const n = t.querySelector('i');
                if(n) n.className = mode === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
            });
        }
        
        // Synchronize UI elements on page load
        document.addEventListener('DOMContentLoaded', () => {
            const savedColor = localStorage.getItem('fees-primary-color');
            const buttons = document.querySelectorAll('#accent-picker .color-dot');
            if (savedColor) {
                try {
                    const colors = JSON.parse(savedColor);
                    buttons.forEach(btn => {
                        const styleBg = btn.style.backgroundColor;
                        const hex = rgb2hex(styleBg);
                        if (hex === colors.primary.toLowerCase()) {
                            btn.classList.add('active-accent');
                        } else {
                            btn.classList.remove('active-accent');
                        }
                    });
                } catch(e) {
                    console.error(e);
                }
            } else {
                // Default orange is active
                const orangeBtn = document.querySelector('#accent-picker .color-dot[title="Sunset Orange"]');
                if(orangeBtn) orangeBtn.classList.add('active-accent');
            }
        });
        
        // Listen to reset / external theme changes
        window.addEventListener('theme-color-changed', () => {
            const savedColor = localStorage.getItem('fees-primary-color');
            const buttons = document.querySelectorAll('#accent-picker .color-dot');
            if (!savedColor) {
                buttons.forEach(btn => {
                    if (btn.getAttribute('title') === 'Sunset Orange') {
                        btn.classList.add('active-accent');
                    } else {
                        btn.classList.remove('active-accent');
                    }
                });
            } else {
                try {
                    const colors = JSON.parse(savedColor);
                    buttons.forEach(btn => {
                        const styleBg = btn.style.backgroundColor;
                        const hex = rgb2hex(styleBg);
                        if (hex === colors.primary.toLowerCase()) {
                            btn.classList.add('active-accent');
                        } else {
                            btn.classList.remove('active-accent');
                        }
                    });
                } catch(e) {
                    console.error(e);
                }
            }
        });

        function rgb2hex(rgb) {
            if (!rgb) return '';
            if (rgb.search("rgb") == -1) return rgb.toLowerCase();
            rgb = rgb.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+))?\)$/);
            function hex(x) {
                return ("0" + parseInt(x).toString(16)).slice(-2);
            }
            return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
        }
    </script>
@endsection
