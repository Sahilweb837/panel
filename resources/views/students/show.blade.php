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
                        @php
                            $badgeColor = $student->status ? 'success' : 'danger';
                            $statusText = $student->status ? 'Active' : 'Inactive';
                            if ($student->status && ($student->student_type ?? '') === 'Regular (Internship)') {
                                $badgeColor = 'info'; // Or custom color for intern
                                $statusText = 'Intern';
                            }
                        @endphp
                        <span class="badge bg-{{ $badgeColor }}">{{ $statusText }}</span>
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
                            <div class="mt-2" style="font-size: 0.75rem; color: var(--muted);">
                                <strong>Total Deal:</strong> ₹{{ number_format($netCourseFee ?? ($totalFees ?? 0), 0) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="metric-card">
                            <div class="metric-value {{ $dueFees > 0 ? 'text-danger' : 'text-success' }}">₹{{ number_format($dueFees, 0) }}</div>
                            <div class="metric-label">Course Due</div>
                            @if($student->fee_tenure)
                                @php
                                    $tenureMonths = match($student->fee_tenure) {
                                        '1 Month' => 1,
                                        '3 Months' => 3,
                                        '6 Months' => 6,
                                        '1 Year' => 12,
                                        default => 1,
                                    };
                                @endphp
                                <div class="mt-2" style="font-size: 0.75rem; color: var(--muted);">
                                    <strong>EMI:</strong> ₹{{ number_format((($netCourseFee ?? 0) / $tenureMonths), 0) }}/mo
                                </div>
                            @endif
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
                            @if($student->fee_tenure)
                            <tr>
                                <td class="text-muted"><i class="fas fa-calendar-days me-2"></i>Fee Tenure:</td>
                                <td class="fw-semibold text-first">{{ $student->fee_tenure }} Installment</td>
                            </tr>
                            @endif
                            <tr>
                                <td class="text-muted"><i class="fas fa-money-bill me-2"></i>Registration Fee:</td>
                                <td class="fw-semibold">₹{{ number_format($student->registration_fee ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted"><i class="fas fa-file-alt me-2"></i>Prospectus Fee:</td>
                                <td class="fw-semibold">₹{{ number_format($student->prospectus_fee ?? 0, 2) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($student->user || in_array(session('user_role_slug'), ['super-admin', 'superadmin', 'root-admin', 'admin', 'subadmin', 'sub-admin']))
                <!-- Student Portal Credentials Card -->
                <div class="card border border-primary border-opacity-25 rounded-3 p-3 mt-4" style="background: rgba(255, 85, 50, 0.03);">
                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="icon-wrapper d-grid place-items-center" style="width: 36px; height: 36px; border-radius: 10px; background: rgba(255, 85, 50, 0.15); color: var(--first-color);">
                                <i class="fas fa-key"></i>
                            </div>
                            <h6 class="fw-bold mb-0" style="font-size: 1rem; color: var(--dark-title);">Student Login Credentials</h6>
                        </div>
                        <span class="badge bg-success"><i class="fas fa-shield-check me-1"></i>Active Portal Access</span>
                    </div>

                    <div class="row g-3 align-items-center">
                        <div class="col-12 col-md-4">
                            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem;">Login Username / ID</small>
                            <div class="fw-bold text-dark-title" style="font-size: 0.95rem;">{{ $student->user?->username ?? $student->admission_no }}</div>
                        </div>
                        <div class="col-12 col-md-4">
                            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem;">Current Password (Plain Text)</small>
                            <div class="d-flex align-items-center gap-2">
                                <span id="show-raw-pw" class="fw-bold font-monospace" style="color: var(--first-color); font-size: 1rem;">
                                    {{ $student->user?->raw_password ?? ($student->admission_no ?? 'N/A') }}
                                </span>
                                <button type="button" class="btn btn-sm btn-light border p-1 px-2" onclick="navigator.clipboard.writeText('{{ $student->user?->raw_password ?? $student->admission_no }}')" title="Copy Password">
                                    <i class="fas fa-copy text-muted"></i>
                                </button>
                            </div>
                        </div>
                        @if(in_array(session('user_role_slug'), ['super-admin', 'superadmin', 'root-admin', 'admin', 'subadmin', 'sub-admin']) && $student->user_id)
                        <div class="col-12 col-md-4">
                            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem;">Update Password</small>
                            <div class="input-group input-group-sm">
                                <input type="text" id="show-new-password" class="form-control" placeholder="New Password..." minlength="6">
                                <button class="btn btn-primary" type="button" onclick="updateStudentPasswordDirect({{ $student->user_id }})">
                                    <i class="fas fa-save me-1"></i>Save
                                </button>
                            </div>
                            <div id="show-pw-msg" class="small mt-1" style="display:none;"></div>
                        </div>
                        @endif
                    </div>
                </div>
                <script>
                    function updateStudentPasswordDirect(userId) {
                        const newPw = document.getElementById('show-new-password').value.trim();
                        const msgEl = document.getElementById('show-pw-msg');
                        if (!newPw || newPw.length < 6) {
                            msgEl.style.display = 'block';
                            msgEl.className = 'small text-danger fw-bold mt-1';
                            msgEl.textContent = 'Minimum 6 characters required.';
                            return;
                        }

                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
                        fetch(`/sub-admins/${userId}/password-update`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ password: newPw })
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                document.getElementById('show-raw-pw').textContent = data.password;
                                document.getElementById('show-new-password').value = '';
                                msgEl.style.display = 'block';
                                msgEl.className = 'small text-success fw-bold mt-1';
                                msgEl.textContent = 'Password updated successfully!';
                                setTimeout(() => { msgEl.style.display = 'none'; }, 3000);
                            } else {
                                msgEl.style.display = 'block';
                                msgEl.className = 'small text-danger fw-bold mt-1';
                                msgEl.textContent = data.error || 'Failed to update.';
                            }
                        })
                        .catch(() => {
                            msgEl.style.display = 'block';
                            msgEl.className = 'small text-danger fw-bold mt-1';
                            msgEl.textContent = 'Error updating password.';
                        });
                    }
                </script>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Fee History -->
    <div class="col-12 col-xl-7">
        <div class="profile-card h-100 mb-0">
            <div class="card-header bg-transparent border-bottom p-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="fw-bold mb-0"><i class="fas fa-file-invoice-dollar text-first me-2"></i>Fee Receipt History</h5>
                <div class="d-flex align-items-center gap-2">
                    <select id="invoice-category-filter" class="form-select form-select-sm" style="width: auto;">
                        <option value="">All Categories</option>
                        <option value="fees">Monthly Fees</option>
                        <option value="registration">Registration</option>
                        <option value="prospectus">Prospectus</option>
                        <option value="seminar">Seminar</option>
                        <option value="fine">Fine</option>
                    </select>
                    <select id="invoice-status-filter" class="form-select form-select-sm" style="width: auto;">
                        <option value="">All Statuses</option>
                        <option value="paid">Paid</option>
                        <option value="unpaid">Unpaid</option>
                        <option value="partial">Partial</option>
                    </select>
                    <a href="{{ route('students.fee-report', $student->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="fas fa-print me-1"></i>Print Report</a>
                    <a href="{{ route('fee_invoices.create', ['student_id' => $student->id]) }}" class="btn btn-sm btn-outline-primary">Generate Receipt</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table history-table table-hover align-middle mb-0" id="invoice-history-table">
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
                                <tr class="invoice-row" data-category="{{ strtolower($invoice->fee_category) }}" data-status="{{ strtolower($invoice->status) }}">
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

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const categoryFilter = document.getElementById('invoice-category-filter');
        const statusFilter = document.getElementById('invoice-status-filter');
        const rows = document.querySelectorAll('.invoice-row');

        function filterInvoices() {
            const cat = categoryFilter.value.toLowerCase();
            const stat = statusFilter.value.toLowerCase();

            rows.forEach(row => {
                const rowCat = row.getAttribute('data-category');
                const rowStat = row.getAttribute('data-status');

                let showCat = true;
                if (cat) {
                    if (cat === 'fees' && !rowCat.includes('registration') && !rowCat.includes('prospectus') && !rowCat.includes('seminar') && !rowCat.includes('fine')) {
                        showCat = true;
                    } else if (rowCat.includes(cat)) {
                        showCat = true;
                    } else {
                        showCat = false;
                    }
                }

                const showStat = stat === '' || rowStat === stat;

                if (showCat && showStat) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        if (categoryFilter) categoryFilter.addEventListener('change', filterInvoices);
        if (statusFilter) statusFilter.addEventListener('change', filterInvoices);
    });
</script>
@endsection
