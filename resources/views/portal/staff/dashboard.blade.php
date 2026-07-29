@extends('layouts.app')

@section('title', 'Staff Portal Dashboard')
@section('page-title', 'My Dashboard')

@section('content')
<style>
    /* Override any lingering dark-mode hardcodes — use the app's CSS vars instead */
    .portal-stat-card {
        background: var(--card-bg, #fff);
        border: 1px solid var(--border, #e2e8f0);
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .portal-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }
    .portal-stat-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--muted);
        margin-bottom: 2px;
    }
    .portal-stat-value {
        font-size: 1.6rem;
        font-weight: 800;
        font-family: 'Poppins', sans-serif;
        line-height: 1;
        color: var(--text);
    }

    .salary-breakdown-card {
        background: var(--card-bg, #fff);
        border: 1px solid var(--border, #e2e8f0);
        border-radius: 14px;
        overflow: hidden;
    }
    .salary-breakdown-card .sb-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border, #e2e8f0);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-weight: 700;
        font-size: 1rem;
    }
    .salary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.85rem 1.5rem;
        border-bottom: 1px solid var(--border, #e2e8f0);
        font-size: 0.9rem;
    }
    .salary-row:last-child { border-bottom: none; }
    .salary-row.total-row {
        background: rgba(255,85,50,0.05);
        font-weight: 800;
        font-family: 'Poppins', sans-serif;
        font-size: 1.1rem;
        color: var(--first-color);
    }
    .salary-row .deduct { color: #ef4444; font-weight: 600; }
    .salary-row .add { color: #10b981; font-weight: 600; }

    .att-row {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1.5rem;
        border-bottom: 1px solid var(--border, #e2e8f0);
        font-size: 0.875rem;
    }
    .att-row:last-child { border-bottom: none; }
    .att-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .profile-card-portal {
        background: var(--card-bg, #fff);
        border: 1px solid var(--border, #e2e8f0);
        border-radius: 14px;
        padding: 1.5rem;
        text-align: center;
    }
    .profile-avatar-portal {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: rgba(255,85,50,0.08);
        color: var(--first-color);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin: 0 auto 1rem;
    }
    .info-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--surface-soft, #f8fafc);
        border: 1px solid var(--border, #e2e8f0);
        border-radius: 20px;
        padding: 5px 12px;
        font-size: 0.8rem;
        font-weight: 600;
        margin: 3px;
    }
</style>

<div class="container-fluid px-0">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(isset($unreadMessageCount) && $unreadMessageCount > 0)
        <div class="alert d-flex align-items-center rounded-3 mb-4 border-0 shadow-sm" style="background: rgba(16, 185, 129, 0.1); color: #047857; border-left: 4px solid #10b981 !important;">
            <i class="fas fa-envelope-open-text fa-2x me-3"></i>
            <div>
                <strong>You have {{ $unreadMessageCount }} unread message(s)!</strong><br>
                <span style="font-size: 0.85rem;">Please check your inbox to stay updated.</span>
            </div>
            <a href="{{ route('messages.index') }}" class="btn btn-sm ms-auto px-4" style="background: #10b981; color: #fff; border-radius: 50px; font-weight: 600;">View Inbox</a>
        </div>
    @endif

    {{-- ── TOP ROW: Profile + Quick Stats ── --}}
    <div class="row g-3 mb-4">

        {{-- Profile Card --}}
        <div class="col-12 col-md-4 col-xl-3">
            <div class="profile-card-portal h-100 position-relative">
                <div class="profile-avatar-portal position-relative" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#appProfilePicModal">
                    @if($employee->user?->profile_pic && $employee->user?->profile_pic !== 'default.png')
                        <img src="{{ asset('uploads/profiles/'.$employee->user->profile_pic) }}" class="rounded-circle" style="width:100%; height:100%; object-fit:cover;">
                    @else
                        <i class="fas fa-user-tie"></i>
                    @endif
                    <div class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 26px; height: 26px; font-size: 0.75rem; transform: translate(10%, 10%); border: 2px solid #fff;">
                        <i class="fas fa-camera"></i>
                    </div>
                </div>
                <h5 class="fw-bold mb-1">{{ $employee->user?->name ?? $employee->employee_code }}</h5>
                <p style="color:var(--muted); font-size:0.85rem; margin-bottom:0.75rem;">{{ $employee->designation ?? 'Staff Member' }}</p>
                <div>
                    <span class="info-chip" style="color:var(--first-color);"><i class="fas fa-id-badge"></i> {{ $employee->employee_code }}</span>
                    @if($employee->department)
                        <span class="info-chip"><i class="fas fa-building"></i> {{ $employee->department }}</span>
                    @endif
                    @if($experience)
                        <span class="info-chip" style="color:#10b981;"><i class="fas fa-briefcase"></i>
                            {{ $experience->y > 0 ? $experience->y.'y ' : '' }}{{ $experience->m }}m exp
                        </span>
                    @endif
                </div>
                <div class="mt-3 pt-3" style="border-top:1px solid var(--border);">
                    <div style="font-size:0.75rem; color:var(--muted);">Joined: <strong>{{ $joiningDate?->format('M d, Y') ?? 'N/A' }}</strong></div>
                </div>
                <a href="{{ route('staff.attendance.capture') }}" class="button button-primary w-100 mt-3 py-2">
                    <i class="fas fa-camera me-2"></i>Mark Attendance
                </a>
            </div>
        </div>

        {{-- Stats Grid --}}
        <div class="col-12 col-md-8 col-xl-9">
            <div class="row g-3 h-100">
                <div class="col-6 col-xl-3">
                    <div class="portal-stat-card">
                        <div class="portal-stat-icon" style="background:rgba(16,185,129,0.1); color:#10b981;">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div>
                            <div class="portal-stat-label">Present</div>
                            <div class="portal-stat-value">{{ $presentDays }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="portal-stat-card">
                        <div class="portal-stat-icon" style="background:rgba(239,68,68,0.1); color:#ef4444;">
                            <i class="fas fa-calendar-times"></i>
                        </div>
                        <div>
                            <div class="portal-stat-label">Absent</div>
                            <div class="portal-stat-value">{{ $absentDays }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="portal-stat-card">
                        <div class="portal-stat-icon" style="background:rgba(245,158,11,0.1); color:#f59e0b;">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <div class="portal-stat-label">Late Days</div>
                            <div class="portal-stat-value">{{ $effectiveLateDays }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="portal-stat-card">
                        <div class="portal-stat-icon" style="background:rgba(59,130,246,0.1); color:#3b82f6;">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <div>
                            <div class="portal-stat-label">Attendance %</div>
                            <div class="portal-stat-value">{{ $attendancePercentage }}%</div>
                        </div>
                    </div>
                </div>

                {{-- Salary Breakdown Card --}}
                <div class="col-12">
                    <div class="salary-breakdown-card">
                        <div class="sb-header">
                            <div style="width:36px; height:36px; border-radius:10px; background:rgba(255,85,50,0.1); color:var(--first-color); display:flex; align-items:center; justify-content:center;">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <span>Monthly Salary Breakdown — {{ now()->format('F Y') }}</span>
                        </div>
                        <div class="salary-row">
                            <span>Basic Monthly Salary</span>
                            <span class="add">₹{{ number_format($monthlySalary, 2) }}</span>
                        </div>
                        <div class="salary-row">
                            <span>Daily Rate (÷ 26 working days)</span>
                            <span style="color:var(--muted);">₹{{ number_format($dailySalary, 2) }}/day</span>
                        </div>
                        @if($effectiveLateDays > 0)
                        <div class="salary-row">
                            <span>
                                <i class="fas fa-clock me-1" style="color:#f59e0b;"></i>
                                Late Arrival Deduction
                                <small style="color:var(--muted); font-weight:400;">&nbsp;({{ $effectiveLateDays }} day{{ $effectiveLateDays > 1 ? 's' : '' }} × ½ day = ₹{{ number_format($halfDayDeduction, 0) }} each)</small>
                            </span>
                            <span class="deduct">- ₹{{ number_format($lateDeduction, 2) }}</span>
                        </div>
                        @endif
                        @if($absentDays > 0)
                        <div class="salary-row">
                            <span>
                                <i class="fas fa-calendar-times me-1" style="color:#ef4444;"></i>
                                Absent Deduction
                                <small style="color:var(--muted); font-weight:400;">&nbsp;({{ $absentDays }} day{{ $absentDays > 1 ? 's' : '' }} × full day)</small>
                            </span>
                            <span class="deduct">- ₹{{ number_format($absentDeduction, 2) }}</span>
                        </div>
                        @endif
                        <div class="salary-row total-row">
                            <span><i class="fas fa-check-circle me-2"></i>Estimated Net Payable</span>
                            <span>₹{{ number_format($netMonthlySalary, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── BOTTOM ROW: Attendance Table + Tasks ── --}}
    <div class="row g-3 mb-4">
        {{-- This Month's Attendance --}}
        <div class="col-12">
            <div class="card premium-stat-card p-0 overflow-hidden">
                <div class="premium-card-header bg-transparent border-bottom p-3 px-4">
                    <h6 class="mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="fas fa-calendar-alt text-first"></i> This Month's Attendance
                    </h6>
                </div>
                <div style="max-height: 340px; overflow-y: auto;">
                    @forelse($monthAttendances as $att)
                        @php
                            $isLateCheckIn = false;
                            if ($att->check_in_time) {
                                try {
                                    $ci = \Carbon\Carbon::parse($att->attendance_date . ' ' . $att->check_in_time);
                                    $co = \Carbon\Carbon::parse($att->attendance_date . ' 10:00:00');
                                    $isLateCheckIn = $ci->gt($co);
                                } catch (\Exception $e) {}
                            }
                        @endphp
                        <div class="att-row">
                            <div class="att-dot" style="background: {{ $att->status === 'Present' ? '#10b981' : ($att->status === 'Absent' ? '#ef4444' : '#f59e0b') }};"></div>
                            <div style="flex:1;">
                                <div style="font-weight:600; font-size:0.875rem;">{{ \Carbon\Carbon::parse($att->attendance_date)->format('D, M d') }}</div>
                                @if($att->check_in_time)
                                    <div style="font-size:0.75rem; color:var(--muted);">
                                        In: {{ \Carbon\Carbon::parse($att->check_in_time)->format('h:i A') }}
                                        @if($att->check_out_time)
                                            &nbsp;→ Out: {{ \Carbon\Carbon::parse($att->check_out_time)->format('h:i A') }}
                                        @endif
                                    </div>
                                @endif
                            </div>
                            <div style="text-align:right;">
                                <span class="status-badge status-{{ strtolower($att->status) }}" style="padding: 3px 8px; border-radius: 6px; font-size: 0.75rem;">{{ $att->status }}</span>
                                @if($isLateCheckIn)
                                    <div style="font-size:0.7rem; color:#f59e0b; font-weight:600; margin-top:2px;"><i class="fas fa-exclamation-triangle"></i> Late (½ day deducted)</div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-calendar-xmark fa-2x mb-2 d-block"></i>No attendance records for this month.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        </div>
    </div>

    {{-- ── Tabs ── --}}
    <div class="row g-3">
        <div class="col-12">
            <div class="card premium-stat-card p-0 overflow-hidden">
                <ul class="nav nav-pills p-3 gap-2 border-bottom" id="staffTab" role="tablist" style="flex-wrap:nowrap; overflow-x:auto;">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold py-1 px-3" data-bs-toggle="tab" data-bs-target="#salary-panel" type="button">
                            <i class="fas fa-file-invoice-dollar me-1"></i>Salary Slips
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold py-1 px-3" data-bs-toggle="tab" data-bs-target="#offer-panel" type="button">
                            <i class="fas fa-file-alt me-1"></i>Offer Letters
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold py-1 px-3" data-bs-toggle="tab" data-bs-target="#leave-panel" type="button">
                            <i class="fas fa-calendar-minus me-1"></i>Leave
                        </button>
                    </li>
                </ul>
                <div class="tab-content" style="max-height:320px; overflow-y:auto;">
                    <div class="tab-pane fade show active p-3" id="salary-panel">
                        <table class="table premium-table align-middle mb-0">
                            <thead><tr class="table-light-head">
                                <th>Month/Year</th><th>Basic</th><th>Deductions</th><th>Net Pay</th><th>Status</th><th></th>
                            </tr></thead>
                            <tbody>
                                @forelse($salarySlips ?? [] as $slip)
                                    <tr>
                                        <td class="fw-medium">{{ $slip->month }} {{ $slip->year }}</td>
                                        <td>₹{{ number_format($slip->basic_salary, 0) }}</td>
                                        <td class="text-danger">-₹{{ number_format($slip->deductions, 0) }}</td>
                                        <td class="fw-bold text-first">₹{{ number_format($slip->net_pay, 0) }}</td>
                                        <td><span class="badge bg-{{ $slip->status === 'Paid' ? 'success' : 'warning' }}">{{ $slip->status }}</span></td>
                                        <td>@if($slip->status === 'Paid')<a href="{{ route('salary_slips.show', $slip) }}" target="_blank" class="button button-secondary" style="padding:4px 10px; font-size:0.75rem;"><i class="fas fa-print"></i></a>@endif</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center py-4 text-muted">No salary slips yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade p-3" id="offer-panel">
                        <table class="table premium-table align-middle mb-0">
                            <thead><tr class="table-light-head">
                                <th>Offer No</th><th>Designation</th><th>Salary</th><th>Date</th><th>Status</th>
                            </tr></thead>
                            <tbody>
                                @forelse($offerLetters ?? [] as $letter)
                                    <tr>
                                        <td class="fw-medium">{{ $letter->offer_letter_no }}</td>
                                        <td>{{ $letter->designation }}</td>
                                        <td class="text-success">₹{{ number_format($letter->offered_salary, 0) }}</td>
                                        <td>{{ \Carbon\Carbon::parse($letter->joining_date)->format('M d, Y') }}</td>
                                        <td><span class="badge bg-{{ $letter->status === 'Accepted' ? 'success' : ($letter->status === 'Rejected' ? 'danger' : 'warning') }}">{{ $letter->status }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center py-4 text-muted">No offer letters.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade p-3" id="leave-panel">
                        <table class="table premium-table align-middle mb-0">
                            <thead><tr class="table-light-head">
                                <th>Type</th><th>From</th><th>To</th><th>Days</th><th>Status</th>
                            </tr></thead>
                            <tbody>
                                @forelse($leaveApplications ?? [] as $leave)
                                    <tr>
                                        <td class="fw-medium">{{ $leave->leave_type }}</td>
                                        <td>{{ \Carbon\Carbon::parse($leave->start_date)->format('M d') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($leave->end_date)->format('M d') }}</td>
                                        <td>{{ $leave->total_days ?? 'N/A' }}</td>
                                        <td><span class="badge bg-{{ $leave->status === 'Approved' ? 'success' : ($leave->status === 'Rejected' ? 'danger' : 'warning') }}">{{ $leave->status }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center py-4 text-muted">No leave applications.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Embedded Messages Panel ── --}}
    <div class="mt-4 mb-2 d-flex align-items-center justify-content-between">
        <h5 class="fw-bold mb-0"><i class="fas fa-comments text-first me-2"></i>Messages & Inbox</h5>
        <a href="{{ route('messages.full') }}" class="button button-primary py-2 px-4" style="font-size:0.85rem;">
            <i class="fas fa-expand-alt me-2"></i>Open Full Messages Panel
        </a>
    </div>
    @include('messages.widget')

</div>
@endsection
