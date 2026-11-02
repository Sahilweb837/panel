@extends('layouts.app')

@section('title', 'LMS Hub & Auto-Fee Management - Netcoder')
@section('page-title', 'LMS Hub & Automated Fees')

@section('content')
<style>
    :root {
        --nc-bg-dark: #0f0f12;
        --nc-card-dark: #18181c;
        --nc-card-hover: #202026;
        --nc-border-dark: rgba(255, 255, 255, 0.07);
        --nc-coral: #ff5733;
        --nc-coral-hover: #e04423;
        --nc-coral-glow: rgba(255, 87, 51, 0.25);
        --nc-text-muted: #8e8e98;
    }

    body {
        background-color: var(--nc-bg-dark) !important;
        color: #f1f1f5 !important;
        font-family: 'Inter', -apple-system, sans-serif;
    }

    .lms-container {
        max-width: 1440px;
        margin: 0 auto;
        padding: 24px;
    }

    .lms-card {
        background: var(--nc-card-dark);
        border: 1px solid var(--nc-border-dark);
        border-radius: 16px;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .lms-card:hover {
        border-color: rgba(255, 87, 51, 0.3);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.35);
    }

    .btn-coral {
        background: linear-gradient(135deg, var(--nc-coral), #f24822);
        color: #fff !important;
        border: none;
        font-weight: 600;
        border-radius: 10px;
        padding: 10px 20px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 14px var(--nc-coral-glow);
        transition: all 0.2s ease;
        text-decoration: none;
        cursor: pointer;
    }

    .btn-coral:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 87, 51, 0.45);
        background: linear-gradient(135deg, #ff6a4a, #ff5733);
    }

    .badge-coral {
        background: rgba(255, 87, 51, 0.12);
        color: var(--nc-coral);
        border: 1px solid rgba(255, 87, 51, 0.25);
        font-size: 0.75rem;
        font-weight: 700;
        border-radius: 6px;
        padding: 4px 10px;
        letter-spacing: 0.3px;
    }

    /* Skeleton Loading State Animation */
    .skeleton-loading .skeleton-box {
        display: inline-block;
        height: 100%;
        width: 100%;
        background: linear-gradient(90deg, #18181c 25%, #26262e 50%, #18181c 75%);
        background-size: 200% 100%;
        animation: skeleton-shimmer 1.5s infinite;
        border-radius: 8px;
    }

    @keyframes skeleton-shimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    .skeleton-table-row {
        height: 48px;
        margin-bottom: 8px;
    }

    .lms-table {
        width: 100%;
        color: #e5e5eb;
        border-collapse: separate;
        border-spacing: 0 8px;
    }

    .lms-table thead th {
        color: var(--nc-text-muted);
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        padding: 12px 16px;
        border: none;
    }

    .lms-table tbody tr {
        background: #18181c;
        border: 1px solid var(--nc-border-dark);
        border-radius: 12px;
        transition: transform 0.2s, background 0.2s;
    }

    .lms-table tbody tr:hover {
        background: #202026;
        transform: translateY(-1px);
    }

    .lms-table td {
        padding: 16px;
        border: none;
        vertical-align: middle;
    }

    .lms-table td:first-child {
        border-top-left-radius: 12px;
        border-bottom-left-radius: 12px;
    }

    .lms-table td:last-child {
        border-top-right-radius: 12px;
        border-bottom-right-radius: 12px;
    }

    .modal-dark .modal-content {
        background: #18181c;
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #f1f1f5;
        border-radius: 18px;
    }

    .modal-dark .form-control, .modal-dark .form-select {
        background: #121214;
        border: 1px solid rgba(255, 255, 255, 0.12);
        color: #fff;
        border-radius: 8px;
        padding: 10px 14px;
    }

    .modal-dark .form-control:focus, .modal-dark .form-select:focus {
        border-color: var(--nc-coral);
        box-shadow: 0 0 0 2px var(--nc-coral-glow);
        background: #151518;
        color: #fff;
    }
</style>

<div class="lms-container">

    {{-- Breadcrumb & Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge-coral">
                    <i class="fas fa-graduation-cap me-1"></i> LMS Course Operations
                </span>
                <span style="color: var(--nc-text-muted); font-size: 0.8rem;">Connected Real-Time</span>
            </div>
            <h1 class="h2 fw-bold text-white mb-1">LMS Enrollment & Auto-Fee Manager</h1>
            <p style="color: var(--nc-text-muted); font-size: 0.92rem;" class="mb-0">
                Enroll students into online/offline LMS courses. Auto-generates student accounts and fee invoices with instant ledger sync.
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button type="button" class="btn btn-outline-secondary text-white border-secondary" onclick="toggleSkeleton()">
                <i class="fas fa-magic me-1"></i> Toggle Skeleton
            </button>
            <button type="button" class="btn-coral" data-bs-toggle="modal" data-bs-target="#quickEnrollModal">
                <i class="fas fa-user-plus"></i>
                <span>Enroll & Auto-Generate Fee</span>
            </button>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm d-flex align-items-center gap-3 mb-4" style="background: rgba(16, 185, 129, 0.15); color: #10b981; border-radius: 12px;">
            <i class="fas fa-check-circle fs-5"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    {{-- Real-time KPI Cards Grid --}}
    <div class="row g-3 mb-4" id="statsGrid">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="lms-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span style="color: var(--nc-text-muted); font-size: 0.78rem; text-transform: uppercase; font-weight: 700;">Enrolled Students</span>
                    <span style="width: 38px; height: 38px; background: rgba(255, 87, 51, 0.12); color: var(--nc-coral); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-users"></i>
                    </span>
                </div>
                <h2 class="h1 fw-bold text-white mb-1">{{ number_format($totalEnrolled) }}</h2>
                <div class="d-flex align-items-center gap-1" style="color: #10b981; font-size: 0.8rem; font-weight: 600;">
                    <i class="fas fa-check"></i>
                    <span>Active in Curriculum</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="lms-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span style="color: var(--nc-text-muted); font-size: 0.78rem; text-transform: uppercase; font-weight: 700;">Portal Active Users</span>
                    <span style="width: 38px; height: 38px; background: rgba(59, 130, 246, 0.12); color: #3b82f6; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-laptop-code"></i>
                    </span>
                </div>
                <h2 class="h1 fw-bold text-white mb-1">{{ number_format($activePortalUsers) }}</h2>
                <div class="d-flex align-items-center gap-1" style="color: #3b82f6; font-size: 0.8rem; font-weight: 600;">
                    <i class="fas fa-shield-alt"></i>
                    <span>Can Log In to LMS</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="lms-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span style="color: var(--nc-text-muted); font-size: 0.78rem; text-transform: uppercase; font-weight: 700;">Fees Auto-Collected</span>
                    <span style="width: 38px; height: 38px; background: rgba(16, 185, 129, 0.12); color: #10b981; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </span>
                </div>
                <h2 class="h1 fw-bold text-white mb-1">₹{{ number_format($totalLmsRevenue) }}</h2>
                <div class="d-flex align-items-center gap-1" style="color: #10b981; font-size: 0.8rem; font-weight: 600;">
                    <i class="fas fa-arrow-up"></i>
                    <span>Paid via Invoices</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="lms-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span style="color: var(--nc-text-muted); font-size: 0.78rem; text-transform: uppercase; font-weight: 700;">Pending LMS Fees</span>
                    <span style="width: 38px; height: 38px; background: rgba(239, 68, 68, 0.12); color: #ef4444; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-clock"></i>
                    </span>
                </div>
                <h2 class="h1 fw-bold text-white mb-1">₹{{ number_format($pendingLmsFees) }}</h2>
                <div class="d-flex align-items-center gap-1" style="color: #ef4444; font-size: 0.8rem; font-weight: 600;">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Balance Due</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="lms-card p-3 mb-4">
        <form method="GET" action="{{ route('lms.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-dark border-secondary text-secondary"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control bg-dark border-secondary text-white" placeholder="Search student name, roll number, or admission ID..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-12 col-md-4">
                <select name="course_id" class="form-select bg-dark border-secondary text-white">
                    <option value="">All Courses & Programs</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                            {{ $course->name }} (₹{{ number_format($course->fee) }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <button type="submit" class="btn btn-outline-light w-100"><i class="fas fa-filter me-1"></i> Filter</button>
            </div>
            <div class="col-6 col-md-1">
                <a href="{{ route('lms.index') }}" class="btn btn-outline-secondary w-100"><i class="fas fa-redo"></i></a>
            </div>
        </form>
    </div>

    {{-- Students & Auto-Fee Ledger Table --}}
    <div class="lms-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="fw-bold text-white mb-0">Enrolled Students & Automated Fee Records</h4>
                <small style="color: var(--nc-text-muted);">Manage LMS portal status, view auto-invoices and payment receipts</small>
            </div>
            <span class="badge bg-dark border border-secondary text-light px-3 py-2">
                Total: {{ $students->total() }} records
            </span>
        </div>

        {{-- Skeleton Loading Placeholder (Toggled via JS or while loading) --}}
        <div id="skeletonContainer" class="skeleton-loading" style="display: none;">
            @for($i = 0; $i < 5; $i++)
                <div class="skeleton-box skeleton-table-row"></div>
            @endfor
        </div>

        {{-- Actual Table Content --}}
        <div id="tableContent" class="table-responsive">
            <table class="lms-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Admission ID</th>
                        <th>Course Enrolled</th>
                        <th>Fee Status</th>
                        <th>Total / Due</th>
                        <th>Portal Access</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $st)
                        @php
                            $latestInvoice = $st->feeInvoices->first();
                            $status = $latestInvoice ? $latestInvoice->status : ($st->course ? 'Unpaid' : 'No Course');
                            $statusBadge = match($status) {
                                'Paid' => 'bg-success',
                                'Partial' => 'bg-warning text-dark',
                                'Unpaid' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                            $totalAmount = $latestInvoice ? $latestInvoice->total_amount : ($st->course?->fee ?? 0);
                            $dueAmount = $latestInvoice ? $latestInvoice->due_amount : $totalAmount;
                        @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, #ff5733, #7928ca); display: flex; align-items: center; justify-content: center; font-weight: 700; color: #fff;">
                                        {{ strtoupper(substr($st->first_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-white">{{ $st->first_name }} {{ $st->last_name }}</div>
                                        <small style="color: var(--nc-text-muted);">{{ $st->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-dark border border-secondary text-light font-monospace">
                                    {{ $st->admission_no }}
                                </span>
                            </td>
                            <td>
                                @if($st->course)
                                    <span class="badge-coral">{{ $st->course->name }}</span>
                                @else
                                    <span class="text-secondary">Not Assigned</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $statusBadge }} px-2 py-1">{{ $status }}</span>
                            </td>
                            <td>
                                <div class="text-white fw-bold">₹{{ number_format($totalAmount) }}</div>
                                @if($dueAmount > 0)
                                    <small class="text-danger">Due: ₹{{ number_format($dueAmount) }}</small>
                                @else
                                    <small class="text-success">Cleared</small>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('lms.toggle-portal', $st->id) }}" method="POST">
                                    @csrf
                                    @if($st->portal_active)
                                        <button type="submit" class="btn btn-sm btn-outline-success px-2 py-1 rounded-pill" title="Click to Suspend">
                                            <i class="fas fa-check-circle me-1"></i> Active
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1 rounded-pill" title="Click to Enable">
                                            <i class="fas fa-ban me-1"></i> Disabled
                                        </button>
                                    @endif
                                </form>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    @if($latestInvoice)
                                        <a href="{{ route('fee_invoices.show', $latestInvoice->id) }}" class="btn btn-sm btn-outline-light" title="View Fee Invoice">
                                            <i class="fas fa-receipt"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('students.show', $st->id) }}" class="btn btn-sm btn-outline-light" title="Student Profile">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-secondary">
                                <i class="fas fa-user-graduate fs-1 mb-3 d-block text-muted"></i>
                                No LMS enrolled students found. Click "Enroll & Auto-Generate Fee" to begin!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $students->links() }}
        </div>
    </div>
</div>

{{-- Quick Enroll & Auto-Fee Modal --}}
<div class="modal fade modal-dark" id="quickEnrollModal" tabindex="-1" aria-labelledby="quickEnrollModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form action="{{ route('lms.enroll') }}" method="POST">
                @csrf
                <div class="modal-header border-secondary">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge-coral"><i class="fas fa-bolt"></i> One-Click</span>
                        <h5 class="modal-title fw-bold text-white mb-0" id="quickEnrollModalLabel">Enroll in LMS & Auto-Generate Fee</h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="form-label text-secondary fw-semibold">Enrollment Target</label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="student_type" id="typeNew" value="new" checked onchange="toggleStudentType('new')">
                                <label class="form-check-label text-white" for="typeNew">
                                    Register New Student & Account
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="student_type" id="typeExisting" value="existing" onchange="toggleStudentType('existing')">
                                <label class="form-check-label text-white" for="typeExisting">
                                    Existing Student
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Existing Student Selector --}}
                    <div id="existingStudentSection" class="mb-3" style="display: none;">
                        <label class="form-label text-white fw-semibold">Select Student</label>
                        <select name="student_id" class="form-select">
                            <option value="">-- Choose Existing Student --</option>
                            @foreach($students as $st)
                                <option value="{{ $st->id }}">{{ $st->first_name }} {{ $st->last_name }} ({{ $st->admission_no }})</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- New Student Fields --}}
                    <div id="newStudentSection">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-white fw-semibold">First Name *</label>
                                <input type="text" name="first_name" class="form-control" placeholder="e.g. Rahul">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white fw-semibold">Last Name</label>
                                <input type="text" name="last_name" class="form-control" placeholder="e.g. Sharma">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white fw-semibold">Email Address *</label>
                                <input type="email" name="email" class="form-control" placeholder="rahul@example.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white fw-semibold">Phone Number</label>
                                <input type="text" name="phone" class="form-control" placeholder="+91 9876543210">
                            </div>
                        </div>
                    </div>

                    <hr class="border-secondary my-4">

                    <h6 class="text-coral fw-bold mb-3" style="color: var(--nc-coral);"><i class="fas fa-book-reader me-1"></i> Course & Automated Fee Details</h6>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-white fw-semibold">Target LMS Course *</label>
                            <select name="course_id" id="courseSelector" class="form-select" required onchange="updateCourseFee(this)">
                                <option value="">-- Select Course --</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" data-fee="{{ $course->fee }}">
                                        {{ $course->name }} (₹{{ number_format($course->fee) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white fw-semibold">Total Course Fee (₹) *</label>
                            <input type="number" step="0.01" name="course_fee" id="courseFeeInput" class="form-control" required placeholder="0.00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white fw-semibold">Initial Payment (₹)</label>
                            <input type="number" step="0.01" name="paid_amount" class="form-control" placeholder="0.00 (leave 0 if due)">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white fw-semibold">Payment Mode</label>
                            <select name="payment_method" class="form-select">
                                <option value="Online LMS">Online LMS Portal</option>
                                <option value="UPI / QR">UPI / QR Code</option>
                                <option value="Cash">Cash at Campus</option>
                                <option value="Bank Transfer">Bank Transfer / NEFT</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-white fw-semibold">Transaction Reference / UTR (Optional)</label>
                            <input type="text" name="transaction_id" class="form-control" placeholder="e.g. UPI-9988220011">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-coral">
                        <i class="fas fa-check-double"></i>
                        <span>Confirm Enrollment & Generate Invoice</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleStudentType(type) {
        if (type === 'existing') {
            document.getElementById('existingStudentSection').style.display = 'block';
            document.getElementById('newStudentSection').style.display = 'none';
        } else {
            document.getElementById('existingStudentSection').style.display = 'none';
            document.getElementById('newStudentSection').style.display = 'block';
        }
    }

    function updateCourseFee(select) {
        const option = select.options[select.selectedIndex];
        const fee = option.getAttribute('data-fee');
        if (fee) {
            document.getElementById('courseFeeInput').value = fee;
        }
    }

    function toggleSkeleton() {
        const sk = document.getElementById('skeletonContainer');
        const tb = document.getElementById('tableContent');
        if (sk.style.display === 'none') {
            sk.style.display = 'block';
            tb.style.display = 'none';
        } else {
            sk.style.display = 'none';
            tb.style.display = 'block';
        }
    }
</script>
@endsection

