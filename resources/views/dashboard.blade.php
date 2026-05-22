@extends('layouts.app')

@section('title', 'Dashboard')

@section('page-title', 'Dashboard')

@section('content')
    <!-- Stat Cards Deck -->
    <div class="row g-4 mb-4">
        <!-- Students Stat -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card border-0 shadow-sm h-100 position-relative overflow-hidden">
                <div class="card-body p-4">
                    <div class="stat-icon-wrapper d-flex align-items-center justify-content-center bg-light-orange text-first rounded-3 mb-3">
                        <i class="fas fa-users fa-lg"></i>
                    </div>
                    <span class="text-muted small-text uppercase-bold d-block mb-1">Students Registered</span>
                    <h3 class="display-6 fw-bold mb-0 text-dark-title">{{ $studentCount }}</h3>
                    <div class="stat-trend mt-2 text-success small d-flex align-items-center gap-1">
                        <i class="fas fa-arrow-trend-up"></i>
                        <span>Active Enrolled</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Staff Stat -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card border-0 shadow-sm h-100 position-relative overflow-hidden">
                <div class="card-body p-4">
                    <div class="stat-icon-wrapper d-flex align-items-center justify-content-center bg-light-orange text-first rounded-3 mb-3">
                        <i class="fas fa-person-chalkboard fa-lg"></i>
                    </div>
                    <span class="text-muted small-text uppercase-bold d-block mb-1">Active Staff</span>
                    <h3 class="display-6 fw-bold mb-0 text-dark-title">{{ $employeeCount }}</h3>
                    <div class="stat-trend mt-2 text-muted small d-flex align-items-center gap-1">
                        <i class="fas fa-circle-check text-success"></i>
                        <span>Fully Managed</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Stat -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card border-0 shadow-sm h-100 position-relative overflow-hidden">
                <div class="card-body p-4">
                    <div class="stat-icon-wrapper d-flex align-items-center justify-content-center bg-light-orange text-first rounded-3 mb-3">
                        <i class="fas fa-clipboard-check fa-lg"></i>
                    </div>
                    <span class="text-muted small-text uppercase-bold d-block mb-1">Attendance Today</span>
                    <h3 class="display-6 fw-bold mb-0 text-dark-title">{{ $attendanceCount }}</h3>
                    <div class="stat-trend mt-2 text-warning small d-flex align-items-center gap-1">
                        <i class="fas fa-clock"></i>
                        <span>Daily Tracking</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Invoices Stat -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card border-0 shadow-sm h-100 position-relative overflow-hidden">
                <div class="card-body p-4">
                    <div class="stat-icon-wrapper d-flex align-items-center justify-content-center bg-light-orange text-first rounded-3 mb-3">
                        <i class="fas fa-file-invoice-dollar fa-lg"></i>
                    </div>
                    <span class="text-muted small-text uppercase-bold d-block mb-1">Pending Invoices</span>
                    <h3 class="display-6 fw-bold mb-0 text-dark-title">{{ $dueInvoices }}</h3>
                    <div class="stat-trend mt-2 text-danger small d-flex align-items-center gap-1">
                        <i class="fas fa-circle-exclamation"></i>
                        <span>Requires Attention</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Tables Grid -->
    <div class="row g-4">
        <!-- Recent Attendance -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="fas fa-clock text-first"></i> Recent Attendance
                    </h5>
                    <a href="{{ route('attendances.index') }}" class="btn btn-sm btn-light text-first fw-semibold px-3 py-1.5 rounded-2">View All</a>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light-head">
                                <tr>
                                    <th class="ps-0">Student</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th class="text-end pe-0">Fine</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentAttendances as $attendance)
                                    <tr>
                                        <td class="fw-semibold ps-0">{{ $attendance->student->first_name }} {{ $attendance->student->last_name }}</td>
                                        <td class="text-muted small">{{ $attendance->attendance_date }}</td>
                                        <td>
                                            <span class="status-badge status-{{ strtolower($attendance->status) }}">
                                                {{ $attendance->status }}
                                            </span>
                                        </td>
                                        <td class="text-end fw-bold pe-0 text-dark-title">
                                            @if($attendance->fine > 0)
                                                ${{ number_format($attendance->fine, 2) }}
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
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="fas fa-receipt text-first"></i> Latest Invoices
                    </h5>
                    <a href="{{ route('fee_invoices.index') }}" class="btn btn-sm btn-light text-first fw-semibold px-3 py-1.5 rounded-2">View All</a>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light-head">
                                <tr>
                                    <th class="ps-0">Invoice</th>
                                    <th>Student</th>
                                    <th>Status</th>
                                    <th class="text-end pe-0">Due Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentInvoices as $invoice)
                                    <tr>
                                        <td class="fw-bold text-first ps-0">{{ $invoice->invoice_no }}</td>
                                        <td class="fw-semibold">{{ $invoice->student->first_name }} {{ $invoice->student->last_name }}</td>
                                        <td>
                                            <span class="status-badge status-{{ strtolower($invoice->status) }}">
                                                {{ $invoice->status }}
                                            </span>
                                        </td>
                                        <td class="text-end fw-bold pe-0 text-dark-title">
                                            ${{ number_format($invoice->due_amount, 2) }}
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
@endsection
