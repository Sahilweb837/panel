@extends('layouts.app')

@section('title', 'Bulk Monthly Fee Generator')
@section('page-title', 'Bulk Monthly Fee Generator')

@section('content')
<style>
    :root {
        --bg-primary: #121212;
        --surface-card: #1E1E1E;
        --accent-primary: #ff5532;
        --accent-alert: #FF453A;
        --accent-status: #32D74B;
        --text-primary: #FFFFFF;
        --text-secondary: #98989D;
        --border-sutil: #2C2C2E;
    }

    .card {
        background-color: var(--surface-card) !important;
        border: 1px solid var(--border-sutil) !important;
        border-radius: 16px !important;
        color: var(--text-primary) !important;
    }

    .card-header, .card-footer {
        border-color: var(--border-sutil) !important;
        background-color: var(--surface-card) !important;
        color: var(--text-primary) !important;
    }

    .table {
        color: var(--text-primary) !important;
    }

    .table-light, .table th {
        background-color: var(--surface-card) !important;
        color: var(--text-secondary) !important;
        border-bottom: 1px solid var(--border-sutil) !important;
    }

    .table tbody tr {
        border-bottom: 1px solid var(--border-sutil) !important;
    }

    .table tbody tr:hover {
        background-color: #252525 !important;
    }

    .btn-primary {
        background-color: var(--accent-primary) !important;
        color: #FFFFFF !important;
        border: none !important;
    }

    .btn-primary:hover {
        background-color: #e04423 !important;
    }

    .btn-outline-secondary {
        color: var(--text-secondary) !important;
        border-color: var(--border-sutil) !important;
    }

    .btn-outline-secondary:hover {
        background-color: var(--border-sutil) !important;
        color: #FFFFFF !important;
    }

    .form-select, .form-control {
        background-color: var(--bg-primary) !important;
        border: 1px solid var(--border-sutil) !important;
        color: var(--text-primary) !important;
    }

    .form-select:focus, .form-control:focus {
        border-color: var(--accent-primary) !important;
        box-shadow: 0 0 8px rgba(255, 85, 50, 0.2) !important;
    }

    .badge-generated {
        background-color: rgba(50, 215, 75, 0.15);
        color: var(--accent-status);
        border: 1px solid rgba(50, 215, 75, 0.3);
    }

    .badge-pending {
        background-color: rgba(255, 159, 11, 0.15);
        color: #f59e0b;
        border: 1px solid rgba(255, 159, 11, 0.3);
    }
</style>

