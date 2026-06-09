@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <!-- Dashboard Styling and Animations -->
    <style>
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
            border-radius: 12px;
            border: 1px solid var(--border);
        }

        @keyframes loadingSkeleton {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        .stat-circle-box {
            position: relative;
            width: 64px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-circle-svg {
            transform: rotate(-90deg);
            width: 64px;
            height: 64px;
        }

        .stat-circle-bg {
            fill: none;
            stroke: var(--border);
            stroke-width: 6;
        }

        .stat-circle-bar {
            fill: none;
            stroke: var(--first-color);
            stroke-width: 6;
            stroke-linecap: round;
            transition: stroke-dashoffset 0.6s ease;
        }

        .premium-stat-card {
            border: 1px solid var(--border) !important;
            border-radius: 16px !important;
            background: var(--surface) !important;
            box-shadow: var(--shadow) !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            position: relative;
        }

        .premium-stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--first-color);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .premium-stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(255, 85, 50, 0.15) !important;
            border-color: var(--first-color) !important;
        }

        .premium-stat-card:hover::before {
            opacity: 1;
        }

        .premium-card-header {
            border-bottom: 1px solid var(--border) !important;
            padding: 20px 24px !important;
        }

        .progress-bar-glow {
            height: 6px;
            background-color: var(--border);
            border-radius: 9999px;
            overflow: hidden;
        }

        .progress-fill-first {
            height: 100%;
            border-radius: 9999px;
            background: linear-gradient(90deg, var(--first-color) 0%, #ff7c60 100%);
            box-shadow: 0 0 10px rgba(255, 85, 50, 0.3);
        }

        .premium-table th {
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.04em;
            color: var(--muted);
            background-color: var(--surface-soft);
            border-bottom: 2px solid var(--border) !important;
            padding: 14px 16px;
        }

        .premium-table td {
            padding: 16px;
            border-bottom: 1px solid var(--border);
        }

        /* Shortcut quick link design */
        .shortcut-btn {
            border: 1px solid var(--border);
            background: var(--surface);
            border-radius: 12px;
            padding: 18px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.25s ease;
            text-align: center;
            color: var(--text);
            font-weight: 600;
        }

        .shortcut-btn:hover {
            border-color: var(--first-color);
            color: var(--first-color);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(255, 85, 50, 0.1);
        }

        .shortcut-btn i {
            font-size: 1.5rem;
            color: var(--muted);
            transition: color 0.25s ease;
        }

        .shortcut-btn:hover i {
            color: var(--first-color);
        }
    </style>

    <div class="dashboard-container">
        <!-- Lazy Loading Skeleton screens overlay -->
        <div class="skeleton-loader-overlay" id="dashboard-skeleton">
            <div class="row g-4">
                <div class="col-12 col-sm-6 col-xl-3"><div class="sk-card" style="height: 160px;"></div></div>
                <div class="col-12 col-sm-6 col-xl-3"><div class="sk-card" style="height: 160px;"></div></div>
                <div class="col-12 col-sm-6 col-xl-3"><div class="sk-card" style="height: 160px;"></div></div>
                <div class="col-12 col-sm-6 col-xl-3"><div class="sk-card" style="height: 160px;"></div></div>
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
                    <div class="card premium-stat-card h-100">
                        <div class="card-body p-4 d-flex align-items-center justify-content-between">
                            <div style="flex: 1;">
                                <span class="text-muted small-text uppercase-bold d-block mb-1">Students Enrolled</span>
                                <h3 class="display-6 fw-bold mb-2 text-dark-title">{{ $studentCount }}</h3>
                                <div class="w-75 mb-2">
                                    <div class="progress-bar-glow">
                                        <div class="progress-fill-first" style="width: 82%;"></div>
                                    </div>
                                </div>
                                <span class="text-success small fw-semibold"><i class="fas fa-arrow-trend-up me-1"></i>Active accounts</span>
                            </div>
                            <div class="stat-circle-box">
                                <svg class="stat-circle-svg">
                                    <circle class="stat-circle-bg" cx="32" cy="32" r="26"></circle>
                                    <circle class="stat-circle-bar" cx="32" cy="32" r="26" stroke-dasharray="163.36" stroke-dashoffset="30"></circle>
                                </svg>
                                <span class="position-absolute" style="font-size: 0.8rem; font-weight: 800; color: var(--first-color);">82%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Staff Stat -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card premium-stat-card h-100">
                        <div class="card-body p-4 d-flex align-items-center justify-content-between">
                            <div style="flex: 1;">
                                <span class="text-muted small-text uppercase-bold d-block mb-1">Active Staff</span>
                                <h3 class="display-6 fw-bold mb-2 text-dark-title">{{ $employeeCount }}</h3>
                                <div class="w-75 mb-2">
                                    <div class="progress-bar-glow">
                                        <div class="progress-fill-first" style="width: 95%; background: linear-gradient(90deg, #10b981 0%, #34d399 100%);"></div>
                                    </div>
                                </div>
                                <span class="text-success small fw-semibold"><i class="fas fa-check-circle me-1"></i>Fully Verified</span>
                            </div>
                            <div class="stat-circle-box">
                                <svg class="stat-circle-svg">
                                    <circle class="stat-circle-bg" cx="32" cy="32" r="26"></circle>
                                    <circle class="stat-circle-bar" cx="32" cy="32" r="26" stroke="#10b981" stroke-dasharray="163.36" stroke-dashoffset="10"></circle>
                                </svg>
                                <span class="position-absolute" style="font-size: 0.8rem; font-weight: 800; color: #10b981;">95%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attendance Stat -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card premium-stat-card h-100">
                        <div class="card-body p-4 d-flex align-items-center justify-content-between">
                            <div style="flex: 1;">
                                <span class="text-muted small-text uppercase-bold d-block mb-1">Attendance Today</span>
                                <h3 class="display-6 fw-bold mb-2 text-dark-title">{{ $attendanceCount }}</h3>
                                <div class="w-75 mb-2">
                                    <div class="progress-bar-glow">
                                        <div class="progress-fill-first" style="width: 74%; background: linear-gradient(90deg, #f59e0b 0%, #fbbf24 100%);"></div>
                                    </div>
                                </div>
                                <span class="text-warning small fw-semibold"><i class="fas fa-clock me-1"></i>Daily Logged</span>
                            </div>
                            <div class="stat-circle-box">
                                <svg class="stat-circle-svg">
                                    <circle class="stat-circle-bg" cx="32" cy="32" r="26"></circle>
                                    <circle class="stat-circle-bar" cx="32" cy="32" r="26" stroke="#f59e0b" stroke-dasharray="163.36" stroke-dashoffset="40"></circle>
                                </svg>
                                <span class="position-absolute" style="font-size: 0.8rem; font-weight: 800; color: #f59e0b;">74%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Invoices Stat -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card premium-stat-card h-100">
                        <div class="card-body p-4 d-flex align-items-center justify-content-between">
                            <div style="flex: 1;">
                                <span class="text-muted small-text uppercase-bold d-block mb-1">Pending Invoices</span>
                                <h3 class="display-6 fw-bold mb-2 text-dark-title">{{ $dueInvoices }}</h3>
                                <div class="w-75 mb-2">
                                    <div class="progress-bar-glow">
                                        <div class="progress-fill-first" style="width: 38%; background: linear-gradient(90deg, #ef4444 0%, #f87171 100%);"></div>
                                    </div>
                                </div>
                                <span class="text-danger small fw-semibold"><i class="fas fa-circle-exclamation me-1"></i>Needs Attention</span>
                            </div>
                            <div class="stat-circle-box">
                                <svg class="stat-circle-svg">
                                    <circle class="stat-circle-bg" cx="32" cy="32" r="26"></circle>
                                    <circle class="stat-circle-bar" cx="32" cy="32" r="26" stroke="#ef4444" stroke-dasharray="163.36" stroke-dashoffset="100"></circle>
                                </svg>
                                <span class="position-absolute" style="font-size: 0.8rem; font-weight: 800; color: #ef4444;">38%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Biometric Connection Status -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card premium-stat-card p-4">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div class="d-flex align-items-center gap-3">
                                @php
                                    $isAdmsConnected = isset($biometricDevice) && $biometricDevice->last_sync && \Carbon\Carbon::parse($biometricDevice->last_sync)->diffInMinutes(now()) < 5;
                                @endphp
                                <div class="stat-circle-box" style="width: 50px; height: 50px; background: {{ $isAdmsConnected ? 'rgba(16, 185, 129, 0.1)' : 'rgba(108, 117, 125, 0.1)' }}; border-radius: 50%;">
                                    <i class="fas fa-satellite-dish {{ $isAdmsConnected ? 'text-success' : 'text-secondary' }} fs-4"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-1 text-dark-title">ZKTeco Biometric Connection</h5>
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
                                <a href="{{ route('biometric.index') }}" class="button button-secondary py-2 px-4">
                                    <i class="fas fa-cog me-2"></i>Hardware Setup Guide
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Summary Cashflow Deck -->
            <div class="row g-4 mb-4">
                <div class="col-12 col-lg-8">
                    <div class="card premium-stat-card p-4 h-100">
                        <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                            <h5 class="fw-bold mb-0 text-dark-title"><i class="fas fa-chart-pie text-first me-2"></i>Financial Overview & Cashflow Analysis</h5>
                            <span class="badge" style="background: rgba(255, 85, 50, 0.1); color: var(--first-color); font-weight: 600;">System Auto-Calculated</span>
                        </div>
                        <div class="row g-4">
                            <div class="col-12 col-md-4 border-end">
                                <span class="text-muted d-block uppercase-bold mb-1" style="font-size: 0.72rem;">TOTAL INCOME RECEIVED</span>
                                <h3 class="fw-bold text-success mb-2">₹{{ number_format($totalIncome, 2) }}</h3>
                                <small class="text-muted">Paid student fee invoices</small>
                            </div>
                            <div class="col-12 col-md-4 border-end">
                                <span class="text-muted d-block uppercase-bold mb-1" style="font-size: 0.72rem;">TOTAL EXPENDITURE OUTFLOW</span>
                                <h3 class="fw-bold text-danger mb-2">₹{{ number_format($totalExpense, 2) }}</h3>
                                <small class="text-muted">Recorded corporate expenses</small>
                            </div>
                            <div class="col-12 col-md-4">
                                <span class="text-muted d-block uppercase-bold mb-1" style="font-size: 0.72rem;">PENDING FEES ARREARS</span>
                                <h3 class="fw-bold text-warning mb-2">₹{{ number_format($totalPendingFees, 2) }}</h3>
                                <small class="text-muted">Outstanding student invoices</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Core Shortcuts Panel -->
                <div class="col-12 col-lg-4">
                    <div class="card premium-stat-card p-4 h-100">
                        <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                            <h5 class="fw-bold mb-0 text-dark-title"><i class="fas fa-bolt text-first me-2"></i>Quick Actions Panel</h5>
                        </div>
                        <div class="grid grid-2 gap-2" style="grid-template-columns: repeat(2, 1fr);">
                            <a href="{{ route('students.create') }}" class="shortcut-btn">
                                <i class="fas fa-user-plus"></i>
                                <span>Add Student</span>
                            </a>
                            <a href="{{ route('attendances.create') }}" class="shortcut-btn">
                                <i class="fas fa-calendar-check"></i>
                                <span>Attendance</span>
                            </a>
                            <a href="{{ route('expenses.create') }}" class="shortcut-btn">
                                <i class="fas fa-receipt"></i>
                                <span>Expense</span>
                            </a>
                            <a href="{{ route('courses.create') }}" class="shortcut-btn">
                                <i class="fas fa-book"></i>
                                <span>Add Course</span>
                            </a>
                            <form action="{{ route('clear-cache') }}" method="POST" class="d-inline-block w-100" style="grid-column: span 2;">
                                @csrf
                                <button type="submit" class="shortcut-btn w-100 flex-row" style="padding: 12px; background: rgba(255, 85, 50, 0.05);">
                                    <i class="fas fa-broom text-first"></i>
                                    <span class="text-first">Clear Application Cache</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Tables Grid -->
            <div class="row g-4">
                <!-- Recent Attendance -->
                <div class="col-12 col-lg-6">
                    <div class="card premium-stat-card h-100">
                        <div class="premium-card-header bg-transparent d-flex align-items-center justify-content-between">
                            <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                                <i class="fas fa-clock text-first"></i> Recent Attendance Logs
                            </h5>
                            <a href="{{ route('attendances.index') }}" class="btn btn-sm btn-light text-first fw-semibold px-3 py-1.5 rounded-2">View All</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table premium-table table-hover align-middle mb-0" style="min-width: auto;">
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
                                                <td class="fw-semibold ps-4">{{ $attendance->student->first_name }} {{ $attendance->student->last_name }}</td>
                                                <td class="text-muted small">{{ $attendance->attendance_date }}</td>
                                                <td class="text-center">
                                                    <span class="status-badge status-{{ strtolower($attendance->status) }}">
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

                <!-- Latest Invoices -->
                <div class="col-12 col-lg-6">
                    <div class="card premium-stat-card h-100">
                        <div class="premium-card-header bg-transparent d-flex align-items-center justify-content-between">
                            <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                                <i class="fas fa-receipt text-first"></i> Latest Fee Invoices
                            </h5>
                            <a href="{{ route('fee_invoices.index') }}" class="btn btn-sm btn-light text-first fw-semibold px-3 py-1.5 rounded-2">View All</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table premium-table table-hover align-middle mb-0" style="min-width: auto;">
                                    <thead>
                                        <tr>
                                            <th class="ps-4">Invoice</th>
                                            <th>Student</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-end pe-4">Due Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentInvoices as $invoice)
                                            <tr>
                                                <td class="fw-bold text-first ps-4">{{ $invoice->invoice_no }}</td>
                                                <td class="fw-semibold">{{ $invoice->student->first_name }} {{ $invoice->student->last_name }}</td>
                                                <td class="text-center">
                                                    <span class="status-badge status-{{ strtolower($invoice->status) }}">
                                                        {{ $invoice->status }}
                                                    </span>
                                                </td>
                                                <td class="text-end fw-bold pe-4 text-dark-title">
                                                    ₹{{ number_format($invoice->due_amount, 2) }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-4">No recent fee invoices.</td>
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

    <!-- Script to simulate dynamic lazy loading and skeleton fading -->
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
    </script>
@endsection
