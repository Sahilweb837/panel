@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<style>
    .premium-admin-card {
        background: var(--card-bg, #fff);
        border: none !important;
        border-radius: 16px !important;
        box-shadow: 0 4px 20px rgba(0,0,0,0.02), 0 0 0 1px rgba(0,0,0,0.02) !important;
        transition: transform 0.3s ease, box-shadow 0.3s ease !important;
        overflow: hidden;
        position: relative;
    }
    .premium-admin-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.05), 0 0 0 1px rgba(0,0,0,0.03) !important;
    }
    .admin-stat-icon {
        width: 48px; height: 48px; border-radius: 12px;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 1.2rem; margin-bottom: 1rem;
        transition: all 0.3s;
    }
    .premium-admin-card:hover .admin-stat-icon {
        transform: scale(1.1) rotate(5deg);
    }
    .shortcut-btn {
        background: var(--surface-soft, #f8fafc) !important;
        border: 1px solid var(--border, #e2e8f0) !important;
        border-radius: 14px !important;
        padding: 1.25rem 1rem !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 0.75rem !important;
        color: var(--text) !important;
        font-weight: 600 !important;
        font-size: 0.85rem !important;
        transition: all 0.2s ease !important;
        text-align: center !important;
    }
    .shortcut-btn:hover {
        background: var(--surface) !important;
        border-color: var(--first-color, #ff5532) !important;
        color: var(--first-color, #ff5532) !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 18px rgba(255, 85, 50, 0.08) !important;
    }
    .card-header-clean {
        padding: 1.25rem 1.5rem !important;
        border-bottom: 1px solid var(--border, #e2e8f0) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        background: transparent !important;
    }
    .table-clean th {
        font-size: 0.75rem !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        font-weight: 700 !important;
        color: var(--muted) !important;
        border-bottom: 1px solid var(--border, #e2e8f0) !important;
        padding: 12px 16px !important;
    }
    .table-clean td {
        padding: 14px 16px !important;
        border-bottom: 1px solid var(--border, #e2e8f0) !important;
        font-size: 0.875rem !important;
    }
    .financial-stat-box {
        padding: 1.5rem !important;
        transition: background-color 0.2s;
    }
    .financial-stat-box:hover {
        background-color: var(--surface-soft, #f8fafc);
        border-radius: 12px;
    }
</style>

    <div class="position-relative">
        <!-- Main Real Dashboard Content -->
        <div id="dashboard-content" style="opacity: 0; transition: opacity 0.5s ease;">

            {{-- ── Admin Welcome Hero Banner ── --}}
            <div class="card border-0 mb-4 position-relative overflow-hidden shadow-sm" style="border-radius: 20px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
                <div style="position: absolute; right: -50px; top: -50px; width: 250px; height: 250px; border-radius: 50%; background: rgba(255, 85, 50, 0.12); filter: blur(30px);"></div>
                <div class="card-body p-4 text-white position-relative" style="z-index: 1;">
                    <div class="row align-items-center">
                        <div class="col-12 col-md-8">
                            <span class="badge mb-2" style="background: rgba(255, 85, 50, 0.2); color: #ff8f76; font-weight: 700; letter-spacing: 0.5px; border: 1px solid rgba(255, 85, 50, 0.3);">MANAGEMENT CONSOLE</span>
                            <h2 class="fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">Welcome back, Administrator! 💼</h2>
                            <p class="mb-0 opacity-75 small">System status is fully operational. Manage students, staff tasks, corporate expenses, and review biometric attendance feeds in real time.</p>
                        </div>
                        <div class="col-md-4 d-none d-md-block text-end">
                            <i class="fas fa-shield-alt fa-4x text-white opacity-20" style="transform: rotate(-10deg);"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stat Cards Deck -->
            <div class="row g-4 mb-4">
                <!-- Students Stat -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card premium-admin-card h-100" style="border-bottom: 3px solid #ff5532 !important;">
                        <div class="card-body p-4">
                            <div class="admin-stat-icon" style="background: rgba(255, 85, 50, 0.1); color: #ff5532;">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <span class="text-muted small fw-bold d-block mb-1 text-uppercase">Students Enrolled</span>
                            <h3 class="fw-bold mb-3 fs-2">{{ $studentCount }}</h3>
                            <div class="progress mb-2 rounded-pill" style="height: 6px;">
                                <div class="progress-bar w-75" style="background-color: #ff5532;"></div>
                            </div>
                            <span class="text-muted small fw-semibold">Active accounts</span>
                        </div>
                    </div>
                </div>

                <!-- Staff Stat -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card premium-admin-card h-100" style="border-bottom: 3px solid #3b82f6 !important;">
                        <div class="card-body p-4">
                            <div class="admin-stat-icon" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                                <i class="fas fa-users-cog"></i>
                            </div>
                            <span class="text-muted small fw-bold d-block mb-1 text-uppercase">Active Staff</span>
                            <h3 class="fw-bold mb-3 fs-2">{{ $employeeCount }}</h3>
                            <div class="progress mb-2 rounded-pill" style="height: 6px;">
                                <div class="progress-bar w-100" style="background-color: #3b82f6;"></div>
                            </div>
                            <span class="text-muted small fw-semibold">Fully Verified</span>
                        </div>
                    </div>
                </div>

                <!-- Attendance Stat -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card premium-admin-card h-100" style="border-bottom: 3px solid #10b981 !important;">
                        <div class="card-body p-4">
                            <div class="admin-stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <span class="text-muted small fw-bold d-block mb-1 text-uppercase">Attendance Today</span>
                            <h3 class="fw-bold mb-3 fs-2">{{ $attendanceCount }}</h3>
                            <div class="progress mb-2 rounded-pill" style="height: 6px;">
                                <div class="progress-bar w-75" style="background-color: #10b981;"></div>
                            </div>
                            <span class="text-muted small fw-semibold">Daily Logged</span>
                        </div>
                    </div>
                </div>

                <!-- Pending Receipts Stat -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card premium-admin-card h-100" style="border-bottom: 3px solid #f59e0b !important;">
                        <div class="card-body p-4">
                            <div class="admin-stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                            <span class="text-muted small fw-bold d-block mb-1 text-uppercase">Pending Receipts</span>
                            <h3 class="fw-bold mb-3 fs-2">{{ $dueInvoices }}</h3>
                            <div class="progress mb-2 rounded-pill" style="height: 6px;">
                                <div class="progress-bar w-25" style="background-color: #f59e0b;"></div>
                            </div>
                            <span class="text-muted small fw-semibold">Needs Attention</span>
                        </div>
                    </div>
                </div>

                <!-- Working Hours 10 to 5 Stat -->
                <div class="col-12 col-sm-6 col-xl-3 mt-xl-0">
                    <div class="card premium-admin-card h-100" style="border-bottom: 3px solid #6366f1 !important;">
                        <div class="card-body p-4">
                            <div class="admin-stat-icon" style="background: rgba(99, 102, 241, 0.1); color: #6366f1;">
                                <i class="fas fa-business-time"></i>
                            </div>
                            <span class="text-muted small fw-bold d-block mb-1 text-uppercase">Core Work Hours (10-5)</span>
                            <h3 class="fw-bold mb-3 fs-2">{{ $workingHoursEmployeesCount ?? 0 }}</h3>
                            <div class="progress mb-2 rounded-pill" style="height: 6px;">
                                <div class="progress-bar w-100" style="background-color: #6366f1;"></div>
                            </div>
                            <span class="text-muted small fw-semibold">Employees Present Today</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Biometric Connection Status End -->

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
                                            @php
                                                $isActiveNow = $staff->user && $staff->user->last_activity_at && now()->diffInMinutes($staff->user->last_activity_at) <= 2;
                                            @endphp
                                            <span class="badge bg-{{ $isActiveNow ? 'success' : 'secondary' }}">{{ $isActiveNow ? 'Active Now' : 'Offline' }}</span>
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

    {{-- ── Embedded Messages Widget ── --}}
    <div class="container-fluid px-0 mt-4">
        @include('messages.widget')
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
