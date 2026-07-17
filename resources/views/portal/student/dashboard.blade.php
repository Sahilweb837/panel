@extends('layouts.app')

@section('title', 'Student Portal Dashboard')
@section('page-title', 'My Dashboard')

@section('content')
<style>
    .s-stat-card {
        background: var(--card-bg, #fff);
        border: 1px solid var(--border, #e2e8f0);
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .s-stat-icon {
        width: 48px; height: 48px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem; flex-shrink: 0;
    }
    .s-stat-label { font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--muted); margin-bottom: 2px; }
    .s-stat-value { font-size: 1.5rem; font-weight: 800; font-family: 'Outfit', sans-serif; line-height: 1; color: var(--text); }

    .fee-bar-bg { height: 10px; border-radius: 20px; background: var(--border, #e2e8f0); overflow: hidden; }
    .fee-bar-fill { height: 100%; border-radius: 20px; background: linear-gradient(90deg, var(--first-color, #ff5532), #ffa032); transition: width 0.8s ease; }

    .att-row { display:flex; align-items:center; gap:0.75rem; padding:0.7rem 1.25rem; border-bottom:1px solid var(--border, #e2e8f0); font-size:0.875rem; }
    .att-row:last-child { border-bottom: none; }
    .att-dot { width:10px; height:10px; border-radius:50%; flex-shrink:0; }

    .milestone-item { display:flex; align-items:center; gap:0.75rem; padding:0.75rem 1.25rem; border-bottom:1px solid var(--border, #e2e8f0); }
    .milestone-item:last-child { border-bottom: none; }
    .ms-dot { width:10px; height:10px; border-radius:50%; flex-shrink:0; }

    .info-chip2 { display:inline-flex; align-items:center; gap:5px; background:var(--surface-soft,#f8fafc); border:1px solid var(--border,#e2e8f0); border-radius:20px; padding:4px 10px; font-size:0.78rem; font-weight:600; margin:2px; }
</style>

<div class="container-fluid px-0">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ── ROW 1: Profile + Stats ── --}}
    <div class="row g-3 mb-4">

        {{-- Profile --}}
        <div class="col-12 col-md-4 col-xl-3">
            <div class="card premium-stat-card p-4 text-center h-100">
                <div style="width:72px; height:72px; border-radius:50%; background:rgba(255,85,50,0.08); color:var(--first-color); display:flex; align-items:center; justify-content:center; font-size:1.75rem; margin:0 auto 1rem;">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <h5 class="fw-bold mb-1">{{ $student->first_name }} {{ $student->last_name }}</h5>
                <p style="font-size:0.85rem; color:var(--muted); margin-bottom:0.75rem;">
                    {{ $student->course?->name ?? 'No course enrolled' }}
                </p>
                <div>
                    <span class="info-chip2" style="color:var(--first-color);"><i class="fas fa-id-card"></i> {{ $student->admission_no }}</span>
                    @if($student->course)
                        <span class="info-chip2"><i class="fas fa-book"></i> {{ $student->course_duration ?? 'N/A' }}</span>
                    @endif
                    @if($student->status)
                        <span class="info-chip2" style="color:#10b981;"><i class="fas fa-circle"></i> Active</span>
                    @endif
                </div>
                @if($student->admission_date)
                    <div class="mt-3 pt-3" style="border-top:1px solid var(--border); font-size:0.75rem; color:var(--muted);">
                        Admitted: <strong>{{ \Carbon\Carbon::parse($student->admission_date)->format('M d, Y') }}</strong>
                    </div>
                @endif
            </div>
        </div>

        {{-- Attendance Stats + Fee Stats --}}
        <div class="col-12 col-md-8 col-xl-9">
            <div class="row g-3">
                <div class="col-6 col-xl-3">
                    <div class="s-stat-card">
                        <div class="s-stat-icon" style="background:rgba(16,185,129,0.1); color:#10b981;"><i class="fas fa-calendar-check"></i></div>
                        <div><div class="s-stat-label">Present</div><div class="s-stat-value">{{ $presentDays }}</div></div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="s-stat-card">
                        <div class="s-stat-icon" style="background:rgba(239,68,68,0.1); color:#ef4444;"><i class="fas fa-calendar-times"></i></div>
                        <div><div class="s-stat-label">Absent</div><div class="s-stat-value">{{ $absentDays }}</div></div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="s-stat-card">
                        <div class="s-stat-icon" style="background:rgba(245,158,11,0.1); color:#f59e0b;"><i class="fas fa-clock"></i></div>
                        <div><div class="s-stat-label">Attendance</div><div class="s-stat-value">{{ $attendancePercentage }}%</div></div>
                    </div>
                </div>
                @if($biometricFine > 0)
                <div class="col-6 col-xl-3">
                    <div class="s-stat-card">
                        <div class="s-stat-icon" style="background:rgba(239,68,68,0.1); color:#ef4444;"><i class="fas fa-exclamation-triangle"></i></div>
                        <div><div class="s-stat-label">Att. Fine</div><div class="s-stat-value">₹{{ number_format($biometricFine, 0) }}</div></div>
                    </div>
                </div>
                @endif

                {{-- Fee Summary Card --}}
                <div class="col-12">
                    <div class="card premium-stat-card p-0 overflow-hidden">
                        <div class="premium-card-header bg-transparent border-bottom p-3 px-4 d-flex align-items-center justify-content-between">
                            <h6 class="mb-0 fw-bold d-flex align-items-center gap-2">
                                <i class="fas fa-file-invoice-dollar text-first"></i> Fee Status
                            </h6>
                            @if($remainingCourseFee > 0)
                                <span class="badge" style="background:rgba(239,68,68,0.1); color:#ef4444; border-radius:8px; padding:5px 10px; font-size:0.75rem;">
                                    ₹{{ number_format($remainingCourseFee, 0) }} Pending
                                </span>
                            @else
                                <span class="badge" style="background:rgba(16,185,129,0.1); color:#10b981; border-radius:8px; padding:5px 10px; font-size:0.75rem;">
                                    <i class="fas fa-check-circle me-1"></i> Fully Paid
                                </span>
                            @endif
                        </div>
                        <div class="p-4">
                            <div class="row g-3 mb-3">
                                <div class="col-4 text-center">
                                    <div style="font-size:0.75rem; font-weight:700; text-transform:uppercase; color:var(--muted); margin-bottom:4px;">Total Fee</div>
                                    <div style="font-size:1.1rem; font-weight:800; font-family:'Outfit',sans-serif;">₹{{ number_format($netCourseFee ?? $totalFees, 0) }}</div>
                                </div>
                                <div class="col-4 text-center">
                                    <div style="font-size:0.75rem; font-weight:700; text-transform:uppercase; color:#10b981; margin-bottom:4px;">Paid</div>
                                    <div style="font-size:1.1rem; font-weight:800; font-family:'Outfit',sans-serif; color:#10b981;">₹{{ number_format($coursePaid, 0) }}</div>
                                </div>
                                <div class="col-4 text-center">
                                    <div style="font-size:0.75rem; font-weight:700; text-transform:uppercase; color:#ef4444; margin-bottom:4px;">Remaining</div>
                                    <div style="font-size:1.1rem; font-weight:800; font-family:'Outfit',sans-serif; color:#ef4444;">₹{{ number_format($remainingCourseFee, 0) }}</div>
                                </div>
                            </div>
                            @php
                                $totalBase = max(1, $netCourseFee ?? $totalFees);
                                $paidPercent = min(100, round(($coursePaid / $totalBase) * 100));
                            @endphp
                            <div style="margin-bottom:6px; display:flex; justify-content:space-between; font-size:0.8rem; color:var(--muted);">
                                <span>Payment Progress</span><span>{{ $paidPercent }}% paid</span>
                            </div>
                            <div class="fee-bar-bg">
                                <div class="fee-bar-fill" style="width:{{ $paidPercent }}%;"></div>
                            </div>
                            @if($netMonthlyFee > 0)
                                <div style="margin-top:10px; font-size:0.8rem; color:var(--muted);">
                                    Monthly installment: <strong>₹{{ number_format($netMonthlyFee, 0) }}</strong>
                                    ({{ $student->fee_tenure ?? 'per tenure' }})
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── ROW 2: Attendance Records + Course/Syllabus ── --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-6">
            <div class="card premium-stat-card p-0 overflow-hidden">
                <div class="premium-card-header bg-transparent border-bottom p-3 px-4">
                    <h6 class="mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="fas fa-clipboard-list text-first"></i> Attendance History
                    </h6>
                </div>
                <div style="max-height:320px; overflow-y:auto;">
                    @forelse($attendances as $att)
                        <div class="att-row">
                            <div class="att-dot" style="background: {{ $att->status === 'Present' ? '#10b981' : ($att->status === 'Late' ? '#f59e0b' : '#ef4444') }};"></div>
                            <div style="flex:1;">
                                <div style="font-weight:600;">{{ \Carbon\Carbon::parse($att->attendance_date)->format('D, M d Y') }}</div>
                                @if($att->check_in_time)
                                    <div style="font-size:0.75rem; color:var(--muted);">Check-in: {{ \Carbon\Carbon::parse($att->check_in_time)->format('h:i A') }}</div>
                                @endif
                                @if($att->fine > 0)
                                    <div style="font-size:0.75rem; color:#ef4444; font-weight:600;"><i class="fas fa-exclamation-triangle"></i> Fine: ₹{{ number_format($att->fine, 0) }}</div>
                                @endif
                            </div>
                            <span class="status-badge status-{{ strtolower($att->status) }}" style="padding:3px 8px; border-radius:6px; font-size:0.75rem;">{{ $att->status }}</span>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-calendar-xmark fa-2x mb-2 d-block"></i>No attendance records yet.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Course Details --}}
        <div class="col-12 col-lg-6">
            <div class="card premium-stat-card p-0 overflow-hidden h-100">
                <div class="premium-card-header bg-transparent border-bottom p-3 px-4">
                    <h6 class="mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="fas fa-graduation-cap text-first"></i> Course Details
                    </h6>
                </div>
                @if($student->course)
                    <div class="p-4">
                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1rem; margin-bottom:1.25rem;">
                            <div style="background:var(--surface-soft,#f8fafc); border-radius:10px; padding:1rem;">
                                <div style="font-size:0.7rem; font-weight:700; text-transform:uppercase; color:var(--muted);">Course</div>
                                <div style="font-weight:700; margin-top:4px;">{{ $student->course->name }}</div>
                            </div>
                            <div style="background:var(--surface-soft,#f8fafc); border-radius:10px; padding:1rem;">
                                <div style="font-size:0.7rem; font-weight:700; text-transform:uppercase; color:var(--muted);">Duration</div>
                                <div style="font-weight:700; margin-top:4px;">{{ $student->course_duration ?? $student->course->duration ?? 'N/A' }}</div>
                            </div>
                            <div style="background:var(--surface-soft,#f8fafc); border-radius:10px; padding:1rem;">
                                <div style="font-size:0.7rem; font-weight:700; text-transform:uppercase; color:var(--muted);">Course Fee</div>
                                <div style="font-weight:700; margin-top:4px; color:var(--first-color);">₹{{ number_format($student->course->fee ?? 0, 0) }}</div>
                            </div>
                            <div style="background:var(--surface-soft,#f8fafc); border-radius:10px; padding:1rem;">
                                <div style="font-size:0.7rem; font-weight:700; text-transform:uppercase; color:var(--muted);">Fee Tenure</div>
                                <div style="font-weight:700; margin-top:4px;">{{ $student->fee_tenure ?? 'N/A' }}</div>
                            </div>
                        </div>
                        @if($student->course->syllabus_path)
                            <a href="{{ Storage::url($student->course->syllabus_path) }}" target="_blank" class="button button-primary w-100">
                                <i class="fas fa-file-pdf me-2"></i>Download Syllabus
                            </a>
                        @else
                            <div style="text-align:center; padding:1rem; background:var(--surface-soft,#f8fafc); border-radius:10px; font-size:0.85rem; color:var(--muted);">
                                <i class="fas fa-file-pdf fa-2x mb-2 d-block"></i>Syllabus not yet uploaded by admin.
                            </div>
                        @endif
                        @if($biometricFine > 0)
                            <div style="margin-top:1rem; background:rgba(239,68,68,0.06); border:1px solid rgba(239,68,68,0.2); border-radius:10px; padding:1rem; font-size:0.85rem; color:#ef4444;">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Attendance Fine this month: ₹{{ number_format($biometricFine, 0) }}</strong>
                                <div style="margin-top:4px; font-size:0.75rem;">{{ $fineDetails }}</div>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="p-4">
                        <div style="text-align:center; padding:2rem; background:var(--surface-soft,#f8fafc); border-radius:12px; margin-bottom:1rem;">
                            <i class="fas fa-book-open fa-2x mb-2" style="color:var(--muted);"></i>
                            <div style="font-weight:600; margin-top:8px; color:var(--muted);">No course enrolled yet</div>
                        </div>
                        <form method="POST" action="{{ route('student.select-course') }}">
                            @csrf
                            <select name="course_id" class="form-input w-100 mb-3" required>
                                <option value="">-- Select a Course to Enroll --</option>
                                @foreach($courses as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->duration ?? 'N/A' }})</option>
                                @endforeach
                            </select>
                            <button type="submit" class="button button-primary w-100"><i class="fas fa-check me-2"></i>Enroll Now</button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── ROW 3: Fee Invoices + Milestones ── --}}
    <div class="row g-3">
        <div class="col-12 col-lg-7">
            <div class="card premium-stat-card p-0 overflow-hidden">
                <div class="premium-card-header bg-transparent border-bottom p-3 px-4">
                    <h6 class="mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="fas fa-receipt text-first"></i> Fee Invoices
                    </h6>
                </div>
                <div class="table-responsive">
                    <table class="table premium-table align-middle mb-0">
                        <thead><tr class="table-light-head">
                            <th class="ps-4">Invoice No.</th>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Paid</th>
                            <th>Due</th>
                            <th>Status</th>
                            <th class="pe-4">Receipt</th>
                        </tr></thead>
                        <tbody>
                            @forelse($feeInvoices as $inv)
                                <tr>
                                    <td class="ps-4 fw-medium">#{{ $inv->invoice_no }}</td>
                                    <td style="font-size:0.8rem; color:var(--muted);">{{ $inv->fee_category }}</td>
                                    <td>₹{{ number_format($inv->total_amount, 0) }}</td>
                                    <td class="text-success">₹{{ number_format($inv->paid_amount, 0) }}</td>
                                    <td class="{{ $inv->due_amount > 0 ? 'text-danger fw-bold' : 'text-muted' }}">₹{{ number_format($inv->due_amount, 0) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $inv->status === 'Paid' ? 'success' : ($inv->status === 'Partial' ? 'warning' : 'danger') }}" style="font-size:0.75rem;">{{ $inv->status }}</span>
                                    </td>
                                    <td class="pe-4">
                                        <a href="{{ route('fee_invoices.show', $inv) }}" class="button button-secondary" style="padding:4px 10px; font-size:0.75rem;" target="_blank">
                                            <i class="fas fa-print me-1"></i>Print
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center py-5 text-muted">No fee invoices found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <div class="card premium-stat-card p-0 overflow-hidden h-100">
                <div class="premium-card-header bg-transparent border-bottom p-3 px-4 d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="fas fa-flag text-first"></i> Learning Milestones
                    </h6>
                    @if($totalMilestones > 0)
                        <span style="font-size:0.8rem; color:var(--muted);">{{ $completedMilestones }}/{{ $totalMilestones }} done</span>
                    @endif
                </div>
                @if($totalMilestones > 0)
                    <div style="padding:0.75rem 1.25rem; border-bottom:1px solid var(--border);">
                        <div class="fee-bar-bg">
                            <div class="fee-bar-fill" style="width:{{ $milestoneProgress }}%;"></div>
                        </div>
                        <div style="font-size:0.75rem; color:var(--muted); margin-top:4px;">{{ $milestoneProgress }}% complete</div>
                    </div>
                @endif
                <div style="max-height:280px; overflow-y:auto;">
                    @forelse($milestones as $ms)
                        <div class="milestone-item">
                            <div class="ms-dot" style="background: {{ $ms->status === 'Completed' ? '#10b981' : ($ms->status === 'In Progress' ? '#3b82f6' : ($ms->status === 'Skipped' ? '#94a3b8' : '#e2e8f0')) }};"></div>
                            <div style="flex:1;">
                                <div style="font-weight:600; font-size:0.875rem;">{{ $ms->title }}</div>
                                @if($ms->target_date)
                                    <div style="font-size:0.75rem; color:var(--muted);">Target: {{ \Carbon\Carbon::parse($ms->target_date)->format('M d, Y') }}</div>
                                @endif
                            </div>
                            <span class="badge bg-{{ $ms->status === 'Completed' ? 'success' : ($ms->status === 'In Progress' ? 'primary' : 'secondary') }}" style="font-size:0.7rem;">{{ $ms->status }}</span>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-flag fa-2x mb-2 d-block"></i>No milestones set yet.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
