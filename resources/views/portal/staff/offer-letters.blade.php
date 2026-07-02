@extends('layouts.app')

@section('title', 'My Offer Letters')
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
    .badge.bg-info { background-color: rgba(0, 229, 255, 0.15) !important; color: var(--accent-primary) !important; }

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
        <div>
            <a href="{{ route('staff.dashboard') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
            </a>
        </div>
        <h4 class="fw-bold mb-0"><i class="fas fa-file-alt text-primary me-2"></i>My Offer Letters</h4>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Offer No</th>
                            <th>Designation</th>
                            <th>Department</th>
                            <th>Offered Salary</th>
                            <th>Joining Date</th>
                            <th>Valid Until</th>
                            <th>Status</th>
                            <th>Letter</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($offerLetters as $letter)
                            <tr>
                                <td class="fw-medium">{{ $letter->offer_letter_no }}</td>
                                <td>{{ $letter->designation }}</td>
                                <td>{{ $letter->department ?? 'N/A' }}</td>
                                <td class="text-success fw-bold">₹{{ number_format($letter->offered_salary, 2) }}</td>
                                <td>{{ \Carbon\Carbon::parse($letter->joining_date)->format('M d, Y') }}</td>
                                <td>{{ $letter->valid_until ? \Carbon\Carbon::parse($letter->valid_until)->format('M d, Y') : 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $letter->status === 'Accepted' ? 'success' : ($letter->status === 'Rejected' ? 'danger' : 'warning') }} rounded-pill">
                                        {{ $letter->status }}
                                    </span>
                                </td>
                                <td>
                                    @if($letter->file_path)
                                        <a href="{{ Storage::url($letter->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary py-1 px-2">
                                            <i class="fas fa-download me-1"></i>View / Download
                                        </a>
                                    @else
                                        <span class="text-muted small">Not uploaded</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No offer letters found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
