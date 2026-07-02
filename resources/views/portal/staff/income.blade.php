@extends('layouts.app')

@section('title', 'My Income Records')
@section('page-title', 'Staff Portal')

@section('content')
<style>
    :root {
        --bg-primary: #121212;
        --surface-card: #1E1E1E;
        --accent-primary: #00E5FF;
        --accent-alert: #FF453A;
        --accent-status: #32D74B;
        --text-primary: #FFFFFF;
        --text-secondary: #98989D;
        --border-sutil: #2C2C2E;
    }

    body {
        background-color: var(--bg-primary) !important;
        color: var(--text-primary) !important;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
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

    .text-muted { color: var(--text-secondary) !important; }
    .text-dark { color: var(--text-primary) !important; }

    .fw-bold, .fw-black, .fw-semibold, .badge, td, .fs-5, h3, h4 {
        font-family: 'JetBrains Mono', 'Fira Code', monospace;
    }

    .bg-light {
        background-color: var(--surface-card) !important;
        border-color: var(--border-sutil) !important;
    }

    .table { color: var(--text-primary) !important; }
    .table-light, .table-light-head, .table th, .table-responsive thead th {
        background-color: var(--surface-card) !important;
        color: var(--text-secondary) !important;
        border-bottom: 1px solid var(--border-sutil) !important;
    }
    .table tbody tr { border-bottom: 1px solid var(--border-sutil) !important; }
    .table tbody tr:hover { background-color: #252525 !important; }

    .badge.bg-success { background-color: rgba(50, 215, 75, 0.15) !important; color: var(--accent-status) !important; }
    .badge.bg-danger { background-color: rgba(255, 69, 58, 0.15) !important; color: var(--accent-alert) !important; }
    .badge.bg-warning { background-color: rgba(255, 159, 11, 0.15) !important; color: #f59e0b !important; }

    .btn-outline-primary {
        color: var(--accent-primary) !important;
        border-color: var(--accent-primary) !important;
    }
    .btn-outline-primary:hover {
        background-color: var(--accent-primary) !important;
        color: #121212 !important;
    }
</style>

<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('staff.dashboard') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
        </a>
        <h4 class="fw-bold mb-0"><i class="fas fa-hand-holding-usd text-primary me-2"></i>My Income Records</h4>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body text-center p-4">
                    <i class="fas fa-rupee-sign fa-2x text-success mb-2"></i>
                    <h3 class="fw-bold mb-0">₹{{ number_format($totalIncome ?? 0, 2) }}</h3>
                    <span class="small text-muted">Total Income Received</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                            <th>Reference No</th>
                            <th>Description</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($incomeRecords as $record)
                            <tr>
                                <td class="fw-medium">{{ \Carbon\Carbon::parse($record->income_date)->format('M d, Y') }}</td>
                                <td>{{ $record->income_type }}</td>
                                <td class="text-success fw-bold">₹{{ number_format($record->amount, 2) }}</td>
                                <td>{{ $record->payment_method ?? 'N/A' }}</td>
                                <td class="small text-muted">{{ $record->reference_no ?? 'N/A' }}</td>
                                <td class="small text-muted">{{ Str::limit($record->description, 40) }}</td>
                                <td>
                                    <span class="badge bg-{{ $record->status === 'Received' ? 'success' : ($record->status === 'Failed' ? 'danger' : 'warning') }} rounded-pill">
                                        {{ $record->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No income records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
