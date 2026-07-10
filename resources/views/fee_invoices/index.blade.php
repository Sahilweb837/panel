@extends('layouts.app')

@section('title', 'Fee Receipts')
@section('page-title', 'Fee Receipt Management')

@section('content')
    <div class="invoice-container">
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
                <form method="GET" action="{{ route('fee_invoices.index') }}" class="filter-form d-flex align-items-center gap-2 flex-grow-1">
                    <div style="position: relative; flex: 1;">
                        <input type="text" name="search" placeholder="Search by receipt number or student name..." value="{{ request('search') }}" class="form-input" style="padding-left: 36px;" />
                        <i class="fas fa-search text-muted position-absolute" style="left: 14px; top: 50%; transform: translateY(-50%);"></i>
                    </div>
                    <div style="position: relative; width: 180px;">
                        <select name="status" class="form-input" style="padding-left: 36px;">
                            <option value="">All Statuses</option>
                            <option value="Paid" {{ request('status') === 'Paid' ? 'selected' : '' }}>Paid</option>
                            <option value="Partial" {{ request('status') === 'Partial' ? 'selected' : '' }}>Partial</option>
                            <option value="Unpaid" {{ request('status') === 'Unpaid' ? 'selected' : '' }}>Unpaid</option>
                        </select>
                        <i class="fas fa-filter text-muted position-absolute" style="left: 14px; top: 50%; transform: translateY(-50%);"></i>
                    </div>
                    <button type="submit" class="button button-secondary px-4 py-2">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                    @if(request('search') || request('status'))
                        <a href="{{ route('fee_invoices.index') }}" class="button button-secondary px-3 py-2">
                            <i class="fas fa-undo"></i>
                        </a>
                    @endif
                </form>
                <div class="d-flex gap-2 align-items-center">
                    <div class="form-check form-switch me-3 d-flex align-items-center gap-2">
                        <input class="form-check-input mt-0" type="checkbox" role="switch" id="toggleTrash" 
                               {{ request('trashed') ? 'checked' : '' }} 
                               onchange="window.location.href='{{ request()->fullUrlWithQuery(['trashed' => request('trashed') ? null : '1']) }}'">
                        <label class="form-check-label fw-bold text-dark-title" for="toggleTrash" style="cursor: pointer; margin-top: 2px;">
                            Show Recycle Bin Data
                        </label>
                    </div>
                    @if(request('trashed') && $invoices->count() > 0)
                        <form action="{{ route('fee_invoices.restore_all') }}" method="POST" class="d-inline" onsubmit="return confirmAction(event, 'Are you sure you want to restore all trashed receipts?');">
                            @csrf
                            <button type="submit" class="button button-success py-2 px-4" style="background-color: #10b981;">
                                <i class="fas fa-trash-restore me-2"></i>Restore All
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('fee_invoices.bulk-generate') }}" class="button button-info py-2 px-4" style="background-color: #4f46e5;">
                        <i class="fas fa-magic me-2"></i>Bulk Monthly Generate
                    </a>
                    <a href="{{ route('fee_invoices.monthly') }}" class="button button-info py-2 px-4" style="background-color: #0d9488;">
                        <i class="fas fa-calendar-alt me-2"></i>Monthly Fee Collection
                    </a>
                    <a href="{{ route('fee_invoices.create') }}" class="button button-primary py-2 px-4">
                        <i class="fas fa-plus me-2"></i>Create Receipt
                    </a>
                </div>
            </div>

            <div class="card premium-stat-card p-0 table-card overflow-hidden">
                <div class="premium-card-header bg-transparent border-bottom p-4">
                    <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="fas fa-file-invoice text-first"></i> Student Fee Receipts
                    </h5>
                </div>
                <div class="card-body p-0">
                    <table class="table premium-table table-hover align-middle mb-0">
                        <thead>
                            <tr class="table-light-head">
                                <th class="ps-4"><i class="fas fa-hashtag me-1"></i> Receipt No</th>
                                <th><i class="fas fa-user-graduate me-1"></i> Student</th>
                                <th><i class="fas fa-coins me-1"></i> Total (INR)</th>
                                <th><i class="fas fa-toggle-on me-1"></i> Status</th>
                                <th><i class="fas fa-wallet me-1"></i> Due (INR)</th>
                                <th><i class="fas fa-calendar me-1"></i> Payment Date</th>
                                <th class="text-end pe-4"><i class="fas fa-cogs me-1"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $invoice)
                                <tr>
                                    <td class="ps-4">
                                        <span class="badge bg-light text-dark border p-2 fw-bold" style="font-size: 0.8rem; {{ request('trashed') ? 'text-decoration: line-through; color: #dc3545 !important;' : '' }}">
                                            {{ $invoice->invoice_no }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="user-info">
                                            <div class="avatar" style="font-weight: 700; background: rgba(255, 85, 50, 0.1); color: var(--first-color);">
                                                {{ strtoupper(substr($invoice->student?->first_name ?? 'S', 0, 1)) }}
                                            </div>
                                            <div>
                                                <strong class="text-dark-title">{{ $invoice->student?->first_name ?? 'Unknown' }} {{ $invoice->student?->last_name }}</strong>
                                                <p class="text-muted small">Adm: {{ $invoice->student?->admission_no ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-muted fw-bold">{{ number_format($invoice->total_amount, 2) }}</td>
                                    <td>
                                        @php
                                            $statusClass = strtolower($invoice->status);
                                        @endphp
                                        <span class="status-badge status-{{ $statusClass }}" style="padding: 4px 10px; border-radius: 6px; font-weight: 600; font-size: 0.8rem;">
                                            <i class="fas fa-{{ $statusClass === 'paid' ? 'check-circle' : ($statusClass === 'partial' ? 'adjust' : 'times-circle') }} me-1"></i>
                                            {{ $invoice->status }}
                                        </span>
                                    </td>
                                    <td class="{{ $invoice->due_amount > 0 ? 'text-danger' : 'text-success' }} fw-bold">
                                        {{ number_format($invoice->due_amount, 2) }}
                                    </td>
                                    <td class="text-muted">{{ $invoice->payment_date ? \Carbon\Carbon::parse($invoice->payment_date)->format('M d, Y') : 'N/A' }}</td>
                                    <td class="text-end pe-4 action-cell">
                                        @if($invoice->trashed())
                                            <form action="{{ route('fee_invoices.restore', $invoice->id) }}" method="POST" class="inline-form d-inline" onsubmit="return confirmAction(event, 'Restore this receipt?');">
                                                @csrf
                                                <button type="submit" class="button button-success small py-1.5 px-3">
                                                    <i class="fas fa-trash-restore me-1"></i>Restore
                                                </button>
                                            </form>
                                        @else
                                            @if($invoice->status !== 'Paid')
                                                <button type="button" class="button button-info small py-1.5 px-3 receive-payment-btn" 
                                                        data-id="{{ $invoice->id }}" 
                                                        data-invoice-no="{{ $invoice->invoice_no }}"
                                                        data-due="{{ $invoice->due_amount }}"
                                                        data-student="{{ $invoice->student?->first_name }} {{ $invoice->student?->last_name }}"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#receivePaymentModal"
                                                        style="background-color: #3b82f6; border-color: #3b82f6;">
                                                    <i class="fas fa-hand-holding-usd me-1"></i>Pay
                                                </button>
                                            @endif
                                            <a href="{{ route('fee_invoices.show', $invoice) }}" class="button button-secondary small py-1.5 px-3" target="_blank">
                                                <i class="fas fa-print me-1"></i>Print
                                            </a>
                                            <form action="{{ route('fee_invoices.destroy', $invoice) }}" method="POST" class="inline-form d-inline" onsubmit="return confirmAction(event, 'Are you sure you want to delete this receipt?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="button button-danger small py-1.5 px-3">
                                                    <i class="fas fa-trash me-1"></i>Delete
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fas fa-file-invoice-dollar fa-2x mb-3 d-block text-muted"></i>
                                        No student fee receipts registered.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="pagination-wrapper mt-4">
                {{ $invoices->links() }}
            </div>
        </div>
    </div>

    <!-- Receive Payment Modal -->
    <div class="modal fade" id="receivePaymentModal" tabindex="-1" aria-labelledby="receivePaymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg" style="background-color: #ffffff; color: #1e293b;">
                <div class="modal-header bg-primary text-white border-0 py-3 px-4">
                    <h5 class="modal-title fw-bold" id="receivePaymentModalLabel"><i class="fas fa-hand-holding-usd me-2"></i>Record Student Payment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="receivePaymentForm" method="POST" action="">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3 p-3 bg-light rounded-3 border">
                            <div class="d-flex justify-content-between mb-1 text-dark"><span class="text-muted small">Student:</span> <strong id="modalStudentName"></strong></div>
                            <div class="d-flex justify-content-between mb-1 text-dark"><span class="text-muted small">Receipt No:</span> <strong id="modalReceiptNo"></strong></div>
                            <div class="d-flex justify-content-between text-dark"><span class="text-muted small">Remaining Due:</span> <strong class="text-danger" id="modalDueAmount"></strong></div>
                        </div>

                        <div class="mb-3">
                            <label for="paid_amount" class="form-label fw-bold text-dark"><i class="fas fa-coins text-primary me-1"></i>Amount to Receive (INR)</label>
                            <input type="number" step="0.01" min="0.01" class="form-control form-input" id="paid_amount" name="paid_amount" required>
                        </div>

                        <div class="mb-3">
                            <label for="payment_method" class="form-label fw-bold text-dark"><i class="fas fa-wallet text-primary me-1"></i>Payment Method</label>
                            <select class="form-select form-input" id="payment_method" name="payment_method" required>
                                <option value="UPI">UPI (GPay / PhonePe / Paytm)</option>
                                <option value="Cash">Cash</option>
                                <option value="Bank Transfer">Bank Transfer / NEFT</option>
                                <option value="Cheque">Cheque</option>
                                <option value="Card">Credit/Debit Card</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="payment_date" class="form-label fw-bold text-dark"><i class="fas fa-calendar-alt text-primary me-1"></i>Payment Date</label>
                            <input type="date" class="form-control form-input" id="payment_date" name="payment_date" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="transaction_id" class="form-label fw-bold text-dark"><i class="fas fa-barcode text-primary me-1"></i>Transaction / Ref / UTR No.</label>
                            <input type="text" class="form-control form-input" id="transaction_id" name="transaction_id" placeholder="Optional reference number">
                        </div>

                        <div class="mb-0">
                            <label for="remarks" class="form-label fw-bold text-dark"><i class="fas fa-comment-alt text-primary me-1"></i>Remarks</label>
                            <input type="text" class="form-control form-input" id="remarks" name="remarks" placeholder="Optional payment remarks">
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 px-4 pb-4">
                        <button type="button" class="btn btn-secondary px-3 py-2 rounded-3 text-dark bg-light border" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-bold"><i class="fas fa-check-circle me-1"></i>Submit Payment</button>
                    </div>
                </form>
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

            // Bind data to Receive Payment Modal
            const paymentModal = document.getElementById('receivePaymentModal');
            if (paymentModal) {
                paymentModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const id = button.getAttribute('data-id');
                    const invoiceNo = button.getAttribute('data-invoice-no');
                    const due = parseFloat(button.getAttribute('data-due'));
                    const student = button.getAttribute('data-student');

                    document.getElementById('modalStudentName').textContent = student;
                    document.getElementById('modalReceiptNo').textContent = invoiceNo;
                    document.getElementById('modalDueAmount').textContent = '₹' + due.toFixed(2);
                    
                    const amountInput = document.getElementById('paid_amount');
                    amountInput.value = due.toFixed(2);
                    amountInput.max = due.toFixed(2);

                    const form = document.getElementById('receivePaymentForm');
                    form.action = `/fee_invoices/${id}/receive-payment`;
                });
            }
        });
    </script>
@endsection
