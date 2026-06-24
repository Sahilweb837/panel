@extends('layouts.app')

@section('title', 'Student Profile')
@section('page-title', 'Student Profile - ' . $student->first_name)

@section('content')
<style>
    .profile-card {
        background: var(--surface);
        border-radius: 16px;
        border: 1px solid var(--border);
        overflow: hidden;
        margin-bottom: 24px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
    }
    html[data-theme="dark"] .profile-card {
        background: rgba(31, 41, 55, 0.45);
        backdrop-filter: blur(12px);
    }
    .profile-header {
        background: linear-gradient(135deg, rgba(255, 85, 50, 0.1), rgba(255, 138, 0, 0.05));
        padding: 30px;
        display: flex;
        align-items: center;
        gap: 20px;
        border-bottom: 1px solid var(--border);
    }
    .profile-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: var(--first-color);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        font-weight: bold;
        box-shadow: 0 8px 20px rgba(255, 85, 50, 0.3);
    }
    .profile-info h3 {
        margin: 0 0 5px 0;
        font-weight: 700;
        color: var(--dark-title);
    }
    .profile-info p {
        margin: 0;
        color: var(--muted);
    }
    .metric-card {
        background: var(--surface-soft);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        transition: transform 0.2s;
    }
    .metric-card:hover {
        transform: translateY(-3px);
    }
    .metric-value {
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 5px;
        color: var(--dark-title);
    }
    .metric-label {
        font-size: 0.85rem;
        color: var(--muted);
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    .history-table th {
        font-size: 0.8rem;
        text-transform: uppercase;
        color: var(--muted);
        background: var(--surface-soft);
    }
</style>

<div class="row">
    <div class="col-12">
        <!-- Main Profile Card -->
        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-avatar">
                    {{ strtoupper(substr($student->first_name, 0, 1)) }}{{ substr($student->last_name, 0, 1) }}
                </div>
                <div class="profile-info flex-grow-1">
                    <h3>{{ $student->first_name }} {{ $student->last_name }}</h3>
                    <p>Admission No: <strong>{{ $student->admission_no }}</strong> | Course: <strong>{{ $student->course?->name ?? 'N/A' }}</strong></p>
                    <div class="mt-2">
                        <span class="badge bg-{{ $student->status ? 'success' : 'danger' }}">{{ $student->status ? 'Active' : 'Inactive' }}</span>
                        <span class="badge bg-primary">{{ $student->student_type }}</span>
                        @if($student->course_duration)
                            <span class="badge bg-secondary"><i class="fas fa-clock me-1"></i>{{ $student->course_duration }}</span>
                        @endif
                    </div>
                </div>
                <div>
                    <a href="{{ route('students.edit', $student) }}" class="btn btn-outline-primary">
                        <i class="fas fa-edit me-2"></i>Edit Profile
                    </a>
                </div>
            </div>
            
            <div class="card-body p-4">
                <div class="row g-4 mb-4">
                    <!-- Key Metrics -->
                    <div class="col-6 col-md-3">
                        <div class="metric-card">
                            <div class="metric-value text-success">₹{{ number_format($paidFees, 0) }}</div>
                            <div class="metric-label">Course Paid</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="metric-card">
                            <div class="metric-value {{ $dueFees > 0 ? 'text-danger' : 'text-success' }}">₹{{ number_format($dueFees, 0) }}</div>
                            <div class="metric-label">Course Due</div>
                            @if($dueSeminarFees > 0 || $dueFines > 0)
                                <div class="mt-2" style="font-size: 0.75rem;">
                                    @if($dueSeminarFees > 0)
                                        <span class="text-warning d-block"><i class="fas fa-exclamation-circle me-1"></i>Seminar Due: ₹{{ number_format($dueSeminarFees, 0) }}</span>
                                    @endif
                                    @if($dueFines > 0)
                                        <span class="text-danger d-block"><i class="fas fa-circle-exclamation me-1"></i>Fines Due: ₹{{ number_format($dueFines, 0) }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="metric-card">
                            <div class="metric-value text-primary">{{ $attendancePercentage }}%</div>
                            <div class="metric-label">Attendance Rate</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="metric-card">
                            <div class="metric-value text-warning">{{ $feeInvoices->count() }}</div>
                            <div class="metric-label">Total Receipts</div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Contact Details -->
                    <div class="col-md-6">
                        <h5 class="fw-bold mb-3 border-bottom pb-2">Contact Information</h5>
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td class="text-muted" width="130"><i class="fas fa-envelope me-2"></i>Email:</td>
                                <td class="fw-semibold">{{ $student->email ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted"><i class="fas fa-phone me-2"></i>Phone:</td>
                                <td class="fw-semibold">{{ $student->phone ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted"><i class="fas fa-user-shield me-2"></i>Guardian:</td>
                                <td class="fw-semibold">{{ $student->guardian_name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted"><i class="fas fa-map-marker-alt me-2"></i>Address:</td>
                                <td class="fw-semibold">{{ $student->current_address ?? ($student->address ?? 'N/A') }}</td>
                            </tr>
                        </table>
                    </div>
                    <!-- Academic Details -->
                    <div class="col-md-6">
                        <h5 class="fw-bold mb-3 border-bottom pb-2">Academic Information</h5>
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td class="text-muted" width="130"><i class="fas fa-book me-2"></i>Course:</td>
                                <td class="fw-semibold">{{ $student->course?->name ?? 'N/A' }} ({{ $student->course?->code ?? '-' }})</td>
                            </tr>
                            <tr>
                                <td class="text-muted"><i class="fas fa-calendar-alt me-2"></i>Joined:</td>
                                <td class="fw-semibold">{{ $student->admission_date ? \Carbon\Carbon::parse($student->admission_date)->format('d M Y') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted"><i class="fas fa-id-card me-2"></i>Biometric ID:</td>
                                <td class="fw-semibold">{{ $student->biometric_id ?? 'Not Assigned' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted"><i class="fas fa-fingerprint me-2"></i>Aadhar No:</td>
                                <td class="fw-semibold">{{ $student->aadhar_number ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Fee History -->
    <div class="col-12 col-xl-7">
        <div class="profile-card h-100 mb-0">
            <div class="card-header bg-transparent border-bottom p-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0"><i class="fas fa-file-invoice-dollar text-first me-2"></i>Fee Receipt History</h5>
                <div>
                    <a href="{{ route('students.fee-report', $student->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary me-2"><i class="fas fa-print me-1"></i>Print Report</a>
                    <a href="{{ route('fee_invoices.create', ['student_id' => $student->id]) }}" class="btn btn-sm btn-outline-primary">Generate Receipt</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table history-table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Receipt No</th>
                                <th>Date</th>
                                <th>Category</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($feeInvoices as $invoice)
                                <tr>
                                    <td class="fw-bold text-first ps-4">{{ $invoice->invoice_no }}</td>
                                    <td>{{ $invoice->created_at->format('d M Y') }}</td>
                                    <td><span class="text-muted" style="font-size: 0.85rem;">{{ $invoice->fee_category ?: 'Fees' }}</span></td>
                                    <td class="fw-bold">₹{{ number_format($invoice->total_amount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ strtolower($invoice->status) === 'paid' ? 'success' : (strtolower($invoice->status) === 'unpaid' ? 'danger' : 'warning') }} rounded-pill">
                                            {{ $invoice->status }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('fee_invoices.show', $invoice->id) }}" class="btn btn-sm btn-light rounded-circle" title="View"><i class="fas fa-eye text-first"></i></a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No fee history available for this student.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance History -->
    <div class="col-12 col-xl-5">
        <div class="profile-card h-100 mb-0">
            <div class="card-header bg-transparent border-bottom p-4">
                <h5 class="fw-bold mb-0"><i class="fas fa-calendar-check text-success me-2"></i>Recent Attendance</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table history-table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Date</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendances as $attendance)
                                <tr>
                                    <td class="fw-medium ps-4">{{ \Carbon\Carbon::parse($attendance->attendance_date)->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ strtolower($attendance->status) === 'present' ? 'success' : (strtolower($attendance->status) === 'absent' ? 'danger' : 'warning') }} rounded-pill">
                                            {{ $attendance->status }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4 text-muted small">
                                        {{ $attendance->remarks ?: '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">No recent attendance records.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3 text-center border-top">
                    <a href="{{ route('attendances.index', ['student_id' => $student->id]) }}" class="text-decoration-none fw-semibold">View Full Attendance Record <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
