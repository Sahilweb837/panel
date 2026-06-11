@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <!-- Dashboard Styling -->
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

        .stat-card {
            border: 1px solid var(--border);
            border-radius: 12px;
            background: var(--surface);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s;
            overflow: hidden;
            position: relative;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        .card-header-clean {
            border-bottom: 1px solid var(--border);
            padding: 16px 20px;
            background: var(--surface-soft);
        }

        .progress-bar-clean {
            height: 6px;
            background-color: var(--border);
            border-radius: 999px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 999px;
        }

        .table-clean th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            color: var(--muted);
            background-color: var(--surface-soft);
            border-bottom: 2px solid var(--border) !important;
            padding: 12px 16px;
        }

        .table-clean td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border);
        }

        .shortcut-btn {
            border: 1px solid var(--border);
            background: var(--surface);
            border-radius: 8px;
            padding: 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s ease;
            text-align: center;
            color: var(--text);
            font-weight: 500;
        }

        .shortcut-btn:hover {
            border-color: var(--first-color);
            color: var(--first-color);
        }

        .shortcut-btn i {
            font-size: 1.25rem;
            color: var(--muted);
        }

        .shortcut-btn:hover i {
            color: var(--first-color);
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
                    <div class="card stat-card h-100">
                        <div class="card-body p-4">
                            <span class="text-muted small fw-bold d-block mb-1 text-uppercase">Students Enrolled</span>
                            <h3 class="display-6 fw-bold mb-3">{{ $studentCount }}</h3>
                            <div class="w-100 mb-2">
                                <div class="progress-bar-clean">
                                    <div class="progress-fill" style="width: 82%; background-color: var(--first-color);"></div>
                                </div>
                            </div>
                            <span class="text-success small fw-semibold"><i class="fas fa-arrow-up me-1"></i>Active accounts</span>
                        </div>
                    </div>
                </div>

                <!-- Staff Stat -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card stat-card h-100">
                        <div class="card-body p-4">
                            <span class="text-muted small fw-bold d-block mb-1 text-uppercase">Active Staff</span>
                            <h3 class="display-6 fw-bold mb-3">{{ $employeeCount }}</h3>
                            <div class="w-100 mb-2">
                                <div class="progress-bar-clean">
                                    <div class="progress-fill" style="width: 95%; background-color: #10b981;"></div>
                                </div>
                            </div>
                            <span class="text-success small fw-semibold"><i class="fas fa-check-circle me-1"></i>Fully Verified</span>
                        </div>
                    </div>
                </div>

                <!-- Attendance Stat -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card stat-card h-100">
                        <div class="card-body p-4">
                            <span class="text-muted small fw-bold d-block mb-1 text-uppercase">Attendance Today</span>
                            <h3 class="display-6 fw-bold mb-3">{{ $attendanceCount }}</h3>
                            <div class="w-100 mb-2">
                                <div class="progress-bar-clean">
                                    <div class="progress-fill" style="width: 74%; background-color: #f59e0b;"></div>
                                </div>
                            </div>
                            <span class="text-warning small fw-semibold"><i class="fas fa-clock me-1"></i>Daily Logged</span>
                        </div>
                    </div>
                </div>

                <!-- Pending Invoices Stat -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card stat-card h-100">
                        <div class="card-body p-4">
                            <span class="text-muted small fw-bold d-block mb-1 text-uppercase">Pending Invoices</span>
                            <h3 class="display-6 fw-bold mb-3">{{ $dueInvoices }}</h3>
                            <div class="w-100 mb-2">
                                <div class="progress-bar-clean">
                                    <div class="progress-fill" style="width: 38%; background-color: #ef4444;"></div>
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
                    <div class="card stat-card">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    @php
                                        $isAdmsConnected = isset($biometricDevice) && $biometricDevice->last_sync && \Carbon\Carbon::parse($biometricDevice->last_sync)->diffInMinutes(now()) < 5;
                                    @endphp
                                    <div style="width: 48px; height: 48px; background: {{ $isAdmsConnected ? 'rgba(16, 185, 129, 0.1)' : 'rgba(108, 117, 125, 0.1)' }}; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
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
                                    <a href="{{ route('biometric.index') }}" class="btn btn-outline-secondary">
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
                    <div class="card stat-card h-100">
                        <div class="card-header-clean d-flex align-items-center justify-content-between">
                            <h5 class="fw-bold mb-0"><i class="fas fa-chart-pie text-first me-2"></i>Financial Overview</h5>
                            <span class="badge bg-light border text-dark">System Calculated</span>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-12 col-md-4 border-end">
                                    <span class="text-muted d-block small fw-bold mb-1 text-uppercase">Total Income Received</span>
                                    <h3 class="fw-bold text-success mb-2">₹{{ number_format($totalIncome, 2) }}</h3>
                                    <small class="text-muted">Paid student fee invoices</small>
                                </div>
                                <div class="col-12 col-md-4 border-end">
                                    <span class="text-muted d-block small fw-bold mb-1 text-uppercase">Total Expenditure</span>
                                    <h3 class="fw-bold text-danger mb-2">₹{{ number_format($totalExpense, 2) }}</h3>
                                    <small class="text-muted">Recorded corporate expenses</small>
                                </div>
                                <div class="col-12 col-md-4">
                                    <span class="text-muted d-block small fw-bold mb-1 text-uppercase">Pending Fees</span>
                                    <h3 class="fw-bold text-warning mb-2">₹{{ number_format($totalPendingFees, 2) }}</h3>
                                    <small class="text-muted">Outstanding student invoices</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Core Shortcuts Panel -->
                <div class="col-12 col-lg-4">
                    <div class="card stat-card h-100">
                        <div class="card-header-clean">
                            <h5 class="fw-bold mb-0"><i class="fas fa-bolt text-first me-2"></i>Quick Actions</h5>
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
                </div>
            </div>

            <!-- Data Tables Grid -->
            <div class="row g-4">
                <!-- Recent Attendance -->
                <div class="col-12 col-lg-6">
                    <div class="card stat-card h-100">
                        <div class="card-header-clean d-flex align-items-center justify-content-between">
                            <h5 class="mb-0 fw-bold"><i class="fas fa-clock text-first me-2"></i>Recent Attendance Logs</h5>
                            <a href="{{ route('attendances.index') }}" class="btn btn-sm btn-outline-secondary">View All</a>
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
                                                <td class="fw-medium ps-4">{{ $attendance->student?->first_name ?? 'Unknown' }} {{ $attendance->student?->last_name }}</td>
                                                <td class="text-muted small">{{ $attendance->attendance_date }}</td>
                                                <td class="text-center">
                                                    <span class="badge bg-{{ strtolower($attendance->status) === 'present' ? 'success' : (strtolower($attendance->status) === 'absent' ? 'danger' : 'warning') }} rounded-pill">
                                                        {{ $attendance->status }}
                                                    </span>
                                                </td>
                                                <td class="text-end fw-bold pe-4">
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
                    <div class="card stat-card h-100">
                        <div class="card-header-clean d-flex align-items-center justify-content-between">
                            <h5 class="mb-0 fw-bold"><i class="fas fa-receipt text-first me-2"></i>Latest Fee Invoices</h5>
                            <a href="{{ route('fee_invoices.index') }}" class="btn btn-sm btn-outline-secondary">View All</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-clean table-hover align-middle mb-0">
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
                                                <td class="fw-medium">{{ $invoice->student?->first_name ?? 'Unknown' }} {{ $invoice->student?->last_name }}</td>
                                                <td class="text-center">
                                                    <span class="badge bg-{{ strtolower($invoice->status) === 'paid' ? 'success' : (strtolower($invoice->status) === 'unpaid' ? 'danger' : 'warning') }} rounded-pill">
                                                        {{ $invoice->status }}
                                                    </span>
                                                </td>
                                                <td class="text-end fw-bold pe-4">
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
