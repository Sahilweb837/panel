@extends('layouts.app')

@section('title', 'Reports')
@section('page-title', 'Financial Reports & Analytics')

@section('content')
    <div class="invoice-container">
        <!-- Lazy Loading Skeleton Overlay -->
        <div class="skeleton-loader-overlay" id="page-skeleton">
            <div class="toolbar mb-4">
                <div class="sk-card" style="width: 320px; height: 42px;"></div>
                <div class="sk-card" style="width: 150px; height: 42px;"></div>
            </div>
            <div class="row g-4 mb-4">
                <div class="col-md-3"><div class="sk-card" style="height: 120px;"></div></div>
                <div class="col-md-3"><div class="sk-card" style="height: 120px;"></div></div>
                <div class="col-md-3"><div class="sk-card" style="height: 120px;"></div></div>
                <div class="col-md-3"><div class="sk-card" style="height: 120px;"></div></div>
            </div>
        </div>

        <!-- Real Content -->
        <div id="page-content" style="opacity: 0; transition: opacity 0.5s ease;">
            <!-- Filter Toolbar -->
            <div class="toolbar mb-4 bg-glass border p-4 rounded-4 shadow-sm">
                <form method="GET" action="{{ route('reports.index') }}" class="filter-form g-3 row align-items-end">
                    <div class="col-12 col-sm-6 col-md-3">
                        <label class="form-label small fw-bold text-muted"><i class="fas fa-calendar me-1"></i> From Date</label>
                        <div style="position: relative;">
                            <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-input" style="padding-left: 36px;" />
                            <i class="fas fa-calendar-alt text-muted position-absolute" style="left: 14px; top: 50%; transform: translateY(-50%);"></i>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <label class="form-label small fw-bold text-muted"><i class="fas fa-calendar me-1"></i> To Date</label>
                        <div style="position: relative;">
                            <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-input" style="padding-left: 36px;" />
                            <i class="fas fa-calendar-alt text-muted position-absolute" style="left: 14px; top: 50%; transform: translateY(-50%);"></i>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <label class="form-label small fw-bold text-muted"><i class="fas fa-book me-1"></i> Course</label>
                        <div style="position: relative;">
                            <select name="course_id" class="form-input" style="padding-left: 36px;">
                                <option value="">All Courses</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>{{ $course->name }}</option>
                                @endforeach
                            </select>
                            <i class="fas fa-graduation-cap text-muted position-absolute" style="left: 14px; top: 50%; transform: translateY(-50%);"></i>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <label class="form-label small fw-bold text-muted"><i class="fas fa-wallet me-1"></i> Payment Mode</label>
                        <div style="position: relative;">
                            <select name="payment_method" class="form-input" style="padding-left: 36px;">
                                <option value="">All Modes</option>
                                @foreach($paymentMethods as $pm)
                                    <option value="{{ $pm }}" {{ request('payment_method') === $pm ? 'selected' : '' }}>{{ $pm }}</option>
                                @endforeach
                            </select>
                            <i class="fas fa-credit-card text-muted position-absolute" style="left: 14px; top: 50%; transform: translateY(-50%);"></i>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3 mt-3">
                        <label class="form-label small fw-bold text-muted"><i class="fas fa-tags me-1"></i> Fee Category</label>
                        <div style="position: relative;">
                            <select name="fee_category" class="form-input" style="padding-left: 36px;">
                                <option value="">All Categories</option>
                                @foreach($feeCategories as $cat)
                                    <option value="{{ $cat }}" {{ request('fee_category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                            <i class="fas fa-tag text-muted position-absolute" style="left: 14px; top: 50%; transform: translateY(-50%);"></i>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3 mt-3">
                        <label class="form-label small fw-bold text-muted"><i class="fas fa-toggle-on me-1"></i> Status</label>
                        <div style="position: relative;">
                            <select name="status" class="form-input" style="padding-left: 36px;">
                                <option value="">All Statuses</option>
                                <option value="Paid" {{ request('status') === 'Paid' ? 'selected' : '' }}>Paid</option>
                                <option value="Partial" {{ request('status') === 'Partial' ? 'selected' : '' }}>Partial</option>
                                <option value="Unpaid" {{ request('status') === 'Unpaid' ? 'selected' : '' }}>Unpaid</option>
                            </select>
                            <i class="fas fa-info-circle text-muted position-absolute" style="left: 14px; top: 50%; transform: translateY(-50%);"></i>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 d-flex gap-2 justify-content-end mt-3">
                        <button type="submit" class="button button-primary px-4 py-2" style="height: 42px;">
                            <i class="fas fa-filter me-2"></i>Filter
                        </button>
                        @if(request()->anyFilled(['from_date', 'to_date', 'course_id', 'payment_method', 'fee_category', 'status']))
                            <a href="{{ route('reports.index') }}" class="button button-secondary px-3 py-2 d-grid place-items-center" style="height: 42px; width: 42px;">
                                <i class="fas fa-undo"></i>
                            </a>
                        @endif
                        <a href="{{ route('reports.export.csv', request()->all()) }}" class="button button-info px-4 py-2 text-white" style="background-color: #0d9488; height: 42px;">
                            <i class="fas fa-file-excel me-2"></i>Export CSV
                        </a>
                        <a href="{{ route('reports.export.pdf', request()->all()) }}" target="_blank" class="button button-success px-4 py-2" style="background-color: #10b981; height: 42px;">
                            <i class="fas fa-file-pdf me-2"></i>Print / PDF
                        </a>
                    </div>
                </form>
            </div>

            <!-- Stats Cards -->
            <div class="row g-4 mb-4">
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="card premium-stat-card h-100 p-4 border shadow-sm">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="icon-wrapper d-grid place-items-center" style="width: 44px; height: 44px; border-radius: 12px; background: rgba(16, 185, 129, 0.1); color: #10b981;">
                                <i class="fas fa-rupee-sign fa-lg"></i>
                            </div>
                        </div>
                        <h4 class="text-muted small fw-bold mb-1" style="font-size: 0.75rem;">TOTAL COLLECTED</h4>
                        <h3 class="fw-bold mb-0 text-dark-title text-success">₹{{ number_format($totalCollected, 2) }}</h3>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="card premium-stat-card h-100 p-4 border shadow-sm">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="icon-wrapper d-grid place-items-center" style="width: 44px; height: 44px; border-radius: 12px; background: rgba(255, 85, 50, 0.1); color: var(--first-color);">
                                <i class="fas fa-percentage fa-lg"></i>
                            </div>
                        </div>
                        <h4 class="text-muted small fw-bold mb-1" style="font-size: 0.75rem;">TOTAL DISCOUNTS</h4>
                        <h3 class="fw-bold mb-0 text-dark-title" style="color: var(--first-color);">₹{{ number_format($totalDiscount, 2) }}</h3>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="card premium-stat-card h-100 p-4 border shadow-sm">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="icon-wrapper d-grid place-items-center" style="width: 44px; height: 44px; border-radius: 12px; background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                                <i class="fas fa-gavel fa-lg"></i>
                            </div>
                        </div>
                        <h4 class="text-muted small fw-bold mb-1" style="font-size: 0.75rem;">TOTAL FINES</h4>
                        <h3 class="fw-bold mb-0 text-dark-title text-warning">₹{{ number_format($totalFine, 2) }}</h3>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="card premium-stat-card h-100 p-4 border shadow-sm">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="icon-wrapper d-grid place-items-center" style="width: 44px; height: 44px; border-radius: 12px; background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                                <i class="fas fa-exclamation-triangle fa-lg"></i>
                            </div>
                        </div>
                        <h4 class="text-muted small fw-bold mb-1" style="font-size: 0.75rem;">TOTAL OUTSTANDING</h4>
                        <h3 class="fw-bold mb-0 text-dark-title text-danger">₹{{ number_format($totalDue, 2) }}</h3>
                    </div>
                </div>
            </div>

            <!-- Invoices Table -->
            <div class="card premium-stat-card p-0 table-card overflow-hidden border shadow-sm">
                <div class="premium-card-header bg-transparent border-bottom p-4 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="fas fa-file-alt text-first"></i> Filtered Transactions Ledger
                    </h5>
                    <span class="badge bg-light text-dark border px-3 py-1.5 fw-bold" style="font-size: 0.8rem;">
                        {{ $totalInvoicesCount }} records found
                    </span>
                </div>
                <div class="card-body p-0">
                    <table class="table premium-table table-hover align-middle mb-0">
                        <thead>
                            <tr class="table-light-head">
                                <th class="ps-4">Receipt No</th>
                                <th>Student</th>
                                <th>Course</th>
                                <th>Category</th>
                                <th>Payment Date</th>
                                <th>Total</th>
                                <th>Paid</th>
                                <th>Due</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $invoice)
                                <tr>
                                    <td class="ps-4">
                                        <span class="badge bg-light text-dark border p-2 fw-bold" style="font-size: 0.8rem;">
                                            {{ $invoice->invoice_no }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong>{{ $invoice->student?->first_name }} {{ $invoice->student?->last_name }}</strong>
                                        <p class="text-muted small mb-0">Adm: {{ $invoice->student?->admission_no ?? '-' }}</p>
                                    </td>
                                    <td>{{ $invoice->student?->course?->name ?? 'N/A' }}</td>
                                    <td>{{ $invoice->fee_category ?? 'Fees' }}</td>
                                    <td>{{ $invoice->payment_date ? $invoice->payment_date->format('M d, Y') : '-' }}</td>
                                    <td class="fw-bold">₹{{ number_format($invoice->total_amount, 2) }}</td>
                                    <td class="text-success fw-bold">₹{{ number_format($invoice->paid_amount, 2) }}</td>
                                    <td class="{{ $invoice->due_amount > 0 ? 'text-danger' : 'text-success' }} fw-bold">
                                        ₹{{ number_format($invoice->due_amount, 2) }}
                                    </td>
                                    <td>
                                        @php
                                            $statusClass = strtolower($invoice->status);
                                        @endphp
                                        <span class="status-badge status-{{ $statusClass }}" style="padding: 4px 10px; border-radius: 6px; font-weight: 600; font-size: 0.8rem;">
                                            {{ $invoice->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        <i class="fas fa-folder-open fa-3x mb-3 text-muted" style="opacity: 0.5;"></i>
                                        <p class="mb-0 fw-semibold">No transactions match your search filters.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($invoices->hasPages())
                    <div class="card-footer bg-transparent border-top p-4">
                        {{ $invoices->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const skeleton = document.getElementById('page-skeleton');
            const content = document.getElementById('page-content');
            setTimeout(() => {
                if (skeleton) skeleton.style.display = 'none';
                if (content) content.style.opacity = '1';
            }, 400);
        });
    </script>
@endsection
