@extends('layouts.app')

@section('title', 'Salary Slips')
@section('page-title', 'Staff Salary Slip Management')

@section('content')
    <div class="salary-container">
        <!-- Lazy Loading Skeleton Overlay -->
        <div class="skeleton-loader-overlay" id="page-skeleton">
            <div class="toolbar mb-4">
                <div class="sk-card" style="width: 280px; height: 42px;"></div>
                <div class="sk-card" style="width: 150px; height: 42px;"></div>
                <div class="sk-card" style="width: 130px; height: 42px;"></div>
            </div>
            <div class="card premium-form-card" style="max-width: 100%;">
                <div class="sk-text heading"></div>
                <div class="sk-row"><div class="sk-text"></div></div>
                <div class="sk-row"><div class="sk-text"></div></div>
                <div class="sk-row"><div class="sk-text"></div></div>
                <div class="sk-row"><div class="sk-text"></div></div>
            </div>
        </div>

        <!-- Real Content -->
        <div id="page-content" style="opacity: 0; transition: opacity 0.5s ease;">
            <div class="toolbar mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <form method="GET" action="{{ route('salary_slips.index') }}" class="filter-form d-flex align-items-center gap-2 flex-grow-1">
                    <div style="position: relative; flex: 1;">
                        <input type="text" name="search" placeholder="Search by employee name or code..." value="{{ request('search') }}" class="form-input" style="padding-left: 36px;" />
                        <i class="fas fa-search text-muted position-absolute" style="left: 14px; top: 50%; transform: translateY(-50%);"></i>
                    </div>
                    <button type="submit" class="button button-secondary px-4 py-2">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                    @if(request('search'))
                        <a href="{{ route('salary_slips.index') }}" class="button button-secondary px-3 py-2">
                            <i class="fas fa-undo"></i>
                        </a>
                    @endif
                </form>
                <a href="{{ route('salary_slips.create') }}" class="button button-primary py-2 px-4">
                    <i class="fas fa-plus me-2"></i>Generate Salary Slip
                </a>
            </div>

            <div class="card premium-stat-card p-0 table-card overflow-hidden">
                <div class="premium-card-header bg-transparent border-bottom p-4">
                    <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="fas fa-wallet text-first"></i> Staff Payroll Salary Slips Registry
                    </h5>
                </div>
                <div class="card-body p-0">
                    <table class="table premium-table table-hover align-middle mb-0">
                        <thead>
                            <tr class="table-light-head">
                                <th class="ps-4"><i class="fas fa-id-card me-1"></i> Staff Member</th>
                                <th><i class="fas fa-calendar-days me-1"></i> Payroll Period</th>
                                <th><i class="fas fa-coins me-1"></i> Net Pay (INR)</th>
                                <th><i class="fas fa-toggle-on me-1"></i> Status</th>
                                <th><i class="fas fa-calendar-check me-1"></i> Payment Date</th>
                                <th class="text-end pe-4"><i class="fas fa-cogs me-1"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($salarySlips as $slip)
                                <tr>
                                    <td class="ps-4">
                                        <div class="user-info">
                                            <div class="avatar" style="font-weight: 700; background: rgba(255, 85, 50, 0.1); color: var(--first-color);">
                                                {{ strtoupper(substr($slip->employee->user?->name ?? 'S', 0, 1)) }}
                                            </div>
                                            <div>
                                                <strong class="text-dark-title">{{ $slip->employee->user?->name ?? 'No login' }}</strong>
                                                <p class="text-muted small">Code: {{ $slip->employee->employee_code }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-muted fw-semibold">{{ $slip->month }} {{ $slip->year }}</td>
                                    <td class="text-muted fw-bold">{{ number_format($slip->net_pay, 2) }}</td>
                                    <td>
                                        @php
                                            $statusClass = strtolower($slip->status);
                                        @endphp
                                        <span class="status-badge status-{{ $statusClass }}" style="padding: 4px 10px; border-radius: 6px; font-weight: 600; font-size: 0.8rem;">
                                            <i class="fas fa-{{ $statusClass === 'paid' ? 'check-circle' : 'hourglass-half' }} me-1"></i>
                                            {{ $slip->status }}
                                        </span>
                                    </td>
                                    <td class="text-muted">{{ $slip->payment_date ? \Carbon\Carbon::parse($slip->payment_date)->format('M d, Y') : 'Not Paid / Pending' }}</td>
                                    <td class="text-end pe-4 action-cell">
                                        <a href="{{ route('salary_slips.show', $slip) }}" class="button button-secondary small py-1.5 px-3" target="_blank">
                                            <i class="fas fa-print me-1"></i>Print
                                        </a>
                                        <form action="{{ route('salary_slips.destroy', $slip) }}" method="POST" class="inline-form d-inline" onsubmit="return confirm('Are you sure you want to delete this salary slip record?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="button button-danger small py-1.5 px-3">
                                                <i class="fas fa-trash me-1"></i>Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fas fa-users-slash fa-2x mb-3 d-block text-muted"></i>
                                        No payroll salary slips generated yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="pagination-wrapper mt-4">
                {{ $salarySlips->links() }}
            </div>
        </div>
    </div>

    <!-- Script to simulate dynamic lazy loading and skeleton fading -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const skeleton = document.getElementById('page-skeleton');
            const content = document.getElementById('page-content');
            
            setTimeout(() => {
                if (skeleton) skeleton.classList.add('fade-out');
                if (content) content.style.opacity = '1';
            }, 600);
        });
    </script>
@endsection