<div class="container-fluid py-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 mb-4 p-3 d-flex align-items-center gap-2" role="alert" style="background-color: rgba(50, 215, 75, 0.15); color: var(--accent-status); border-left: 4px solid var(--accent-status) !important;">
            <i class="fas fa-check-circle fa-lg"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 mb-4 p-3 d-flex align-items-center gap-2" role="alert" style="background-color: rgba(255, 69, 58, 0.15); color: var(--accent-alert); border-left: 4px solid var(--accent-alert) !important;">
            <i class="fas fa-exclamation-circle fa-lg"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Selector Card -->
    <div class="card shadow-sm mb-4 rounded-4">
        <div class="card-header bg-transparent py-3">
            <h5 class="fw-bold mb-0 text-white"><i class="fas fa-calendar-alt text-primary me-2"></i>Select Billing Period</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('fee_invoices.bulk-generate') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="month" class="form-label text-muted small fw-bold">Select Billing Month</label>
                    <select name="month" id="month" class="form-select rounded-3">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ Carbon\Carbon::create()->month($m)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="year" class="form-label text-muted small fw-bold">Select Billing Year</label>
                    <select name="year" id="year" class="form-select rounded-3">
                        @for($y = 2024; $y <= 2030; $y++)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100 py-2.5 rounded-3 fw-bold shadow-sm">
                        <i class="fas fa-sync-alt me-2"></i>Fetch & Calculate Dues
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Generator Card -->
    <form method="POST" action="{{ route('fee_invoices.bulk-generate.post') }}">
        @csrf
        <input type="hidden" name="billing_month" value="{{ $month }}">
        <input type="hidden" name="billing_year" value="{{ $year }}">

        <div class="card shadow-sm rounded-4">
            <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-0 text-white"><i class="fas fa-tasks text-primary me-2"></i>Manual Crosscheck & Generate</h5>
                    <p class="text-muted small mb-0 mt-1">Review the calculated amounts below before generating invoices.</p>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-bold shadow-sm" id="btn-generate" disabled>
                        <i class="fas fa-file-invoice-dollar me-2"></i>Generate Invoices
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 50px;" class="text-center">
                                    <input type="checkbox" id="check-all" class="form-check-input">
                                </th>
                                <th>Student Details</th>
                                <th>Course</th>
                                <th>Cycle</th>
                                <th>Monthly Dues</th>
                                <th>Discount</th>
                                <th>Late Fine</th>
                                <th>Att. Fine</th>
                                <th>Total Payable</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($studentsData as $data)
                                <tr>
                                    <td class="text-center">
                                        @if(!$data['has_invoice'])
                                            <input type="checkbox" name="student_ids[]" value="{{ $data['student']->id }}" class="form-check-input student-check">
                                        @else
                                            <i class="fas fa-lock text-muted" title="Invoice already exists"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-bold text-white d-block">{{ $data['student']->first_name }} {{ $data['student']->last_name }}</span>
                                        <span class="text-muted small">Adm. No: {{ $data['student']->admission_no }}</span>
                                    </td>
                                    <td>{{ $data['student']->course->name ?? 'N/A' }}</td>
                                    <td><span class="badge bg-secondary rounded-pill px-2.5 py-1">{{ $data['student']->fee_tenure ?? '1 Year' }}</span></td>
                                    <td>₹{{ number_format($data['net_monthly_fee'] + $data['discount'], 2) }}</td>
                                    <td class="text-danger">-₹{{ number_format($data['discount'], 2) }}</td>
                                    <td>₹{{ number_format($data['late_fine'], 2) }}</td>
                                    <td>₹{{ number_format($data['attendance_fine'], 2) }}</td>
                                    <td class="fw-bold text-success">₹{{ number_format($data['total_amount'], 2) }}</td>
                                    <td>
                                        @if($data['has_invoice'])
                                            <span class="badge badge-generated rounded-pill px-2.5 py-1.5 fw-bold">
                                                <i class="fas fa-check-circle me-1"></i>Generated ({{ $data['existing_invoice_no'] }})
                                            </span>
                                        @else
                                            <span class="badge badge-pending rounded-pill px-2.5 py-1.5 fw-bold">
                                                <i class="fas fa-clock me-1"></i>Pending
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-5">
                                        <i class="fas fa-user-slash fa-2x mb-3 opacity-50"></i>
                                        <p class="mb-0">No active students found with courses mapping.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkAll = document.getElementById('check-all');
        const checkboxes = document.querySelectorAll('.student-check');
        const generateBtn = document.getElementById('btn-generate');

        function updateGenerateButtonState() {
            const checkedCount = document.querySelectorAll('.student-check:checked').length;
            generateBtn.disabled = checkedCount === 0;
            if (checkedCount > 0) {
                generateBtn.innerText = `Generate Invoices (${checkedCount})`;
            } else {
                generateBtn.innerText = 'Generate Invoices';
            }
        }

        if (checkAll) {
            checkAll.addEventListener('change', function () {
                checkboxes.forEach(cb => {
                    cb.checked = this.checked;
                });
                updateGenerateButtonState();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function () {
                if (!this.checked) {
                    checkAll.checked = false;
                } else {
                    const allChecked = document.querySelectorAll('.student-check:checked').length === checkboxes.length;
                    checkAll.checked = allChecked;
                }
                updateGenerateButtonState();
            });
        });
    });
</script>
@endsection
